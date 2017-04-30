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

class RemoveMenuItems extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $this->dropTable('menu_items');

        $this->clearModelCache();
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down()
    {
        $table = $this->table('menu_items');
        $table
            ->addColumn('menu_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('parent_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => true, 'default' => null])
            ->addColumn('lft', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('rght', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('type', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('target', 'text', ['null' => true, 'default' => null])
            ->addColumn('external_link', 'text', ['null' => true, 'default' => null])
            ->addColumn('foreign_model', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('foreign_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => true, 'default' => null])
            ->addColumn('plugin', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('controller', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('action', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('params', 'text', ['null' => true, 'default' => null])
            ->addColumn('query', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('menu_id', ['name' => 'FK_MENU_ID', 'unique' => false]);
        $table->addIndex('parent_id', ['name' => 'FK_PARENT_ID', 'unique' => false]);
        $table->create();

        $this->unsignedIntId($table);
        $table->save();
    }
}
