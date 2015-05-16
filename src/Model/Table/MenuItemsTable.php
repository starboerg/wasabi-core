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

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Class MenuItemsTable
 * @package Wasabi\Core\Model\Table
 */
class MenuItemsTable extends Table
{
    /**
     * The maximum nesting level of menu items.
     */
    const MAXIMUM_NESTING_LEVEL = 2;

    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function initialize(array $config)
    {
        $this->belongsTo('Menus', [
            'className' => 'Wasabi/Core.Menus',
            'counterCache' => true
        ]);

        $this->addBehavior('CounterCache', ['Menus' => ['menu_item_count']]);
        $this->addBehavior('Tree');
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
        return $validator->notEmpty('name', __d('wasabi_core', 'Please enter a name for the menu item.'));
    }

    /**
     * Set the scope of the menu item to a specific $menuId.
     *
     * @param string $menuId
     */
    public function setScope($menuId)
    {
        $this->behaviors()->Tree->config('scope', [
            'menu_id' => $menuId
        ]);
    }
}
