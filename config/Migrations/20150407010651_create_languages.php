<?php
use Cake\ORM\TableRegistry;
use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;
use Wasabi\Core\Model\Entity\Language;

class CreateLanguages extends AbstractMigration
{
    /**
     * Migrate up
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

        $id = new Column();
        $id->setIdentity(true)
            ->setType('integer')
            ->setOptions(['limit' => 11, 'signed' => false, 'null' => false]);

        $table->changeColumn('id', $id)->save();

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
     */
    public function down() {
        $this->table('languages')->drop();
    }
}
