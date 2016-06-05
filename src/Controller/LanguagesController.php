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

use Cake\Collection\Collection;
use Cake\Database\Connection;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Session;

/**
 * Class LanguagesController
 *
 * @property \Wasabi\Core\Model\Table\LanguagesTable $Languages
 */
class LanguagesController extends BackendAppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Index action
     * GET
     *
     * @return void
     */
    public function index()
    {
        $languages = $this->Languages->find('allFrontendBackend')->hydrate(false);
        $this->set([
            'languages' => $languages,
            'language' => $this->Languages->newEntity()
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
        $language = $this->Languages->newEntity();
        if ($this->request->is('post') && !empty($this->request->data)) {
            $this->Languages->patchEntity($language, $this->request->data);
            if ($this->Languages->save($language)) {
                $this->Flash->success(__d('wasabi_core', 'The language <strong>{0}</strong> has been created.', $language->name));
                $this->redirect(['action' => 'index']);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set('language', $language);
    }

    /**
     * Edit action
     * GET | PUT
     *
     * @param string $id The language id.
     * @return void
     */
    public function edit($id)
    {
        if (!$this->request->is(['get', 'put'])) {
            throw new MethodNotAllowedException();
        }

        $language = $this->Languages->get($id);
        if ($this->request->is('put')) {
            $this->Languages->patchEntity($language, $this->request->data);
            if ($this->Languages->save($language)) {
                $this->Flash->success(__d('wasabi_core', 'The language <strong>{0}</strong> has been saved.', $language->name));
            } else {
                $this->Flash->error($this->dbErrorMessage);
            }
            $this->redirect(['action' => 'index']);
            return;
        }

        $this->set('language', $language);
        $this->render('add');
    }

    /**
     * Delete action
     * POST
     *
     * @param string $id The language id.
     * @return void
     */
    public function delete($id)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $language = $this->Languages->get($id);
        if ($this->Languages->delete($language)) {
            $this->Flash->success(__d('wasabi_core', 'The language <strong>{0}</strong> has been deleted.', $language->name));
        } else {
            $this->Flash->error($this->dbErrorMessage);
        }
        $this->redirect(['action' => 'index']);
        //@codingStandardIgnoreStart
        return;
        //@codingStandardIgnoreEnd
    }

    /**
     * Sort action
     * AJAX POST
     *
     * Save the order of languages.
     *
     * @return void
     */
    public function sort()
    {
        if (!$this->request->is('ajax') || !$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        if (empty($this->request->data)) {
            throw new BadRequestException();
        }

        // save the new language positions
        $languages = $this->Languages->patchEntities(
            $this->Languages->find('allFrontendBackend'),
            $this->request->data
        );
        /** @var Connection $connection */
        $connection = $this->Languages->connection();
        $connection->begin();
        foreach ($languages as $language) {
            if (!$this->Languages->save($language)) {
                $connection->rollback();
                break;
            }
        }
        if ($connection->inTransaction()) {
            $connection->commit();
            $status = 'success';
            $flashMessage = __d('wasabi_core', 'The language position has been updated.');
        } else {
            $status = 'error';
            $flashMessage = $this->dbErrorMessage;
        }

        // create the json response
        $frontendLanguages = $this->Languages
            ->filterFrontend(new Collection($languages))
            ->sortBy('position', SORT_ASC, SORT_NUMERIC)
            ->toList();
        $backendLanguages = $this->Languages
            ->filterBackend(new Collection($languages))
            ->sortBy('position', SORT_ASC, SORT_NUMERIC)
            ->toList();

        $this->set(compact('status', 'flashMessage', 'frontendLanguages', 'backendLanguages'));
        $this->set('_serialize', ['status', 'flashMessage', 'frontendLanguages', 'backendLanguages']);

        $this->RequestHandler->renderAs($this, 'json');
    }

    /**
     * Change action
     * GET
     *
     * Change the content language to $id and update the session.
     *
     * @param string $id The language id.
     * @return void
     */
    public function change($id = null)
    {
        if ($id === null || !$this->Languages->exists(['id' => $id])) {
            $this->Flash->error($this->invalidRequestMessage);
            $this->redirect($this->referer());
            return;
        }
        $this->request->session()->write('contentLanguageId', (int)$id);
        $this->redirect($this->referer());
    }
}
