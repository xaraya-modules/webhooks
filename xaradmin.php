<?php
/**
 * @package modules\webhooks
 * @category Xaraya Web Applications Framework
 * @version 2.4.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182630.html
 */

/**
 * Admin main
 *
 * @uses Xaraya\Modules\Webhooks\AdminGui::main()
 * @param array<string, mixed> $args
 * @param mixed $context
 * @return mixed template variables or output in HTML
 */
function webhooks_admin_main(array $args = [], $context = null)
{
    $admingui = xarMod::getModule('webhooks')->getAdminGUI();
    $admingui->setContext($context);
    return $admingui->main($args);
}

/**
 * Admin modifyconfig
 *
 * @uses Xaraya\Modules\Webhooks\AdminGui::modify()
 * @param array<string, mixed> $args
 * @param mixed $context
 * @return mixed template variables or output in HTML
 */
function webhooks_admin_modifyconfig(array $args = [], $context = null)
{
    // Security
    if (!xarSecurity::check('AdminWebhooks')) {
        return;
    }

    $admingui = xarMod::getModule('webhooks')->getAdminGUI();
    $admingui->setContext($context);

    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
        return;
    }
    switch (strtolower($phase)) {
        case 'update':
            // Confirm authorisation code
            if (!xarSec::confirmAuthKey()) {
                return xarController::badRequest('bad_author', $context);
            }
            if (!xarVar::fetch('input', 'array', $args['input'], [], xarVar::NOT_REQUIRED)) {
                return;
            }
            return $admingui->update($args);

        case 'modify':
        default:
            return $admingui->modify($args);
    }
}
