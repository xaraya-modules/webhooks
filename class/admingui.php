<?php

/**
 * @package modules\webhooks
 * @category Xaraya Web Applications Framework
 * @version 2.5.3
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 *
 * @author mikespub <mikespub@xaraya.com>
 **/

namespace Xaraya\Modules\Webhooks;

use Xaraya\Core\Traits\AdminGuiInterface;
use Xaraya\Core\Traits\AdminGuiTrait;
use Xaraya\Modules\Webhooks\Configuration\WebhooksConfig;
use xarController;
use xarSec;
use xarVar;
use sys;

sys::import('modules.dynamicdata.class.objects.factory');
sys::import('modules.webhooks.class.userapi');
sys::import('xaraya.traits.adminguitrait');

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

    /**
     * Admin modifyconfig GUI function
     * @param array<string, mixed> $args
     * @return mixed template variables or output in HTML
     */
    public function modifyconfig(array $args = [])
    {
        // Security
        if (!$this->checkAccess('AdminWebhooks')) {
            return;
        }
        $phase = null;
        if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
            return;
        }
        switch (strtolower($phase)) {
            case 'update':
                // Confirm authorisation code
                if (!xarSec::confirmAuthKey()) {
                    return xarController::badRequest('bad_author', $this->getContext());
                }
                if (!xarVar::fetch('input', 'array', $args['input'], [], xarVar::NOT_REQUIRED)) {
                    return;
                }
                return $this->update($args);

            case 'modify':
            default:
                return $this->modify($args);
        }
    }

    /**
     * Admin modify GUI function
     * @param array<string, mixed> $args
     * @return array<mixed>
     */
    public function modify(array $args = [])
    {
        sys::import('modules.webhooks.src.Configuration.WebhooksConfig');
        $config = new WebhooksConfig();
        $args['config'] = $config->getConfig();

        // Pass along the context for xarTpl::module() if needed
        $args['context'] ??= $this->getContext();
        return $args;
    }

    /**
     * Admin update GUI function
     * @param array<string, mixed> $args
     * @return array<mixed>
     */
    public function update(array $args = [])
    {
        sys::import('modules.webhooks.src.Configuration.WebhooksConfig');
        $config = new WebhooksConfig();
        $args['config'] = $config->getConfig();

        $args['input'] ??= [];
        foreach ($args['config'] as $name => $params) {
            if (empty($args['input'][$name])) {
                $args['config'][$name]['enabled'] = false;
                continue;
            }
            if (empty($args['input'][$name]['enabled'])) {
                $args['config'][$name]['enabled'] = false;
            } else {
                $args['config'][$name]['enabled'] = true;
            }
            if (empty($args['input'][$name]['env'])) {
                continue;
            }
        }
        $config->setConfig($args['config']);
        $filepath = sys::varpath() . '/cache/api/webhooks_config.php';
        $config->saveConfig($filepath);

        // Pass along the context for xarTpl::module() if needed
        $args['context'] ??= $this->getContext();
        return $args;
    }
}
