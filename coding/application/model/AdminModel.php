<?php

namespace markpthomas\coding;

/**
 * Handles all data manipulation of the admin part
 */
class AdminModel
{
    public static function setAccountActivationStatus($userId, $userStatus)
    {
        // Prevent to suspend or delete own account.
        // If admin suspend or delete own account will not be able to do any action.
        if ($userId == Session::get('user_id')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CANT_DELETE_SUSPEND_OWN'));
            return false;
        }

        // Save user activation state to database
        if (self::writeAccountActivationToDatabase($userId, $userStatus)) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_STATUS_CHANGE_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_STATUS_CHANGE_FAILED'));
            return false;
        }
    }

    public static function writeAccountActivationToDatabase($userId, $userStatus)
    {
        // if $type is not 1 or 2
        if (!in_array($userStatus, [0, 1])) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE user
                                     SET
                                      is_active = :is_active,
                                      activation_hash = null
                                     WHERE id = :user_id
                                     LIMIT 1");
        $query->execute([
            ':is_active' => $userStatus,
            ':user_id' => $userId
        ]);

        if ($query->rowCount() == 1) {
            return true;
        }

        return false;
    }


    public static function changeUserRole($userId, $userRole){
        // Prevent to change to own account.
        // If admin suspend or delete own account will not be able to do any action.
        if ($userId == Session::get('user_id')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CANT_DELETE_SUSPEND_OWN'));
            return false;
        }

        // Save user activation state to database
        if (self::writeUserRoleToDatabase($userId, $userRole)) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_ROLE_CHANGE_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_ROLE_CHANGE_FAILED'));
            return false;
        }
    }

    public static function writeUserRoleToDatabase($userId, $userRole){
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE user
                                     SET user_account_type_id = :new_type
                                     WHERE id = :user_id
                                     LIMIT 1");
        $query->execute([
            ':new_type' => $userRole,
            ':user_id' => $userId
        ]);

        if ($query->rowCount() == 1) {
            return true;
        }

        return false;
    }

    public static function setAccountSuspension($userId, $suspensionInDays)
    {
        // Prevent to suspend or delete own account.
        // If admin suspend or delete own account will not be able to do any action.
        if ($userId == Session::get('user_id')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CANT_DELETE_SUSPEND_OWN'));
            return false;
        }

        if ($suspensionInDays > 0) {
            $suspensionTime = time() + ($suspensionInDays * 60 * 60 * 24);
        } else {
            $suspensionTime = null;
        }

        // Save user activation state to database
        $result = self::writeSuspensionInfoToDatabase($userId, $suspensionTime);

        // if suspension or deletion should happen, then also kick user out of the application instantly by resetting
        // the user's session :)
        if ($result && $suspensionTime != null) {
            self::resetUserSession($userId);
        }

        if ($result) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_STATUS_CHANGE_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_STATUS_CHANGE_FAILED'));
            return false;
        }
    }


    private static function writeSuspensionInfoToDatabase($userId, $suspensionTime)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE user
                                     SET
                                      suspension_timestamp = :user_suspension_timestamp
                                     WHERE id = :user_id
                                     LIMIT 1");
        $query->execute([
            ':user_suspension_timestamp' => $suspensionTime,
            ':user_id' => $userId
        ]);

        if ($query->rowCount() == 1) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_SUSPENSION_DELETION_STATUS'));
            return true;
        }
        return false;
    }

    public static function setAccountSoftDelete($userId, $softDelete)
    {
        // Prevent to suspend or delete own account.
        // If admin suspend or delete own account will not be able to do any action.
        if ($userId == Session::get('user_id')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CANT_DELETE_SUSPEND_OWN'));
            return false;
        }

        // FYI "on" is what a checkbox delivers by default when submitted. Didn't know that for a long time :)
        if ($softDelete == "on" || $softDelete == "true") {
            $delete = 1;
        } else {
            $delete = 0;
        }

        // write the above info to the database
        $result = self::writeDeleteInfoToDatabase($userId, $delete);

        // if suspension or deletion should happen, then also kick user out of the application instantly by resetting
        // the user's session :)
        if ($delete = 1) {
            return self::resetUserSession($userId);
        }
        return $result;
    }

    /**
     * Simply write the deletion and suspension info for the user into the database, also puts feedback into session
     *
     * @param $userId
     * @param $delete
     * @return bool
     */
    private static function writeDeleteInfoToDatabase($userId, $delete)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE user
                                     SET
                                      is_active = :is_active,
                                      is_deleted = :user_deleted
                                     WHERE id = :user_id
                                     LIMIT 1");
        $isActive = $delete? 0 : 1;
        $query->execute([
            ':is_active' => $isActive,
            ':user_deleted' => $delete,
            ':user_id' => $userId
        ]);

        if ($query->rowCount() == 1) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_SUSPENSION_DELETION_STATUS'));
            return true;
        }
        return false;
    }

    /**
     * Sets the deletion and suspension values
     *
     * @param $suspensionInDays
     * @param $softDelete
     * @param $userId
     * @return bool
     */
    public static function setAccountSuspensionAndDeletionStatus($suspensionInDays, $softDelete, $userId)
    {

        // Prevent to suspend or delete own account.
        // If admin suspend or delete own account will not be able to do any action.
        if ($userId == Session::get('user_id')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CANT_DELETE_SUSPEND_OWN'));
            return false;
        }

        if ($suspensionInDays > 0) {
            $suspensionTime = time() + ($suspensionInDays * 60 * 60 * 24);
        } else {
            $suspensionTime = null;
        }

        // FYI "on" is what a checkbox delivers by default when submitted. Didn't know that for a long time :)
        if ($softDelete == "on") {
            $delete = 1;
        } else {
            $delete = 0;
        }

        // write the above info to the database
        $result = self::writeDeleteAndSuspensionInfoToDatabase($userId, $suspensionTime, $delete);

        // if suspension or deletion should happen, then also kick user out of the application instantly by resetting
        // the user's session :)
        if ($suspensionTime != null OR $delete = 1) {
            return self::resetUserSession($userId);
        }
        return $result;
    }

    /**
     * Simply write the deletion and suspension info for the user into the database, also puts feedback into session
     *
     * @param $userId
     * @param $suspensionTime
     * @param $delete
     * @return bool
     */
    private static function writeDeleteAndSuspensionInfoToDatabase($userId, $suspensionTime, $delete)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE user
                                     SET
                                      suspension_timestamp = :user_suspension_timestamp,
                                      is_deleted = :user_deleted
                                     WHERE id = :user_id
                                     LIMIT 1");
        $query->execute([
                ':user_suspension_timestamp' => $suspensionTime,
                ':user_deleted' => $delete,
                ':user_id' => $userId
        ]);

        if ($query->rowCount() == 1) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_SUSPENSION_DELETION_STATUS'));
            return true;
        }
        return false;
    }

    /**
     * Kicks the selected user out of the system instantly by resetting the user's session.
     * This means, the user will be "logged out".
     *
     * @param $userId
     * @return bool
     */
    private static function resetUserSession($userId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE user
                                    SET session_id = :session_id
                                    WHERE id = :user_id
                                    LIMIT 1");
        $query->execute([
                ':session_id' => null,
                ':user_id' => $userId
        ]);

        if ($query->rowCount() == 1) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_USER_SUCCESSFULLY_KICKED'));
            return true;
        }
        return false;
    }
}
