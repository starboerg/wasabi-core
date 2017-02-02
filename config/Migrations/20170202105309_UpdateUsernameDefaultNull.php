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

use Wasabi\Core\BaseMigration;

class UpdateUsernameDefaultNull extends BaseMigration
{
    /**
     * Migrate up
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('users');
        $table->changeColumn('username', 'string', ['null' => true, 'default' => null]);
        $table->update();
    }

    /**
     * Migrate down
     *
     * @return void
     */
    public function down()
    {
        $table = $this->table('users');
        $table->changeColumn('username', 'string', ['null' => false]);
        $table->update();
    }
}
