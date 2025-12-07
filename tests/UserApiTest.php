<?php

namespace Xaraya\Modules\Webhooks\Tests;

use PHPUnit\Framework\TestCase;
use Xaraya\Context\SessionContext;
use Xaraya\Modules\Webhooks\UserApi;
use Xaraya\Services\xar;
use sys;

//use Xaraya\Sessions\SessionHandler;

final class UserApiTest extends TestCase
{
    protected static $oldDir;

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
        $xar->mls()->init();
        $xar->session()->setSessionClass(SessionContext::class);

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
        $module = xar::mod()->getModule('webhooks');
        /** @var UserApi $userapi */
        $userapi = $module->userapi();
        $itemtypes = $userapi->getItemTypes();
        $this->assertCount($expected, $itemtypes);
    }
}
