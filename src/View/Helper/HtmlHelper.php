<?php
/**
 * Wasabi Core
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @link          https://github.com/wasabi-cms/core Wasabi Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\View\Helper;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Class HtmlHelper
 */
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
     * @param string $title The title.
     * @return void
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Set the secondary title of the title pad.
     *
     * @param string $subTitle The subtitle.
     * @return void
     */
    public function setSubTitle($subTitle)
    {
        $this->_subTitle = $subTitle;
    }

    /**
     * Add an action to the title pad.
     *
     * @param string $action The action(s).
     * @return void
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
     * Create a confirmation link.
     *
     * @param string $title The link title.
     * @param array $options Options passed to the html link helper method.
     * @return string
     */
    public function confirmationLink($title, $options)
    {
        if (!isset($options['confirm-message'])) {
            user_error('\'confirm-message\' option is not set on protectedConfirmationLink.');
            $options['confirm-message'] = '';
        }
        if (!isset($options['confirm-title'])) {
            user_error('\'confirm-title\' option is not set on protectedConfirmationLink.');
            $options['confirm-title'] = '';
        }

        $url = 'javascript:void(0)';

        $linkOptions = [
            'data-modal-header' => $options['confirm-title'],
            'data-modal-body' => '<p>' . $options['confirm-message'] . '</p>',
            'data-toggle' => 'confirm'
        ];
        unset($options['confirm-title'], $options['confirm-message']);

        if (isset($options['notify'])) {
            $linkOptions['data-modal-notify'] = $options['notify'];
            unset($options['notify']);
        }

        if (isset($options['event'])) {
            $linkOptions['data-modal-event'] = $options['event'];
            unset($options['event']);
        }

        $linkOptions = Hash::merge($linkOptions, $options);
        return $this->link($title, $url, $linkOptions);
    }

    /**
     * Used internally to render the title (primary and secondary) of the title pad.
     *
     * @param string $title The title.
     * @param bool $subtitle The subtitle.
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

    /**
     * Transform the supplied $url into a properly prefixed backend url.
     *
     * @param array|string $url The url to transform.
     * @param bool $rel Whether to generate a relative or absolute url.
     * @return array|string
     */
    protected function _getUrl($url, $rel = false)
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
