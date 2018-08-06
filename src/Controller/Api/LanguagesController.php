<?php

namespace Wasabi\Core\Controller\Api;

use Cake\Collection\Collection;
use Cake\Database\Connection;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\MethodNotAllowedException;
use Wasabi\Core\Model\Table\LanguagesTable;

/**
 * Class LanguagesController
 *
 * @property LanguagesTable Languages
 */
class LanguagesController extends ApiAppController
{
    /**
     * Initialization hook method.
     *
     * @throws \Exception
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadModel('Wasabi/Core.Languages');
    }

    /**
     * Sort action
     * AJAX POST
     *
     * Save the order of languages.
     *
     * @return void
     * @throws \Aura\Intl\Exception
     */
    public function sort()
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        if (empty($this->request->getData())) {
            throw new BadRequestException();
        }

        // save the new language positions
        $languages = $this->Languages->patchEntities(
            $this->Languages->find('allFrontendBackend'),
            $this->request->getData()
        );
        /** @var Connection $connection */
        $connection = $this->Languages->getConnection();
        $connection->begin();
        foreach ($languages as $language) {
            if (!$this->Languages->save($language)) {
                $connection->rollback();
                break;
            }
        }

        $success = false;

        if ($connection->inTransaction()) {
            $connection->commit();
            $success = true;
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

        $this->setResponseData([
            'frontendLanguages' => $frontendLanguages,
            'backendLanguages' => $backendLanguages
        ]);

        if ($success) {
            $this->setResponseData('flashMessage', __d('wasabi_core', 'The language position has been updated.'));
            $this->respondOk();
        } else {
            $this->errorMessage = $this->dbErrorMessage;
            $this->respondWithBadRequest();
        }
    }
}
