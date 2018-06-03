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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
     */
    public static function getSelect()
    {
        return Hash::combine(self::getConstants(), '{s}.value', '{s}.name');
    }

    /**
     * Get all available values for this enum.
     *
     * @return array
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
     * Get the descriptive value for the given name.
     *
     * @param int|string $name The name to get the value of a constant for.
     * @return bool|string
     * @throws \ReflectionException
     */
    public static function getValueForName($name)
    {
        foreach (self::getConstants() as $constant) {
            if ($constant['name'] === $name) {
                return $constant['value'];
            }
        }
        return false;
    }

    /**
     * Check wheter the given $value is valid.
     *
     * @param int|string $value The value to check.
     * @return bool
     * @throws \ReflectionException
     */
    public static function isValidValue($value)
    {
        return in_array($value, self::getValues());
    }

    /**
     * Get the specified $attribute for the given enum $value.
     *
     * @param string $attribute The name of the attribute.
     * @param int|string $value The value of the enum entry.
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getAttributeByValue($attribute, $value)
    {
        $constant = self::getConstantConfigByValue($value);
        if (isset($constant[$attribute])) {
            return $constant[$attribute];
        }
        return false;
    }
}
