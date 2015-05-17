<?php
/**
 * Wasabi Core AssetFlter
 * Provides access to src/Assets/... via http requests to /vkal_core/... during development.
 *
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
namespace Wasabi\Core\Routing\Filter;

use Cake\Core\Plugin;
use Cake\Utility\Inflector;

class AssetFilter extends \Cake\Routing\Filter\AssetFilter
{
    /**
     * Default priority for all methods in this filter
     * This filter should run before CakePHP's AssetFilter.
     *
     * @var int
     */
    protected $_priority = 8;

    /**
     * Builds asset file path based off url
     *
     * @param string $url Asset URL
     * @return string Absolute path for asset file
     */
    protected function _getAssetFile($url)
    {
        $parts = explode('/', $url);
        $pluginPart = [];
        for ($i = 0; $i < 2; $i++) {
            if (!isset($parts[$i])) {
                break;
            }
            $pluginPart[] = Inflector::camelize($parts[$i]);
            $plugin = implode('/', $pluginPart);
            $pos = strlen('Wasabi');
            if (strpos($plugin, 'Wasabi') !== false && strpos($plugin, '/') === false && (strlen($plugin) > $pos)) {
                $plugin = join('/', [
                    substr($plugin, 0, $pos),
                    substr($plugin, $pos)
                ]);
            }
            if ($plugin && Plugin::loaded($plugin)) {
                $parts = array_slice($parts, $i + 1);
                $fileFragment = implode(DS, $parts);
                $pluginWebroot = Plugin::path($plugin) . 'src' . DS . 'Assets' . DS;
                return $pluginWebroot . $fileFragment;
            }
        }
    }

}

