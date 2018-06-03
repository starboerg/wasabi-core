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
namespace Wasabi\Core\Form;

use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class ContactForm extends Form
{
    /**
     * The schema definition for the fields of this form.
     *
     * @param Schema $schema The schema to customize.
     * @return Schema
     */
    protected function _buildSchema(Schema $schema)
    {
        return $schema->addField('name', 'string')
            ->addField('email', 'string')
            ->addField('subject', 'string')
            ->addField('message', 'text');
    }

    /**
     * Validation rules for the submitted fields of this form.
     *
     * @param Validator $validator The validator to customize.
     * @return Validator
     * @throws \Aura\Intl\Exception
     */
    protected function _buildValidator(Validator $validator)
    {
        $validator->setProvider('googleRecaptcha', 'Wasabi\Core\Model\Validation\GoogleRecaptchaValidationProvider');

        return $validator
            ->notEmpty('name', __d('wasabi_core', 'Please enter your name.'))
            ->notEmpty('email', __d('wasabi_core', 'Please enter your email address.'))
            ->add('email', 'email', ['rule' => 'email', 'message' => __d('wasabi_core', 'Please enter a valid email address.')])
            ->notEmpty('subject', __d('wasabi_core', 'Please enter a subject for your contact request.'))
            ->notEmpty('message', __d('wasabi_core', 'Please provide a message for your contact request.'))
            ->notEmpty('g-recaptcha-response', __d('wasabi_core', 'Please confirm you are human.'))
            ->add('g-recaptcha-response', 'googleRecaptcha', [
                'rule' => 'googleRecaptcha',
                'message' => __d('wasabi_core', 'Please confirm you are human.'),
                'provider' => 'googleRecaptcha'
            ]);
    }

    /**
     * Execute the form's action.
     *
     * @param array $data The form data.
     * @return bool
     */
    protected function _execute(array $data)
    {
        EventManager::instance()->dispatch(new Event('Wasabi.Core.Contact.submit', $this, [$data]));
        return true;
    }
}
