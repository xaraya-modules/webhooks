<?php
/**
 * Entrypoint for webhooks (via ws.php) using FastRoute dispatcher
 *
 * @see https://github.com/xaraya/core
 */

namespace Xaraya\Modules\Webhooks\Endpoint;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function FastRoute\simpleDispatcher;

class FastRouteEndpoint
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
        // use config to generate routes
        $routes = $this->getConfig('routes');

        $dispatcher = simpleDispatcher(function (RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });
        return $dispatcher;
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
                return new Response(null, 404);
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                return new Response(null, 405);
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // ... call $handler with $vars
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
