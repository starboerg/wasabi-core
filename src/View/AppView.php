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
use Cake\Core\Configure;
use Cake\View\View;

/**
 * Class AppView
 *
 * @property \Cake\View\Helper\FlashHelper $Flash
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Wasabi\Core\View\Helper\AssetHelper $Asset
 * @property \FrankFoerster\Filter\View\Helper\FilterHelper $Filter
 * @property \Wasabi\Core\View\Helper\GuardianHelper $Guardian
 * @property \Wasabi\Core\View\Helper\HtmlHelper $Html
 * @property \Wasabi\Core\View\Helper\MenuHelper $Menu
 * @property \Wasabi\Core\View\Helper\EmailHelper $Email
 * @property array activeFilters
 * @property array filterFields
 * @property array activeSort
 * @property array sortFields
 * @property array paginationParams
 * @property array defaultSort
 */
class AppView extends View
{
    /**
     * All plugin controller actions that use CakePHP's default form templates.
     *
     * @var array
     */
    public $defaultFormTemplateActions = [
        'Wasabi/Core.Users.login',
        'Wasabi/Core.Users.lostPassword',
        'Wasabi/Core.Users.requestNewVerificationEmail'
    ];

    /**
     * The default layout to render.
     *
     * @var string
     */
    public $layout = 'Wasabi/Core.default';

    /**
     * Initialization hook method.
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadHelper('Asset', [
            'className' => 'Wasabi/Core.Asset'
        ]);
        $this->loadHelper('Html', [
            'className' => 'Wasabi/Core.Html'
        ]);
        $this->loadHelper('Menu', [
            'className' => 'Wasabi/Core.Menu'
        ]);
        $this->loadHelper('Guardian', [
            'className' => 'Wasabi/Core.Guardian'
        ]);
        $this->loadHelper('Filter', [
            'className' => 'FrankFoerster/Filter.Filter'
        ]);
        $this->loadHelper('Email', [
            'className' => 'Wasabi/Core.Email'
        ]);

        $this->defaultFormTemplateActions = array_merge(
            $this->defaultFormTemplateActions,
            Configure::read('defaultFormTemplateActions') ?? []
        );

        if (!in_array(join('.', [
            $this->request->params['plugin'] ? $this->request->params['plugin'] : 'App',
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
                    'textarea' => ['Wasabi\Core\View\Widget\TextAreaWidget'],
                    '_default' => ['Wasabi\Core\View\Widget\BasicWidget']
                ]
            ]);
        }
    }
}
