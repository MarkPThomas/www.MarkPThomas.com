<?php

namespace markpthomas\main;

/**
 * UserModel
 * Handles all the PUBLIC profile stuff. This is not for getting data of the logged in user, it's more for handling
 * data of all the other users. Useful for displaying profile information, creating user lists etc.
 */
class UserModel
{
    /**
     * Gets an array that contains all the users in the database. The array's keys are the user ids.
     * Each array element is an object, containing a specific user's data.
     * The avatar line is built using Ternary Operators, have a look here for more:
     * @see http://davidwalsh.name/php-shorthand-if-else-ternary-operators
     *
     * @return array The profiles of all users.
     */
    public static function getPublicProfilesOfAllUsers()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                    id,
                    username,
                    email,
                    is_active,
                    has_local_avatar,
                    is_deleted,
                    suspension_timestamp,
                    user_account_type_id
                FROM user";
        $query = $database->prepare($sql);
        $query->execute();

        $all_users_profiles = array();

        foreach ($query->fetchAll() as $user) {

            // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
            // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
            // the user's values
            array_walk_recursive($user, __NAMESPACE__ . '\Filter::XSSFilter');

                $all_users_profiles[$user->id] = new \stdClass();
                $all_users_profiles[$user->id]->id = $user->id;
                $all_users_profiles[$user->id]->username = $user->username;
                $all_users_profiles[$user->id]->email = $user->email;
                $all_users_profiles[$user->id]->suspension_timestamp = round(($user->suspension_timestamp- time()) / (60 * 60 * 24));
                $all_users_profiles[$user->id]->is_active = $user->is_active;
                $all_users_profiles[$user->id]->is_deleted = $user->is_deleted;
                $all_users_profiles[$user->id]->user_account_type_id = $user->user_account_type_id;
                $all_users_profiles[$user->id]->user_avatar_link = (Config::get('USE_GRAVATAR') ? 
                                    AvatarModel::getGravatarLinkByEmail($user->email) :
                                    AvatarModel::getPublicAvatarFilePathOfUser($user->has_local_avatar, $user->id));
        }

