<?php
use Migrations\AbstractMigration;

class CreateFilters extends AbstractMigration
{
    /**
     * Migrate up.
     *
     * @return void
     */
    public function up()
    {
        $this->table('filters')
            ->addColumn('plugin', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('controller', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('action', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 14, 'null' => false])
            ->addColumn('filter_data', 'text', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addIndex('slug', ['name' => 'BY_SLUG', 'unique' => true])
            ->create();
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down() {
        $this->table('filters')->drop();
    }
}
