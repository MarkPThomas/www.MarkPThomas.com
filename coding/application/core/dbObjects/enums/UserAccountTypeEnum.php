<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 3:28 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class UserAccountTypeEnum extends Lib\EnumExpandedDb {
    const Pending = 1;
    const Subscriber = 2;
    const Admin = 3;
    const Basic = 4;
    const Premium = 5;


    public static function getFullName($nameOrValue){
        // Class name must be specified here
        self::$className = get_called_class();
        return parent::getFullName($nameOrValue);
    }

    public static function getDescription($nameOrValue){
        // Class name must be specified here
        self::$className = get_called_class();
        return parent::getDescription($nameOrValue);
    }

    protected static function fillNames(){
        parent::fillNamesByTableName('user_account_type');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('user_account_type');
    }
} 