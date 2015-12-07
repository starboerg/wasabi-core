<?php

namespace Wasabi\Core\Model\Entity;

use Cake\ORM\Entity;

/**
 * Class Route
 *
 * @property int id
 * @property string url
 * @property string table
 * @property int foreign_id
 * @property int language_id
 * @property int redirect_to
 * @property int status_code
 * @property \DateTime created
 * @property \DateTime modified
 */
class Route extends Entity
{

}
