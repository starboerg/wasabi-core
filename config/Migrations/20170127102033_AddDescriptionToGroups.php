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

class AddDescriptionToGroups extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('groups');

        if ($table->hasColumn('description')) {
            return;
        }

        $table->addColumn('description', 'text', ['null' => true, 'after' => 'name']);
        $table->update();

        $this->clearModelCache();

        $Groups = TableRegistry::get('Groups');
        /** @var Group $group */
        $group = $Groups->get(1);
        $group->set('description', '-');
        $Groups->save($group);

        $table->changeColumn('description', 'text', ['null' => false]);
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
        $table = $this->table('groups');

        if (!$table->hasColumn('description')) {
            return;
        }

        $table->removeColumn('description');
        $table->update();

        $this->clearModelCache();
    }
}
