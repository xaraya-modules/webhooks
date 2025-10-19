<?php

/**
 * Webhooks version information
 *
 * @package modules\webhooks
 * @copyright (C) 2024 Mike's Pub
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/xaraya-modules
 * @author mikespub
 */

namespace Xaraya\Modules\Webhooks;

class Version
{
    /**
     * Get module version information
     *
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'name' => 'webhooks',
            'id' => '182630',
            'version' => '2.5.3',
            'displayname' => 'Webhooks',
            'description' => 'Webhooks to and from Xaraya',
            'credits' => '',
            'help' => '',
            'changelog' => '',
            'license' => '',
            'official' => false,
            'author' => 'mikespub',
            'contact' => 'https://github.com/xaraya-modules/webhooks',
            'admin' => true,
            'user' => false,
            'class' => 'Utility',
            'category' => 'Miscellaneous',
            'namespace' => 'Xaraya\\Modules\\Webhooks',
            'securityschema'
             => [
             ],
            'dependency'
             => [
             ],
            'twigtemplates' => true,
            'dependencyinfo'
             => [
                 0
                  => [
                      'name' => 'Xaraya Core',
                      'version_ge' => '2.4.1',
                  ],
             ],
        ];
    }
}
