<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 3:02 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class FeatureLocationTypeEnum extends Lib\EnumExpandedDb {
    const NA = 1;
    const Alpine = 2;
    const Lowland = 3;
    const Desert = 4;
    const Urban = 5;
    const River = 6;
    const Lake = 7;
    const Sea = 8;


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
        parent::fillNamesByTableName('feature_location_type');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('feature_location_type');
    }
} 