<?php
/**
 * Webhooks module configuration
 */

namespace Xaraya\Modules\Webhooks\Configuration;

use Exception;
use Throwable;

class WebhooksConfig
{
    /** @var array<string, mixed> */
    protected array $config;

    public function __construct()
    {
        $this->loadConfig();
    }

    public function loadConfig(): void
    {
        $rootDir = dirname(__DIR__, 5);
        $apiCacheDir = $rootDir . '/html/var/cache/api';
        try {
            $this->config = require $apiCacheDir . '/webhooks_config.php';
        } catch (Throwable) {
            file_put_contents($apiCacheDir . '/webhooks_config.php', '<?' . "php\nreturn [];");
        }
        if (empty($this->config)) {
            $this->config = $this->getDefaultConfig();
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultConfig()
    {
        return [
            'xaraya' => [
                'endpoint' => \Xaraya\Modules\Webhooks\Endpoint\XarayaEndpoint::class,
                'mapping' => [
                ],
            ],
            'symfony' => [
                'endpoint' => \Xaraya\Modules\Webhooks\Endpoint\SymfonyEndpoint::class,
                'parsers' => [
                ],
                'bus' => null,
            ],
            'laravel' => [
                'endpoint' => \Xaraya\Modules\Webhooks\Endpoint\LaravelEndpoint::class,
                'mapping' => [
                ],
            ],
            'fastroute' => [
                'endpoint' => \Xaraya\Modules\Webhooks\Endpoint\FastRouteEndpoint::class,
                'mapping' => [
                ],
            ],
            'test' => [
                'endpoint' => \Xaraya\Modules\Webhooks\Endpoint\TestEndpoint::class,
                'mapping' => [
                ],
            ],
        ];
    }

    public function getEndpoint(string $name = '')
    {
        $name = $name ?: 'test';
        if (empty($this->config[$name])) {
            throw new Exception('Invalid entrypoint for ' . htmlspecialchars($name));
        }
        return new $this->config[$name]['endpoint']($this->config[$name]);
    }
}
