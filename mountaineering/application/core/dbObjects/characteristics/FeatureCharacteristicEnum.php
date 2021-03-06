<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 3:01 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class FeatureCharacteristicEnum extends Lib\EnumExpandedDb {
    const Glacier = 1;
    const Volcano = 2;


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
        parent::fillNamesByTableName('feature_characteristic');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('feature_characteristic');
    }
} 