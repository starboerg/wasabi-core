<?php
use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;

class CreateMenuItems extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $table = $this->table('menu_items');
        $table
            ->addColumn('menu_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('parent_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => true, 'default' => null])
            ->addColumn('lft', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('rght', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('type', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('target', 'text', ['null' => true, 'default' => null])
            ->addColumn('external_link', 'text', ['null' => true, 'default' => null])
            ->addColumn('foreign_model', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('foreign_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => true, 'default' => null])
            ->addColumn('plugin', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('controller', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('action', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('params', 'text', ['null' => true, 'default' => null])
            ->addColumn('query', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('menu_id', ['name' => 'FK_MENU_ID', 'unique' => false]);
        $table->addIndex('parent_id', ['name' => 'FK_PARENT_ID', 'unique' => false]);
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
        $this->table('menu_items')->drop();
    }
}
