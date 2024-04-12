<?php
/**
 * Webhooks module configuration
 */

namespace Xaraya\Modules\Webhooks\Configuration;

use Xaraya\Modules\Webhooks\Controller\TestController;
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
                'mapping' => [
                    '/symfony/' => '/',
                ],
            ],
            'laravel' => [
                'endpoint' => \Xaraya\Modules\Webhooks\Endpoint\LaravelEndpoint::class,
                'mapping' => [
                    '/laravel/' => '/',
                ],
            ],
            'fastroute' => [
                'endpoint' => \Xaraya\Modules\Webhooks\Endpoint\FastRouteEndpoint::class,
                'mapping' => [
                    // '/fastroute/' => '/',
                ],
                'routes' => [
                    // ['GET', '/', [TestController::class, 'handle']],
                    ['GET', '/fastroute/', [TestController::class, 'handle']],
                ],
            ],
            'test' => [
                'endpoint' => \Xaraya\Modules\Webhooks\Endpoint\TestEndpoint::class,
                'mapping' => [
                ],
            ],
            'hello' => [
                'endpoint' => \Xaraya\Modules\Webhooks\Endpoint\SymfonyEndpoint::class,
                'parsers' => [
                ],
                'bus' => null,
            ],
            'hello-laravel' => [
                'endpoint' => \Xaraya\Modules\Webhooks\Endpoint\LaravelEndpoint::class,
                'mapping' => [
                ],
            ],
        ];
    }

    public function getEndpoint(string $type = '', string $name = '')
    {
        $type = $type ?: 'webhook';
        $name = $name ?: 'home';
        if ($type !== 'webhook') {
            // pass through to other framework
            if (empty($this->config[$type])) {
                throw new Exception('Invalid entrypoint for ' . htmlspecialchars($type));
            }
            // map original uri to framework uri
            $mapping = $this->config[$type]['mapping'] ?? [];
            if (!empty($mapping)) {
                $_SERVER['REQUEST_URI'] = str_replace(array_keys($mapping), array_values($mapping), $_SERVER['REQUEST_URI']);
                if (!empty($_SERVER['PATH_INFO'])) {
                    $_SERVER['PATH_INFO'] = str_replace(array_keys($mapping), array_values($mapping), $_SERVER['PATH_INFO']);
                }
            }
            $name = $type;
        }
        if (empty($this->config[$name])) {
            throw new Exception('Invalid entrypoint for ' . htmlspecialchars($name));
        }
        return new $this->config[$name]['endpoint']($this->config[$name]);
    }

    public function getWebhooks()
    {
        return [
            'webhook/test',
            'webhook/hello',
            'webhook/hello-laravel',
        ];
    }
}
