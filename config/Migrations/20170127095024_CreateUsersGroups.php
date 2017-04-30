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

use Wasabi\Core\Database\Migration\BaseMigration;

class CreateUsersGroups extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        if ($this->hasTable('users_groups')) {
            return;
        }

        $table = $this->table('users_groups');
        $table->addColumn('user_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('group_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false]);
        $table->addIndex('user_id', ['name' => 'FK_USER_ID', 'unique' => false])
            ->addIndex('group_id', ['name' => 'FK_GROUP_ID', 'unique' => false]);
        $table->create();

        $this->unsignedIntId($table);
        $table->save();

        $UsersGroups = \Cake\ORM\TableRegistry::get('UsersGroups');
        $UsersGroups->save($UsersGroups->newEntity([
            'user_id' => 1,
            'group_id' => 1
        ]));
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down()
    {
        $this->dropTable('users_groups');

        $this->clearModelCache();
    }
}
