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
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\MethodNotAllowedException;

/**
 * Class LanguagesController
 *
 * @property \Wasabi\Core\Model\Table\LanguagesTable $Languages
 */
class LanguagesController extends BackendAppController
{
    /**
     * Initialization hook method.
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * index action
     * GET
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
     * @param string $id
     */
    public function edit($id)
    {
        if (!$id || !$this->Languages->exists($id)) {
            throw new NotFoundException();
        }
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
}
