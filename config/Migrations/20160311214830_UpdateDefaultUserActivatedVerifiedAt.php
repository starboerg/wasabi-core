<?php
/**
 * Wasabi Core
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @link          https://github.com/wasabi-cms/core Wasabi Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\ORM\TableRegistry;
use Wasabi\Core\BaseMigration;

class UpdateDefaultUserActivatedVerifiedAt extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $date = date('Y-m-d H:i:s');
        $Users = TableRegistry::get('Wasabi/Core.Users');
        $Users->updateAll([
            'verified_at' => $date,
            'activated_at' => $date
        ], ['id' => 1]);
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down()
    {
        $Users = TableRegistry::get('Wasabi/Core.Users');
        $Users->updateAll([
            'verified_at' => null,
            'activated_at' => null
        ], ['id' => 1]);
    }
}
