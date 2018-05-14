<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 2:57 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class ClimbingRouteCharacteristicRockEnum extends Lib\EnumExpandedDb {
    const Ridge_Arete = 1;
    const Dihedral = 2;
    const Chimney = 3;
    const SqueezeChimney = 4;
    const Offwidth = 5;
    const Flare = 6;
    const FingerCrack = 7;
    const HandCrack = 8;
    const FistCrack = 9;
    const Lieback = 10;
    const Face = 11;
    const Friction = 12;
    const Roof = 13;


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
        parent::fillNamesByTableName('climbing_route_characteristic_rock');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('climbing_route_characteristic_rock');
    }
} 