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

use Cake\ORM\Query;
use Cake\ORM\Table;

class UsersTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function initialize(array $config)
    {
        $this->belongsTo('Groups', [
            'className' => 'Wasabi/Core.Groups'
        ]);

        $this->addBehavior('CounterCache', ['Groups' => ['user_count']]);
        $this->addBehavior('Timestamp');
    }

    /**
     * Find all active users.
     *
     * @param Query $query
     * @return Query
     */
    public function findActive(Query $query)
    {
        $query->where([
            'Users.active' => true
        ]);
        return $query;
    }

    /**
     * Find users including their group name.
     *
     * @param Query $query
     * @return $this|array
     */
    public function findWithGroupName(Query $query) {
        return $query
            ->select([
                'id',
                'username',
                'email',
                'active'
            ])
            ->contain([
                'Groups' => function(Query $q) {
                    return $q->select(['name']);
                }
            ]);
    }
}
