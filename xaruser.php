<?php
/**
 * @package modules\skeleton
 * @category Xaraya Web Applications Framework
 * @version 2.4.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/18257.html
 */
sys::import('modules.skeleton.class.usergui');
use Xaraya\Modules\Skeleton\UserGui;

/**
 * User main
 *
 * @uses Xaraya\Modules\Skeleton\UserGui::main()
 * @param array<string, mixed> $args
 * @param mixed $context
 * @return mixed template variables or output in HTML
 */
function skeleton_user_main(array $args = [], $context = null)
{
    $usergui = new UserGui();
    $usergui->setContext($context);
    return $usergui->main($args);
}
