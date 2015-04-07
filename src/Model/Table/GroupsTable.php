<?php
/**
 * Wasabi CMS
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\Model\Table;

use Cake\ORM\Table;

class GroupsTable extends Table
{
    public function initialize(array $config)
    {
        $this->hasMany('Users', [
            'className' => 'Wasabi/Core.Users'
        ]);
        $this->hasMany('GroupPermissions', [
            'className' => 'Wasabi/Core.GroupPermissions'
        ]);

        $this->addBehavior('Timestamp');
    }
}
