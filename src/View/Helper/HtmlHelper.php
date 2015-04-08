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
    protected $_title = false;
    protected $_subTitle = false;
    protected $_actions = [];

    /**
     * Create a properly prefixed backend link.
     *
     * automatically prepends the backend url prefix to the desired $url
     *
     * @param string $title
     * @param array|string $url
     * @param array $options
     * @param boolean $displayLinkTextIfUnauthorized
     * @return string
     */
    public function backendLink($title, $url, $options = [], $displayLinkTextIfUnauthorized = false)
    {
        $url = $this->_getBackendUrl($url);
        if (!guardian()->hasAccess($url)) {
            if ($displayLinkTextIfUnauthorized) {
                return $title;
            }
            return '';
        }
        return $this->link($title, $url, $options);
    }

    /**
     * Create a properly prefixed backend link and
     * don't check permissions.
     *
     * @param string $title
     * @param array|string $url
     * @param array $options
     * @return string
     */
    public function backendUnprotectedLink($title, $url, $options = [])
    {
        $url = $this->_getBackendUrl($url);
        return $this->link($title, $url, $options);
    }

    /**
     * Create a backend confirmation link.
     *
     * @param string $title
     * @param array|string $url
     * @param array $options
     * @param bool $displayLinkTextIfUnauthorized
     * @return string
     * @throws Exception
     */
    public function backendConfirmationLink($title, $url, $options, $displayLinkTextIfUnauthorized = false)
    {
        if (!isset($options['confirm-message'])) {
            user_error('\'confirm-message\' option is not set on backendConfirmationLink.');
            $options['confirm-message'] = '';
        }
        if (!isset($options['confirm-title'])) {
            user_error('\'confirm-title\' option is not set on backendConfirmationLink.');
            $options['confirm-title'] = '';
        }

        $url = $this->_getBackendUrl($url);
        if (!guardian()->hasAccess($url)) {
            if ($displayLinkTextIfUnauthorized) {
                return $title;
            }
            return '';
        }

        $linkOptions = array(
            'data-modal-title' => $options['confirm-title'],
            'data-modal-body' => '<p>' . $options['confirm-message'] . '</p>',
            'data-method' => 'post',
            'data-toggle' => 'confirm'
        );
        unset($options['confirm-title'], $options['confirm-message']);

        if (isset($options['ajax']) && $options['ajax'] === true) {
            $linkOptions['data-ajax'] = 1;
            unset($options['ajax']);

            if (isset($options['notify'])) {
                $linkOptions['data-notify'] = $options['notify'];
                unset($options['notify']);
            }

            if (isset($options['event'])) {
                $linkOptions['data-event'] = $options['event'];
                unset($options['event']);
            }
        }

        $linkOptions = Hash::merge($linkOptions, $options);
        return $this->link($title, $url, $linkOptions);
    }

    public function getBackendUrl($url, $rel = false)
    {
        $checkUrl = $this->_getBackendUrl($url);
        if (!guardian()->hasAccess($checkUrl)) {
            return false;
        }
        return $this->_getBackendUrl($url, $rel);
    }

    public function setTitle($title)
    {
        $this->_title = $title;
    }

    public function setSubTitle($subTitle)
    {
        $this->_subTitle = $subTitle;
    }

    public function addAction($action)
    {
        $this->_actions[] = $action;
    }

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

    protected function _pageTitle($title, $subtitle = false)
    {
        $out = '<h1 class="titlepad-title">' . $title;
        if ($subtitle !== false) {
            $out .= ' <small>' . $subtitle . '</small>';
        }
        $out .= '</h1>';

        return $out;
    }

    /**
     * Transform the supplied $url into a properly prefixed backend url.
     *
     * @param array|string $url
     * @param bool $rel
     * @return array|string
     */
    protected function _getBackendUrl($url, $rel = false)
    {
        if (!is_array($url)) {
            $url = ltrim($url, '/');
            $url = '/' . $url;
        }
        if ($rel !== false) {
            $url = Router::url($url);
        }
        return $url;
    }
}
