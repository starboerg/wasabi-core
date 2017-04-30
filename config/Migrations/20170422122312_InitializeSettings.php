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

use Cake\ORM\TableRegistry;
use Wasabi\Core\Database\Migration\BaseMigration;
use Wasabi\Core\Model\Table\SettingsTable;

/**
 * Class InitializeSettings
 *
 * @property SettingsTable Settings
 */
class InitializeSettings extends BaseMigration
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->Settings = TableRegistry::get('Wasabi/Core.Settings');
    }

    /**
     * Migrate up.
     *
     * @return void
     */
    public function up()
    {
        $settings = $this->Settings->newEntities([
            [
                'scope' => 'Core',
                'field' => 'instance_name',
                'value' => 'My App'
            ],
            [
                'scope' => 'Core',
                'field' => 'instance_short_name',
                'value' => 'A'
            ],
            [
                'scope' => 'Core',
                'field' => 'html_title_suffix',
                'value' => 'My App'
            ],
            [
                'scope' => 'Core',
                'field' => 'Login__Message__show',
                'value' => 0
            ],
            [
                'scope' => 'Core',
                'field' => 'Login__Message__text',
                'value' => ''
            ],
            [
                'scope' => 'Core',
                'field' => 'Login__Message__class',
                'value' => 'info'
            ],
            [
                'scope' => 'Core',
                'field' => 'Login__HeartBeat__max_login_time',
                'value' => '3600000'
            ],
            [
                'scope' => 'Core',
                'field' => 'Email__email_sender_name',
                'value' => 'John Doe'
            ],
            [
                'scope' => 'Core',
                'field' => 'Email__email_sender',
                'value' => 'john@doe.me'
            ],
            [
                'scope' => 'Core',
                'field' => 'Auth__max_login_attempts',
                'value' => 5
            ],
            [
                'scope' => 'Core',
                'field' => 'Auth__failed_login_recognition_time',
                'value' => 10
            ],
            [
                'scope' => 'Core',
                'field' => 'Auth__block_time',
                'value' => 15
            ],
            [
                'scope' => 'Core',
                'field' => 'User__has_username',
                'value' => 0
            ],
            [
                'scope' => 'Core',
                'field' => 'User__has_firstname_lastname',
                'value' => 0
            ],
            [
                'scope' => 'Core',
                'field' => 'User__allow_timezone_change',
                'value' => 0
            ],
            [
                'scope' => 'Core',
                'field' => 'Core.User.belongs_to_many_groups',
                'value' => 0
            ]
        ]);

        $this->Settings->saveMany($settings);
    }

    /**
     * Migrate down.
     *
     * @return void
     */
    public function down()
    {
        $this->Settings->deleteAll(['1=1']);
    }
}
