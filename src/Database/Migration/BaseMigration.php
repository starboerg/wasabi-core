<?php

namespace Wasabi\Core\Database\Migration;

use Cake\Cache\Cache;
use Migrations\AbstractMigration;
use Phinx\Db\Table;
use Phinx\Db\Table\Column;

abstract class BaseMigration extends AbstractMigration
{
    /**
     * Initialize the migration.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->clearModelCache();
    }

    /**
     * Make the id primary column an unsigned integer.
     *
     * @param Table $table
     * @return void
     */
    public function unsignedIntId(Table $table)
    {
        $id = new Column();
        $id->setIdentity(true)
            ->setType('integer')
            ->setOptions(['limit' => 11, 'signed' => false, 'null' => false]);

        $table->changeColumn('id', $id);
    }

    /**
     * Clear the cake model cache.
     */
    public function clearModelCache()
    {
        Cache::clear(false, '_cake_model_');
    }
}
