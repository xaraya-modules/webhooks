<?php
/**
 * Initialise the webhooks module
 *
 * @package modules\webhooks
 * @category Xaraya Web Applications Framework
 * @version 2.4.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182630.html
 */

namespace Xaraya\Modules\Webhooks;

use xarMod;
use xarModVars;

/**
 * Initialise this module
 *
 * @access public
 * @return  boolean true on success or false on failure
 */
function webhooks_init()
{
    $module = 'webhooks';
    $objects = [
        // add your DD objects here
    ];
    if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', ['module' => $module, 'objects' => $objects])) {
        return false;
    }

    // Re-use modules ItemCreate event
    //xarHooks::registerSubject('ItemCreate', 'item', 'webhooks');

    // Set up module variables
    xarModVars::set($module, 'hello', 'world');

    // Installation complete; check for upgrades
    return webhooks_upgrade('2.4.1');
}

/**
 * Activate this module
 *
 * @access public
 * @return boolean
 */
function webhooks_activate()
{
    return true;
}

/**
 * Deactivate this module
 *
 * @access public
 * @return boolean
 */
function webhooks_deactivate()
{
    return true;
}

/**
 * Upgrade this module from an old version
 *
 * @param string $oldversion
 * @return boolean true on success, false on failure
 */
function webhooks_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '2.4.1':
            // fall through to next upgrade
        case '2.4.2':
            break;
        default:
            break;
    }
    return true;
}

/**
 * Delete this module
 *
 * @return boolean
 */
function webhooks_delete()
{
    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => 'webhooks']);
}
