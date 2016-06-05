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
 * @author Frank Förster <frank at frankfoerster.com>
 * @author Stephan Gonder <stephan.gonder at gmx.de>
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\Enum;

use Cake\Utility\Hash;

trait EnumTrait
{
    /**
     * Holds the cache of all constants of a class using this trait.
     *
     * @var array
     */
    protected static $_constantsCache;

    /**
     * Get and cache all constants of for this enum.
     *
     * @return array
     */
    public static function getConstants()
    {
        if (empty(self::$_constantsCache)) {
            $reflectionClass = new \ReflectionClass(get_called_class());
            self::$_constantsCache = $reflectionClass->getConstants();
        }
        return self::$_constantsCache;
    }

    /**
     * Get an array of key -> values for a select.
     *
     * @return array
     */
    public static function getSelect()
    {
        return Hash::combine(self::getConstants(), '{s}.value', '{s}.name');
    }

    /**
     * Get all available values for this enum.
     *
     * @return array
     */
    public static function getValues()
    {
        return Hash::extract(self::getConstants(), '{s}.value');
    }

    /**
     * Get a single enum constant configuration for a specific value.
     *
     * @param int|string $value The value to find a constant config for.
     * @return array|bool
     */
    public static function getConstantConfigByValue($value)
    {
        foreach (self::getConstants() as $constant) {
            if ($constant['value'] === $value) {
                return $constant;
            }
        }
        return false;
    }

    /**
     * Get the descriptive name for the given value.
     *
     * @param int|string $value The value to get the name of a constant for.
     * @return bool|string
     */
    public static function getNameForValue($value)
    {
        foreach (self::getConstants() as $constant) {
            if ($constant['value'] === $value) {
                return $constant['name'];
            }
        }
        return false;
    }

    /**
     * Check wheter the given $value is valid.
     *
     * @param int|string $value The value to check.
     * @return bool
     */
    public static function isValidValue($value)
    {
        return in_array($value, self::getValues());
    }
}
