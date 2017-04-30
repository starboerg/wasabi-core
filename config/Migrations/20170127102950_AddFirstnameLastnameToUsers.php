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

class AddFirstnameLastnameToUsers extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('users');

        if (!$table->hasColumn('firstname')) {
            $table->addColumn('firstname', 'string', ['limit' => 255, 'null' => true, 'default' => null, 'after' => 'language_id']);
        }

        if (!$table->hasColumn('lastname')) {
            $table->addColumn('lastname', 'string', ['limit' => 255, 'null' => true, 'default' => null, 'after' => 'firstname']);
        }

        $table->update();

        $this->clearModelCache();
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down()
    {
        $table = $this->table('users');

        if ($table->hasColumn('firstname')) {
            $table->removeColumn('firstname');
        }

        if ($table->hasColumn('lastname')) {
            $table->removeColumn('lastname');
        }

        $table->update();

        $this->clearModelCache();
    }
}
