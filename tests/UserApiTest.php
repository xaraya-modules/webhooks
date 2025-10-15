<?php

namespace Xaraya\Modules\Webhooks\Tests;

use PHPUnit\Framework\TestCase;
use Xaraya\Context\SessionContext;
use Xaraya\Modules\Webhooks\UserApi;
use sys;
use xarCache;
use xarDatabase;
use xarLog;
use xarMLS;
use xarMod;
use xarSession;

//use Xaraya\Sessions\SessionHandler;

final class UserApiTest extends TestCase
{
    protected static $oldDir;

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
        xarMLS::init();
        xarSession::setSessionClass(SessionContext::class);

        // file paths are relative to parent directory
        static::$oldDir = getcwd();
        chdir(dirname(__DIR__));
    }

    public static function tearDownAfterClass(): void
    {
        chdir(static::$oldDir);
    }

    protected function setUp(): void {}

    protected function tearDown(): void {}

    public function testUserApi(): void
    {
        $expected = 1;
        $module = xarMod::getModule('webhooks');
        /** @var UserApi $userapi */
        $userapi = $module->userapi();
        $itemtypes = $userapi->getItemTypes();
        $this->assertCount($expected, $itemtypes);
    }
}
