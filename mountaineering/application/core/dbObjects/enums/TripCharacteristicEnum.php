<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 3:27 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class TripCharacteristicEnum extends Lib\EnumExpandedDb {
    const Traverses_Linkups = 1;
    const MountaineeringClinic = 2;
    const MiscClinics = 3;
    const Humor = 4;
    const Gym = 5;


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
        parent::fillNamesByTableName('trip_characteristic');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('trip_characteristic');
    }
} 