<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 3:00 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class ClimbingTypeEnum extends Lib\EnumExpandedDb {
    const Bouldering = 1;
    const FreeClimbing = 2;
    const Mountaineering = 3;
    const AidClimbing = 4;
    const MixedClimbing = 5;
    const IceClimbing = 6;
    const SnowClimbing = 7;
    const Glacier = 8;
    const CrossCountry = 9;


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
        parent::fillNamesByTableName('climbing_type');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('climbing_type');
    }
} 