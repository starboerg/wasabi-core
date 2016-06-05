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
namespace Wasabi\Core\Mailer;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Wasabi\Core\Model\Entity\User;
use Wasabi\Core\Wasabi;

class UserMailer extends Mailer
{
    /**
     * Send a "verify" email to the user, so that he can verify his email address.
     *
     * @param User $user The user who should verify his email address.
     * @param string $token The verification token.
     * @return void
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
            'instanceName' => Wasabi::getInstanceName()
        ]);
    }

    /**
     * Send a "verify" email to the user that contains a link to verify his email address and setup his password.
     * This mail is sent, whenever an Admin creates a new user via the backend.
     *
     * @param User $user The user who wants to reset his password.
     * @param string $token The verify and reset token.
     * @return void
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
            'instanceName' => Wasabi::getInstanceName()
        ]);
    }

    /**
     * Send a "verified" email to the user, when his email address has been verified.
     *
     * @param User $user The user who has verified his email address.
     * @return void
     */
    public function verifiedEmail(User $user)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Email address verified'));
        $this->_email->template('Wasabi/Core.User/verfied');
        $this->_email->viewVars([
            'user' => $user,
            'instanceName' => Wasabi::getInstanceName()
        ]);
    }

    /**
     * Send a verification email, when an admin has marked a user’s email as verified.
     *
     * @param User $user The user who has been verified by an administrator.
     * @return void
     */
    public function verifiedByAdminEmail(User $user)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Email address verified'));
        $this->_email->template('Wasabi/Core.User/verified-by-admin');
        $this->_email->viewVars([
            'user' => $user,
            'instanceName' => Wasabi::getInstanceName()
        ]);
    }

    /**
     * Send an "activated" email to the user when an admin activated a user account.
     *
     * @param User $user The user that has been activated.
     * @return void
     */
    public function activatedEmail(User $user)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Your account has been activated'));
        $this->_email->template('Wasabi/Core.User/activated');
        $this->_email->viewVars([
            'user' => $user,
            'instanceName' => Wasabi::getInstanceName()
        ]);
    }

    /**
     * Send a "deactivated" email to the user when an admin deactivated a user account.
     *
     * @param User $user The user who has been deactivated.
     * @return void
     */
    public function deactivatedEmail(User $user)
    {
        $this->_prepareEmail($user, __d('wasabi_core', 'Your account has been deactivated'));
        $this->_email->template('Wasabi/Core.User/deactivated');
        $this->_email->viewVars([
            'user' => $user,
            'instanceName' => Wasabi::getInstanceName()
        ]);
    }

    /**
     * Send a lost password email, when a user has requested a new one.
     *
     * @param User $user The user who want to reset his password.
     * @param string $token The lost password token.
     * @return void
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
            'instanceName' => Wasabi::getInstanceName()
        ]);
    }

    /**
     * Prepare the UserMailer Email instance.
     *
     * @param User $user The user to send the email to.
     * @param string $subject The subject of the email.
     * @return void
     */
    protected function _prepareEmail(User $user, $subject)
    {
        $this->layout('Wasabi/Core.responsive');
        $this->_email->transport('default');
        $this->_email->emailFormat('both');
        $this->_email->from(Wasabi::getSenderEmail(), Wasabi::getSenderName());
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
     * @throws \Cake\Mailer\Exception\MissingActionException
     * @throws \BadMethodCallException
     * @return array
     */
    public function send($action, $args = [], $headers = [])
    {
        $results = [];

        try {
            $results = parent::send($action, $args, $headers);
        } catch (\Exception $e) {
            Log::write(LOG_CRIT, 'Emails cannot be sent: ' . $e->getMessage(), $this->_email);
        }

        return $results;
    }
}
