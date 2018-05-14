<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 1:46 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;

abstract class HeaderEnum extends Lib\EnumExpandedDb
{
    const None = 1;
    const H1 = 2;
    const H2 = 3;
    const H3 = 4;
    const H4 = 5;


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
        self::fillNamesByTableName('header_type');
    }

    protected static function fillDescriptions(){
        self::fillDescriptionsByTableName('header_type');
    }
}