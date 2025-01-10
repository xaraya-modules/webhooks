<?php

/**
 * @package modules\webhooks
 * @category Xaraya Web Applications Framework
 * @version 2.4.2
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\Webhooks;

use Xaraya\DataObject\Traits\UserApiInterface;
use Xaraya\DataObject\Traits\UserApiTrait;
use sys;

sys::import('modules.dynamicdata.class.traits.userapi');

/**
 * Class to handle the Webhooks User API
 */
class UserApi implements UserApiInterface
{
    /** @use UserApiTrait<Module> */
    use UserApiTrait;
}
