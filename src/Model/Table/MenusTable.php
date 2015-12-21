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

use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Class MenusTable
 * @property \Wasabi\Core\Model\Table\MenuItemsTable MenuItems
 * @package Wasabi\Core\Model\Table
 */
class MenusTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function initialize(array $config)
    {
        $this->hasMany('MenuItems', [
            'className' => 'Wasabi/Core.MenuItems',
            'dependent' => true
        ]);

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->notEmpty('name', __d('wasabi_core', 'Please enter a name for the menu.'));
        return $validator;
    }

    /**
     * Find all menu items as list.
     *
     * @return $this
     */
    public function findAsList()
    {
        return $this->find('all', [
                'keyValue' => 'id',
                'fieldValue' => 'name',
            ])
            ->order(['name' => 'ASC']);
    }

    /**
     * Get available link types via an Event trigger
     * This fetches avilable Links from all activated Plugins.
     *
     * @return array
     */
    public function getLinkTypes()
    {
        $event = new Event('Wasabi.Backend.MenuItems.getLinkTypes', $this);
        EventManager::instance()->dispatch($event);

        return $event->result;
    }
}
