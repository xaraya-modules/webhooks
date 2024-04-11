# Webhooks to and from Xaraya (WIP)

The following providers may be available to process incoming webhook calls (someday):

1. Xaraya module api functions and object methods
2. [FastRoute](https://github.com/nikic/FastRoute) dispatcher handlers
3. Test controller

You can integrate applications using other frameworks via composer:

4. [xaraya/with-symfony](https://packagist.org/packages/xaraya/with-symfony) package: use [Symfony Webhook](https://symfony.com/doc/current/webhook.html) and [Symfony RemoteEvent](https://symfony.com/components/RemoteEvent) in combination with Symfony [Mailer](https://symfony.com/doc/current/mailer.html#mailer_3rd_party_transport) or [Notifier](https://symfony.com/doc/current/notifier.html) components
5. [xaraya/with-laravel](https://packagist.org/packages/xaraya/with-laravel) package: use [Laravel (Spatie) Webhook Client](https://github.com/spatie/laravel-webhook-client) with many [existing packages](https://packagist.org/packages/spatie/laravel-webhook-client/dependents?order_by=downloads) to process webhook calls from other providers
