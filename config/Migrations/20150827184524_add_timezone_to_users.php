<?php
use Phinx\Migration\AbstractMigration;

class AddTimezoneToUsers extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users');
        $table->addColumn('timezone', 'string', ['limit' => 255, 'null' => false, 'default' => 'Europe/Berlin', 'after' => 'email']);
        $table->update();
    }

    public function down()
    {
        $table = $this->table('users');
        $table->removeColumn('timezone');
        $table->update();
    }
}
