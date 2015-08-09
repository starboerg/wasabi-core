<?php
use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;

class CreateRoutes extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $table = $this->table('routes');
        $table->addColumn('url', 'text', ['default' => null, 'null' => false])
            ->addColumn('language_id', 'integer', ['length' => 11, 'signed' => false, 'default' => null, 'null' => false])
            ->addColumn('model', 'string', ['length' => 255, 'default' => null, 'null' => false])
            ->addColumn('foreign_key', 'integer', ['length' => 11, 'signed' => false, 'default' => null, 'null' => false])
            ->addColumn('redirect_to', 'integer', ['length' => 11, 'signed' => false, 'default' => null, 'null' => true])
            ->addColumn('status_code', 'integer', ['length' => 11, 'signed' => false, 'default' => null, 'null' => true])
            ->addColumn('created', 'datetime', ['default' => null, 'null' => false])
            ->addColumn('modified', 'datetime', ['default' => null, 'null' => false]);
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
        $this->table('routes')->drop();
    }
}
