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
use Wasabi\Core\Model\Entity\Language;

class CreateLanguages extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('languages');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('iso2', 'string', ['limit' => 2, 'null' => false])
            ->addColumn('iso3', 'string', ['limit' => 3, 'null' => false])
            ->addColumn('lang', 'string', ['limit' => 5, 'null' => false])
            ->addColumn('available_at_frontend', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('available_at_backend', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('in_progress', 'boolean', ['null' => false, 'default' => 1])
            ->addColumn('position', 'integer', ['null' => false, 'default' => 99999])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('name', ['name' => 'BY_NAME', 'unique' => false])
            ->addIndex('available_at_frontend', ['name' => 'BY_AVAILABLE_AT_FRONTEND', 'unique' => false])
            ->addIndex('available_at_backend', ['name' => 'BY_AVAILABLE_AT_BACKEND', 'unique' => false])
            ->addIndex('in_progress', ['name' => 'BY_IN_PROGRESS', 'unique' => false]);
        $table->create();

        $this->unsignedIntId($table);
        $table->save();

        $languages = [
            new Language([
                'name' => 'English',
                'iso2' => 'en',
                'iso3' => 'eng',
                'lang' => 'en_US',
                'available_at_frontend' => 1,
                'available_at_backend' => 1,
                'in_progress' => 0,
                'position' => 1
            ]),
            new Language([
                'name' => 'Deutsch',
                'iso2' => 'de',
                'iso3' => 'deu',
                'lang' => 'de_DE',
                'available_at_frontend' => 1,
                'available_at_backend' => 1,
                'in_progress' => 0,
                'position' => 2
            ])
        ];
        $Languages = TableRegistry::get('Wasabi/Core.Languages');
        $Languages->connection()->transactional(function () use ($Languages, $languages) {
            foreach ($languages as $language) {
                $Languages->save($language);
            }
        });
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down()
    {
        $this->dropTable('languages');

        $this->clearModelCache();
    }
}
