<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 4:57 PM
 */

namespace markpthomas\library;

use markpthomas\Mountaineering as Core;


abstract class EnumExpandedDb extends EnumExpanded{


    protected static function fillNamesByTableName($tableName, $nameField = 'name'){
        $database = Core\DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT $nameField
                FROM $tableName";
        $query = $database->prepare($sql);
        $query->execute();
        self::$fullNames = $query->fetchAll(\PDO::FETCH_NUM);
    }


    protected static function fillDescriptionsByTableName($tableName, $descriptionField = 'description'){
        $database = Core\DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT $descriptionField
                FROM $tableName";
        $query = $database->prepare($sql);
        $query->execute();
        self::$descriptions = $query->fetchAll(\PDO::FETCH_NUM);
    }
} 