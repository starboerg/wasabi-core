<?php

namespace Wasabi\Core\Model\Entity;

use Cake\I18n\Time;
use Cake\ORM\Entity;
use DateTime;

/**
 * Class Token
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 * @property string $token_type
 * @property bool $used
 * @property DateTime $created
 * @property Datetime $expires
 *
 * @property User $user
 *
 * @package Wasabi\Core\Model\Entity
 */
class Token extends Entity
{
    /**
     * Accessible fields.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];

    /**
     * Check if a token expired.
     *
     * @return bool True if the token has expired, false otherwise.
     */
    public function hasExpired()
    {
        return (new Time($this->expires))->isPast();
    }
}
