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

use Cake\ORM\Entity;
use DateTime;

/**
 * Class LoginLog
 *
 * @property integer $id
 * @property string $login_field
 * @property string $login_field_value
 * @property string $client_ip
 * @property bool $success
 * @property bool $blocked
 * @property DateTime $created
 */
class LoginLog extends Entity
{
}
