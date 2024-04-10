<?php
/**
 * Webhook Test Controller
 * independent of the Symfony Webhook + RemoteEvent + Messenger combo
 * or the Laravel (Spatie) Webhook Client Controller + Processor + ...
 *
 * @see https://github.com/symfony/symfony/blob/7.0/src/Symfony/Component/Webhook/Controller/WebhookController.php
 * @see https://github.com/spatie/laravel-webhook-client/blob/main/src/Http/Controllers/WebhookController.php
 */

namespace Xaraya\Modules\Webhooks\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController
{
    public function __construct(array $config = []) {}

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
