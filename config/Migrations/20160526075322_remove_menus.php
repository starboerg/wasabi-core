<?php
use Cake\Cache\Cache;
use Migrations\AbstractMigration;
use Phinx\Db\Table\Column;

class RemoveMenus extends AbstractMigration
{
    /**
     * Initialize
     */
    public function init()
    {
        parent::init();

        Cache::clear();
    }

    /**
     * Migrate up
     */
    public function up()
    {
        $this->dropTable('menus');

        Cache::clear();
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $table = $this->table('menus');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('menu_item_count', 'integer', ['default' => 0, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('name', ['name' => 'BY_NAME', 'unique' => false]);
        $table->create();

        $id = new Column();
        $id->setIdentity(true)
            ->setType('integer')
            ->setOptions(['limit' => 11, 'signed' => false, 'null' => false]);
        $table->changeColumn('id', $id)->save();

        Cache::clear();
    }
}
