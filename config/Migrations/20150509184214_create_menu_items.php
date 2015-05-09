<?php
use Phinx\Migration\AbstractMigration;

class CreateMenuItems extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $table = $this->table('menu_items');
        $table->addColumn('parent_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('menu_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('item', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('type', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('external_link', 'text', ['null' => true])
            ->addColumn('foreign_model', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('foreign_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => true])
            ->addColumn('plugin', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('controller', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('action', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('params', 'text', ['null' => true])
            ->addColumn('query', 'text', ['null' => true])
            ->addColumn('lft', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('rght', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00']);
        $table->addIndex('menu_id', ['name' => 'FK_MENU_ID', 'unique' => false]);
        $table->create();
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('menu_items')->drop();
    }
}
