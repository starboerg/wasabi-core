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
use Wasabi\Core\Model\Entity\CacheSetting;
use Wasabi\Core\Model\Entity\GeneralSetting;
use Wasabi\Core\Model\Table\CacheSettingsTable;
use Wasabi\Core\Model\Table\GeneralSettingsTable;

/**
 * Class SettingsController
 *
 * @property CacheSettingsTable $CacheSettings
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
        $keys = [
            'instance_name',
            'Login__Message__show',
            'Login__Message__text',
            'Login__Message__class',
            'Email__email_sender',
            'Email__Activation__subject',
            'Email__Verification__subject_admin'
        ];
        $this->GeneralSettings = $this->loadModel('Wasabi/Core.GeneralSettings');
        $settings = $this->GeneralSettings->getKeyValues(new GeneralSetting(), $keys);
        if ($this->request->is('post') && !empty($this->request->data)) {
            $settings = $this->GeneralSettings->newEntity($this->request->data);
            if (!$settings->errors()) {
                if ($this->GeneralSettings->saveKeyValues($settings, $keys)) {
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
        $this->set('settings', $settings);
    }

    public function cache()
    {
        $keys = [
            'enable_caching',
            'cache_duration'
        ];
        $this->CacheSettings = $this->loadModel('Wasabi/Core.CacheSettings');
        $settings = $this->CacheSettings->getKeyValues(new CacheSetting(), $keys);
        if ($this->request->is('post') && !empty($this->request->data)) {
            $settings = $this->CacheSettings->newEntity($this->request->data);
            if (!$settings->errors()) {
                if ($this->CacheSettings->saveKeyValues($settings, $keys)) {
                    $this->Flash->success(__d('wasabi_core', 'The cache settings have been updated.'));
                    $this->redirect(['action' => 'cache']);
                    return;
                } else {
                    $this->Flash->error($this->dbErrorMessage);
                }
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set([
            'settings' => $settings,
            'cacheDurations' => $this->CacheSettings->cacheDurations
        ]);
    }
}
