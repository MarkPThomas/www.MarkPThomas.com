<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 7:48 PM
 */

namespace markpthomas\mountaineering\dbObjects;


use markpthomas\library as Lib;


class ReportTypeEnum extends Lib\EnumExpandedDb
{
    const Summary = 1;
    const Introduction = 2;
    const Standard = 3;
    const Article = 4;

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
        parent::fillNamesByTableName('report_trip_type');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('report_trip_type');
    }
} 