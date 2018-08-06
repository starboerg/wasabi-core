<?php

namespace Wasabi\Core\Controller;

use Wasabi\Core\Authentication\Exception\AuthenticationException;
use Wasabi\Core\Controller\Component\AuthenticatorComponent;
use Wasabi\Core\Wasabi;

/**
 * Class AuthenticationController
 *
 * @property AuthenticatorComponent Authenticator
 */
class AuthenticationController extends BackendAppController
{
    /**
     * Initialization hook method
     *
     * @return void
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Wasabi/Core.Authenticator');
    }

    /**
     * login action
     * GET | POST
     *
     * @return void
     * @throws \Aura\Intl\Exception
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $success = false;
            $errorType = null;
            $errorMessage = null;

            try {
                $this->Authenticator->login();
                $success = true;
            } catch (AuthenticationException $e) {
                $errorType = $e->getErrorType();
                $errorMessage = $e->getMessage();
            }

            if ($success) {
                $this->Flash->success(__d('wasabi_core', 'Welcome back.'), 'auth');
                $this->redirect($this->Auth->redirectUrl());
                return;
            }

            $this->Authenticator->storeIdentityInSession();
            $this->Flash->{$errorType}($errorMessage, 'auth', false);
            $this->redirect(['action' => 'login']);
            return;
        }

        $this->request = $this->request->withData(
            $this->Authenticator->getLogin()->getIdentityField(),
            $this->Authenticator->getLogin()->getIdentity()
        );

        $this->Authenticator->removeIdentityFromSession();

        $this->render(
            Wasabi::setting('Auth.login.view', null),
            Wasabi::setting('Auth.login.layout', 'Wasabi/Core.support')
        );
    }

    /**
     * logout action
     * GET
     *
     * @return void
     */
    public function logout()
    {
        $this->redirect($this->Auth->logout());
        //@codingStandardIgnoreStart
        return;
        //@codingStandardIgnoreEnd
    }
}
