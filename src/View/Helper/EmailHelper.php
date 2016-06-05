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
 * Class EmailHelper
 *
 * @property Helper\UrlHelper $Url
 * @property HtmlHelper $Html
 */
class EmailHelper extends Helper
{
    /**
     * Helpers used by this helper.
     *
     * @var array
     */
    public $helpers = [
        'Url',
        'Html'
    ];

    /**
     * Render a big action button.
     *
     * @param string $linkText The button text to display.
     * @param array $url The url the button should link to.
     * @param string $bgColor The background color.
     * @param string $textColor The text color.
     * @return string
     */
    public function bigActionButton($linkText, array $url, $bgColor = '#368ee0', $textColor = "#ffffff")
    {
        return $this->_View->element('Wasabi/Core.Email/big-action-button', [
            'linkText' => $linkText,
            'url' => $this->Url->build($url, true),
            'bgColor' => $bgColor,
            'textColor' => $textColor
        ]);
    }

    /**
     * Link to the home page.
     *
     * @return string
     */
    public function linkToCmsHomepage()
    {
        return $this->Html->link(
            preg_replace('/https{0,1}:\/{2}/', '', $this->Url->build('/', true)),
            $this->Url->build('/', true)
        );
    }
}
