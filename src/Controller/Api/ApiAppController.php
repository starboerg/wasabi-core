<?php

namespace Wasabi\Core\Controller\Api;

use Cake\Event\Event;
use Cake\Http\Exception\MethodNotAllowedException;
use Wasabi\Core\Controller\BackendAppController;

class ApiAppController extends BackendAppController
{
    /**
     * Holds all response data to be returned in the json response.
     *
     * @var array
     */
    protected $responseData = [];

    /**
     * Holds the error message on errornous api requests.
     *
     * @var null
     */
    protected $errorMessage = null;

    /**
     * Initialization hook method.
     *
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->RequestHandler->renderAs($this, 'json');
        $this->response = $this->response->withType('application/json');
    }

    /**
     * beforeFilter callback
     *
     * - ensure the incoming request is an ajax request
     * - check if a valid user token is provided
     * - if not log out the user
     *
     * @param Event $event
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if (!$this->request->is('ajax')) {
            throw new MethodNotAllowedException();
        }
    }

    /**
     * Saves a variable or an associative array of variables to return in the json response.
     *
     * @param string|array $name A string or an array of data.
     * @param mixed $value Value in case $name is a string (which then works as the key).
     *   Unused if $name is an associative array, otherwise serves as the values to $name's keys.
     * @return $this
     */
    public function setResponseData($name, $value = null)
    {
        if (is_array($name)) {
            if (is_array($value)) {
                $data = array_combine($name, $value);
            } else {
                $data = $name;
            }
        } else {
            $data = [$name => $value];
        }
        $this->responseData = $data + $this->responseData;

        return $this;
    }

    /**
     * Respond with the given status $code and the specified $reason.
     *
     * @param int $code
     * @param string $reason
     * @return void
     */
    protected function respondWith($code = 200, $reason = 'OK')
    {
        if (!empty($this->responseData)) {
            $this->set('data', $this->responseData);
        }

        $this->set('status', $code === 200 ? 'success' : 'error');

        if ($code !== 200) {
            $this->set('error', [
                'code' => $code,
                'message' => $this->errorMessage,
                'reason' => $reason
            ]);
        }

        $serialize = array_merge(
            $this->viewVars['_serialize'] ?? [],
            ['data', 'error', 'status']
        );

        $this->set('_serialize', $serialize);

        $this->response = $this->response->withStatus(200, 'OK');
    }

    protected function respondOk()
    {
        $this->respondWith();
    }

    protected function respondWithBadRequest()
    {
        $this->respondWith(400, 'BAD REQUEST');
    }

    protected function respondWithUnauthorized()
    {
        $this->respondWith(401, 'UNAUTHORIZED');
    }

    protected function respondWithNotFound()
    {
        $this->respondWith(404, 'NOT FOUND');
    }

    protected function respondWithMethodNotAllowed()
    {
        $this->respondWith(405, 'METHOD NOT ALLOWED');
    }

    protected function respondWithValidationErrors()
    {
        $this->respondWith(422, 'UNPROCESSABLE ENTITY');
    }

    protected function respondWithConflict()
    {
        $this->respondWith(409, 'CONFLICT');
    }
}
