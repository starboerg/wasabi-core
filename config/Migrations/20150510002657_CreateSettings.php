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

class CreateSettings extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('settings');
        $table->addColumn('scope', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('field', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('value', 'text', ['null' => true])
            ->addColumn('serialized', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
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
        $this->dropTable('settings');

        $this->clearModelCache();
    }
}
