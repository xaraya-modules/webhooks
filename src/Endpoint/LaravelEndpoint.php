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
        $this->runWithProcessor();
        //$this->runWithController();
        //$this->runWithKernel();
    }

    public function getConfig(string $name)
    {
        // @todo this also relies on app() internally
        require_once dirname(__DIR__) . '/helpers.php';
        return new WebhookConfig($this->mapping[$name] ?? $this->getDefaultConfig($name));
    }

    public function getDefaultConfig(string $name)
    {
        return [
            'name' => $name
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
     * @return WebhookProcessor
     */
    public function getProcessor(HttpRequest $request, WebhookConfig $webhookConfig)
    {
        return new WebhookProcessor($request, $webhookConfig);
    }

    /**
     * @return WebhookController
     */
    public function getController()
    {
        return new WebhookController();
    }

    /**
     * @return HttpKernelContract
     */
    public function getKernel()
    {
        //$kernel = $this->make(HttpKernelContract::class);
        return '@todo';
    }

    /**
     * Using processor
     */
    public function runWithProcessor()
    {
        $request = $this->getRequest();
        $type = $request->input('name', '');

        $processor = $this->getProcessor($request, $this->getConfig($type));
        $response = $processor->process();

        $response->send();
    }

    /**
     * Using controller
     */
    public function runWithController()
    {
        $request = $this->getRequest();
        $type = $request->input('name', '');

        $controller = $this->getController();
        $response = $controller($request, $this->getConfig($type));

        $response->send();
    }

    /**
     * Using HttpKernel with events
     * @todo needs full laravel/framework package - see if/how we should use this
     *
     * @see https://laravel.com/docs/11.x/lifecycle
     */
    public function runWithKernel()
    {
        $request = $this->getRequest();

        $kernel = $this->getKernel();

        $response = $kernel->handle($request)->send();

        $kernel->terminate($request, $response);
    }
}
