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
use Wasabi\Core\BaseMigration;

class RemoveGroupIdFromUsers extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('users');

        if (!$table->hasColumn('group_id')) {
            return;
        }

        $table->removeColumn('group_id');
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

        if ($table->hasColumn('group_id')) {
            return;
        }

        $table->addColumn('group_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => true, 'default' => 0, 'after' => 'id']);
        $table->update();

        $this->clearModelCache();

        $Users = TableRegistry::get('Users');
        $Users->save($Users->get(1)->set('group_id', 1));

        $table->changeColumn('group_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false]);
        $table->save();

        $this->clearModelCache();
    }
}
