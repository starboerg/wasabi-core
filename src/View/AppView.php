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

use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Event\EventManager;

/**
 * Class AppView
 *
 * @property \Cake\View\Helper\FlashHelper $Flash
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Wasabi\Core\View\Helper\AssetHelper $Asset
 * @property \Wasabi\Core\View\Helper\GuardianHelper $Guardian
 * @property \Wasabi\Core\View\Helper\HtmlHelper $Html
 * @property \Wasabi\Core\View\Helper\MenuHelper $Menu
 */
class AppView extends \App\View\AppView
{
    public $activeFilters;
    public $filterFields;
    public $activeSort;
    public $sortFields;
    public $defaultSort;

    public function initialize()
    {
        if (join('.', [
                $this->request->params['plugin'],
                $this->request->params['controller'],
                $this->request->params['action']
            ]) !== 'Wasabi/Core.Users.login'
        ) {
            $this->loadHelper('Form', [
                'templates' => 'Wasabi/Core.form_templates',
                'widgets' => [
                    'label' => ['Wasabi\Core\View\Widget\LabelWidget'],
                    'section' => ['Wasabi\Core\View\Widget\SectionWidget'],
                    '_default' => ['Wasabi\Core\View\Widget\BasicWidget']
                ]
            ]);
        }
    }

    public function __construct(Request $request = null, Response $response = null, EventManager $eventManager = null, array $viewOptions = [])
    {
        parent::__construct($request, $response, $eventManager, $viewOptions);
//		if (isset($controller->Filter)) {
//			$this->activeFilters = $controller->Filter->activeFilters;
//			$this->filterFields = $controller->Filter->filterFields;
//			$this->activeSort = $controller->Filter->activeSort;
//			$this->sortFields = $controller->Filter->sortFields;
//			$this->paginationParams = $controller->Filter->paginationParams;
//			$this->defaultSort = $controller->Filter->defaultSort;
//		}
    }
}
