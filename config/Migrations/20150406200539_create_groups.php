<?php
use Cake\ORM\TableRegistry;
use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;
use Wasabi\Core\Model\Entity\Group;

class CreateGroups extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $table = $this->table('groups');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('user_count', 'integer', ['limit' => 11, 'signed' => false, 'null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('name', ['name' => 'BY_NAME', 'unique' => true]);
        $table->create();

        $id = new Column();
        $id->setIdentity(true)
            ->setType('integer')
            ->setOptions(['limit' => 11, 'signed' => false, 'null' => false]);

        $table->changeColumn('id', $id)->save();

        $group = new Group([
            'name' => 'Super Admin'
        ]);
        $Groups = TableRegistry::get('Wasabi/Core.Groups');
        $Groups->save($group);
    }

    /**
     * Migrate down
     */
    public function down() {
        $this->table('groups')->drop();
    }
}
