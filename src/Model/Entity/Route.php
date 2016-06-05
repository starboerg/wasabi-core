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
namespace Wasabi\Core\Model\Entity;

use Cake\ORM\Entity;

/**
 * Class Route
 *
 * @property int id
 * @property string url
 * @property string model
 * @property int foreign_key
 * @property int language_id
 * @property string page_type
 * @property int redirect_to
 * @property int status_code
 * @property \DateTime created
 * @property \DateTime modified
 */
class Route extends Entity
{
}
