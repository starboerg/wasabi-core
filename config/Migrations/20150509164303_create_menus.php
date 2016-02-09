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
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addIndex('name', ['name' => 'BY_NAME', 'unique' => false]);
        $table->create();

        $id = new Column();
        $id->setIdentity(true)
            ->setType('integer')
            ->setOptions(['limit' => 11, 'signed' => false, 'null' => false]);
        $table->changeColumn('id', $id)->save();

        $menus = [
            new Menu([
                'name' => 'Main'
            ]),
            new Menu([
                'name' => 'Footer'
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
