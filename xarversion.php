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
$modversion = [];
$modversion['name']           = 'webhooks';
$modversion['id']             = '182630';  // @todo replace with fixed number
$modversion['version']        = '2.4.2';
$modversion['displayname']    = 'Webhooks';
$modversion['description']    = 'Webhooks to and from Xaraya';
$modversion['credits']        = '';
$modversion['help']           = '';
$modversion['changelog']      = '';
$modversion['license']        = '';
$modversion['official']       = false;
$modversion['author']         = 'mikespub';
$modversion['contact']        = 'https://github.com/xaraya-modules/webhooks';
$modversion['admin']          = true;
$modversion['user']           = false;
$modversion['class']          = 'Utility';
$modversion['category']       = 'Miscellaneous';
$modversion['namespace']      = 'Xaraya\Modules\Webhooks';
$modversion['securityschema'] = [];
$modversion['dependency']     = [];
$modversion['twigtemplates']  = true;
$modversion['dependencyinfo'] = [
    0 => [
        'name' => 'Xaraya Core',
        'version_ge' => '2.4.1',
    ],
];
