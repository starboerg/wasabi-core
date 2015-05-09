<?php
/**
 * Wasabi Core Backend App Controller
 *
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
namespace Wasabi\Core\Controller;

use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Exception\NotFoundException;
use Wasabi\Core\Model\Table\MenusTable;

/**
 * Class MenusController
 * @property MenusTable $Menus
 */
class MenusController extends BackendAppController
{
    /**
     * Filter fields definitions
     *
     * `actions` describes on which controller
     * action this filter field is available.
     *
     * @var array
     */
    public $filterFields = [
        'search' => [
            'modelField' => [
                'Menu.name'
            ],
            'type' => 'like',
            'actions' => ['index']
        ]
    ];

    /**
     * Controller actions where slugged filters are used.
     *
     * @var array
     */
    public $sluggedFilterActions = [
        'index'
    ];

    /**
     * Sortable Fields definition
     *
     * `actions` describes on which controller
     * action this field is sortable.
     *
     * @var array
     */
    public $sortFields = [
        'name' => [
            'modelField' => 'Menu.name',
            'default' => 'asc',
            'actions' => ['index']
        ],
        'menu_item_count' => [
            'modelField' => 'Menu.menu_item_count',
            'actions' => ['index']
        ]
    ];

    /**
     * Initialization hook method.
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Wasabi/Core.Filter');
    }

    /**
     * index action
     * GET
     */
    public function index()
    {
        $menus = $this->Menus->find('all')->hydrate(false);
        $this->set('menus', $menus);
    }

    /**
     * Add action
     * GET | POST
     */
    public function add()
    {
        $menu = $this->Menus->newEntity();
        if ($this->request->is('post') && !empty($this->request->data)) {
            $this->Menus->patchEntity($menu, $this->request->data);
            if ($this->Menus->save($menu)) {
                $this->Flash->success(__d('wasabi_core', 'The menu <strong>{0}</strong> has been created.', $this->request->data['name']));
                $this->redirect(['action' => 'index']);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set('menu', $menu);
    }

    /**
     * Edit Action
     * GET | PUT
     *
     * @param $id
     */
    public function edit($id)
    {
        if (!$id || !$this->Menus->exists(['id' => $id])) {
            throw new NotFoundException();
        }
        if (!$this->request->is(['get', 'put'])) {
            throw new MethodNotAllowedException();
        }

        $menu = $this->Menus->get($id);

        if ($this->request->is('put')) {
            $menu = $this->Menus->patchEntity($menu, $this->request->data);
            if ($this->Menus->save($menu)) {
                $this->Flash->success(__d('wasabi_core', 'The menu <strong>{0}</strong> has been saved.', $this->request->data['name']));
                $this->redirect(['action' => 'index']);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set('menu', $menu);
        $this->render('add');
    }

    /**
     * Delete action
     * POST
     *
     * @param string $id
     */
    public function delete($id)
    {
        if (!$id || !$this->Menus->exists(['id' => $id])) {
            throw new NotFoundException();
        }
        if (!$this->request->is(['post'])) {
            throw new MethodNotAllowedException();
        }

        $menu = $this->Menus->get($id);
        if ($this->Menus->delete($menu)) {
            $this->Flash->success(__d('wasabi_core', 'The menu <strong>{0}</strong> has been deleted.', $menu->name));
        } else {
            $this->Flash->error($this->dbErrorMessage);
        }
        $this->redirect(['action' => 'index']);
        return;
    }
}