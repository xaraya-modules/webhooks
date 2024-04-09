<?php
/**
 * Entrypoint for webhooks (via ws.php)
 */

namespace Xaraya\Modules\Webhooks;

use Symfony\Component\HttpFoundation\Request;

//use Symfony\Component\Webhook\Controller\WebhookController;

// access via ws.php
if (php_sapi_name() === 'cli' || empty($_SERVER['SERVER_FRAMEWORK'])) {
    echo 'Entrypoint for webhooks (via ws.php)';
    return;
}

/**
 * Using controller
 */
$request = Request::createFromGlobals();

$controller = new Controller\WebhookTestController();
$response = $controller->handle('generic', $request);

$response->send();

/**
 * Using HttpKernel with events
 *
 * @see https://symfony.com/doc/current/components/http_kernel.html#httpkernel-driven-by-events
 *
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;

// create the Request object
$request = Request::createFromGlobals();
// for example, possibly set its _controller manually
$request->attributes->set('_controller', [WebhookController::class, 'input']);

$dispatcher = new EventDispatcher();
// ... add some event listeners

// create your controller and argument resolvers
$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

// instantiate the kernel
$kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

// actually execute the kernel, which turns the request into a response
// by dispatching events, calling a controller, and returning the response
$response = $kernel->handle($request);

// send the headers and echo the content
$response->send();

// trigger the kernel.terminate event
$kernel->terminate($request, $response);
 */
