<?php

/**
 * Handle module installer functions
 *
 * @package modules\webhooks
 * @category Xaraya Web Applications Framework
 * @version 2.5.6
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\Webhooks;

use Xaraya\Modules\InstallerInterface;
use Xaraya\Modules\InstallerTrait;
use xarMasks;
use xarPrivileges;
use sys;

sys::import('xaraya.modules.installertrait');

/**
 * Handle module installer functions
 */
class Installer implements InstallerInterface
{
    use InstallerTrait;

    /**
     * Configure this module - override this method
     *
     * @return void
     */
    public function configure()
    {
        $this->objects = [
            // add your DD objects here
            'webhooks_log',
        ];
        $this->variables = [
            // add your module variables here
            'hello' => 'world',
        ];
        $this->oldversion = '2.4.1';
    }

    /**
     * Upgrade this module from an old version - override this method
     *
     * @param string $oldversion
     * @return boolean true on success, false on failure
     */
    public function upgrade($oldversion)
    {
        // Upgrade dependent on old version number
        switch ($oldversion) {
            case '2.4.1':
                // fall through to next upgrade
            case '2.4.2':
                xarMasks::register('AdminWebhooks', 'All', 'webhooks', 'All', 'All', 'ACCESS_ADMIN');
                xarPrivileges::register('AdminWebhooks', 'All', 'webhooks', 'All', 'All', 'ACCESS_ADMIN');
                // fall through to next upgrade
                // no break
            case '2.4.3':
                break;
            default:
                break;
        }
        return true;
    }
}
