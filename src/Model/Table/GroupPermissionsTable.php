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

use Cake\Cache\Cache;
use Cake\ORM\Table;

class GroupPermissionsTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Groups', [
            'className' => 'Wasabi/Core.Groups'
        ]);

        $this->addBehavior('Timestamp');
    }

    public function findAllForGroup($groupId)
    {
        if (!$groupId) {
            return [];
        }

        $permissions = Cache::remember($groupId, function() use ($groupId) {
            return $this
                ->find('all')
                ->select(['path'])
                ->where([
                    'group_id' => $groupId,
                    'allowed' => true
                ])
                ->hydrate(false);
        }, 'wasabi/core/group_permissions');

        return $permissions;
    }
}
