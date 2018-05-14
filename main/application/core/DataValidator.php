<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/25/18
 * Time: 1:23 PM
 */

namespace markpthomas\main;


class DataValidator {
    /**
     * Validates the username.
     *
     * @param $user_name
     * @return bool
     */
    public static function validateUserName($user_name)
    {
        if (empty($user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_FIELD_EMPTY'));
            return false;
        }

        // if username is too short (2), too long (64) or does not fit the pattern (aZ09)
        if (!preg_match('/^[a-zA-Z0-9_]{2,64}$/', $user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        return true;
    }

    /**
     * Validates the email.
     *
     * @param $user_email
     * @param $user_email_repeat
     * @return bool
     */
    public static function validateUserEmail($user_email, $user_email_repeat)
    {
        if (empty($user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_FIELD_EMPTY'));
            return false;
        }

        if ($user_email !== $user_email_repeat) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_REPEAT_WRONG'));
            return false;
        }

        // validate the email with PHP's internal filter
        // side-fact: Max length seems to be 254 chars
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        return true;
    }

    /**
     * Validates the password.
     *
     * @param $user_password_new
     * @param $user_password_repeat
     * @param bool $containUpperCase
     * @param bool $containLowerCase
     * @param bool $containNumber
     * @param bool $containSpecialCharacter
     * @return bool
     */
    public static function validateUserPassword($user_password_new, $user_password_repeat,
                                                $containUpperCase = false, $containLowerCase = false,
                                                $containNumber = false, $containSpecialCharacter = false)
    {
        if (empty($user_password_new) OR empty($user_password_repeat)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_FIELD_EMPTY'));
            return false;
        }

        if ($user_password_new !== $user_password_repeat) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_REPEAT_WRONG'));
            return false;
        }

        if (strlen($user_password_new) < 6) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_TOO_SHORT'));
            return false;
        }

        if ($containUpperCase && !preg_match('/[A-Z]/', $user_password_new)){
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_NEEDS_UPPERCASE'));
            return false;
        }

        if ($containLowerCase && !preg_match('/[a-z]/', $user_password_new)){
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_NEEDS_LOWERCASE'));
            return false;
        }

        if ($containNumber && !preg_match('/[0-9]/', $user_password_new)){
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_NEEDS_NUMERIC'));
            return false;
        }

        if ($containSpecialCharacter && !preg_match('/[^a-z0-9 ]+/i', $user_password_new)){
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_NEEDS_SPECIAL_CHARACTER'));
            return false;
        }

        return true;
    }

    /**
     * Checks that the phone number is a valid U.S. number.
     * This assumes that the phone number has already been normalized of spaces, hyphens, parentheses, etc.
     * @param $phoneNumber
     * @return string
     */
    public static function validatePhoneNumber($phoneNumber){
        if(empty($phoneNumber)){
            Session::add('feedback_negative', Text::get('FEEDBACK_PHONE_NUMBER_FIELD_EMPTY'));
            return false;
        }

        // Assuming that the data has been cleaned, there should be only numbers and any of the formats:
        // \d{3} + \d{4} = 7 (no area code)
        // \d{3} + \d{3} + \d{4} = 10 (area code)
        // \d{1} + \d{3} + \d{3} + \d{4} = 11 (area code preceded by '1')
        if(ctype_digit($phoneNumber) &&
            (strlen($phoneNumber) === 7 || strlen($phoneNumber) === 10 || strlen($phoneNumber) === 11)){
            Session::add('feedback_negative', Text::get('FEEDBACK_PHONE_NUMBER_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        return true;
    }

    /**
     * Strips all parentheses, hyphens, dots, spaces, and pluses from phone numbers.
     * @param $phoneNumber
     * @return mixed
     */
    public static function normalizePhoneNumber($phoneNumber){
        return preg_replace('/[\(\)\-\.+\s/', '', $phoneNumber);
    }
} 