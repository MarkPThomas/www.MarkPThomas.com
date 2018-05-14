<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 2:59 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class ClimbingRouteCharacteristicSnowIceEnum extends Lib\EnumExpandedDb {
    const Ridge_arete = 1;
    const Couloir = 2;
    const Waterfall = 3;
    const Glacier = 4;
    const Chandelier = 5;
    const Mushrooms = 6;
    const Cornices = 7;
    const Rime = 8;
    const Neve = 9;
    const Icefall_IceCliff = 10;


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
        parent::fillNamesByTableName('climbing_route_characteristic_snow_ice');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('climbing_route_characteristic_snow_ice');
    }
} 