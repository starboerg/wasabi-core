<?php
use Cake\Cache\Cache;
use Migrations\AbstractMigration;
use Cake\ORM\TableRegistry;

class UpdateDefaultUserActivatedVerifiedAt extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        Cache::clear();

        $date = date('Y-m-d H:i:s');
        $Users = TableRegistry::get('Wasabi/Core.Users');
        $Users->updateAll([
            'verified_at' => $date,
            'activated_at' => $date
        ], ['id' => 1]);
    }

    /**
     * Migrate down
     */
    public function down()
    {
        Cache::clear();

        $Users = TableRegistry::get('Wasabi/Core.Users');
        $Users->updateAll([
            'verified_at' => null,
            'activated_at' => null
        ], ['id' => 1]);
    }
}
