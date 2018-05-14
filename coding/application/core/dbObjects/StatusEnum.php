<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 4:54 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class StatusEnum extends Lib\EnumExpandedDb
{
    const NA = 1;
    const TBA = 2;
    const IP = 3;
    const Complete = 4;

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
        parent::fillNamesByTableName('status');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('status');
    }
}