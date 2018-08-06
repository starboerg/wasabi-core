<?php

namespace Wasabi\Core\Authentication;

class Login
{
    /**
     * Holds the identity field that is used to login a user.
     *
     * @var string
     */
    private $identityField;

    /**
     * Holds the identity of the user.
     * Depending on the authentication configuration this property can hold the username or email address.
     *
     * @var string
     */
    private $identity;

    /**
     * Holds the password of the user.
     *
     * @var string
     */
    private $password;

    /**
     * Holds the ip address of the user trying to login.
     *
     * @var string
     */
    private $ip;

    /**
     * Tells if the user trying to login is blocked.
     *
     * @var bool
     */
    private $ipIsBlocked;

    /**
     * Set the identity field.
     *
     * @param string $field
     * @return void
     */
    public function setIdentityField($field)
    {
        $this->identityField = (string)$field;
    }

    /**
     * Get the identity field.
     *
     * @return string
     */
    public function getIdentityField() : string
    {
        return $this->identityField;
    }

    /**
     * Set the identity of the user trying to login.
     *
     * @param string $identity
     * @return void
     */
    public function setIdentity($identity)
    {
        $this->identity = (string)$identity;
    }

    /**
     * Get the identity of the user trying to login.
     *
     * @return null|string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Set the password of the user trying to login.
     *
     * @param $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = (string)$password;
    }

    /**
     * Set the client ip address of the user trying to login.
     *
     * @param string $ip
     * @return void
     */
    public function setClientIp($ip)
    {
        $this->ip = (string)$ip;
    }

    /**
     * Get the client ip address of the user trying to login.
     *
     * @return string
     */
    public function getClientIp() : string
    {
        return $this->ip;
    }

    /**
     * Set if the ip of the user that tries to login is blocked.
     *
     * @param bool $ipIsBlocked
     * @return void
     */
    public function setIpIsBlocked($ipIsBlocked)
    {
        $this->ipIsBlocked = (bool)$ipIsBlocked;
    }

    /**
     * Check if the up of the user trying to login is blocked.
     *
     * @return bool
     */
    public function ipIsBlocked() : bool
    {
        return $this->ipIsBlocked;
    }
}
