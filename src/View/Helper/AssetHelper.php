<?php
/**
 * Wasabi CMS
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\View\Helper;

use Cake\Core\Plugin;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\View\Helper;

/**
 * Class AssetHelper
 *
 * @property Helper\UrlHelper Url
 */
class AssetHelper extends Helper
{
    public $helpers = [
        'Url'
    ];

    public function css($path, $plugin = false, $appendTime = true)
    {
        $href = $this->_getUrl($path, 'css', $plugin, $appendTime);
        return '<link rel="stylesheet" type="text/css" href="' . $href . '">';
    }

    public function js($path, $plugin = false, $appendTime = true)
    {
        $src = $this->_getUrl($path, 'js', $plugin, $appendTime);
        return '<script type="text/javascript" src="' . $src . '"></script>';
    }

    protected function _getUrl($path, $type, $plugin, $appendTime = true)
    {
        $absPath = $this->_getBasePath($plugin) . $type . DS . $path;
        $time = $appendTime ? $this->_getModifiedTime($absPath . '.' . $type) : '';

        return $this->Url->assetUrl($plugin . '.' . $path . '.' . $type, ['pathPrefix' => $type . '/']) . $time;
    }

    protected function _getBasePath($plugin = false)
    {
        if ($plugin && Plugin::loaded($plugin)) {
            $pluginPath = Plugin::path($plugin);
            return $pluginPath . 'webroot' . DS;
        }

        return WWW_ROOT;
    }

    protected function _getModifiedTime($path)
    {
        $time = '';
        if (file_exists($path)) {
            //@codingStandardsIgnoreStart
            $time = '?t=' . @filemtime($path);
            //@codingStandardsIgnoreEnd
        }

        return $time;
    }
}
