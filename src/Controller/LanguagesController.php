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
        $this->set('languages', $languages);
        $this->set('language', $this->Languages->newEntity());
    }
}
