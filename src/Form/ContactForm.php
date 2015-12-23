<?php

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
     * @param Schema $schema
     * @return $this
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
     * @param Validator $validator
     * @return $this
     */
    protected function _buildValidator(Validator $validator)
    {
        $validator->provider('googleRecaptcha', 'Wasabi\Core\Model\Validation\GoogleRecaptchaValidationProvider');

        return $validator
            ->notEmpty('name', __d('wasabi', 'Please enter your name.'))
            ->notEmpty('email', __d('wasabi', 'Please enter your email address.'))
            ->add('email', 'email', ['rule' => 'email', 'message' => __d('wasabi', 'Please enter a valid email address.')])
            ->notEmpty('subject', __d('wasabi', 'Please enter a subject for your contact request.'))
            ->notEmpty('message', __d('wasabi', 'Please provide a message for your contact request.'))
            ->notEmpty('g-recaptcha-response', __d('wasabi', 'Please confirm you are human.'))
            ->add('g-recaptcha-response', 'googleRecaptcha', [
                'rule' => 'googleRecaptcha',
                'message' => __d('wasabi', 'Please confirm you are human.'),
                'provider' => 'googleRecaptcha'
            ]);
    }

    /**
     * Execute the form's action.
     *
     * @param array $data
     * @return bool
     */
    protected function _execute(array $data)
    {
        EventManager::instance()->dispatch(new Event('Wasabi.Core.Contact.submit', $this, [$data]));
        return true;
    }
}