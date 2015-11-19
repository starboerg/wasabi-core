<?php

namespace Wasabi\Core;

use Cake\Core\Configure;
use Wasabi\Core\Model\Entity\Language;
use Wasabi\Core\Model\Entity\User;

class Wasabi
{
    /**
     * Holds the currently logged in user.
     *
     * @var User
     */
    protected static $_user;

    /**
     * Get the currently active content language.
     *
     * @return Language
     */
    public static function contentLanguage()
    {
        return Configure::read('contentLanguage');
    }

    /**
     * Set the currently logged in user.
     *
     * @param $user
     */
    public static function setUser($user)
    {
        self::$_user = $user;
    }

    /**
     * Get the currently logged in user.
     *
     * @return User
     */
    public static function user()
    {
        return self::$_user;
    }
}
