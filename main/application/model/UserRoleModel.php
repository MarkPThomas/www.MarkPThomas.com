<?php

namespace markpthomas\main;

/**
 * Class UserRoleModel
 *
 * This class contains everything that is related to upgrading and downgrading accounts.
 */
class UserRoleModel
{
    public static function getUserRoles()
    {
        return self::getRolesFromDatabase();
    }

    public static function getRolesFromDatabase()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT
                                        u.id AS name,
                                        u.name AS value
                                     FROM user_account_type u");
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_KEY_PAIR);
    }


    public static function getUserRole($type_id)
    {
        if (!$type_id) {
            return false;
        }
        return self::getUserRoleFromDatabase($type_id);
    }

    public static function getUserRoleFromDatabase($type_id)
    {

        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT name
                                     FROM user_account_type
                                     WHERE id = :id
                                     LIMIT 1");
        $query->execute([':id' => $type_id]);

        return ($query->rowCount() === 1)? $query->fetch() : null;
    }


    /**
     * Upgrades / downgrades the user's account. Currently it's just the field user_account_type in the database that
     * can be 1 or 2 (maybe "basic" or "premium"). Put some more complex stuff in here, maybe a pay-process or whatever
     * you like.
     *
     * @param $type
     *
     * @return bool
     */
    public static function changeUserRole($type)
    {
        if (!$type) {
            return false;
        }

        // save new role to database
        if (self::saveRoleToDatabase($type)) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_TYPE_CHANGE_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_TYPE_CHANGE_FAILED'));
            return false;
        }
    }

    /**
     * Writes the new account type marker to the database and to the session.
     *
     * @param $type
     *
     * @return bool
     */
    public static function saveRoleToDatabase($type)
    {
        // if $type is not of an existing defined role
        $roles = array_keys(self::getUserRoles());
        if (!in_array($type, $roles)) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE user
                                     SET user_account_type_id = :new_type
                                     WHERE id = :user_id
                                     LIMIT 1");
        $query->execute(array(
            ':new_type' => $type,
            ':user_id' => Session::get('user_id')
        ));

        if ($query->rowCount() == 1) {
            // set account type in session
            Session::set('user_account_type', $type);
            return true;
        }

        return false;
    }
}
