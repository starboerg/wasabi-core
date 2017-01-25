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

use Cake\Database\Connection;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\MethodNotAllowedException;
use FrankFoerster\Filter\Controller\Component\FilterComponent;
use Wasabi\Core\Model\Table\GroupsTable;

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
        if ($this->request->is('post') && !empty($this->request->data)) {
            $this->Groups->patchEntity($group, $this->request->data);
            if ($this->Groups->save($group)) {
                $this->Flash->success(__d('wasabi_core', 'The group <strong>{0}</strong> has been created.', $this->request->data['name']));
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
            $group = $this->Groups->patchEntity($group, $this->request->data);
            if ($this->Groups->save($group)) {
                $this->Flash->success(__d('wasabi_core', 'The group <strong>{0}</strong> has been saved.', $this->request->data['name']));
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
        if ($id === '1') {
            throw new ForbiddenException();
        }
        if (!$this->request->is(['post'])) {
            throw new MethodNotAllowedException();
        }

        /** @var Connection $connection */
        $connection = $this->Groups->connection();
        $connection->begin();
        $group = $this->Groups->get($id);
        $userCount = (int)$group->user_count;
        $groupCanBeDeleted = ($userCount === 0);

        // @TODO: handle differently if (Configure::read('Wasabi.User.belongsToManyGroups'))

        $alternativeGroup = null;
        if (($alternativeGroupId = $this->request->data('alternative_group_id')) !== null &&
            $this->Groups->exists($alternativeGroupId)
        ) {
            // move existing users of the group to the submitted alternative group
            $alternativeGroup = $this->Groups->get($alternativeGroupId);
            $groupCanBeDeleted = (bool)$this->Groups->moveUsersToAlternativeGroup($group, $alternativeGroup);
        }

        if ($groupCanBeDeleted === true) {
            if ($this->Groups->delete($group)) {
                $connection->commit();
                if ($userCount > 0) {
                    $this->Flash->success(__d('wasabi_core', 'The group <strong>{0}</strong> has been deleted. Prior <strong>{1}</strong> group member(s) ha(s/ve) been moved to the <strong>{2}</strong> group.', $group->name, $userCount, $alternativeGroup->name));
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
                'group' => $group,
                'groups' => $this->Groups->find('list')->where(['not' => ['id' => $id]])
            ]);
        }
    }
}
