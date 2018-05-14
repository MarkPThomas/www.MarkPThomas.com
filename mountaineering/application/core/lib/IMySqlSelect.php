<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/16/18
 * Time: 10:53 PM
 */

namespace markpthomas\library;


interface IMySqlSelect {

    public function mySqlSelect(\PDO $mysqlPdo);
} 