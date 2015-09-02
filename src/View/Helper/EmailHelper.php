<?php

namespace Wasabi\Core\View\Helper;

use Cake\View\Helper;

/**
 * Class EmailHelper
 * @property Helper\UrlHelper $Url
 * @property HtmlHelper $Html
 * @package Wasabi\Core\View\Helper
 */
class EmailHelper extends Helper
{
    public $helpers = [
        'Url',
        'Html'
    ];

    public function bigActionButton($linkText, array $url, $bgColor = '#368ee0', $textColor = "#ffffff")
    {
        return $this->_View->element('Wasabi/Core.Email/big-action-button', [
            'linkText' => $linkText,
            'url' => $this->Url->build($url, true),
            'bgColor' => $bgColor,
            'textColor' => $textColor
        ]);
    }

    public function linkToCmsHomepage()
    {
        return $this->Html->link(
            preg_replace('/https{0,1}:\/{2}/', '', $this->Url->build('/', true)),
            $this->Url->build('/', true)
        );
    }
}
