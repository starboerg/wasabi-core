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
     * Check if a token expired.
     *
     * @return bool True if the token has expired, false otherwise.
     */
    public function hasExpired()
    {
        return (new Time($this->expires->getTimestamp()))->isPast();
    }
}