        return $all_users_profiles;
    }

    /**
     * Gets a user's profile data, according to the given $user_id.
     * @param int $user_id The user's id.
     * @return mixed The selected user's profile.
     */
    public static function getPublicProfileOfUser($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                  id,
                  username,
                  email,
                  is_active,
                  has_local_avatar,
                  is_deleted,
                  user_account_type_id
                FROM user
                WHERE id = :user_id
                LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));

        $user = $query->fetch();

        if ($query->rowCount() == 1) {
            if (Config::get('USE_GRAVATAR')) {
                $user->user_avatar_link = AvatarModel::getGravatarLinkByEmail($user->email);
            } else {
                $user->user_avatar_link = AvatarModel::getPublicAvatarFilePathOfUser($user->has_local_avatar, $user->id);
            }
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_USER_DOES_NOT_EXIST'));
        }

        // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
        // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
        // the user's values
        array_walk_recursive($user, __NAMESPACE__ . '\Filter::XSSFilter');

        return $user;
    }

    /**
     * @param $user_name_or_email
     *
     * @return mixed
     */
    public static function getUserDataByUserNameOrEmail($user_name_or_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT
                                      id,
                                      username,
                                      email
                                     FROM user
                                     WHERE (username = :user_name_or_email
                                           OR email = :user_name_or_email)
                                           AND provider_type = :provider_type
                                           LIMIT 1");
        $query->execute(array(':user_name_or_email' => $user_name_or_email, ':provider_type' => 'DEFAULT'));

        return $query->fetch();
    }

    /**
     * Checks if a username is already taken.
     *
     * @param $user_name string Username.
     *
     * @return bool
     */
    public static function doesUsernameAlreadyExist($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT id
        FROM user
        WHERE username = :user_name
        LIMIT 1");
        $query->execute(array(':user_name' => $user_name));
        if ($query->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Checks if an email is already used.
     *
     * @param $user_email string Email.
     *
     * @return bool
     */
    public static function doesEmailAlreadyExist($user_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT id
                                        FROM user
                                        WHERE email = :user_email
                                        LIMIT 1");
        $query->execute(array(':user_email' => $user_email));
        if ($query->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Writes new username to database.
     *
     * @param $user_id int User id.
     * @param $new_user_name string New username.
     *
     * @return bool
     */
    public static function saveNewUserName($user_id, $new_user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE user
                                        SET username = :user_name
                                        WHERE id = :user_id
                                        LIMIT 1");
        $query->execute(array(':user_name' => $new_user_name, ':user_id' => $user_id));
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    /**
     * Writes new email address to database.
     *
     * @param $user_id int User id.
     * @param $new_user_email string New email address.
     *
     * @return bool
     */
    public static function saveNewEmailAddress($user_id, $new_user_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE user
                                        SET email = :user_email
                                        WHERE id = :user_id
                                        LIMIT 1");
        $query->execute(array(':user_email' => $new_user_email, ':user_id' => $user_id));
        $count = $query->rowCount();
        if ($count == 1) {
            return true;
        }
        return false;
    }

    /**
     * Edit the user's name, provided in the editing form.
     *
     * @param $new_user_name string The new username.
     *
     * @return bool Success status.
     */
    public static function editUserName($new_user_name)
    {
        // new username same as old one ?
        if ($new_user_name == Session::get('user_name')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_SAME_AS_OLD_ONE'));
            return false;
        }

        // username cannot be empty and must be azAZ09 and 2-64 characters
        if (!preg_match("/^[a-zA-Z0-9]{2,64}$/", $new_user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        // clean the input, strip usernames longer than 64 chars (maybe fix this ?)
        $new_user_name = substr(strip_tags($new_user_name), 0, 64);

        // check if new username already exists
        if (self::doesUsernameAlreadyExist($new_user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_ALREADY_TAKEN'));
            return false;
        }

        $status_of_action = self::saveNewUserName(Session::get('user_id'), $new_user_name);
        if ($status_of_action) {
            Session::set('user_name', $new_user_name);
            Session::add('feedback_positive', Text::get('FEEDBACK_USERNAME_CHANGE_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
            return false;
        }
    }

    /**
     * Edit the user's email.
     *
     * @param $new_user_email
     *
     * @return bool Success status.
     */
    public static function editUserEmail($new_user_email)
    {
        // email provided ?
        if (empty($new_user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_FIELD_EMPTY'));
            return false;
        }

        // check if new email is same like the old one
        if ($new_user_email == Session::get('user_email')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_SAME_AS_OLD_ONE'));
            return false;
        }

        // user's email must be in valid email format, also checks the length
        // @see http://stackoverflow.com/questions/21631366/php-filter-validate-email-max-length
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if (!filter_var($new_user_email, FILTER_VALIDATE_EMAIL)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        // strip tags, just to be sure
        $new_user_email = substr(strip_tags($new_user_email), 0, 254);

        // check if user's email already exists
        if (self::doesEmailAlreadyExist($new_user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN'));
            return false;
        }

        // write to database, if successful ...
        // ... then write new email to session, Gravatar too (as this relies to the user's email address)
        if (self::saveNewEmailAddress(Session::get('user_id'), $new_user_email)) {
            Session::set('user_email', $new_user_email);
            Session::set('user_gravatar_image_url', AvatarModel::getGravatarLinkByEmail($new_user_email));
            Session::add('feedback_positive', Text::get('FEEDBACK_EMAIL_CHANGE_SUCCESSFUL'));
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
        return false;
    }

    /**
     * Gets the user's id.
     *
     * @param $user_name
     *
     * @return mixed
     */
    public static function getUserIdByUsername($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id
                FROM user
                WHERE username = :user_name
                    AND provider_type = :provider_type
                LIMIT 1";
        $query = $database->prepare($sql);

        // DEFAULT is the marker for "normal" accounts (that have a password etc.)
        // There are other types of accounts that don't have passwords etc. (FACEBOOK)
        $query->execute(array(':user_name' => $user_name, ':provider_type' => 'DEFAULT'));

        // return one row (we only have one result or nothing)
        return $query->fetch()->id;
    }

    /**
     * Gets the user's data.
     *
     * @param $user_name string User's name.
     *
     * @return mixed Returns false if user does not exist, returns object with user's data when user exists.
     */
    public static function getUserDataByUsername($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id, username, email, password_hash, is_active, is_deleted, suspension_timestamp, user_account_type_id,
                       failed_logins, last_failed_login
                  FROM user
                 WHERE (username = :user_name
                        OR email = :user_name)
                     AND provider_type = :provider_type
                 LIMIT 1";
        $query = $database->prepare($sql);

        // DEFAULT is the marker for "normal" accounts (that have a password etc.)
        // There are other types of accounts that don't have passwords etc. (FACEBOOK)
        $query->execute(array(':user_name' => $user_name, ':provider_type' => 'DEFAULT'));

        // return one row (we only have one result or nothing)
        return $query->fetch();
    }

    /**
     * Gets the user's data by user's id and a token (used by login-via-cookie process).
     *
     * @param $user_id
     * @param $token
     *
     * @return mixed Returns false if user does not exist, returns object with user's data when user exists.
     */
    public static function getUserDataByUserIdAndToken($user_id, $token)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        // get real token from database (and all other data)
        $query = $database->prepare("SELECT id, username, email, password_hash, is_active,
                                          user_account_type_id,  has_local_avatar, failed_logins, last_failed_login
                                     FROM user
                                     WHERE user_id = :user_id
                                       AND remember_me_token = :user_remember_me_token
                                       AND remember_me_token IS NOT NULL
                                       AND provider_type = :provider_type
                                     LIMIT 1");
        $query->execute(array(':user_id' => $user_id, ':user_remember_me_token' => $token, ':provider_type' => 'DEFAULT'));

        // return one row (we only have one result or nothing)
        return $query->fetch();
    }
}
