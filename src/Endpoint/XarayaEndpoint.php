<?php
/**
 * Entrypoint for webhooks (via ws.php) using Xaraya Core
 * Note: require_once vendor/autoload.php and sys::init() already done in ws.php
 *
 * @see https://github.com/xaraya/core
 */

namespace Xaraya\Modules\Webhooks\Endpoint;

use xarCore;
use xarController;
use xarEvents;
use xarLog;
use xarRequest;
use sys;

class XarayaEndpoint
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
        //$this->runWithController();
        $this->runWithCore();
    }

    public function getConfig(string $name)
    {
        return $this->config[$name];
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
        xarLog::message('Retrieved a request: ' . $request->getModule() . "_" . $request->getType() . "_" . $request->getFunction(), xarLog::LEVEL_NOTICE);

        return $request;
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
        xarLog::message('Dispatching request: ' . $request->getModule() . "_" . $request->getType() . "_" . $request->getFunction(), xarLog::LEVEL_NOTICE);
        xarController::dispatch($request);

        // Retrieve the output to send to the browser
        xarLog::message('Processing request ' . $request->getModule() . "_" . $request->getType() . "_" . $request->getFunction(), xarLog::LEVEL_NOTICE);
        $mainModuleOutput = xarController::getResponse()->getOutput();

        // We're all done, one ServerRequest made
        xarLog::message('Notifying listeners of this request', xarLog::LEVEL_NOTICE);
        xarEvents::notify('ServerRequest');

        // Render page with the output + pass along the current context
        //xarLog::message('Creating the page output', xarLog::LEVEL_NOTICE);
        //$pageOutput = xarTpl::renderPage($mainModuleOutput, null, $context);

        return $mainModuleOutput;
    }

    /**
     * Using controller
     */
    public function runWithController()
    {
        // @todo
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
