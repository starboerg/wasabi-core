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
use Cake\ORM\Entity;
use Cake\Validation\Validator;
use ArrayObject;
use Cake\Event\Event;
use Wasabi\Core\Model\Entity\Setting;

/**
 * Class GeneralSettingsTable
 *
 * @method getKeyValues(Entity $entity, array $fields) KeyValueBehavior::getKeyValues(Entity $entity, array $fields)
 * @method saveKeyValues(Entity $entity, array $fields) KeyValueBehavrio::saveKeyValues(Entity $entity, array $fields)
 */
class GeneralSettingsTable extends SettingsTable
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
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
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty(
                'instance_name',
                __d('wasabi_core', 'Please enter a name for your CMS instance.')
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
            ]);
    }

    /**
     * Called before an entity is saved.
     * Strips unallowed tags from 'Login__Message__text'
     *
     * @param Event $event
     * @param Setting $setting
     * @param ArrayObject $options
     */
    public function beforeSave(Event $event, Setting $setting, ArrayObject $options)
    {
        if ($setting->key === 'Login__Message__text') {
            $allowed = '<b><strong><a><br>';
            $setting->value = strip_tags($setting->value, $allowed);
        }
    }
}
