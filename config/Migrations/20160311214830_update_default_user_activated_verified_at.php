<?php
use Migrations\AbstractMigration;
use Cake\ORM\TableRegistry;

class UpdateDefaultUserActivatedVerifiedAt extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $date = date('Y-m-d H:i:s');
        $Users = TableRegistry::get('Users');
        $Users->save($Users->get(1)
            ->set('verified_at', $date)
            ->set('activated_at', $date)
        );
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $Users = TableRegistry::get('Users');
        $Users->save($Users->get(1)
            ->set('verified_at', null)
            ->set('activated_at', null)
        );
    }
}
