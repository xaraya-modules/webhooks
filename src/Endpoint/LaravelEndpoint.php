<?php

namespace Xaraya\Modules\Webhooks\Endpoint;

use Illuminate\Http\Request as HttpRequest;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookConfigRepository;

/**
 * Entrypoint for webhooks (via ws.php) using Laravel (Spatie) Webhook Client
 *
 * @see https://github.com/spatie/laravel-webhook-client
 */
class LaravelEndpoint implements EndpointInterface
{
    /** @var array<string, mixed> */
    protected array $config = [];
    /** @var array<string, mixed> */
    protected array $mapping = [];
    protected WebhookConfigRepository $configRepository;

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
        //$this->mapping = $config['mapping'] ?? [];
        //$this->configRepository = new WebhookConfigRepository();
        //foreach ($mapping as $name => $properties) {
        //    $this->configRepository->addConfig(new WebhookConfig($properties));
        //}
    }

    public function run()
    {
        $this->runWithApp();
    }

    public function getConfig(string $name)
    {
        return $this->config[$name];
        // @todo this also relies on app() internally
        //require_once dirname(__DIR__) . '/helpers.php';
        //return new WebhookConfig($this->mapping[$name] ?? $this->getDefaultConfig($name));
    }

    public function getDefaultConfig(string $name)
    {
        return [
            'name' => $name,
        ];
    }

    /**
     * @return HttpRequest
     */
    public function getRequest()
    {
        return HttpRequest::capture();
    }

    /**
     * Using Laravel App
     * @see https://github.com/xaraya-modules/with-laravel
     */
    public function getApp()
    {
        // @checkme if this was installed in vendor or not
        $dir = dirname(__DIR__, 2);
        while (strlen($dir) > 1 && str_contains($dir, '/vendor')) {
            $dir = dirname($dir);
        }
        foreach ($this->getConfig('environment') as $key => $value) {
            if (str_ends_with($key, '_DIR') || str_ends_with($key, '_PATH')) {
                // fix dirs relative to root dir
                if (!str_starts_with($key, '/')) {
                    $value = $dir . '/' . $value;
                }
                // create dir if needed
                if (!is_dir($value)) {
                    mkdir($value, 0o755, true);
                }
            }
            // set env var only if it's not defined yet
            $_ENV[$key] ??= $value;
        }
        // Special case for bootstrap cache path - it must exist first
        $value = $_ENV['LARAVEL_BOOTSTRAP_PATH'] . '/cache';
        // create dir if needed
        if (!is_dir($value)) {
            mkdir($value, 0o755, true);
        }
        // @see Illuminate\Foundation\Application::normalizeCachePath()
        //$_ENV['APP_PACKAGES_CACHE'] = $dir . '/var/bootstrap/cache/packages.php';
        //$_ENV['APP_SERVICES_CACHE'] = $dir . '/var/bootstrap/cache/services.php';
        //$_ENV['APP_EVENTS_CACHE'] = $dir . '/var/bootstrap/cache/events.php';
        //$_ENV['APP_ROUTES_CACHE'] = $dir . '/var/bootstrap/cache/routes-v7.php';
        //$_ENV['APP_CONFIG_CACHE'] = $dir . '/var/bootstrap/cache/config.php';
        // @see Illuminate\View\ViewServiceProvider - $app['config']['view.compiled']
        //$_ENV['VIEW_COMPILED_PATH'] = $dir . '/var/storage/framework/views';
        // Copy initial Laravel database from webhooks
        if (!file_exists($_ENV['LARAVEL_DATABASE_PATH'] . '/database.sqlite')) {
            $filepath = $dir . '/vendor/xaraya/webhooks/var/database/database.sqlite';
            if (file_exists($filepath)) {
                copy($filepath, $_ENV['LARAVEL_DATABASE_PATH'] . '/database.sqlite');
                chmod($_ENV['LARAVEL_DATABASE_PATH'] . '/database.sqlite', 0o644);
            }
        }

        // Bootstrap Laravel...
        $app = require_once $dir . '/vendor/xaraya/with-laravel/bootstrap/app.php';

        // Set paths where possible
        $app->useBootstrapPath($_ENV['LARAVEL_BOOTSTRAP_PATH']);
        $app->useDatabasePath($_ENV['LARAVEL_DATABASE_PATH']);
        $app->useStoragePath($_ENV['LARAVEL_STORAGE_PATH']);

        return $app;
    }

    /**
     * Using Laravel App
     */
    public function runWithApp()
    {
        // Bootstrap Laravel and handle the request...
        //(require_once __DIR__.'/../bootstrap/app.php')
        //->handleRequest(Request::capture());

        // Bootstrap Laravel...
        $app = $this->getApp();

        // ...and handle the request
        $app->handleRequest(HttpRequest::capture());
    }
}
