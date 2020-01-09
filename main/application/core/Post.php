<?php

namespace markpthomas\main;

/**
 * Post class
 *
 * Handles the Post stuff.
 */
class Post
{

    /**
     * Gets/returns the value of a specific key of the Post.
     *
     * @param mixed $key Usually a string
     * @return mixed The key's value or nothing.
     */
    public static function get($key)
    {
        if (isset($_POST[$key])) {
            $value = $_POST[$key];

            // filter the value for XSS vulnerabilities
            return Filter::XSSFilter($value);
        }
        return null;
    }
}