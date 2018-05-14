<?php
namespace markpthomas\library;
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 12/23/17
 * Time: 9:44 PM
 */

/**
 * Interface IMySqlUpdate is for objects that update an entry in MySQL.
 */
interface IMySqlUpdate {

    /**
     * Updates MySQL entry.
     * @param \PDO $mysqlPdo
     * @return MyPDOStatement
     */
    public function mySqlUpdate(\PDO $mysqlPdo);
} 