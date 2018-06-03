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

use Cake\Datasource\EntityInterface;
use Cake\ORM\Entity;
use Cake\Validation\Validator;
use Wasabi\Core\Model\Entity\CacheSetting;

/**
 * Class CacheSettingsTable
 *
 * @method getKeyValues(Entity $entity, array $keys) KeyValueBehavior::getKeyValues(Entity $entity, array $keys)
 * @method saveKeyValues(Entity $entity, array $keys) KeyValueBehavrio::saveKeyValues(Entity $entity, array $keys)
 */
class CacheSettingsTable extends SettingsTable
{
    /**
     * Holds all select options for cache_duration.
     *
     * @var array
     */
    public $cacheDurations = [];

    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor.
     * @return void
     * @throws \Aura\Intl\Exception
     */
    public function initialize(array $config)
    {
        $this->setTable('settings');

        $this->addBehavior('Wasabi/Core.KeyValue', [
            'scope' => 'Core'
        ]);

        $this->cacheDurations = [
            '1 hour' => __d('wasabi_core', '1 hour'),
            '2 hours' => __d('wasabi_core', '{0} hours', 2),
            '4 hours' => __d('wasabi_core', '{0} hours', 4),
            '8 hours' => __d('wasabi_core', '{0} hours', 8),
            '16 hours' => __d('wasabi_core', '{0} hours', 16),
            '1 day' => __d('wasabi_core', '1 day'),
            '2 days' => __d('wasabi_core', '{0} days', 2),
            '5 days' => __d('wasabi_core', '{0} days', 5),
            '7 days' => __d('wasabi_core', '{0} days', 7),
            '14 days' => __d('wasabi_core', '{0} days', 14),
            '30 days' => __d('wasabi_core', '{0} days', 30),
            '60 days' => __d('wasabi_core', '{0} days', 60),
            '90 days' => __d('wasabi_core', '{0} days', 90),
            '180 days' => __d('wasabi_core', '{0} days', 180),
            '365 days' => __d('wasabi_core', '{0} days', 365),
            '999 days' => __d('wasabi_core', '{0} days', 999)
        ];

        parent::initialize($config);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator The validator to customize.
     * @return Validator
     * @throws \Aura\Intl\Exception
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->requirePresence('enable_caching', true, __d('wasabi_core', 'Please select a cache status.'))
            ->add('enable_caching', 'inList', [
                'rule' => ['inList', ['0', '1']],
                'message' => __d('wasabi_core', 'Please select a valid cache status.')
            ])
            ->requirePresence('cache_duration', true, __d('wasabi_core', 'Please select a cache duration.'))
            ->add('cache_duration', 'custom', [
                'rule' => function ($value, $context) {
                    return in_array($value, $this->cacheDurations);
                },
                'message' => __d('wasabi_core', 'Please select a valid cache duration.')
            ]);
    }

    /**
     * {@inheritdoc}
     *
     * @return CacheSetting
     */
    public function newEntity($data = null, array $options = [])
    {
        return parent::newEntity($data, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return CacheSetting
     */
    public function patchEntity(EntityInterface $entity, array $data, array $options = [])
    {
        return parent::patchEntity($entity, $data, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @return array|CacheSetting
     */
    public function get($primaryKey, $options = [])
    {
        return parent::get($primaryKey, $options);
    }
}
