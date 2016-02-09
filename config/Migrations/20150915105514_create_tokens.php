<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Table\Column;

class CreateTokens extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $table = $this->table('tokens');
        $table->addColumn('user_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('token', 'string', ['limit' => 32, 'null' => false])
            ->addColumn('token_type', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('used', 'boolean', ['default' => 0, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('expires', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('user_id', ['name' => 'FK_USER_ID', 'unique' => false])
            ->addIndex('token', ['name' => 'BY_TOKEN', 'unique' => true]);
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
        $this->table('tokens')->drop();
    }
}
