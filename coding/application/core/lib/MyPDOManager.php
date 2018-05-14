<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/29/18
 * Time: 10:10 AM
 */

namespace markpthomas\library;

class MyPDOManager {

    /**
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     * @param array $options
     * @return \PDO
     */
    public static function mysqlPdoObject($host, $user, $password, $database, array $options = null){
        try {
            $connection = new \PDO(
                'mysql' .
                ':host=' . $host .
                ';dbname=' . $database . ';charset=utf8',
                $user,
                $password,
                $options
            );
        } catch (\PDOException $e) {
            echo 'Database connection cannot be established. Please try again later.' . '<br>';
            echo 'Error code: ' . $e->getCode() . '<br />';
            echo 'Error description: ' . $e->getMessage() . '<br />';

            // Stop application :(
            // No connection, reached limit connections etc. so no point to keep it running
            exit;
        }
        
        return $connection;
    }


    /**
     * Writes the object data of all objects to the appropriate MySQL table.
     *
     * Each call to this method will open a new connection to the database.
     *
     * @param array $items An array of objects implementing IMySQLInsert.
     * @param \PDO $mysqlPdo
     * @param bool $closeConnection
     * @return void
     */
    public static function insertItemsToMySql(array $items, \PDO $mysqlPdo, $closeConnection = true){
        MyLogger::log('Writing to database...<br />');

        foreach ($items as $item){
            if (!self::insertItemToMySql($mysqlPdo, $item)) { break; }
        }
        if ($closeConnection) $mysqlPdo = null;
        MyLogger::log('Writing complete. Connection is closed. <br />');
    }

    /**
     * Writes the object data to the appropriate MySQL table.
     * @param \PDO $mysqlPdo MySQL connection to use.
     * @param IMySqlInsert $item Object containing data to insert into a MySQL table.
     * @return bool True if operation was successful.
     */
    public static function insertItemToMySql(\PDO $mysqlPdo, IMySqlInsert $item){
        if (!$item->mySqlExist($mysqlPdo)) {
            $query = $item->mySqlInsert($mysqlPdo);
            return self::confirmInsert($query);
        } else {
            MyLogger::log("Entry already exists in MySQL. Ignoring... <br />");
        }
        return true;
    }


    public static function updateItemsInMySQL(array $items, \PDO $mysqlPdo, $closeConnection = true){
        MyLogger::log('Updating entries in database...<br >');

        foreach ($items as $item){
            if (!self::updateItemInMySQL($mysqlPdo, $item)) { break; }
        }
        if ($closeConnection) $mysqlPdo = null;
        MyLogger::log('Writing complete. Connection is closed. <br />');
    }


    public static function updateItemInMySQL(\PDO $mysqlPdo, IMySqlUpdate $item, $addIfNotExist = true){
        if ($item instanceof IMySqlInsert){
            if ($item->mySqlExist($mysqlPdo) && $item instanceof IMySqlUpdate) {
                $query = $item->mySqlUpdate($mysqlPdo);
                return self::confirmUpdate($query);
            } elseif ($addIfNotExist) {
                MyLogger::log("Entry does not exist in MySQL. Inserting... <br />");
                return self::insertItemToMySql($mysqlPdo, $item);
            }
        }
        return false;
    }

    public static function clearTable(\PDO $mysqlPdo, $tableName, $idName = 'id'){
        $sql = "SELECT * FROM $tableName";
        MyLogger::log($sql . '<br />');
        $query = $mysqlPdo->prepare($sql);
        $query->execute();
        $ids = [];
        while ($row = $query->fetch()){
            array_push($ids, $row->idName);
        }
        foreach ($ids as $id){
            self::deleteFromTable($mysqlPdo, $tableName, $id, $idName);
        }
    }

    public static function deleteFromTable(\PDO $mysqlPdo, $tableName, $idValue, $idName = 'id'){
        $sql = "DELETE FROM $tableName
                WHERE $idName = :idValue";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':idValue' => $idValue]);
        return self::confirmDelete($query);
    }



    public static function confirmDelete(\PDOStatement $query)
    {
        $rowsAffected = $query->rowCount();
        if ($rowsAffected){
            MyLogger::log($rowsAffected . ' Rows deleted.<br />');
            return true;
        }

        MyLogger::log('Database delete failed.' . '<br />');
        self::logSqlQuery($query);
        return false;
    }


    public static function confirmInsert(\PDOStatement $query)
    {
        $rowsAffected = $query->rowCount();
        if ($rowsAffected){
            MyLogger::log($rowsAffected . ' Rows inserted.<br />');
            return true;
        }

        MyLogger::log('Database insert failed.' . '<brv>');
        self::logSqlQuery($query);
        return false;
    }


    /**
     * Confirms whether or not the update was successful.
     * @see https://stackoverflow.com/questions/6237212/why-does-pdo-rowcount-return-0-after-update-a-table-without-modifying-the-exis
     * @see https://stackoverflow.com/questions/11820914/with-pdo-how-can-i-make-sure-that-an-update-statement-was-successful/11820939#11820939
     * @param \PDOStatement $query
     * @param bool $useRowCount If true, then success is determined by checking the number of rows affected.
     * This may give an incorrect failure if the data has not been changed.
     * If it is desired to have success be marked if the update was successful but no data need be changed, leave this as False.
     * @return bool
     */
    public static function confirmUpdate(\PDOStatement $query, $useRowCount = false)
    {
        if ($useRowCount){
            $rowsAffected = $query->rowCount();
            if ($rowsAffected){
                MyLogger::log($rowsAffected . ' Rows updated.<br />');
                return true;
            }
        } else {
            if ($query){
                MyLogger::log('Update successful. <br />');
                return true;
            }
        }


        MyLogger::log('Database update failed.' . '<br />');
        self::logSqlQuery($query);
        return false;
    }

    /**
     * Confirms whether or not the transaction failed based on the result.
     * Throws an exception if it fails.
     * @param $result
     * @throws \Exception
     */
    public static function confirmTransaction($result){
        if (!$result){
            MyLogger::log('Transaction failed.' . '<br />');
            throw new \Exception('Transaction failed... <br />');
        }
    }

    public static function logSqlQuery(\PDOStatement $query){
        if ($query instanceof MyPDOStatement){
            MyLogger::log($query->_debugQuery() . '<br />');
        }
    }




    // TODO: Incomplete port from MySqliManager
    public static function mySqlIsUniqueKey(\PDO $mysqlPdo, $tableName, array $queryHash){
        if (count($queryHash) === 0) return false;
        $sql = "SELECT COUNT(*) AS num_rows FROM $tableName WHERE $queryHash[0][0] = $queryHash[0][1]";
        for ($i = 1; $i < count($queryHash); $i++){
            $sql .= " AND $queryHash[$i][0] = $queryHash[$i][1]";
        }
        $result = $mysqlPdo->query($sql);
        return ($result->num_rows === 0);
    }





} 