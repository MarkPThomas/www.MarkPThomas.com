<?php

namespace markpthomas\mountaineering;

/**
 * This is under development. Expect changes!
 * Class Request
 * Abstracts the access to $_GET, $_POST and $_COOKIE, preventing direct access to these super-globals.
 * This makes PHP code quality analyzer tools very happy.
 * @see http://php.net/manual/en/reserved.variables.request.php
 */
class Request
{
    /**
     * Checks if the specified POST super-global is set.
     * @param mixed $key Key.
     * @return bool True if the variable is set.
     */
    public static function postIsSet($key){
        return isset($_POST[$key]);
    }

    /**
     * Checks if the specified GET super-global is set.
     * @param mixed $key Key.
     * @return bool True if the variable is set.
     */
    public static function getIsSet($key){
        return isset($_GET[$key]);
    }

    /**
     * Gets/returns the value of a specific key of the POST super-global.
     * When using just Request::post('x') it will return the raw and untouched $_POST['x'], when using it like
     * Request::post('x', true) then it will return a trimmed and stripped $_POST['x'] !
     *
     * @param mixed $key Key.
     * @param bool $clean Marker for optional cleaning of the var.
     * @return mixed The key's value or nothing.
     */
    public static function post($key, $clean = false)
    {
        if (isset($_POST[$key])) {
            // we use the Ternary Operator here which saves the if/else block
            // @see http://davidwalsh.name/php-shorthand-if-else-ternary-operators
            return ($clean) ? trim(strip_tags($_POST[$key])) : $_POST[$key];
        }
    }

    /**
     * Returns the state of a checkbox.
     *
     * @param mixed $key Key.
     * @return mixed State of the checkbox.
     */
    public static function postCheckbox($key)
    {
        return isset($_POST[$key]) ? 1 : NULL;
    }

    /**
     * Gets/returns the value of a specific key of the GET super-global.
     * @param mixed $key Key.
     * @return mixed The key's value or nothing.
     */
    public static function get($key)
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
    }

    /**
     * Gets/returns the value of a specific key of the COOKIE super-global.
     * @param mixed $key Key.
     * @return mixed The key's value or nothing.
     */
    public static function cookie($key)
    {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
    }
}
