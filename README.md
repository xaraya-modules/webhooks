# Webhooks to and from Xaraya (WIP)

## Available Webhooks

The following providers may be available to process incoming webhook calls (someday):

1. [Test controller](./src/Controller/TestController.php)
2. [FastRoute](https://github.com/nikic/FastRoute) dispatcher handlers
3. [Xaraya](https://github.com/xaraya/core) module api functions and object methods

You can integrate applications using other frameworks via composer:

4. [xaraya/with-symfony](https://packagist.org/packages/xaraya/with-symfony) package: use [Symfony Webhook](https://symfony.com/doc/current/webhook.html) and [Symfony RemoteEvent](https://symfony.com/components/RemoteEvent) in combination with Symfony [Mailer](https://symfony.com/doc/current/mailer.html#mailer_3rd_party_transport) or [Notifier](https://symfony.com/doc/current/notifier.html) components
5. [xaraya/with-laravel](https://packagist.org/packages/xaraya/with-laravel) package: use [Laravel (Spatie) Webhook Client](https://github.com/spatie/laravel-webhook-client) with many [existing packages](https://packagist.org/packages/spatie/laravel-webhook-client/dependents?order_by=downloads) to process webhook calls from other providers

## Passthru Integration

The xaraya/with-* packages also allow you to pass through *other* URLs besides webhooks to Symfony or Laravel applications.
This can be enabled or disabled in `Modify Configuration`.

## Test Webhooks

* <a href="ws.php/webhook/test">webhook/test</a>
* <a href="ws.php/webhook/fastroute">webhook/fastroute</a>
* <a href="ws.php/webhook/xaraya-base">webhook/xaraya-base</a>
* <a href="ws.php/webhook/xaraya-data">webhook/xaraya-data</a>
* <a href="ws.php/webhook/hello-symfony">webhook/hello-symfony</a>
* <a href="ws.php/webhook/hello-laravel">webhook/hello-laravel</a>
* <a href="ws.php/passthru/symfony/">passthru/symfony/...</a>
* <a href="ws.php/passthru/laravel/">passthru/laravel/...</a>
