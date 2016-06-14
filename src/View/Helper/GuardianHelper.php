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

use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\View\View;

/**
 * Class GuardianHelper
 *
 * @property HtmlHelper $Html
 * @property View $_View
 */
class GuardianHelper extends HtmlHelper
{
    /**
     * Create a properly prefixed backend link.
     *
     * automatically prepends the backend url prefix to the desired $url
     *
     * @param string $title The link title.
     * @param array|string $url The url to link to.
     * @param array $options Options passed to the html link helper method.
     * @param bool $displayLinkTextIfUnauthorized Whether to display the link title if the user is
     *                                               not authorized to access the link.
     * @return string
     */
    public function protectedLink($title, $url, $options = [], $displayLinkTextIfUnauthorized = false)
    {
        $url = $this->_getUrl($url);
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
     * @param string $title The link title.
     * @param array|string $url The url to link to.
     * @param array $options Options passed to the html link helper method.
     * @return string
     */
    public function unprotectedLink($title, $url, $options = [])
    {
        $url = $this->_getUrl($url);
        return $this->link($title, $url, $options);
    }

    /**
     * Create a backend confirmation link.
     *
     * @param string $title The link title.
     * @param array|string $url The url to link to.
     * @param array $options Options passed to the html link helper method.
     * @param bool $displayLinkTextIfUnauthorized Whether to display the link title if the user is
     *                                            not authorized to access the link.
     * @return string
     */
    public function protectedConfirmationLink($title, $url, $options, $displayLinkTextIfUnauthorized = false)
    {
        if (!isset($options['confirm-message'])) {
            user_error('\'confirm-message\' option is not set on protectedConfirmationLink.');
            $options['confirm-message'] = '';
        }
        if (!isset($options['confirm-title'])) {
            user_error('\'confirm-title\' option is not set on protectedConfirmationLink.');
            $options['confirm-title'] = '';
        }

        $url = $this->_getUrl($url);
        if (!guardian()->hasAccess($url)) {
            if ($displayLinkTextIfUnauthorized) {
                return $title;
            }
            return '';
        }

        $linkOptions = [
            'data-modal-header' => $options['confirm-title'],
            'data-modal-body' => '<p>' . $options['confirm-message'] . '</p>',
            'data-method' => 'post',
            'data-toggle' => 'confirm'
        ];
        unset($options['confirm-title'], $options['confirm-message']);

        if (isset($options['ajax']) && $options['ajax'] === true) {
            $linkOptions['data-modal-ajax'] = 1;
            unset($options['ajax']);

            if (isset($options['notify'])) {
                $linkOptions['data-modal-notify'] = $options['notify'];
                unset($options['notify']);
            }

            if (isset($options['event'])) {
                $linkOptions['data-modal-event'] = $options['event'];
                unset($options['event']);
            }
        }
        if (isset($options['void']) && $options['void'] === true) {
            $linkOptions['data-modal-action'] = Router::url($url);
            $url = 'javascript:void(0)';
        }

        $linkOptions = Hash::merge($linkOptions, $options);
        return $this->link($title, $url, $linkOptions);
    }
}
