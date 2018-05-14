<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 2:59 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class ClimbingTravelStyleEnum extends Lib\EnumExpandedDb {
    const NA = 1;
    const Cragging = 2;
    const SingleDay = 3;
    const MultiDay = 4;
    const Carryover = 5;
    const Expedition = 6;
    const Gym = 7;


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
        parent::fillNamesByTableName('climbing_travel_style');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('climbing_travel_style');
    }
} 