<?php
/**
 * @package modules\skeleton
 * @category Xaraya Web Applications Framework
 * @version 2.4.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\Skeleton;

use Xaraya\DataObject\Traits\UserApiInterface;
use Xaraya\DataObject\Traits\UserApiTrait;
use DataObjectFactory;
use DataObjectList;
use sys;

sys::import('modules.dynamicdata.class.traits.userapi');

/**
 * Class to handle the Skeleton User API (static for now)
 */
class UserApi implements UserApiInterface
{
    use UserApiTrait;

    public static string $moduleName = 'skeleton';
    protected static int $moduleId = 123456;  // @todo replace with fixed number - see xarversion.php
    protected static int $itemtype = 0;

}
