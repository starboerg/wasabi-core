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
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Wasabi\Core\Config;
use Wasabi\Core\Model\Entity\User;

class UserMailer extends Mailer
{
    /**
     * Send a "verify" email to the user, so that he can verify his email address.
     *
     * @param User $user
     * @param string $token
     */
    public function verifyEmail(User $user, $token)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Verify your email address'));
        $this->_email->template('Wasabi/Core.User/verify');
        $this->_email->viewVars([
            'user' => $user,
            'verifyEmailLink' => [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Users',
                'action' => 'verifyByToken',
                'token' => $token
            ],
            'instanceName' => Config::getInstanceName()
        ]);
    }

    /**
     * Send a "verify" email to the user that contains a link to verify his email address and setup his password.
     * This mail is sent, whenever an Admin creates a new user via the backend.
     *
     * @param User $user
     * @param string $token
     */
    public function verifyAndResetPasswordEmail(User $user, $token)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Verify your email address'));
        $this->_email->template('Wasabi/Core.User/verify');
        $this->_email->viewVars([
            'user' => $user,
            'verifyEmailLink' => [
                'plugin' => null,
                'controller' => 'Backend/Users',
                'action' => 'verifyByTokenResetPassword',
                'token' => $token
            ],
            'instanceName' => Config::getInstanceName()
        ]);
    }

    /**
     * Send a "verified" email to the user, when his email address has been verified.
     *
     * @param User $user
     */
    public function verifiedEmail(User $user)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Email address verified'));
        $this->_email->template('Wasabi/Core.User/verfied');
        $this->_email->viewVars([
            'user' => $user,
            'instanceName' => Config::getInstanceName()
        ]);
    }

    /**
     * Send a verification email, when an admin has marked a user’s email as verified.
     *
     * @param User $user
     */
    public function verifiedByAdminEmail(User $user)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Email address verified'));
        $this->_email->template('Wasabi/Core.User/verified-by-admin');
        $this->_email->viewVars([
            'user' => $user,
            'instanceName' => Config::getInstanceName()
        ]);
    }

    /**
     * Send an "activated" email to the user when an admin activated a user account.
     *
     * @param User $user
     */
    public function activatedEmail(User $user)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Your account has been activated'));
        $this->_email->template('Wasabi/Core.User/activated');
        $this->_email->viewVars([
            'user' => $user,
            'instanceName' => Config::getInstanceName()
        ]);
    }

    /**
     * Send a "deactivated" email to the user when an admin deactivated a user account.
     *
     * @param User $user
     */
    public function deactivatedEmail(User $user)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Your account has been deactivated'));
        $this->_email->template('Wasabi/Core.User/deactivated');
        $this->_email->viewVars([
            'user' => $user,
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
            'user' => $user,
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
        $this->_email->from(Config::getSenderEmail(), Config::getSenderName());
        $this->_email->to($user->email, $user->username);
        $this->_email->subject($subject);
        $this->_email->helpers([
            'Email' => [
                'className' => 'Wasabi/Core.Email'
            ]
        ]);
    }

    /**
     * Wrap the original send to catch erros and log them.
     *
     * @param string $action The name of the mailer action to trigger.
     * @param array $args Arguments to pass to the triggered mailer action.
     * @param array $headers Headers to set.
     * @return array
     * @throws \Cake\Mailer\Exception\MissingActionException
     * @throws \BadMethodCallException
     */
    public function send($action, $args = [], $headers = [])
    {
        $results = [];

        try {
            $results = @parent::send($action, $args, $headers);
        } catch(\Exception $e) {
            Log::write(LOG_CRIT, 'Emails cannot be sent: ' . $e->getMessage(), $this->_email);
        }

        return $results;
    }
}
