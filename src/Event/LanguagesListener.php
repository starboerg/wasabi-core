<?php
/**
 * Wasabi Core Languages Event Listener
 *
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
namespace Wasabi\Core\Event;

use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Entity;

class LanguagesListener implements EventListenerInterface
{
    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'LanguagesTable.afterSave' => [
                'callable' => 'deleteLanguageCache',
                'priority' => 10
            ],
            'LanguagesTable.afterDelete' => [
                'callable' => 'deleteLanguageCache',
                'priority' => 10
            ]
        ];
    }

    /**
     * Delete the language cache.
     *
     * @param Event $event
     * @param Entity $entity
     */
    public function deleteLanguageCache(Event $event, Entity $entity)
    {
        Cache::delete('languages', 'wasabi/core/longterm');
    }
}
