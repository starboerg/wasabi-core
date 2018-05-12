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
namespace Wasabi\Core\Policy;

use Cake\Http\ServerRequest;
use Cake\ORM\Entity;
use Wasabi\Core\Model\Entity\User;

class PolicyManager
{
    /**
     * Holds all registered policies.
     *
     * @var array
     */
    protected $_policies;

    /**
     * Holds the current request instance.
     *
     * @var ServerRequest
     */
    protected $_request;

    /**
     * Holds the user instance to check policies on.
     *
     * @var User
     */
    protected $_user;

    /**
     * PolicyManager constructor.
     *
     * @param ServerRequest $request
     */
    public function __construct(ServerRequest $request)
    {
        $this->_request = $request;
    }

    /**
     * Add a new policy class for the given entity class.
     *
     * @param string $entityClass
     * @param string $policyClass
     * @return PolicyManager
     */
    public function addPolicy($entityClass, $policyClass)
    {
        if (!isset($this->_policies[$entityClass])) {
            $this->_policies[$entityClass] = [$policyClass];
        } else {
            $this->_policies[$entityClass][] = $policyClass;
        }

        return $this;
    }

    /**
     * Get all registered policies for the given entity/entity class.
     *
     * @param Entity|string $entity
     * @return array
     */
    public function getPoliciesFor($entity)
    {
        $entityClass = is_string($entity) ? $entity : get_class($entity);

        return $this->_policies[$entityClass] ?? [];
    }

    /**
     * Set the user to check policies on.
     *
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->_user = $user;

        return $this;
    }

    /**
     * Check if all registered policies for the given action and params pass.
     *
     * @param string $action
     * @param Entity|array $params
     * @return bool
     */
    public function checkPolicyFor($action, $params)
    {
        if (!is_array($params)) {
            $params = array($params);
        }

        $entity = $params[0];

        $policies = $this->getPoliciesFor($entity);

        if(!empty($policies)) {
            if (!is_array($params)) {
                $params = array($params);
            }

            foreach ($policies as $policy) {
                $policyInstance = new $policy;
                $passed = true;

                if (method_exists($policy, $action)) {
                    $params = array_merge([$this->_user], $params);
                    $passed = call_user_func_array([$policyInstance, $action], $params);
                }

                if (!$passed) {
                    return false;
                }
            }
        }

        return true;
    }
}
