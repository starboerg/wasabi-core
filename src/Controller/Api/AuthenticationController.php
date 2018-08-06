<?php

namespace Wasabi\Core\Controller\Api;

use Cake\Http\Exception\MethodNotAllowedException;
use Wasabi\Core\Authentication\Exception\AuthenticationException;
use Wasabi\Core\Controller\Component\AuthenticatorComponent;
use Wasabi\Core\View\AppView;
use Wasabi\Core\Wasabi;

/**
 * Class AuthenticationController
 *
 * @property AuthenticatorComponent Authenticator
 */
class AuthenticationController extends ApiAppController
{
    /**
     * Initialization hook method.
     *
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Wasabi/Core.Authenticator');
    }

    /**
     * login action
     * AJAX POST
     *
     * @return void
     * @throws \Aura\Intl\Exception
     */
    public function login()
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $success = false;
        $errorType = null;
        $errorMessage = null;

        try {
            $this->Authenticator->login();
            $success = true;
        } catch (AuthenticationException $e) {
            $errorType = $e->getErrorType();
            $errorMessage = $e->getMessage();
            $this->errorMessage = $errorMessage;
        }

        if (!$success) {
            $this->Flash->{$errorType}($errorMessage, 'auth', false);
            $formContent = (new AppView($this->request, $this->response, $this->getEventManager()))->element('Wasabi/Core.login-form-ajax');
            $this->setResponseData('content', $formContent);
            $this->respondWithUnauthorized();
            return;
        }

        $this->respondOk();
    }

    /**
     * HeartBeat action
     * AJAX POST
     *
     * @return void
     * @throws \Aura\Intl\Exception
     */
    public function heartbeat()
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $loginTime = $this->request->getSession()->check('loginTime') ? $this->request->getSession()->read('loginTime') : 0;
        $maxLoggedInTime = (int)Wasabi::setting('Core.Login.HeartBeat.max_login_time', 0) / 1000;
        $logoutTime = $loginTime + $maxLoggedInTime;

        if (time() <= $logoutTime) {
            $this->respondOk();
        } else {
            $this->Auth->logout();

            $this->errorMessage = __d('wasabi_core', 'Your login session timed out.');
            $this->respondWithUnauthorized();
        }
    }
}
