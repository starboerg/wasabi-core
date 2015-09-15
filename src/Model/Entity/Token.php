<?php

namespace Wasabi\Core\Model\Entity;

use Cake\ORM\Entity;
use DateTime;

/**
 * Class Token
 * @package Wasabi\Core\Model\Entity
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
}
