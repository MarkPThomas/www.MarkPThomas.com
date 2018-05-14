<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/16/18
 * Time: 10:53 PM
 */

namespace markpthomas\library;


interface IMySqlDelete {

    public function mySqlDelete(\PDO $mysqlPdo);
} 