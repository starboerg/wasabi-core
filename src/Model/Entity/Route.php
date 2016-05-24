<?php

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
