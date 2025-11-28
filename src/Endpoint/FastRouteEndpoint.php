<?php

namespace Xaraya\Modules\Webhooks\Endpoint;

use FastRoute\ConfigureRoutes;
use FastRoute\Dispatcher;
use FastRoute\FastRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Entrypoint for webhooks (via ws.php) using FastRoute dispatcher
 *
 * @see https://github.com/nikic/FastRoute
 */
class FastRouteEndpoint implements EndpointInterface
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
        $this->runWithDispatcher();
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

    public function getDispatcher()
    {
        $dispatcher = FastRoute::recommendedSettings($this->getRoutes(...), 'test')
            ->disableCache()
            ->dispatcher();

        return $dispatcher;
    }

    public function getRoutes(ConfigureRoutes $collector)
    {
        // use config to generate routes
        $routes = $this->getConfig('routes');

        foreach ($routes as $route) {
            $collector->addRoute($route[0], $route[1], $route[2]);
        }
    }

    public function getHandler($handler)
    {
        if (is_array($handler) && is_string($handler[0])) {
            $handler[0] = new $handler[0]();
        }
        return $handler;
    }

    public function handle($request, $routeInfo)
    {
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                $message = 'No route found matching ' . str_replace('%2F', '/', rawurlencode($request->getPathInfo()));
                return new Response($message, 404);
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                return new Response(null, 405);
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // extra options defined in route (_name) or set by route collector (_route = regex path)
                $extra = $routeInfo->extraParameters;
                // ... call $handler with $vars + $extra
                $handler = $this->getHandler($handler);
                $result = call_user_func($handler, 'fastroute', $request, $vars);
                return $result;
        }
    }

    /**
     * Using FastRoute dispatcher
     */
    public function runWithDispatcher()
    {
        $request = $this->getRequest();
        // url-decoded uri without query string
        $uri = $request->getPathInfo();

        $dispatcher = $this->getDispatcher();

        $routeInfo = $dispatcher->dispatch($request->getMethod(), $uri);

        $response = $this->handle($request, $routeInfo);

        $response->send();
    }
}
