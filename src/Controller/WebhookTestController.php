<?php
/**
 * Webhook Test Controller
 * independent of the Symfony Webhook + RemoteEvent + Messenger combo
 *
 * @see https://github.com/symfony/symfony/blob/7.0/src/Symfony/Component/Webhook/Controller/WebhookController.php
 */

namespace Xaraya\Modules\Webhooks\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class WebhookTestController
{
    protected mixed $config;

    public function __construct()
    {
        $this->getConfig();
    }

    public function getConfig(): void
    {
        // ...
        $rootDir = dirname(__DIR__, 5);
        $apiCacheDir = $rootDir . '/html/var/cache/api';
        try {
            $this->config = include $apiCacheDir . '/webhooks_config.php';
        } catch (Exception) {
            $this->config = [];
        }
    }

    public function handle(string $type, Request $request): Response
    {
        //$type = $request->query->get('type', 'webhook');
        // ...
        // Re-use modules ItemCreate event
        // $item = array('module' => $module, 'itemid' => $itemid [, 'itemtype' => $itemtype, ...]);
        // xarHooks::notify('ItemCreate', $item);
        // ...
        return new Response('Hello, World!');
    }
}
