<?php

namespace Wasabi\Core\Controller\Component;

use Cake\Datasource\ModelAwareTrait;
use Cake\Routing\Router;
use Wasabi\Core\Authentication\Exception\AccountDisabledException;
use Wasabi\Core\Authentication\Exception\AccountNotActivatedException;
use Wasabi\Core\Authentication\Exception\InvalidCredentialsException;
use Wasabi\Core\Authentication\Exception\EmailNotVerifiedException;
use Wasabi\Core\Authentication\Exception\TooManyFailedLoginAttemptsException;
use Wasabi\Core\Authentication\Login;
use Wasabi\Core\Model\Table\LoginLogsTable;
use Wasabi\Core\View\Helper\RouteHelper;
use Wasabi\Core\Wasabi;

/**
 * Class AuthenticatorComponent
 *
 * @property LoginLogsTable LoginLogs
 */
class AuthenticatorComponent extends \Cake\Controller\Component
{
    use ModelAwareTrait;

    /**
     * Holds a login model instance to store and provide login specific data,
     * e.g. identity, password, ip, etc.
     *
     * @var Login
     */
    protected $login;

    /**
     * Initialization hook method.
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->login = $this->initializeLogin();

        $this->loadModel('Wasabi/Core.LoginLogs');
    }

    /**
     * Try to login the user.
     *
     * @throws AccountDisabledException
     * @throws AccountNotActivatedException
     * @throws InvalidCredentialsException
     * @throws EmailNotVerifiedException
     * @throws TooManyFailedLoginAttemptsException
     * @throws \Aura\Intl\Exception
     */
    public function login()
    {
        $this->populateLoginWithRequestData();

        if ($this->login->ipIsBlocked()) {
            throw new TooManyFailedLoginAttemptsException(__d(
                'wasabi_core',
                'You made too many failed login attempts in a short period of time. Please try again later.'
            ));
        }

        if (!($user = $this->getController()->Auth->identify())) {
            $this->getController()->dispatchEvent('Auth.failedLogin', [
                $this->login->getClientIp(),
                $this->login->getIdentityField(),
                $this->login->getIdentity()
            ]);
            switch ($this->login->getIdentityField()) {
                case 'email':
                    $message = __d('wasabi_core', 'Email or password is wrong.');
                    break;
                case 'username':
                    $message = __d('wasabi_core', 'Username or password is wrong.');
                    break;
                default:
                    $message = __d('wasabi_core', 'Your login credentials are wrong.');
            }
            throw new InvalidCredentialsException($message);
        }

        if (
            Wasabi::setting('Auth.newAccountsNeedEmailVerificationToLogin') &&
            !(bool)$user['verified']
        ) {
            throw new EmailNotVerifiedException(__d(
                'wasabi_core',
                'Please verify your email address or request a {0}.',
                '<a href="' . Router::url(RouteHelper::requestNewVerificationEmail()) . '">' . __d('wasabi_core', 'new verification email') . '</a>'
            ));
        }

        if (
            Wasabi::setting('Auth.newAccountsNeedActivationToLogin') &&
            $user['activated_at'] === null
        ) {
            throw new AccountNotActivatedException(__d(
                'wasabi_core',
                'Your account has not yet been activated. Once your account has been activated by an administrator, you will receive a notification email.'
            ));
        }

        if (!(bool)$user['active']) {
            throw new AccountDisabledException(__d(
                'wasabi_core',
                'Your account has been locked.'
            ));
        }

        $this->getController()->Auth->setUser($user);
        $this->getController()->getRequest()->getSession()->write('loginTime', time());
    }

    /**
     * Store the login identity in the session to repopulate the login form.
     *
     * @return void
     */
    public function storeIdentityInSession()
    {
        $this->getController()->getRequest()->getSession()->write(
            'Authenticator.loginIdentity',
            $this->login->getIdentity()
        );
    }

    /**
     * Remove the stored identity from the session.
     *
     * @return void
     */
    public function removeIdentityFromSession()
    {
        $this->getController()->getRequest()->getSession()->delete('Authenticator.loginIdentity');
    }

    /**
     * Get the instantiated login instance.
     *
     * @return Login
     */
    public function getLogin() : Login
    {
        return $this->login;
    }

    /**
     * Initialize the login instance.
     *
     * @return Login
     */
    protected function initializeLogin() : Login
    {
        $login = new Login();

        $identityField = Wasabi::setting('Auth.identityField');
        $login->setIdentityField($identityField);

        if ($this->getController()->getRequest()->getSession()->check($identityField)) {
            $login->setIdentity($this->getController()->getRequest()->getSession()->read($identityField));
        }

        return $login;
    }

    /**
     * Populate the login instance with request data.
     *
     * @return void
     */
    protected function populateLoginWithRequestData()
    {
        $this->login->setIdentity($this->getController()->getRequest()->getData($this->login->getIdentityField(), ''));
        $this->login->setPassword($this->getController()->getRequest()->getData('password'));

        $clientIp = $this->getController()->getRequest()->clientIp();
        $this->login->setClientIp($clientIp);
        $this->login->setIpIsBlocked($this->LoginLogs->ipIsBlocked($clientIp));
    }
}
