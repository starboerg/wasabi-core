<?php
/**
 * Wasabi Core Menu Event Listener
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
namespace Wasabi\Core\Mailer;

use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Wasabi\Core\Config;
use Wasabi\Core\Model\Entity\User;

class UserMailer extends Mailer
{
    /**
     * Send a verification email, when an admin has marked a user’s email as verified.
     *
     * @param User $user
     */
    public function verifiedByAdminEmail(User $user)
    {
        $this->_prepareEmail($user, Config::getVerifiedByAdminEmailSubject());
        $this->_email->template('Wasabi/Core.User/verified-by-admin');
        $this->_email->viewVars([
            'username' => $user->username,
            'instanceName' => Config::getInstanceName()
        ]);
    }

    /**
     * Send an activation email when an admin activated a user account.
     *
     * @param User $user
     */
    public function activationEmail(User $user)
    {
        $this->_prepareEmail($user, Config::getActivationEmailSubject());
        $this->_email->template('Wasabi/Core.User/activated');
        $this->_email->viewVars([
            'username' => $user->username,
            'instanceName' => Config::getInstanceName()
        ]);
    }

    /**
     * Send a lost password email, when a user has requested a new one.
     *
     * @param User $user
     * @param $token
     */
    public function lostPasswordEmail(User $user, $token)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Password Reset'));
        $this->_email->template('Wasabi/Core.User/lost-password');
        $this->_email->viewVars([
            'username' => $user->username,
            'resetPasswordLink' => [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Users',
                'action' => 'resetPassword',
                'token' => $token
            ],
            'instanceName' => Config::getInstanceName()
        ]);
    }

    /**
     * Prepare the UserMailer Email instance.
     *
     * @param User $user
     * @param $subject
     */
    protected function _prepareEmail(User $user, $subject)
    {
        $this->layout('Wasabi/Core.responsive');
        $this->_email->transport('default');
        $this->_email->emailFormat('both');
        $this->_email->from(Config::getEmailSender(), Config::getInstanceName());
        $this->_email->to($user->email, $user->username);
        $this->_email->subject($subject);
        $this->_email->helpers([
            'Email' => [
                'className' => 'Wasabi/Core.Email'
            ]
        ]);
    }
}
