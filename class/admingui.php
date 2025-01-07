<?php

/**
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

use Xaraya\Modules\AdminGuiInterface;
use Xaraya\Modules\AdminGuiTrait;
use sys;

sys::import('xaraya.modules.adminguitrait');

/**
 * Class instance to handle the Webhooks User GUI
 */
class AdminGui implements AdminGuiInterface
{
    use AdminGuiTrait;

    /**
     * Admin main GUI function
     * @param array<string, mixed> $args
     * @return array<mixed>
     */
    public function main(array $args = [])
    {
        $args['description'] ??= 'Description of Webhooks';

        // Pass along the context for xarTpl::module() if needed
        $args['context'] ??= $this->getContext();
        return $args;
    }
}
