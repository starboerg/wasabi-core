<?php
use Cake\ORM\TableRegistry;
use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;
use Wasabi\Core\Model\Entity\Menu;

class CreateMenus extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $table = $this->table('menus');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('menu_item_count', 'integer', ['default' => 0, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00']);
        $table->addIndex('name', ['name' => 'BY_NAME', 'unique' => false]);
        $table->create();

        $id = new Column();
        $id->setIdentity(true)
            ->setType('integer')
            ->setOptions(['limit' => 11, 'signed' => false, 'null' => false]);

        $table->changeColumn('id', $id)->save();

        $menus = [
            new Menu([
                'name' => 'Main Menu'
            ]),
            new Menu([
                'name' => 'Footer Menu'
            ])
        ];

        $Menus = TableRegistry::get('Wasabi/Core.Menus');
        $Menus->connection()->transactional(function () use ($Menus, $menus) {
            foreach ($menus as $menu) {
                $Menus->save($menu);
            }
        });
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('menus')->drop();
    }
}
