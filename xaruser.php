<?php
/**
 * @package modules\webhooks
 * @category Xaraya Web Applications Framework
 * @version 2.4.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182630.html
 */
sys::import('modules.webhooks.class.usergui');
use Xaraya\Modules\Webhooks\UserGui;

/**
 * User main
 *
 * @uses Xaraya\Modules\Webhooks\UserGui::main()
 * @param array<string, mixed> $args
 * @param mixed $context
 * @return mixed template variables or output in HTML
 */
function webhooks_user_main(array $args = [], $context = null)
{
    $usergui = new UserGui();
    $usergui->setContext($context);
    return $usergui->main($args);
}
