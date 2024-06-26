<?php
/**
 * @package modules\webhooks
 * @category Xaraya Web Applications Framework
 * @version 2.4.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182630.html
 */
sys::import('modules.webhooks.class.adminapi');
use Xaraya\Modules\Webhooks\AdminApi;

/**
 * Utility function to retrieve the list of itemtypes of this module (if any).
 * @uses Xaraya\Modules\Webhooks\AdminApi::getItemTypes()
 * @param array<string, mixed> $args array of optional parameters
 * @return array<mixed> the itemtypes of this module and their description
 */
function webhooks_userapi_getitemtypes(array $args = [], $context = null)
{
    return AdminApi::getItemTypes($args, $context);
}

/**
 * Utility function to pass individual item links to whoever
 * @uses Xaraya\Modules\Webhooks\AdminApi::getItemLinks()
 * @param array<string, mixed> $args array of optional parameters
 *        string   $args['itemtype'] item type (optional)
 *        array    $args['itemids'] array of item ids to get
 * @return array<mixed> containing the itemlink(s) for the item(s).
 */
function webhooks_userapi_getitemlinks(array $args = [], $context = null)
{
    return AdminApi::getItemLinks($args, $context);
}
