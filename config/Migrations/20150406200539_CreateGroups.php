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
use Wasabi\Core\Model\Entity\Group;

class CreateGroups extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('groups');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('user_count', 'integer', ['limit' => 11, 'signed' => false, 'null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('name', ['name' => 'BY_NAME', 'unique' => true]);
        $table->create();

        $this->unsignedIntId($table);
        $table->save();

        $group = new Group([
            'name' => 'Super Admin'
        ]);
        $Groups = TableRegistry::get('Wasabi/Core.Groups');
        $Groups->save($group);
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down()
    {
        $this->dropTable('groups');

        $this->clearModelCache();
    }
}
