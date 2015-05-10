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
use Wasabi\Core\Model\Entity\GeneralSetting;
use Wasabi\Core\Model\Table\GeneralSettingsTable;

/**
 * Class SettingsController
 *
 * @property GeneralSettingsTable $GeneralSettings
 */
class SettingsController extends BackendAppController
{
    /**
     * general action
     * GET | POST
     *
     * Edit general settings.
     */
    public function general()
    {
        $this->GeneralSettings = $this->loadModel('Wasabi/Core.GeneralSettings');
        $settings = $this->GeneralSettings->getKeyValues(new GeneralSetting());
        if ($this->request->is('post') && !empty($this->request->data)) {
            $settings = $this->GeneralSettings->newEntity($this->request->data);
            if (!$settings->errors()) {
                if ($this->GeneralSettings->saveKeyValues($settings)) {
                    $this->Flash->success(__d('wasabi_core', 'The general settings have been updated.'));
                    $this->redirect(['action' => 'general']);
                    return;
                } else {
                    $this->Flash->error($this->dbErrorMessage);
                }
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set([
            'settings' => $settings
        ]);
    }
}
