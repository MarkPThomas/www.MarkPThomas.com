<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 3:26 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class TravelModeEnum extends Lib\EnumExpandedDb {
    const Cycling = 1;
    const Hiking = 2;
    const Scrambling = 3;
    const Rock = 4;
    const Mixed = 5;
    const Snow_Neve = 6;
    const Ice = 7;
    const Snowshoeing = 8;
    const Skiing = 9;
    const Canoeing = 10;
    const Sailing = 11;
    const Whitewater = 12;
    const Running = 13;
    const Swimming = 14;
    const Kayaking = 15;
    const Rowing = 16;


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
        parent::fillNamesByTableName('travel_mode');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('travel_mode');
    }
} 