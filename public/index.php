<?php
/**
 * Entrypoint for webhooks (via ws.php)
 */
if (empty($_SERVER['SERVER_FRAMEWORK'])) {
    if (php_sapi_name() === 'cli' ||
        !is_dir(dirname(__DIR__) . '/vendor')) {
        echo 'Entrypoint for webhooks (via ws.php)';
        return;
    }
    require_once dirname(__DIR__) . '/vendor/autoload.php';
}

use Symfony\Component\HttpFoundation\Request;
use Xaraya\Modules\Webhooks\Configuration\WebhooksConfig;

$request = Request::createFromGlobals();
$path = $request->getPathInfo();

// name is the second part in path info, e.g. /webhook/github/...
$parts = explode('/', trim($path, '/'));
$type = array_shift($parts);
$name = array_shift($parts) ?? '';

// find the right endpoint (Xaraya, Symfony, Laravel, FastRoute, Test, ...)
$config = new WebhooksConfig();
try {
    $entrypoint = $config->getEndpoint($type, $name);
} catch (Throwable $e) {
    http_response_code(404);
    echo $e->getMessage();
    $webhooks = $config->listWebhooks();
    $prefix = $request->getBaseUrl() . '/';
    echo '<pre>';
    foreach ($webhooks as $name) {
        echo '- <a href="' . $prefix . $name . '">' . $name . "</a>\n";
    }
    echo '</pre>';
    return;
}

// run the endpoint
try {
    $entrypoint->run();
} catch (Throwable $e) {
    http_response_code(500);
    echo $e->getMessage();
    return;
}

// that's all folks
return;
