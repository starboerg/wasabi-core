<?php
use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;

class CreateSettings extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $table = $this->table('settings');
        $table->addColumn('scope', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('field', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('value', 'text', ['null' => true])
            ->addColumn('serialized', 'boolean', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00']);
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
        $this->table('settings')->drop();
    }
}
