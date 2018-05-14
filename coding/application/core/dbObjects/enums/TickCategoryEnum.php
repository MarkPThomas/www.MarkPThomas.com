<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 3:25 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class TickCategoryEnum extends Lib\EnumExpandedDb {
    const Unknown = 1;
    const Yes = 2;
    const No = 3;


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
        parent::fillNamesByTableName('trailhead_developed');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('trailhead_developed');
    }
} 