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
namespace Wasabi\Core\View;

use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Event\EventManager;

/**
 * Class AppView
 *
 * @property \Cake\View\Helper\FlashHelper $Flash
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Wasabi\Core\View\Helper\AssetHelper $Asset
 * @property \Wasabi\Core\View\Helper\FilterHelper $Filter
 * @property \Wasabi\Core\View\Helper\GuardianHelper $Guardian
 * @property \Wasabi\Core\View\Helper\HtmlHelper $Html
 * @property \Wasabi\Core\View\Helper\MenuHelper $Menu
 * @property array activeFilters
 * @property array filterFields
 * @property array activeSort
 * @property array sortFields
 * @property array paginationParams
 * @property array defaultSort
 */
class AppView extends \App\View\AppView
{
    public $defaultFormTemplateActions = [
        'Wasabi/Core.Users.login'
    ];

    public function initialize()
    {
        if (!in_array(join('.', [
            $this->request->params['plugin'],
            $this->request->params['controller'],
            $this->request->params['action']
        ]), $this->defaultFormTemplateActions)
        ) {
            $this->loadHelper('Form', [
                'className' => 'Wasabi/Core.Form',
                'templates' => 'Wasabi/Core.form_templates',
                'widgets' => [
                    'label' => ['Wasabi\Core\View\Widget\LabelWidget'],
                    'section' => ['Wasabi\Core\View\Widget\SectionWidget'],
                    'select' => ['Wasabi\Core\View\Widget\SelectBoxWidget'],
                    '_default' => ['Wasabi\Core\View\Widget\BasicWidget']
                ]
            ]);
        }
    }
}
