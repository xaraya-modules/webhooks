<?php

namespace Xaraya\Modules\Webhooks\Tests;

use PHPUnit\Framework\TestCase;
use Xaraya\Context\SessionContext;
use Xaraya\Modules\Webhooks\AdminGui;
use sys;
use xarCache;
use xarDatabase;
use xarLog;
use xarMod;
use xarSession;

//use Xaraya\Sessions\SessionHandler;

final class AdminGuiTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // initialize bootstrap
        sys::init();
        // initialize caching - delay until we need results
        xarCache::init();
        // initialize loggers
        xarLog::init();
        // initialize database - delay until caching fails
        xarDatabase::init();
        // initialize modules
        //xarMod::init();
        // initialize users
        //xarUser::init();
        xarSession::setSessionClass(SessionContext::class);
    }

    public static function tearDownAfterClass(): void {}

    protected function setUp(): void {}

    protected function tearDown(): void {}

    public function testAdminGui(): void
    {
        $expected = AdminGui::class;
        $module = xarMod::getModule('webhooks');
        $admingui = $module->admingui();
        $this->assertEquals($expected, $admingui::class);
    }

    public function testMain(): void
    {
        $context = null;
        $admingui = xarMod::getModule('webhooks')->admingui();
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
