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
namespace Wasabi\Core\View\Cell;

use Cake\View\Cell;
use Wasabi\Core\Nav;

class MenuCell extends Cell
{
    /**
     * Prepares the menu items before rendering the cell.
     *
     * @param string $alias the name of the menu
     */
    public function display($alias)
    {
        $items = Nav::getMenu($alias, true);
        $this->set('menuItems', $this->_processMenuItems($items, $this->request->params));
    }

    /**
     * Process provided menu items and add
     * classes 'active', 'open' depending
     * on the provided request params.
     *
     * @param array $items
     * @param array $requestParams
     * @param boolean $subActiveFound
     * @return array
     */
    protected function _processMenuItems($items, $requestParams, &$subActiveFound = false)
    {
        foreach ($items as &$item) {
            if (isset($item['url']) &&
                $item['url']['plugin'] === $requestParams['plugin'] &&
                $item['url']['controller'] === $requestParams['controller']
            ) {
                if (!empty($item['doNotMatchAction'])) {
                    $skip = false;
                    foreach ($item['doNotMatchAction'] as $doNotMatchAction) {
                        if ($doNotMatchAction === $requestParams['action']) {
                            $skip = true;
                            break;
                        }
                    }
                    if ($skip) {
                        break;
                    }
                }
                if ($item['matchAction'] !== true) {
                    $item['active'] = true;
                    $subActiveFound = true;
                } elseif ($item['url']['action'] === $requestParams['action']) {
                    $item['active'] = true;
                    $subActiveFound = true;
                }
            }
            if (isset($item['children']) && !empty($item['children'])) {
                $sub = false;

                $item['children'] = $this->_processMenuItems($item['children'], $requestParams, $sub);

                if ($sub === true) {
                    $item['active'] = true;
                    $item['open'] = true;
                    $subActiveFound = true;
                }
            }
        }

        return $items;
    }
}
