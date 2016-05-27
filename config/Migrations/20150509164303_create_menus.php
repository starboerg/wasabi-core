<?php
use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;

class CreateMenus extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
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
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('menus')->drop();
    }
}
