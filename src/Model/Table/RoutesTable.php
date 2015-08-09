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

use ArrayObject;
//use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\ORM\Table;
use Wasabi\Core\Model\Entity\Route;

/**
 * Class RoutesTable
 */
class RoutesTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    /**
     * Called after an entity is saved.
     *
     * @param Event $event
     * @param Route $entity
     * @param ArrayObject $options
     */
    public function afterSave(Event $event, Route $entity, ArrayObject $options)
    {
//        Cache::delete('settings', 'wasabi/core/longterm');
//        $this->eventManager()->dispatch(new Event('Wasabi.Settings.changed'));
    }
}
