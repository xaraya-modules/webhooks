<?php
/**
 * Entrypoint for webhooks (via ws.php) using Symfony Webhook
 *
 * @see https://github.com/symfony/webhook
 */

namespace Xaraya\Modules\Webhooks\Endpoint;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Webhook\Controller\WebhookController;

class SymfonyEndpoint
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
        $this->runWithController();
        //$this->runWithKernel();
    }

    public function getConfig(string $name)
    {
        return $this->config[$name];
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        // create the Request object
        return Request::createFromGlobals();
    }

    /**
     * @return WebhookController
     */
    public function getController(array $parsers, MessageBusInterface $bus)
    {
        return new WebhookController($parsers, $bus);
    }

    /**
     * @return HttpKernel
     */
    public function getKernel()
    {
        $dispatcher = new EventDispatcher();
        // ... add some event listeners

        // create your controller and argument resolvers
        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        // instantiate the kernel
        return new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);
    }

    /**
     * Using controller
     */
    public function runWithController()
    {
        $request = $this->getRequest();
        $type = $request->get('name', '');

        $controller = $this->getController($this->getConfig('parsers'), $this->getConfig('bus'));
        $response = $controller->handle($type, $request);

        $response->send();
    }

    /**
     * Using HttpKernel with events
     *
     * @see https://symfony.com/doc/current/components/http_kernel.html#httpkernel-driven-by-events
     */
    public function runWithKernel()
    {
        $request = $this->getRequest();
        // for example, possibly set its _controller manually
        $request->attributes->set('_controller', [WebhookController::class, 'handle']);
        //$request->attributes->set('_controller', [WebhookTestController::class, 'handle']);

        $kernel = $this->getKernel();

        // actually execute the kernel, which turns the request into a response
        // by dispatching events, calling a controller, and returning the response
        $response = $kernel->handle($request);

        // send the headers and echo the content
        $response->send();

        // trigger the kernel.terminate event
        $kernel->terminate($request, $response);
    }
}
