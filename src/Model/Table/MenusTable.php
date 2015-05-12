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
            'className' => 'Wasabi/Core.MenuItems'
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

    public function findAsList()
    {
        return $this->find('all', [
                'keyValue' => 'id',
                'fieldValue' => 'name',
            ])
            ->order(['name' => 'ASC']);
    }
}
