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
use Cake\Utility\Hash;
use Wasabi\Core\Model\Table\MenusTable;
use Wasabi\Core\Model\Table\MenuItemsTable;

/**
 * Class MenusController
 * @property MenusTable $Menus
 * @property MenuItemsTable $MenuItems
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
            'modelField' => 'Menus.name',
            'default' => 'asc',
            'actions' => ['index']
        ],
        'menu_item_count' => [
            'modelField' => 'Menus.menu_item_count',
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
        $menus = $this->Filter->filter($this->Menus->find('all'))->hydrate(false);
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
        $this->set([
            'menu' => $menu,
            'menuItems' => $this->Menus->MenuItems->find('threaded')->where(['MenuItems.menu_id' => $id])->hydrate(false)
        ]);
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

    /**
     * Add action
     * GET | POST
     *
     * @param $menuId
     * @param null $parentId
     */
    public function add_item($menuId = null, $parentId = null)
    {
        if ($menuId === null || !$this->Menus->exists(['id' => $menuId])) {
            $this->Flash->error($this->invalidRequestMessage);
            $this->redirect(['action' => 'index']);
            return;
        }

        $menuItem = $this->Menus->MenuItems->newEntity();
        if ($this->request->is('post') && !empty($this->request->data)) {
            $menuItem = $this->Menus->MenuItems->patchEntity($menuItem, Hash::merge($this->request->data, [
                'menu_id' => $menuId,
                'parent_id' => $parentId
            ]));
            if ($this->Menus->MenuItems->save($menuItem)) {
                $this->Flash->success(__d('wasabi_core', 'Menu Item <strong>{0}</strong> has been updated.', [$this->request->data['name']]));
                $this->redirect(['action' => 'edit', $menuId]);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set([
            'menu' => $this->Menus->get($menuId),
            'menuItem' => $menuItem
        ]);
        $this->render('add_item');
    }

    /**
     * Edit action
     * GET | PUT
     *
     * @param $id
     */
    public function edit_item($id)
    {
        if ($id === null || !$this->Menus->MenuItems->exists(['id' => $id])) {
            $this->Flash->error($this->invalidRequestMessage);
            $this->redirect(['action' => 'index']);
            return;
        }

        if (!$this->request->is(['get', 'put'])) {
            throw new MethodNotAllowedException();
        }

        $menuItem = $this->Menus->MenuItems->get($id, [
            'fields' => [
                'id',
                'name',
                'menu_id'
            ]
        ]);
        if ($this->request->is('put')) {
            $menuItem = $this->Menus->MenuItems->patchEntity($menuItem, $this->request->data);
            if ($this->Menus->MenuItems->save($menuItem)) {
                $this->Flash->success(__d('wasabi_core', 'Menu Item <strong>{0}</strong> has been updated.', $this->request->data['name']));
                $this->redirect(['action' => 'edit', $menuItem->get('menu_id')]);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set([
            'menu' => $this->Menus->get($menuItem->get('menu_id')),
            'menuItem' => $menuItem
        ]);
        $this->render('add_item');
    }
}
