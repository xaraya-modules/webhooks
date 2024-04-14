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

use Xaraya\DataObject\Traits\UserGuiInterface;
use Xaraya\DataObject\Traits\UserGuiTrait;
use Xaraya\Modules\Webhooks\Configuration\WebhooksConfig;
use sys;

sys::import('modules.dynamicdata.class.objects.factory');
sys::import('modules.dynamicdata.class.traits.usergui');
sys::import('modules.webhooks.class.adminapi');

/**
 * Class instance to handle the Webhooks User GUI
 */
class AdminGui implements UserGuiInterface
{
    use UserGuiTrait;

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
