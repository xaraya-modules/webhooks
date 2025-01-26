<?php

namespace Xaraya\Modules\Webhooks\Endpoint;

use xarCore;
use xarController;
use xarEvents;
use xarLog;
use xarMod;
use xarRequest;
use sys;
use DataObjectFactory;
use Exception;

sys::import('xaraya.facades.logger');
use Xaraya\Facades\xarLog3;

/**
 * Entrypoint for webhooks (via ws.php) using Xaraya Core
 * Note: require_once vendor/autoload.php and sys::init() already done in ws.php
 *
 * @see https://github.com/xaraya/core
 */
class XarayaEndpoint implements EndpointInterface
{
    /** @var array<string, mixed> */
    protected array $config = [];

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function run()
    {
        if (!empty($this->getConfig('object'))) {
            $this->runWithObjectMethod();
        } elseif (!empty($this->getConfig('module'))) {
            $this->runWithModuleApiFunction();
        } else {
            //$this->runWithCore();
            echo '@todo';
        }
    }

    public function getConfig(string $name)
    {
        return $this->config[$name] ?? null;
    }

    /**
     * Initialize Xaraya core
     */
    public function initCore($whatToLoad = xarCore::SYSTEM_ALL)
    {
        /**
         * Set up caching
         * Note: this happens first so we can serve cached pages to first-time visitors
         *       without loading the core
         */
        // This should not be the case here for webhooks
        //sys::import('xaraya.caching');
        // Note: we may already exit here if session-less page caching is enabled
        //xarCache::init();

        /**
        * Load the Xaraya core
        */
        sys::import('xaraya.core');
        xarCore::xarInit($whatToLoad);
    }

    /**
     * @todo
     * @return xarRequest
     */
    public function getRequest()
    {
        // Create the object that models this request
        $request = xarController::getRequest();
        xarController::normalizeRequest();
        xarLog3::notice('Retrieved a request: ' . $request->getModule() . "_" . $request->getType() . "_" . $request->getFunction());

        return $request;
    }

    /**
     * @param xarRequest $request
     * @param bool $isJson use POST'ed input as json
     * @param bool $isMixed allow mixing POST'ed input and GET url params
     * @return array<string, mixed>
     */
    public function getArguments($request, $isJson = true, $isMixed = false)
    {
        if (empty($isJson)) {
            return $request->getFunctionArgs();
        }
        $rawInput = file_get_contents('php://input');
        if (!empty($rawInput)) {
            $args = json_decode($rawInput, true, 10, JSON_THROW_ON_ERROR);
        } else {
            $args = [];
        }
        if (!empty($isMixed)) {
            $args += $request->getFunctionArgs();
        }
        return $args;
    }

    /**
     * @param string $logname
     * @param xarRequest $request
     * @param mixed $context
     * @return void
     */
    public function logRequest($logname, $request, $context)
    {
        $logobject = DataObjectFactory::getObject(['name' => $logname], $context);
        $server = $request->getServerContext();
        $data = [
            'request' => $request->getRawURL(),
            'headers' => json_encode(getallheaders()),  // @todo $server->getHeaders(),
            'payload' => $server->getRawInput(),
        ];
        $logobject->createItem($data);
    }

    /**
     * @param xarRequest $request
     * @param array<string, mixed> $security
     * @return bool
     */
    public function verifySignature($request, $security)
    {
        if (empty($security) ||
            empty($security['signature']) ||
            empty($security['secret'])) {
            return true;
        }
        $server = $request->getServerContext();
        $serverVar = 'HTTP_' . strtoupper(str_replace('-', '_', $security['signature']));
        $signature = $server->getServerVar($serverVar);
        if (empty($signature)) {
            return false;
        }
        $rawInput = $server->getRawInput();
        if (empty($rawInput)) {
            return false;
        }
        // verify timestamp (in header = string or body = array)
        if (!empty($security['timestamp'])) {
            if (is_string($security['timestamp'])) {
                $serverVar = 'HTTP_' . strtoupper(str_replace('-', '_', $security['timestamp']));
                $timestamp = $server->getServerVar($serverVar);
                if (empty($timestap)) {
                    return false;
                }
                // timestamp is not part of body - add to hmac to verify
                $verify = hash_hmac('sha256', $timestamp . $rawInput, $security['secret']);
            } else {
                // check body field for timestamp
                $body = json_decode($rawInput, true, 10, JSON_THROW_ON_ERROR);
                if (!array_key_exists($security['timestamp'], $body)) {
                    return false;
                }
                $timestamp = $body[$security['timestamp']];
                // timestamp is already part of body - do not add to hmac
                $verify = hash_hmac('sha256', $rawInput, $security['secret']);
            }
            $now = time();
            if (intval($timestamp) > $now + 60 || intval($timestamp) < $now - 60) {
                return false;
            }
        } else {
            $verify = hash_hmac('sha256', $rawInput, $security['secret']);
        }
        if (!hash_equals($verify, $signature)) {
            return false;
        }
        return true;
    }

