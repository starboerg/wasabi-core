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

class CreateRoutes extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('routes');
        $table->addColumn('url', 'text', ['default' => null, 'null' => false])
            ->addColumn('model', 'string', ['length' => 255, 'default' => null, 'null' => false])
            ->addColumn('foreign_key', 'integer', ['length' => 11, 'signed' => false, 'default' => null, 'null' => false])
            ->addColumn('language_id', 'integer', ['length' => 11, 'signed' => false, 'default' => null, 'null' => false])
            ->addColumn('page_type', 'string', ['length' => 255, 'default' => 'simple', 'null' => false])
            ->addColumn('redirect_to', 'integer', ['length' => 11, 'signed' => false, 'default' => null, 'null' => true])
            ->addColumn('status_code', 'integer', ['length' => 11, 'signed' => false, 'default' => null, 'null' => true])
            ->addColumn('created', 'datetime', ['default' => null, 'null' => false])
            ->addColumn('modified', 'datetime', ['default' => null, 'null' => false]);
        $table->addIndex(['model', 'foreign_key', 'language_id'], ['name' => 'BY_MODEL_FOREIGN_KEY_LANG_ID', 'unique' => false])
            ->addIndex('redirect_to', ['name' => 'BY_REDIRECT_TO', 'unique' => false]);
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
        $this->dropTable('routes');

        $this->clearModelCache();
    }
}
