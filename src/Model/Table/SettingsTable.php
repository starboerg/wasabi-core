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
namespace Wasabi\Core\Model\Table;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Wasabi\Core\Model\Entity\Setting;

/**
 * Class SettingsTable
 *
 * @method Entity getAllKeyValues() KeyValueBehavior::getAllKeyValues()
 * @method bool|mixed saveKeyValues(Entity $settings, array $keys) KeyValueBehavior::saveKeyValues()
 */
class SettingsTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->addBehavior('Wasabi/Core.KeyValue');
        $this->addBehavior('Timestamp');
    }

    /**
     * Called after an entity is saved.
     *
     * @param Event $event An event instance.
     * @param Setting $entity The entity that triggered the event.
     * @param ArrayObject $options Additional options passed to the save call.
     * @return void
     */
    public function afterSave(Event $event, Setting $entity, ArrayObject $options)
    {
        Cache::delete('settings', 'wasabi/core/longterm');
        $this->eventManager()->dispatch(new Event('Wasabi.Settings.changed'));
    }
}
