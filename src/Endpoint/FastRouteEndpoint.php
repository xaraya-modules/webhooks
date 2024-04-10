<?php
/**
 * Entrypoint for webhooks (via ws.php) using FastRoute dispatcher
 *
 * @see https://github.com/xaraya/core
 */

namespace Xaraya\Modules\Webhooks\Endpoint;

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

    public function handle($request) {}

    /**
     * Using FastRoute dispatcher
     */
    public function runWithDispatcher()
    {
        echo '@todo';
    }
}
