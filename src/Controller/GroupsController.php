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

use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Exception\NotFoundException;
use Wasabi\Core\Model\Table\GroupsTable;

/**
 * Class GroupsController
 *
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
        'search' => [
            'modelField' => [
                'Group.name'
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
        'user' => [
            'modelField' => 'User.firstname',
            'default' => 'asc',
            'actions' => ['index']
        ],
        'email' => [
            'modelField' => 'User.email',
            'actions' => ['index']
        ],
        'group' => [
            'modelField' => 'Group.name',
            'actions' => ['index']
        ],
        'status' => [
            'modelField' => 'User.active',
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
        $groups = $this->Groups->find('all')->hydrate(false);
        $this->set('groups', $groups);
    }

    /**
     * Add action
     * GET | POST
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
     * @param int|string $id
     */
    public function edit($id)
    {
        if (!$id || !$this->Groups->exists(['id' => $id])) {
            throw new NotFoundException();
        }
        if (!$this->request->is(['get', 'put'])) {
            throw new MethodNotAllowedException();
        }

        $group = $this->Groups->get($id);

        if ($this->request->is('put')) {
            $group = $this->Groups->patchEntity($group, $this->request->data);
            if ($this->Groups->save($group)) {
                $this->Flash->success(__d('wasabi_core', 'The group <strong>{0}</strong> has been saved.', $this->request->data['name']));
                $this->redirect(['action' => 'index']);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set('group', $group);
        $this->render('add');
    }
}
