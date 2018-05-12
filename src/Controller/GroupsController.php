<?php
/**
 * Wasabi Core
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @link          https://github.com/wasabi-cms/core Wasabi Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\Controller;

use Cake\Database\Connection;
use Cake\Database\Query;
use Cake\Network\Exception\MethodNotAllowedException;
use FrankFoerster\Filter\Controller\Component\FilterComponent;
use Wasabi\Core\Model\Table\GroupsTable;
use Wasabi\Core\Wasabi;

/**
 * Class GroupsController
 *
 * @property FilterComponent $Filter
 * @property GroupsTable $Groups
 */
class GroupsController extends BackendAppController
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
        'id' => [
            'modelField' => 'Groups.id',
            'type' => 'like',
            'actions' => ['index']
        ],
        'group' => [
            'modelField' => 'Groups.name',
            'type' => 'like',
            'actions' => ['index']
        ],
        'description' => [
            'modelField' => 'Groups.description',
            'type' => 'like',
            'actions' => ['index']
        ]
    ];

    /**
     * Controller actions where slugged filters are used.
     *
     * @var array
     */
    public $filterActions = [
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
        'id' => [
            'modelField' => 'Groups.id',
            'default' => 'asc',
            'actions' => ['index']
        ],
        'group' => [
            'modelField' => 'Groups.name',
            'actions' => ['index']
        ],
        'count' => [
            'modelField' => 'Groups.user_count',
            'actions' => ['index']
        ]
    ];

    /**
     * Limit options determine the available dropdown
     * options (display items per page) for each action.
     *
     * @var array
     */
    public $limits = [
        'index' => [
            'limits' => [10, 25, 50, 75, 100, 150, 200],
            'default' => 10,
            'fieldName' => 'l'
        ]
    ];

    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('FrankFoerster/Filter.Filter');
    }

    /**
     * Index action
     * GET
     *
     * @return void
     */
    public function index()
    {
        $groups = $this->Filter->filter($this->Groups->find('all'));
        $this->set([
            'groups' => $this->Filter->paginate($groups)
        ]);
    }

    /**
     * Add action
     * GET | POST
     *
     * @return void
     */
    public function add()
    {
        $group = $this->Groups->newEntity();

        if ($this->request->is('post')) {
            $this->Groups->patchEntity($group, $this->request->getData());
            if ($this->Groups->save($group)) {
                $this->Flash->success(__d('wasabi_core', 'The group <strong>{0}</strong> has been created.', $group->name));
                $this->redirect(['action' => 'index']);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }

        $this->set('group', $group);
    }

    /**
     * Edit action
     * GET | PUT
     *
     * @param string $id The group id.
     * @return void
     */
    public function edit($id)
    {
        if (!$this->request->is(['get', 'put'])) {
            throw new MethodNotAllowedException();
        }

        $group = $this->Groups->get($id);

        if ($this->request->is('put')) {
            $group = $this->Groups->patchEntity($group, $this->request->getData());
            if ($this->Groups->save($group)) {
                $this->Flash->success(__d('wasabi_core', 'The group <strong>{0}</strong> has been saved.', $group->name));
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }

        $this->set('group', $group);

        $this->render('add');
    }

    /**
     * Delete action
     * POST
     *
     * @param string $id The group id.
     * @return void
     */
    public function delete($id)
    {
        if (!$this->request->is(['post'])) {
            throw new MethodNotAllowedException();
        }

        /** @var Connection $connection */
        $connection = $this->Groups->getConnection();
        $connection->begin();

        $group = $this->Groups->get($id);

        if (Wasabi::user()->cant('delete', $group)) {
            $this->redirect($this->Auth->getConfig('unauthorizedRedirect'));
            return;
        }

        $users = $this->Groups->UsersGroups->Users->find()
            ->matching('UsersGroups', function (Query $q) use ($id) {
                return $q->where(['UsersGroups.group_id' => $id]);
            });

        // Filter the resulting users to match only users assigned to more than one user group.
        $skipUserIds = $this->Groups->UsersGroups->findUserIdsWithOnlyOneGroup();
        if (!empty($skipUserIds)) {
            $users->where([
                $this->Groups->Users->aliasField('id') . ' IN' => $skipUserIds
            ]);
        }

        $userCount = $users->all()->count();
        $groupCanBeDeleted = ($userCount === 0);

        if (($alternativeGroupIds = $this->request->getData('alternative_group_id')) !== null) {
            // check if a valid alternative group has been selected for every user
            $valid = true;
            $validAlternativeGroupIds = [];
            foreach ($users as $user) {
                if (!isset($alternativeGroupIds[$user->id]) ||
                    (int)$alternativeGroupIds[$user->id] === (int)$id ||
                    !$this->Groups->exists(['id' => $alternativeGroupIds[$user->id]])
                ) {
                    $valid = false;
                } else {
                    $validAlternativeGroupIds[$user->id] = (int)$alternativeGroupIds[$user->id];
                }
            }
            if ($valid) {
                // move existing users of the group to the submitted alternative group
                $groupCanBeDeleted = (bool)$this->Groups->moveUsersToAlternativeGroups($validAlternativeGroupIds);
            } else {
                $this->Flash->error(__d('wasabi_core', 'Please select an alternative user group for every listed user.'));
            }
        }

        if ($groupCanBeDeleted === true) {
            if ($this->Groups->delete($group)) {
                $connection->commit();
                if ($userCount > 0) {
                    $this->Flash->success(__d('wasabi_core', 'The group <strong>{0}</strong> has been deleted. Prior <strong>{1}</strong> group member(s) ha(s/ve) been moved to another group.', $group->name, $userCount));
                } else {
                    $this->Flash->success(__d('wasabi_core', 'The group <strong>{0}</strong> has been deleted.', $group->name));
                }
            } else {
                $connection->rollback();
                $this->Flash->error($this->dbErrorMessage);
            }
            $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
            return;
        } else {
            $connection->rollback();
            $this->Flash->warning(__d('wasabi_core', 'The group <strong>{0}</strong> has <strong>{1}</strong> member(s) that need to be moved to another group.', $group->name, $userCount));
            $this->set([
                'users' => $users,
                'group' => $group,
                'groups' => $this->Groups->find('list')->where(['not' => ['id' => $id]])
            ]);
        }
    }
}
