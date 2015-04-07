<?php
use Cake\ORM\TableRegistry;
use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;
use Wasabi\Core\Model\Entity\User;

class CreateUsers extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $table = $this->table('users');
        $table->addColumn('group_id', 'integer', ['limit' => 11, 'signed' => false, 'null' => false])
            ->addColumn('username', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 60, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('verified', 'boolean', ['default' => 0, 'null' => false])
            ->addColumn('active', 'boolean', ['default' => 0, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00']);
        $table->addIndex('username', ['name' => 'BY_USERNAME', 'unique' => false])
            ->addIndex('group_id', ['name' => 'FK_GROUP_ID', 'unique' => false])
            ->addIndex('email', ['name' => 'BY_EMAIL', 'unique' => true])
            ->addIndex('active', ['name' => 'BY_ACTIVE', 'unique' => false]);
        $table->create();

        $id = new Column();
        $id->setIdentity(true)
            ->setType('integer')
            ->setOptions(['limit' => 11, 'signed' => false, 'null' => false]);

        $table->changeColumn('id', $id)->save();

        $user = new User([
            'username' => 'admin',
            'group_id' => 1,
            'email' => 'example@example.com',
            'password' => 'admin',
            'verified' => 1,
            'active' => 1,
        ]);
        $Users = TableRegistry::get('Wasabi/Core.Users');
        $Users->save($user);
    }

    /**
     * Migrate down
     */
    public function down() {
        $this->table('users')->drop();
    }
}
