<?php

namespace Xaraya\Modules\Webhooks\Tests;

use PHPUnit\Framework\TestCase;
use Xaraya\Context\SessionContext;
use Xaraya\Modules\Webhooks\AdminGui;
use Xaraya\Services\xar;
use sys;

//use Xaraya\Sessions\SessionHandler;

final class AdminGuiTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // initialize bootstrap
        sys::init();
        $xar = xar::getServicesClass();
        // initialize caching - delay until we need results
        $xar->cache()->init();
        // initialize loggers
        //$xar->log()->init();
        // initialize database - delay until caching fails
        $xar->db()->init();
        // initialize modules
        //$xar->mod()->init();
        // initialize users
        //$xar->user()->init();
        $xar->session()->setSessionClass(SessionContext::class);
    }

    public static function tearDownAfterClass(): void {}

    protected function setUp(): void {}

    protected function tearDown(): void {}

    public function testAdminGui(): void
    {
        $expected = AdminGui::class;
        $module = xar::mod()->getModule('webhooks');
        $admingui = $module->admingui();
        $this->assertEquals($expected, $admingui::class);
    }

    public function testMain(): void
    {
        $context = null;
        $admingui = xar::mod()->getModule('webhooks')->admingui();
        $admingui->setContext($context);

        $args = ['hello' => 'world'];
        $data = $admingui->main($args);

        $expected = array_merge($args, [
            'context' => $context,
            'description' => 'Description of Webhooks',
        ]);
        $this->assertEquals($expected, $data);
    }
}
