<?php

namespace Xaraya\Modules\Webhooks\Endpoint;

/**
 * Entrypoint for webhooks (via ws.php) interface
 */
interface EndpointInterface
{
    /**
     * @param array<string, mixed> $config
     * @return void
     */
    public function setConfig(array $config);

    /**
     * @return mixed
     */
    public function getConfig(string $name);

    /**
     * @return void
     */
    public function run();
}
