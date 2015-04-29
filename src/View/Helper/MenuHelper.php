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

use Cake\View\Helper;

/**
 * Class MenuHelper
 *
 * @property HtmlHelper $Html
 */
class MenuHelper extends Helper
{

    /**
     * Helpers used by this helper
     *
     * @var array
     */
    public $helpers = array(
        'Html' => array(
            'className' => 'Wasabi/Core.Html'
        )
    );

    /**
     * Render provided $items as <li> elements
     *
     * @param array $items
     * @param string $activeClass css class to add to active items
     * @return string
     */
    public function render($items, $activeClass = 'active')
    {
        $out = '';
        foreach ($items as $item) {
            $class = '';
            if (isset($item['active']) && $item['active'] === true) {
                $class = ' class="' . $activeClass . '"';
            }
            $out .= '<li' . $class . '>';
            $out .= $this->Html->backendLink($item['name'], $item['url']);
            $out .= '</li>';
        }
        return $out;
    }

    /**
     * Render all nested items of a menu.
     *
     * @param array $items items to render
     * @param string $activeClass active css class that is applied to active items
     * @param string $openClass open class that is applied to a parent menu item when children are active
     * @param string $subNavClass css class applied to any child ul of a parent menu item
     * @return string the rendered items without an outer ul
     */
    public function renderNested(array $items, $activeClass = 'active', $openClass = 'open', $subNavClass = 'sub-nav')
    {
        $out = '';
        foreach ($items as $item) {
            $cls = [];
            if (isset($item['active']) && $item['active'] === true) {
                $cls[] = $activeClass;
            }
            if (isset($item['open']) && $item['open'] === true) {
                $cls[] = $openClass;
            }
            $out .= '<li' . ((count($cls) > 0) ? ' class="' . join(' ', $cls) . '"' : '') . '>';
            if (isset($item['url'])) {
                $options = [];
                if (isset($item['icon'])) {
                    $item['name'] = '<i class="' . $item['icon'] . '"></i><span class="item-name">' . $item['name'] . '</span>';
                    $options['escape'] = false;
                }
                $out .= $this->Html->backendLink($item['name'], $item['url'], $options);
            } else {
                $out .= '<a href="javascript:void(0)">';
                if (isset($item['icon'])) {
                    $out .= '<i class="' . $item['icon'] . '"></i>';
                }
                $out .= '<span class="item-name">' . $item['name'] . '</span>';
                if (isset($item['children']) && !empty($item['children'])) {
                    $out .= ' <i class="icon-arrow-down"></i>';
                    $out .= ' <i class="icon-arrow-up"></i>';
                }
                $out .= '</a>';
            }
            if (isset($item['children']) && !empty($item['children'])) {
                $out .= '<ul class="' . $subNavClass . '">';
                $out .= $this->renderNested($item['children']);
                $out .= '</ul>';
            }
            $out .= '</li>';
        }

        return $out;
    }
}
