<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/20/18
 * Time: 12:28 AM
 */

/**
 * A quick parsing helper method that raises a descriptive exception when an error is found.
 * See: <a href=http://nitschinger.at/Handling-JSON-like-a-boss-in-PHP/>Handling JSON like a boss in PHP</a>
 */
class JsonHandler {
    protected static $_messages = array(
        JSON_ERROR_NONE => 'No error has occurred',
        JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'Syntax error',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    );

    /**
     * Returns the JSON representation of a value
     * @param mixed $value The value being encoded. Can be any type except a resource. This function only works with UTF-8 encoded data.
     * @param int $options [optional] Bitmask consisting of:
     * JSON_HEX_QUOT - All " are converted to \u0022.<br />
     * JSON_HEX_TAG - All < and > are converted to \u003C and \u003E. <br />
     * JSON_HEX_AMP - All &s are converted to \u0026.<br />
     * JSON_HEX_APOS - All ' are converted to \u0027.<br />
     * JSON_NUMERIC_CHECK - Converts string-formatted numbers to actual numbers. <br />
     * JSON_PRETTY_PRINT - Use whitespace in returned data to format it.<br />
     * JSON_UNESCAPED_SLASHES - Don't escape /.<br />
     * JSON_FORCE_OBJECT - Forces the array to be translated into an object.<br />
     * JSON_UNESCAPED_UNICODE - Encode multibyte Unicode characters literally (default is to escape as \uXXXX). <br />
     * The behaviour of these constants is described on the JSON constants page.
     * @param $depth [optional] Set the maximum depth. Must be greater than zero.
     * @throws RuntimeException Is thrown if there is a failure.
     * @return string A JSON encoded string on success or exception on failure.
     */
    public static function encode($value, $options = 0, $depth = 512) {
        $result = json_encode($value, $options, $depth);

        if($result)  {
            return $result;
        }

        throw new RuntimeException(static::$_messages[json_last_error()]);
    }

    /**
     * Decodes a JSON string
     * @param string $json The json string being decoded. This function only works with UTF-8 encoded data.
     * @param bool $assoc [optional] When TRUE, returned objects will be converted into associative arrays.
     * @param int $options [optional] Bitmask of JSON decode options. Currently only JSON_BIGINT_AS_STRING is supported (default is to cast large integers as floats).
     * @param int $depth [optional] User specified recursion depth.
     * @throws RuntimeException Is thrown if the json cannot be decoded or if the encoded data is deeper than the recursion limit.
     * @return mixed The value encoded in json in appropriate PHP type. Values true, false and null (case-insensitive) are returned as TRUE, FALSE and NULL respectively.
     */
    public static function decode($json, $assoc = false, $options = 0, $depth = 512) {
        $result = json_decode($json, $assoc, $depth, $options);

        if($result) {
            return $result;
        }

        throw new RuntimeException(static::$_messages[json_last_error()]);
    }

}