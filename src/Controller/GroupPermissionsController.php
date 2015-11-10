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
namespace Wasabi\Core\Controller;

use Cake\Event\Event;
use Cake\Network\Exception\MethodNotAllowedException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Wasabi\Core\Model\Entity\Group;
use Wasabi\Core\Model\Table\GroupPermissionsTable;

/**
 * Class GroupPermissionsController
 * @property GroupPermissionsTable GroupPermissions
 */
class GroupPermissionsController extends BackendAppController
{
    /**
     * Initialization hook method
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Index action
     * GET
     */
    public function index()
    {
        $permissions = $this->GroupPermissions->find('all')
            ->contain(['Groups'])
            ->order([
                'Groups.name' => 'ASC',
                'path' => 'ASC'
            ])
            ->hydrate(false)
            ->toArray();

        $plugins = [];
        if (!empty($permissions)) {
            foreach ($permissions as $p) {
                $plugin = $p['plugin'];
                $controller = $p['controller'];
                $action = $p['action'];
                $groupId = $p['group']['id'];
                $plugins[$plugin][$controller][$action][$groupId]['permission_id'] = $p['id'];
                $plugins[$plugin][$controller][$action][$groupId]['name'] = $p['group']['name'];
                $plugins[$plugin][$controller][$action][$groupId]['allowed'] = $p['allowed'];
            }
        }

        $this->set([
            'plugins' => $plugins,
            'permission' => $this->GroupPermissions->newEntity()
        ]);
    }

    /**
     * Sync action
     * GET
     *
     * @return void
     */
    public function sync()
    {
        $actionMap = $this->Guardian->getActionMap();

        // check existance of all permission entries for each individual group
        /** @var Group $groups */
        $groups = $this->GroupPermissions->Groups->find('all')
            ->where(['Groups.id <>' => 1]);// ignore Administrator group

        $this->GroupPermissions->connection()->begin();

        // delete guest actions
        $this->GroupPermissions->deleteAll([
            'path IN' => $this->Guardian->getGuestActions()
        ]);


        foreach ($groups as $group) {
            try {
                $this->GroupPermissions->createMissingPermissions($group->id, $actionMap);
                $this->GroupPermissions->deleteOrphans($group->id, $actionMap);
            } catch (Exception $e) {
                $this->GroupPermissions->connection()->rollback();
            }
        }

        if ($this->GroupPermissions->connection()->inTransaction()) {
            $this->GroupPermissions->connection()->commit();
        }
        // delete guardian path cache
        $this->eventManager()->dispatch(new Event('Guardian.GroupPermissions.afterSync'));

        $this->Flash->success(__d('wasabi_core', 'All permissions have been synchronized.'));
        $this->redirect(['action' => 'index']);
        return;
    }

    /**
     * Update action
     * POST | AJAX
     */
    public function update()
    {
        if (!$this->request->is('post')) {
            if ($this->request->is('ajax')) {
                throw new MethodNotAllowedException();
            } else {
                $this->Flash->error($this->invalidRequestMessage);
                $this->redirect(['action' => 'index']);
                return;
            }
        }

        if (empty($this->request->data) && !$this->request->is('ajax')) {
            $this->Flash->warning(__d('wasabi_core', 'There are no permissions to update yet.'));
            $this->redirect(['action' => 'index']);
            return;
        }

        // save the new language positions
        $permissions = $this->GroupPermissions->patchEntities(
            $this->GroupPermissions->find('all'),
            $this->request->data()
        );

        $this->GroupPermissions->connection()->begin();
        foreach ($permissions as $permission) {
            if (!$this->GroupPermissions->save($permission)) {
                $this->GroupPermissions->connection()->rollback();
                break;
            }
        }

        if ($this->GroupPermissions->connection()->inTransaction()) {
            $this->GroupPermissions->connection()->commit();
            if ($this->request->is('ajax')) {
                $status = 'success';
                $this->set(compact('status'));
                $this->set('_serialize', ['status']);
            } else {
                $this->Flash->success(__d('wasabi_core', 'All permissions have been saved.'));
                $this->redirect(['action' => 'index']);
                return;
            }
        } else {
            $this->Flash->error($this->dbErrorMessage);
        }
    }
}
