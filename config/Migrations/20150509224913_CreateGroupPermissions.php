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

use Wasabi\Core\BaseMigration;

class CreateGroupPermissions extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('group_permissions');
        $table->addColumn('group_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('path', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('plugin', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('controller', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('action', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('allowed', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('group_id', ['name' => 'FK_GROUP_ID', 'unique' => false]);
        $table->create();

        $this->unsignedIntId($table);
        $table->save();
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down()
    {
        $this->dropTable('group_permissions');

        $this->clearModelCache();
    }
}
