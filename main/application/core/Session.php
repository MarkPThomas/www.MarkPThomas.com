<?php

namespace markpthomas\main;

/**
 * Session class
 *
 * Handles the session stuff. Creates session when none exists, sets and gets values, and closes the session
 * properly (=logout). Not to forget the check if the user is logged in or not.
 */
class Session
{
    /**
     * Starts the session.
     */
    public static function init()
    {
        // if no session exist, start the session
        if (session_id() == '') {
            session_start();
        }
    }

    /**
     * Sets a specific value to a specific key of the session.
     *
     * @param mixed $key key
     * @param mixed $value value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Gets/returns the value of a specific key of the session.
     *
     * @param mixed $key Usually a string, right?
     * @return mixed The key's value or nothing.
     */
    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];

            // filter the value for XSS vulnerabilities
            return Filter::XSSFilter($value);
        }
        return null;
    }

    /**
     * Adds a value as a new array element to the key.
     * Useful for collecting error messages, etc.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public static function add($key, $value)
    {
        $_SESSION[$key][] = $value;
    }

    /**
     * Deletes the session (= logs the user out).
     */
    public static function destroy()
    {
        session_destroy();
    }

    /**
     * Update session id in database.
     *
     * @access public
     * @static static method
     * @param  string $userId
     * @param  string $sessionId
     */
    public static function updateSessionId($userId, $sessionId = null)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "UPDATE user
                SET session_id = :session_id
                WHERE id = :user_id";

        $query = $database->prepare($sql);
        $query->execute(array(':session_id' => $sessionId, ":user_id" => $userId));
    }

    /**
     * Checks for session concurrency.
     *
     * This is done as the following:
     * UserA logs in with his session id('123') and it will be stored in the database.
     * Then, UserB logs in, also using the same email and password of UserA from another PC,
     * and application will also store the session id('456') in the database.
     *
     * Now, Whenever UserA performs any action,
     * you then check the session_id() against the last one stored in the database('456').
     * If they don't match then log both of them out.
     *
     * @access public
     * @static static method
     * @return bool
     * @see Session::updateSessionId()
     * @see http://stackoverflow.com/questions/6126285/php-stop-concurrent-user-logins
     */
    public static function isConcurrentSessionExists()
    {
        $session_id = session_id();
        $userId     = Session::get('user_id');

        if (isset($userId) && isset($session_id)) {

            $database = DatabaseFactory::getFactory()->getConnection();
            $sql = "SELECT session_id
                    FROM user
                    WHERE id = :user_id
                    LIMIT 1";

            $query = $database->prepare($sql);
            $query->execute(array(":user_id" => $userId));

            $result = $query->fetch();
            $userSessionId = !empty($result)? $result->session_id: null;

            return $session_id !== $userSessionId;
        }

        return false;
    }

    /**
     * Checks if the user is logged in or not.
     *
     * @return bool User's login status.
     */
    public static function userIsLoggedIn()
    {
        return (self::get('user_logged_in') ? true : false);
    }

    /**
     * Checks if the user is an admin or not.
     *
     * @return bool User's login status.
     */
    public static function userIsAdmin()
    {
        return (Session::get('user_account_type') == 3);
    }


    /**
     * Returns the current user role.
     * @return mixed
     */
    public static function userRole()
    {
        return (Session::get('user_account_type'));
    }
}
