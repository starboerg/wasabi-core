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

/**
 * Class GroupPermission
 *
 * @property int $id
 * @property int $group_id
 * @property string $path
 * @property string $plugin
 * @property string $controller
 * @property string $action
 * @property bool|int $allowed
 * @property \DateTime $created
 * @property \DateTime $modified
 */
class GroupPermission extends Entity
{
}
