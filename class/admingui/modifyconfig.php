<?php

/**
 * @package modules\dynamicdata
 * @subpackage dynamicdata
 * @category Xaraya Web Applications Framework
 * @version 2.5.6
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
 **/

namespace Xaraya\Modules\Webhooks\AdminGui;


use Xaraya\Modules\Webhooks\AdminGui;
use Xaraya\Modules\MethodClass;
use Xaraya\Modules\Webhooks\Configuration\WebhooksConfig;
use xarController;
use xarSec;
use xarVar;
use sys;

sys::import('xaraya.modules.method');

/**
 * Admin modifyconfig GUI function
 * @extends MethodClass<AdminGui>
 */
class ModifyconfigMethod extends MethodClass
{
    public function __invoke(array $args = [])
    {
        // Security
        if (!$this->sec()->checkAccess('AdminWebhooks')) {
            return;
        }
        $phase = null;
        if (!$this->var()->find('phase', $phase, 'str:1:100', 'modify')) {
            return;
        }
        switch (strtolower($phase)) {
            case 'update':
                // Confirm authorisation code
                if (!$this->sec()->confirmAuthKey()) {
                    return xarController::badRequest('bad_author', $this->getContext());
                }
                if (!$this->var()->find('input', $args['input'], 'array', [])) {
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
