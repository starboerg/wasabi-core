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

use Wasabi\Core\Model\Entity\Language;
use Wasabi\Core\Model\Table\LanguagesTable;

/**
 * Class LanguagesController
 *
 * @property LanguagesTable $Languages
 */
class LanguagesController extends BackendAppController
{
    /**
     * Initialization of this controller.
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
        $languages = $this->Languages->find('all')->hydrate(false);
        $this->set('languages', $languages);
        $this->set('language', new Language());
    }
}
