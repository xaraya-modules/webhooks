<?php

/**
 * @see https://github.com/symfony/symfony/blob/7.0/src/Symfony/Component/Webhook/Controller/WebhookController.php
 * @see https://github.com/spatie/laravel-webhook-client/blob/main/src/Http/Controllers/WebhookController.php
 */

namespace Xaraya\Modules\Webhooks\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Webhook Test Controller
 * independent of the Symfony Webhook + RemoteEvent + Messenger combo
 * or the Laravel (Spatie) Webhook Client Controller + Processor + ...
 */
class TestController
{
    public function __construct(array $config = []) {}

    /**
     * @param array<string, mixed> $pathParams from FastRoute dispatcher (optional)
     */
    public function handle(mixed $type, Request $request, array $pathParams = []): Response
    {
        //$type = $request->query->get('type', 'webhook');
        // ...
        // Re-use modules ItemCreate event
        // $item = array('module' => $module, 'itemid' => $itemid [, 'itemtype' => $itemtype, ...]);
        // xarHooks::notify('ItemCreate', $item);
        // ...
        if (empty($type)) {
            $type = 'world';
        }
        $name = rawurlencode(ucwords($request->get('name', $type)));
        return new Response("Hello, {$name}!");
    }
}
