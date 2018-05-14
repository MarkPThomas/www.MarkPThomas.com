<?php
/**
 * Class BasicEnum is a base class to derive from for creating enumerations.
 * Example:
 * abstract class DaysOfWeek extends BasicEnum {
 * const Sunday = 0;
 * const Monday = 1;
 * const Tuesday = 2;
 * const Wednesday = 3;
 * const Thursday = 4;
 * const Friday = 5;
 * const Saturday = 6;
 * }
 * @see https://stackoverflow.com/questions/254514/php-and-enumerations
 */
abstract class BasicEnum {
    /**
     * @var mixed Cached array of the constants queried. This is to reduce performance reduction from large numbers of queries.
     */
    private static $constCacheArray = NULL;

    /**
     * @return mixed The enum to use.
     */
    private static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    /**
     * Checks if the provided name exists in the enum.
     * @param string $name Name to check for in the enum.
     * @param bool $strict True: Name comparison is case-sensitive; otherwise, comparison is case-insensitive.
     * @return bool True if the name exists in the enum.
     */
    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    /**
     * Checks if the provided value exists in the enum.
     * @param int $value Value to check for in the enum.
     * @param bool $strict True: Types must match as well (===; otherwise (==).
     * @return bool True if the value is defined in the enum.
     */
    public static function isValidValue($value, $strict = true) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }
}