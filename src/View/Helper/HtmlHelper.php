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

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Routing\Router;
use Cake\Utility\Hash;

class HtmlHelper extends \Cake\View\Helper\HtmlHelper
{
    /**
     * Holds the primary title of the title pad.
     *
     * @var bool|string
     */
    protected $_title = false;

    /**
     * Holds the secondary title of the title pad.
     *
     * @var bool
     */
    protected $_subTitle = false;

    /**
     * Holds all registered/added actions of the title pad.
     *
     * @var array
     */
    protected $_actions = [];

    /**
     * Set the primary title of the title pad.
     *
     * @param $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Set the secondary title of the title pad.
     *
     * @param $subTitle
     */
    public function setSubTitle($subTitle)
    {
        $this->_subTitle = $subTitle;
    }

    /**
     * Add an action to the title pad.
     *
     * @param $action
     */
    public function addAction($action)
    {
        $this->_actions[] = $action;
    }

    /**
     * Render the title pad including primary and secondary title as well as all added actions.
     *
     * @return string
     */
    public function titlePad()
    {
        $out = '';
        if ($this->_title === false) {
            return $out;
        }
        $out .= '<div class="titlepad">';
        $out .= $this->_pageTitle($this->_title, $this->_subTitle);
        if (!empty($this->_actions)) {
            $out .= '<ul class="titlepad-actions">';
            foreach ($this->_actions as $action) {
                $out .= '<li>' . $action . '</li>';
            }
            $out .= '</ul>';
        }
        $out .= '</div>';

        return $out;
    }

    /**
     * @param string $type
     * @param string $title
     * @param array $params
     * @param array $options
     * @return string
     * @deprecated
     * @todo new implementation needed
     */
    public function linkTo($type = 'page', $title = '', $params = [], $options = [])
    {
        $link = '';
        switch ($type) {
            case 'page':
                if (!isset($params['page_id'])) {
                    user_error('Html::linkTo(\'page\', ...) $params requires the key \'page_id\'.');
                }
                if (!isset($params['language_id'])) {
                    $params['language_id'] = Configure::read('Wasabi.content_language_id');
                }
                $url = array(
                    'plugin' => 'cms',
                    'controller' => 'cms_pages_frontend',
                    'action' => 'view',
                    $params['page_id'],
                    $params['language_id']
                );
                $link = $this->link($title, $url, $options);
                break;
            case 'collection_item':
                if (!isset($params['collection'])) {
                    user_error('Html::linkTo(\'collection_item\', ...) $params requires the key \'collection\'.');
                }
                if (!isset($params['item_id'])) {
                    user_error('Html::linkTo(\'collection_item\', ...) $params requires the key \'item_id\'.');
                }
                break;
        }

        return $link;
    }

    /**
     * Used internally to render the title (primary and secondary) of the title pad.
     *
     * @param string $title
     * @param bool $subtitle
     * @return string
     */
    protected function _pageTitle($title, $subtitle = false)
    {
        $out = '<h1 class="titlepad-title">' . $title;
        if ($subtitle !== false) {
            $out .= ' <small>' . $subtitle . '</small>';
        }
        $out .= '</h1>';

        return $out;
    }
}
