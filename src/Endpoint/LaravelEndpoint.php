<?php
/**
 * Entrypoint for webhooks (via ws.php) using Laravel (Spatie) Webhook Client
 *
 * @see https://github.com/spatie/laravel-webhook-client
 */

namespace Xaraya\Modules\Webhooks\Endpoint;

use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Http\Request as HttpRequest;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookConfigRepository;
use Spatie\WebhookClient\WebhookProcessor;
use Spatie\WebhookClient\Http\Controllers\WebhookController;

class LaravelEndpoint
{
    /** @var array<string, mixed> */
    protected array $mapping = [];
    protected WebhookConfigRepository $configRepository;

    public function __construct(array $config = [])
    {
        $this->setConfig($config['mapping']);
    }

    public function setConfig(array $mapping)
    {
        $this->mapping = $mapping;
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
        // @todo this also relies on app() internally
        //require_once dirname(__DIR__) . '/helpers.php';
        return new WebhookConfig($this->mapping[$name] ?? $this->getDefaultConfig($name));
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
        // @todo verify default app settings
        $_ENV['APP_DEBUG'] = 0;
        $_ENV['APP_WEBHOOK_ECHO'] = 1;
        # TODO: replace APP_KEY with your own for production
        $_ENV['APP_KEY'] = 'base64:ODU2OThiNjM0YWJkOGRlNWQ0NDljZDBjYmVmMTk2OTg=';
        //$_ENV['LARAVEL_STORAGE_PATH'] = $dir . '/var/storage';
        $_ENV['COMPOSER_VENDOR_DIR'] = $dir . '/vendor';
        // @see Illuminate\Foundation\Application::normalizeCachePath()
        //$_ENV['APP_PACKAGES_CACHE'] = $dir . '/var/bootstrap/cache/packages.php';
        //$_ENV['APP_SERVICES_CACHE'] = $dir . '/var/bootstrap/cache/services.php';
        //$_ENV['APP_EVENTS_CACHE'] = $dir . '/var/bootstrap/cache/events.php';
        //$_ENV['APP_ROUTES_CACHE'] = $dir . '/var/bootstrap/cache/routes-v7.php';
        //$_ENV['APP_CONFIG_CACHE'] = $dir . '/var/bootstrap/cache/config.php';
        // @see Illuminate\View\ViewServiceProvider - $app['config']['view.compiled']
        $_ENV['VIEW_COMPILED_PATH'] = $dir . '/var/storage/framework/views';

        // Bootstrap Laravel...
        $app = require_once $dir . '/vendor/xaraya/with-laravel/bootstrap/app.php';

        // Set paths where possible
        $app->useBootstrapPath($dir . '/var/bootstrap');
        $app->useDatabasePath($dir . '/var/database');
        $app->useStoragePath($dir . '/var/storage');

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
