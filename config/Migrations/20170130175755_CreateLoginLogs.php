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
 * @link          https://github.com/wasabi-cms/core Wasabi Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Wasabi\Core\Database\Migration\BaseMigration;

class CreateLoginLogs extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('login_logs');
        $table
            ->addColumn('login_field', 'string', ['limit' => 32, 'null' => false])
            ->addColumn('login_field_value', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('client_ip', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('success', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('blocked', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table
            ->addIndex('client_ip', ['name' => 'BY_CLIENT_IP', 'unique' => false])
            ->addIndex('success', ['name' => 'BY_SUCCESS', 'unique' => false])
            ->addIndex('blocked', ['name' => 'BY_BLOCKED', 'unique' => false])
            ->addIndex('created', ['name' => 'BY_CREATED', 'unique' => false]);

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
        $this->dropTable('login_logs');

        $this->clearModelCache();
    }
}
