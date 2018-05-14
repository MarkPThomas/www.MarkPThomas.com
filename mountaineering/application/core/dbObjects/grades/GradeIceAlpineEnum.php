<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/3/18
 * Time: 3:10 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class GradeIceAlpineEnum extends Lib\EnumExpandedDb {
    const NA = 1;

    protected static function fillNames(){
        parent::fillNamesByTableName('travel_mode');
    }

    protected static function fillDescriptions(){
        parent::fillDescriptionsByTableName('travel_mode');
    }
} 