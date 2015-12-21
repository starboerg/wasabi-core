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

use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Hash;
use FrankFoerster\Filter\Controller\Component\FilterComponent;
use Wasabi\Core\Model\Table\MenusTable;
use Wasabi\Core\Model\Table\MenuItemsTable;

/**
 * Class MenusController
 *
 * @property FilterComponent $Filter
 * @property MenusTable $Menus
 * @property MenuItemsTable $MenuItems
 */
class MenusController extends BackendAppController
{
    /**
     * Sortable Fields definition
     *
     * `actions` describes on which controller
     * action this field is sortable.
     *
     * @var array
     */
    public $sortFields = [
        'id' => [
            'modelField' => 'Menus.id',
            'default' => 'asc',
            'actions' => ['index']
        ],
        'name' => [
            'modelField' => 'Menus.name',
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
        $this->loadComponent('FrankFoerster/Filter.Filter');
        $this->loadComponent('RequestHandler');
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
            'menuItems' => $this->Menus->MenuItems->find('threaded', ['order' => ['lft' => 'ASC']])->where(['MenuItems.menu_id' => $id])->hydrate(false)
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
     * @param string $menuId
     * @param null|string $parentId
     */
    public function addItem($menuId, $parentId = null)
    {
        if (!$menuId || !$this->Menus->exists(['id' => $menuId])) {
            throw new NotFoundException();
        }

        if ($parentId !== null && !$this->Menus->MenuItems->exists(['id' => $parentId])) {
            $this->Flash->error(__d('wasabi_core', 'A menu item with id <strong>{0}</strong> does not exist.', $parentId));
            $this->redirect(['action' => 'edit', $menuId]);
            return;
        }

        if (!$this->request->is(['get', 'post'])) {
            throw new MethodNotAllowedException();
        }

        $menuItem = $this->Menus->MenuItems->newEntity();
        if ($this->request->is('post') && !empty($this->request->data)) {
            $menuItem = $this->Menus->MenuItems->patchEntity($menuItem, Hash::merge($this->request->data, [
                'menu_id' => $menuId,
                'parent_id' => $parentId
            ]));
            $this->Menus->MenuItems->setScope($menuId);
            if ($this->Menus->MenuItems->save($menuItem)) {
                $this->Flash->success(__d('wasabi_core', 'Menu Item <strong>{0}</strong> has been created.', [$this->request->data['name']]));
                $this->redirect(['action' => 'edit', $menuId]);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set([
            'menu' => $this->Menus->get($menuId),
            'menuItem' => $menuItem,
            'linkTypes' => $this->Menus->getLinkTypes()
        ]);
    }

    /**
     * Edit action
     * GET | PUT
     *
     * @param $id
     */
    public function editItem($id)
    {
        if (!$id || !$this->Menus->MenuItems->exists(['id' => $id])) {
            throw new NotFoundException();
        }

        if (!$this->request->is(['get', 'put'])) {
            throw new MethodNotAllowedException();
        }

        $menuItem = $this->Menus->MenuItems->get($id);
        if ($this->request->is('put') && !empty($this->request->data)) {
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

    /**
     * Delete a menu item.
     * POST
     *
     * @param string $id
     */
    public function deleteItem($id)
    {
        if (!$id || !$this->Menus->MenuItems->exists(['id' => $id])) {
            throw new NotFoundException();
        }

        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $menuItem = $this->Menus->MenuItems->get($id);
        if ($this->Menus->MenuItems->delete($menuItem)) {
            $this->Flash->success(__d('wasabi_core', 'The menu item <strong>{0}</strong> has been deleted.', $menuItem->get('name')));
        } else {
            $this->Flash->error($this->dbErrorMessage);
        }

        $this->redirect(['action' => 'edit', $menuItem->get('menu_id')]);
        return;
    }

    /**
     * reorderItems action
     * AJAX POST
     */
    public function reorderItems()
    {
        if (!$this->request->isAll(['ajax', 'post'])) {
            throw new MethodNotAllowedException();
        }

        if (empty($this->request->data)) {
            throw new BadRequestException();
        }

        $this->request->data = Hash::map($this->request->data, '{n}', function($item) {
            if ($item['parent_id'] === 'null') {
                $item['parent_id'] = null;
            }
            return $item;
        });

        // save the new language positions
        $menuItems = $this->Menus->MenuItems->patchEntities(
            $this->Menus->MenuItems->find('threaded'),
            $this->request->data()
        );
        $this->Menus->MenuItems->connection()->begin();
        foreach ($menuItems as $menuItem) {
            $this->Menus->MenuItems->behaviors()->unload('Tree');
            if (!$this->Menus->MenuItems->save($menuItem)) {
                $this->Menus->MenuItems->connection()->rollback();
                break;
            }
        }
        if ($this->Menus->MenuItems->connection()->inTransaction()) {
            $this->Menus->MenuItems->connection()->commit();
            $status = 'success';
            $flashMessage = __d('wasabi_core', 'The menu item positions have been updated.');
        } else {
            $status = 'error';
            $flashMessage = $this->dbErrorMessage;
        }

        $this->set([
            'status' => $status,
            'flashMessage' => $flashMessage,
            '_serialize' => ['status', 'flashMessage']
        ]);

        $this->RequestHandler->renderAs($this, 'json');
    }
}