    /**
     * Using module api function
     */
    public function runWithModuleApiFunction()
    {
        // we need to initialize the core first here
        $this->initCore();

        $request = $this->getRequest();
        // Get context of the request if available
        $context = $request->getServerContext()?->getContext();

        // Verify signature based on secret (if any)
        $security = $this->getConfig('security');
        if (!$this->verifySignature($request, $security)) {
            echo 'Invalid webhook message';
            return;
        }
        $logname = $this->getConfig('logger');
        if (!empty($logname)) {
            $this->logRequest($logname, $request, $context);
        }

        $info = $this->getConfig('module');
        $info['json'] ??= true;
        // allow mixing POST'ed input and GET url params by default for module api function
        $info['mixed'] ??= true;
        $args = $this->getArguments($request, $info['json'], $info['mixed']);
        $data = xarMod::apiFunc($info['name'], $info['type'], $info['func'], $args, $context);

        if (is_string($data)) {
            echo $data;
            return;
        }
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Using object method
     */
    public function runWithObjectMethod()
    {
        // we need to initialize the core first here
        $this->initCore();

        $request = $this->getRequest();
        // Get context of the request if available
        $context = $request->getServerContext()?->getContext();

        // Verify signature based on secret (if any)
        $security = $this->getConfig('security');
        if (!$this->verifySignature($request, $security)) {
            echo 'Invalid webhook message';
            return;
        }
        $logname = $this->getConfig('logger');
        if (!empty($logname)) {
            $this->logRequest($logname, $request, $context);
        }

        $info = $this->getConfig('object');
        $info['json'] ??= true;
        // do *not* allow mixing POST'ed input and GET url params by default for object method
        $info['mixed'] ??= false;
        $args = $this->getArguments($request, $info['json'], $info['mixed']);
        if (!empty($info['handler'])) {
            // @todo add handler method
            $handler = new $info['handler']($info);
            if (method_exists($handler, 'setContext')) {
                $handler->setContext($context);
            }
        } elseif (!empty($info['name'])) {
            $object = DataObjectFactory::getObject($info, $context);
            $info['method'] ??= 'createItem';
            if (!method_exists($object, $info['method'])) {
                throw new Exception('Invalid object method for Xaraya endpoint');
            }
            if (empty($args)) {
                echo 'Hello! You didn\'t specify any arguments for the object method...';
                return;
            }
            $data = $object->{$info['method']}($args);
        } elseif (!empty($info['class'])) {
            // @todo add class method
            $object = new $info['class']($info);
            if (method_exists($object, 'setContext')) {
                $object->setContext($context);
            }
        } else {
            throw new Exception('Invalid object for Xaraya endpoint');
        }

        if (is_string($data)) {
            echo $data;
            return;
        }
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function getKernel()
    {
        // we'll handle this ourselves :-)
        return $this;
    }

    public function handle($request)
    {
        // Get context of the request if available
        //$context = $request->getServerContext()?->getContext();
        // Use Twig templates with Xaraya - install xaraya/twig package with composer first
        /** un-comment the next line to activate Twig templates */
        //$context['twig'] = true;

        // Process the request
        xarLog3::notice('Dispatching request: ' . $request->getModule() . "_" . $request->getType() . "_" . $request->getFunction());
        xarController::dispatch($request);

        // Retrieve the output to send to the browser
        xarLog3::notice('Processing request ' . $request->getModule() . "_" . $request->getType() . "_" . $request->getFunction());
        $mainModuleOutput = xarController::getResponse()->getOutput();

        // We're all done, one ServerRequest made
        xarLog3::notice('Notifying listeners of this request');
        xarEvents::notify('ServerRequest');

        // Render page with the output + pass along the current context
        //xarLog3::notice('Creating the page output');
        //$pageOutput = xarTpl::renderPage($mainModuleOutput, null, $context);

        return $mainModuleOutput;
    }

    /**
     * Using Core with events
     */
    public function runWithCore()
    {
        // we need to initialize the core first here
        $this->initCore();

        $request = $this->getRequest();

        // use fake kernel = this to align with other endpoints
        $kernel = $this->getKernel();

        $mainModuleOutput = $kernel->handle($request);

        echo $mainModuleOutput;
    }
}
