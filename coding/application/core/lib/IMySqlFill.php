<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/17/18
 * Time: 10:14 PM
 */

namespace markpthomas\library;

/**
 * Interface IMySqlFill is for objects that fill their properties from a MySQL database.
 */
interface IMySqlFill {
    /**
     * @param \PDO $mysqlPdo
     * @param string $pageId ID of the page to fill from in the database.
     * @return bool True if the object was successfully filled from the database.
     */
    public function mySqlFill(\PDO $mysqlPdo, $pageId);
} 