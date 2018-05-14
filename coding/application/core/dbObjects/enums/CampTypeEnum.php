<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 2:56 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class CampTypeEnum extends Lib\EnumExpandedDb {
    const Roadside = 1;
    const Bivy = 2;
    const Primitve = 3;
    const Campground = 4;
    const RVPark = 5;
    const Hut = 6;
    const Primitive_Beach = 7;
    const Chickee = 8;
    const LeanTo = 9;


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
        parent::fillNamesByTableName('camp_type');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('camp_type');
    }
} 