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
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\Validation\Validator;
use Wasabi\Core\Model\Entity\GeneralSetting;
use Wasabi\Core\Model\Entity\Setting;

/**
 * Class GeneralSettingsTable
 *
 * @method getKeyValues(Entity $entity, array $fields) KeyValueBehavior::getKeyValues(Entity $entity, array $fields)
 * @method saveKeyValues(Entity $entity, array $fields) KeyValueBehavior::saveKeyValues(Entity $entity, array $fields)
 */
class GeneralSettingsTable extends SettingsTable
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('settings');

        $this->addBehavior('Wasabi/Core.KeyValue', [
            'scope' => 'Core'
        ]);

        parent::initialize($config);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator The validator to customize.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty(
                'instance_name',
                __d('wasabi_core', 'Please enter a name for your app.')
            )
            ->notEmpty(
                'instance_short_name',
                __d('wasabi_core', 'Please enter a short name for your app.')
            )
            ->notEmpty(
                'Email__email_sender',
                __d('wasabi_core', 'Please enter an email address.')
            )
            ->notEmpty(
                'Email__email_sender_name',
                __d('wasabi_core', 'Please enter a name for the email sender.')
            )
            ->add('Email__email_sender', 'validEmail', [
                'rule' => 'email',
                'message' => __d('wasabi_core', 'Please enter a valid email address.')
            ])
            ->notEmpty(
                'Login__HeartBeat__max_login_time',
                __d('wasabi_core', 'Please choose a maximum login time.')
            )
            ->add('Login__HeartBeat__max_login_time', 'valid', [
                'rule' => function ($value, $context) {
                    return in_array((int)$value, [900000, 1800000, 2700000, 3600000, 6400000]);
                },
                'message' => __d('wasabi_core', 'Please choose a valid maximum login time.')
            ])
            ->numeric(
                'Auth__max_login_attempts',
                __d('wasabi_core', 'Please enter a number above 0.')
            )
            ->greaterThan(
                'Auth__max_login_attempts',
                0,
                __d('wasabi_core', 'Please enter a number above 0.')
            )
            ->notEmpty(
                'User__has_username',
                __d('wasabi_core', 'Please choose if users have a username.')
            )
            ->notEmpty(
                'User__has_firstname_lastname',
                __d('wasabi_core', 'Please choose if users have a firstname and lastname.')
            )
            ->notEmpty(
                'User__allow_timezone_change',
                __d('wasabi_core', 'Please choose if users may change his timezone.')
            )
            ->notEmpty(
                'User__belongs_to_many_groups',
                __d('wasabi_core', 'Please choose if users may belong to multiple groups.')
            );
    }

    /**
     * Called before an entity is saved.
     * Strips unallowed tags from 'Login__Message__text'
     *
     * @param Event $event An event instance.
     * @param Setting $setting The entity the event is triggered on.
     * @param ArrayObject $options Options passed to the save call.
     * @return void
     */
    public function beforeSave(Event $event, Setting $setting, ArrayObject $options)
    {
        if ($setting->key === 'Login__Message__text') {
            $allowed = '<b><strong><a><br>';
            $setting->value = strip_tags($setting->value, $allowed);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return GeneralSetting
     */
    public function newEntity($data = null, array $options = [])
    {
        return parent::newEntity($data, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return GeneralSetting
     */
    public function patchEntity(EntityInterface $entity, array $data, array $options = [])
    {
        return parent::patchEntity($entity, $data, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @return array|GeneralSetting
     */
    public function get($primaryKey, $options = [])
    {
        return parent::get($primaryKey, $options);
    }
}
