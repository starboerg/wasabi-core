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

/**
 * Class MediaTable
 * @package Wasabi\Core\Model\Table
 * @property UsersTable $Users
 */
class MediaTable extends Table
{

    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function initialize(array $config)
    {
        $this->belongsTo('Users', [
            'className' => 'Wasabi/Core.Users'
        ]);
        $this->addBehavior('Timestamp');
    }
}
