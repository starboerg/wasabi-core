<?php
use Phinx\Migration\AbstractMigration;

class AddActivatedAtToUsers extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users');
        $table->addColumn('activated_at', 'datetime', ['null' => true, 'default' => null, 'after' => 'active']);
        $table->update();
    }

    public function down()
    {
        $table = $this->table('users');
        $table->removeColumn('activated_at');
        $table->update();
    }
}
