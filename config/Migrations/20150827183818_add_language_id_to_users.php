<?php
use Phinx\Migration\AbstractMigration;

class AddLanguageIdToUsers extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users');
        $table->addColumn('language_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false, 'default' => 1, 'after' => 'group_id']);
        $table->update();
    }

    public function down()
    {
        $table = $this->table('users');
        $table->removeColumn('language_id');
        $table->update();
    }
}
