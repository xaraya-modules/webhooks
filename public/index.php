<?php
/**
 * Entrypoint for webhooks (via ws.php)
 */

use Xaraya\Modules\Webhooks\Configuration\WebhooksConfig;

// access via ws.php
if (php_sapi_name() === 'cli' || empty($_SERVER['SERVER_FRAMEWORK'])) {
    echo 'Entrypoint for webhooks (via ws.php)';
    return;
}

// name is the second part in path info, e.g. /webhook/github/...
$parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$type = array_shift($parts);
$name = array_shift($parts) ?? '';

// find the right endpoint (Xaraya, Symfony, Laravel, FastRoute, Test, ...)
$config = new WebhooksConfig();
try {
    $entrypoint = $config->getEndpoint($name);
} catch (Throwable $e) {
    http_response_code(404);
    echo $e->getMessage();
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
