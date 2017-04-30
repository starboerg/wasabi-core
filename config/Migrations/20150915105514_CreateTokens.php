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

class CreateTokens extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('tokens');
        $table->addColumn('user_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('token', 'string', ['limit' => 32, 'null' => false])
            ->addColumn('token_type', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('used', 'boolean', ['default' => 0, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('expires', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('user_id', ['name' => 'FK_USER_ID', 'unique' => false])
            ->addIndex('token', ['name' => 'BY_TOKEN', 'unique' => true]);
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
        $this->dropTable('tokens');

        $this->clearModelCache();
    }
}
