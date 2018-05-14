<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 8:23 PM
 */

namespace markpthomas\mountaineering;

use markpthomas\library as Lib;


class Privacy {


    /**
     * Sets the object to null if it is being accessed by someone other than an admin or the owner.
     * @param Lib\IObject $object
     * @return Lib\IObject|null
     */
    public static function enforcePrivacy(Lib\IObject $object){
        return (self::isPrivate($object))? null : $object;
    }

    /**
     * Sets the object to null if it is being accessed by someone other than an admin or the owner.
     * @param \stdClass $object
     * @return \stdClass|null
     */
    public static function enforcePrivacyStd(\stdClass $object){
        return (self::isPrivateStd($object))? null : $object;
    }

    /**
     * Removes the specified property from the object if it is being accessed by someone other than an admin or the owner.
     * @param Lib\IObject $object
     * @param $property
     * @return Lib\IObject
     */
    public static function enforcePrivacyOfEntry(Lib\IObject $object, $property){
        if (self::isPrivate($object)){
//            unset($object->{$property});  // Removing the property might be excessive, as then isset/empty checks are needed, and not just booleans.
            $object->{$property} = null;
        }
        return $object;
    }

    /**
 * Determines if the object is private based on its attribute and whether it is being accessed by someone other than an admin or the owner.
 * @param Lib\IObject $object
 * @return bool
 */
    public static function isPrivate(Lib\IObject $object){
        // Return object if it is public or the admin is viewing
        if (empty($object->is_public) || $object->is_public || Session::userIsAdmin())
            return false;

        // Return object if it is owned by the logged in owner
        $sessionId = session_id();
        $userId     = Session::get('user_id');
        $ownerId = !empty($object->user_id)? $object->user_id : 0;

        if (isset($sessionId) && isset($userId) &&
            $userId == $ownerId){
            return false;
        }
        return true;
    }

    /**
     * Determines if the object is private based on its attribute and whether it is being accessed by someone other than an admin or the owner.
     * @param \stdClass $object
     * @return bool
     */
    public static function isPrivateStd(\stdClass $object){
        // Return object if it is public or the admin is viewing
        if (empty($object->is_public) || $object->is_public || Session::userIsAdmin())
            return false;

        // Return object if it is owned by the logged in owner
        $sessionId = session_id();
        $userId     = Session::get('user_id');
        $ownerId = !empty($object->user_id)? $object->user_id : 0;

        if (isset($sessionId) && isset($userId) &&
            $userId == $ownerId){
            return false;
        }
        return true;
    }
} 