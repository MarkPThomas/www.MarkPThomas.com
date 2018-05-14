<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 3:02 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class FeatureTypeEnum extends Lib\EnumExpandedDb {
    const NA = 1;
    const DesertTower = 1;
    const Spire_Pinnacle = 1;
    const Big_wall = 1;
    const Crag = 1;
    const Waterfall = 1;
    const Canyon = 1;
    const Mountain = 1;
    const Water = 1;
    const Road = 1;
    const Trail = 1;
    const City = 1;
    const Bridge = 1;
    const Building = 1;
    const Dome = 1;
    const AlpineWall_Face = 1;


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
        parent::fillNamesByTableName('feature_type');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('feature_type');
    }
} 