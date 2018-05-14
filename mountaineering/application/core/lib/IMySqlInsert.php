<?php
namespace markpthomas\library;

/**
 * Interface IMySQLInsert is for objects that generate an insert query for MySQL.
 */
interface IMySqlInsert{
    /**
     * Returns a MySQL insert query.
     * @param \PDO $mysqlPdo.
     * @return MyPDOStatement
     */
    public function mySqlInsert(\PDO $mysqlPdo);

    /**
     * Returns 'False' if the object does not yet have an entry in the table.
     * @param \PDO $mysqlPdo
     * @return bool Indication of if the record already exists in the table.
     */
    public function mySqlExist(\PDO $mysqlPdo);
}