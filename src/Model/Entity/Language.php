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
 * Class Language
 *
 * @property integer $id
 * @property string $name
 * @property string $iso2
 * @property string $iso3
 * @property string $lang
 * @property bool $available_at_backend
 * @property bool $available_at_frontend
 */
class Language extends Entity
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
