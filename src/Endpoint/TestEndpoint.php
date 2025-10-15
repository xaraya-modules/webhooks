<?php

namespace Xaraya\Modules\Webhooks\Endpoint;

use Symfony\Component\HttpFoundation\Request;
use Xaraya\Modules\Webhooks\Controller\TestController;

/**
 * Entrypoint for webhooks (via ws.php) using TestController
 */
class TestEndpoint implements EndpointInterface
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
     * @return TestController
     */
    public function getController(array $config = [])
    {
        return new TestController($config);
    }

    /**
     * Using controller
     */
    public function runWithController()
    {
        $request = $this->getRequest();
        $type = $request->get('name', 'test');

        $controller = $this->getController();
        $response = $controller->handle($type, $request);

        $response->send();
    }
}
