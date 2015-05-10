<?php
use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;

class CreateGroupPermissions extends AbstractMigration
{
    /**
     * Migration up
     */
    public function up()
    {
        $table = $this->table('group_permissions');
        $table->addColumn('group_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('path', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('plugin', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('controller', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('action', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('allowed', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00']);
        $table->addIndex('group_id', ['name' => 'FK_GROUP_ID', 'unique' => false]);
        $table->create();

        $id = new Column();
        $id->setIdentity(true)
            ->setType('integer')
            ->setOptions(['limit' => 11, 'signed' => false, 'null' => false]);

        $table->changeColumn('id', $id)->save();
    }

    /**
     * Migration down
     */
    public function down()
    {
        $this->table('group_permissions')->drop();
    }
}
