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
use Wasabi\Core\Model\Entity\User;

class CreateUsers extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('users');
        $table->addColumn('group_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('username', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 60, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('verified', 'boolean', ['default' => 0, 'null' => false])
            ->addColumn('active', 'boolean', ['default' => 0, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('username', ['name' => 'BY_USERNAME', 'unique' => true])
            ->addIndex('group_id', ['name' => 'FK_GROUP_ID', 'unique' => false])
            ->addIndex('email', ['name' => 'BY_EMAIL', 'unique' => true])
            ->addIndex('active', ['name' => 'BY_ACTIVE', 'unique' => false]);
        $table->create();

        $this->unsignedIntId($table);
        $table->save();

        $user = new User([
            'username' => 'admin',
            'group_id' => 1,
            'email' => 'example@example.com',
            'password' => 'admin',
            'verified' => 1,
            'active' => 1,
        ]);
        $Users = TableRegistry::get('Wasabi/Core.Users');
        $Users->save($user);
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down()
    {
        $this->dropTable('users');

        $this->clearModelCache();
    }
}
