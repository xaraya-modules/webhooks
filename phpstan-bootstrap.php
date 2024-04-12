<?php

require_once dirname(__DIR__, 2) . '/xaraya-core/phpstan-bootstrap.php';
if (!class_exists('\Xaraya\Modules\Webhooks\AdminApi')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

if (!function_exists('xarML')) {
    /**
     * Summary of xarML
     * @param mixed $rawstring
     * @param array<mixed> $args
     * @return mixed
     */
    function xarML($rawstring, ...$args)
    {
        return call_user_func_array(['xarMLS', 'translate'], func_get_args());
    }
}
