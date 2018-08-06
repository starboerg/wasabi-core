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

use Cake\Collection\Collection;
use Cake\Database\Connection;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\MethodNotAllowedException;
use Wasabi\Core\Model\Table\LanguagesTable;

/**
 * Class LanguagesController
 *
 * @property LanguagesTable $Languages
 */
class LanguagesController extends BackendAppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Index action
     * GET
     *
     * @return void
     */
    public function index()
    {
        $languages = $this->Languages->find('allFrontendBackend');

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
     * @throws \Aura\Intl\Exception
     */
    public function add()
    {
        $language = $this->Languages->newEntity();

        if ($this->request->is('post')) {
            $this->Languages->patchEntity($language, $this->request->getData());
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
     * @throws \Aura\Intl\Exception
     */
    public function edit($id)
    {
        if (!$this->request->is(['get', 'put'])) {
            throw new MethodNotAllowedException();
        }

        $language = $this->Languages->get($id);

        if ($this->request->is('put')) {
            $this->Languages->patchEntity($language, $this->request->getData());
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
     * @throws \Aura\Intl\Exception
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

        $this->request->getSession()->write('contentLanguageId', (int)$id);
        $this->redirect($this->referer());
    }
}
