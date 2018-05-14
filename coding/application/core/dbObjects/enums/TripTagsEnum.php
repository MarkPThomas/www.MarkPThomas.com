<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 3:29 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class TripTagsEnum extends Lib\EnumExpandedDb {
    const NA = 1;


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
        parent::fillNamesByTableName('trip_tags');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('trip_tags');
    }
} 