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
 * Class TokensTable
 * @property GroupsTable Groups
 * @package Wasabi\Core\Model\Table
 */
class TokensTable extends Table
{

    const TYPE_EMAIL_VERIFICATION = 'email_verification';
    const TYPE_LOST_PASSWORD = 'lost_password';

    /**
     * Holds the configuration for all token types
     * and tells how long a token is valid to be used.
     *
     * @var array
     */
    public $timeToLive = [
        self::TYPE_EMAIL_VERIFICATION => '+2 days',
        self::TYPE_LOST_PASSWORD => '+2 days'
    ];

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
