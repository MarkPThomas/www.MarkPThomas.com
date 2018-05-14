<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 2:21 PM
 */

namespace markpthomas\library;


abstract class EnumExpanded extends EnumBasic {
    protected static $className = null;
    protected static $lastClassName = null;
    protected static $descriptions = null;
    protected static $fullNames = null;

    protected static function getId($nameOrValue){
        return (self::isValidName($nameOrValue, $strict = true))? self::getValue($nameOrValue) : $nameOrValue;
    }

    /**
     * Returns full name associated with the enum key name or value.
     * @param string $nameOrValue
     * @return mixed|null
     */
    public static function getFullName($nameOrValue){
        // Only refill names if a different class is calling OR the names have not been filled.
        if (self::$lastClassName != self::$className ||
            !self::$fullNames){
            $callback = [self::$className, 'fillNames'];
            if (is_callable($callback)){
                call_user_func($callback);
                self::$lastClassName = self::$className;
            }
        }

        $id = self::getId($nameOrValue);
        return (self::isValidValue($id))?
            self::$fullNames[$id - 1][0] :
            null;
    }

    /**
     * Returns description associated with the enum key name or value.
     * @param string $nameOrValue
     * @return mixed|null
     */
    public static function getDescription($nameOrValue){
        // Only refill names if a different class is calling OR the names have not been filled.
        if (self::$lastClassName != self::$className ||
            !self::$descriptions){
            $callback = [self::$className, 'fillDescriptions'];
            if (is_callable($callback)){
                call_user_func($callback);
                self::$lastClassName = self::$className;
            }
        }

        $id = self::getId($nameOrValue);
        return (self::isValidValue($id))?
            self::$descriptions[$id - 1][0] :
            null;
    }

    /**
     * Returns the first key name that has a matching value.
     * @param mixed $value Value to look up the name for.
     * @return string|null Name associated with the enum value, or null if it does not exist.
     */
    public static function getName($value){
        if (!self::isValidValue($value)) return null;

        $constants = self::getConstants();
        $name = array_search($value, $constants);
        return $name;
    }

    /**
     * Returns all key names that have a matching value.
     * @param mixed $value Value to look up the names for.
     * @return array|null Names associated with the enum value, or null if it does not exist.
     */
    public static function getNames($value){
        if (!self::isValidValue($value)) return null;

        $constants = self::getConstants();
        $name = array_filter($value, $constants);
        return $name;
    }

    /**
     * Returns the value associated with the enum key name.
     * @param string $name Enum key name to look up the value for.
     * @param bool $strict True: Name comparison is case-sensitive; otherwise, comparison is case-insensitive.
     * @return mixed|null Value associated with the enum key, or null if it does not exist.
     */
    public static function getValue($name, $strict = false){
        if (!$strict) {
            $name = strtolower($name);
        }
        if (!self::isValidName($name, $strict)) return null;

        $constants = self::getConstants();
        if (!$strict) {
            $constants = array_change_key_case($constants, CASE_LOWER);
        }
        return $constants[$name];
    }


    /**
     * Fills the list of full enum names. <br />
     * OVERRIDE THIS FUNCTION! Base function doesn't do anything. <br />
     * Note: This should be protected abstract static but is not allowed.
     * Should be resolved in PHP 7+ but my IDE currently cannot use that standard.
     * Fix once IDE upgraded.
     * @see https://stackoverflow.com/questions/999066/why-does-php-5-2-disallow-abstract-static-class-methods
     */
    protected static function fillNames(){

    }

    /**
     * Fills the list of descriptions. <br />
     * OVERRIDE THIS FUNCTION! Base function doesn't do anything. <br />
     * Note: This should be protected abstract static but is not allowed.
     * Should be resolved in PHP 7+ but my IDE currently cannot use that standard.
     * Fix once IDE upgraded.
     * @see https://stackoverflow.com/questions/999066/why-does-php-5-2-disallow-abstract-static-class-methods
     */
    protected static function fillDescriptions(){

    }
} 