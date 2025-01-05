<?php
/**
 * @package modules\webhooks
 * @category Xaraya Web Applications Framework
 * @version 2.4.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182630.html
 */

use Xaraya\Modules\Webhooks\AdminGui;

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
    /** @var AdminGui $admingui */
    $admingui = xarMod::getModule('webhooks')->getAdminGUI();
    $admingui->setContext($context);
    return $admingui->main($args);
}

/**
 * Admin modifyconfig
 *
 * @uses Xaraya\Modules\Webhooks\AdminGui::modifyconfig()
 * @param array<string, mixed> $args
 * @param mixed $context
 * @return mixed template variables or output in HTML
 */
function webhooks_admin_modifyconfig(array $args = [], $context = null)
{
    /** @var AdminGui $admingui */
    $admingui = xarMod::getModule('webhooks')->getAdminGUI();
    $admingui->setContext($context);
    return $admingui->modifyconfig($args);
}
