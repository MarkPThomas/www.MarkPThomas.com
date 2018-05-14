<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/29/18
 * Time: 10:10 AM
 */

namespace markpthomas\library;


class MySqliManager {

    /**
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     * @see https://websitebeaver.com/prepared-statements-in-php-mysqli-to-prevent-sql-injection
     * @return \mysqli
     */
    public static function mySqliObject($host, $user, $password, $database){
        $mysqli = new \mysqli($host, $user, $password, $database);
        if ($mysqli->connect_errno) {
            MyLogger::log('Connection failed: <br >' . $mysqli->connect_error . '<br >');
            die('Connection failed: <br >' . $mysqli->connect_error . '<br >');
        }
        $mysqli->set_charset("utf8");
        return $mysqli;
    }

    /**
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     * @return \mysqli
     */
    public static function mySqliProcedural($host, $user, $password, $database){
        $connection = \mysqli_connect($host, $user, $password, $database);
        if (mysqli_connect_errno()) {
            MyLogger::log('Connection failed: <br >' . mysqli_connect_error() . '<br >');
            die('Connection failed: <br >' . mysqli_connect_error() . '<br >');
        }
        mysqli_set_charset($connection, 'utf8');

        return $connection;
    }

    /**
     * Writes the object data of all objects to the appropriate MySQL table.
     *
     * Each call to this method will open a new connection to the database.
     *
     * @param array $items An array of objects implementing IMySQLInsert.
     * @param \mysqli $mysqli
     * @param bool $closeConnection
     * @return void
     */
    public static function insertItemsToMySql(array $items, \mysqli $mysqli, $closeConnection = true){
        MyLogger::log('Writing to database...<br />');

        foreach ($items as $item){
            if (!self::insertItemToMySql($mysqli, $item)) { break; }
        }
        if ($closeConnection) $mysqli->close();
        MyLogger::log('Writing complete. Connection is closed. <br />');
    }

    /**
     * Writes the object data to the appropriate MySQL table.
     * @param \mysqli $mysqli MySQL connection to use.
     * @param IMySqlInsert $item Object containing data to insert into a MySQL table.
     * @return bool True if operation was successful.
     */
    public static function insertItemToMySql(\mysqli $mysqli, IMySqlInsert $item){
        if (!self::mySqlExist($mysqli, $item)) {
            $query = $item->mySqlInsert($mysqli);
            if ($mysqli->query($query) || $query === true){
                MyLogger::log($mysqli->affected_rows . ' Row inserted.<br />');
            } else {
                try {
                    throw new \Exception('MySQL error ' . $mysqli->error . '<br /> Query:<br /> ' . $query, $mysqli->errno);
                } catch(\Exception $e ) {
                    MyLogger::log('Error No: ' . $e->getCode() . ' - ' . $e->getMessage() . '<br />');
                    MyLogger::log(nl2br($e->getTraceAsString()));
                }
                return false;
            }
        } else {
            MyLogger::log("Entry already exists in MySQL. Ignoring... <br />");
        }
        return true;
    }


    public static function updateItemsInMySQL(array $items, \mysqli $mysqli, $closeConnection = true){
        MyLogger::log('Updating entries in database...<br >');

        foreach ($items as $item){
            if (!self::updateItemInMySQL($mysqli, $item)) { break; }
        }
        if ($closeConnection) $mysqli->close();
        MyLogger::log('Writing complete. Connection is closed. <br />');
    }


    public static function updateItemInMySQL(\mysqli $mysqli, IMySqlUpdate $item){
        if ($item instanceof IMySqlInsert){
            if (self::mySqlExist($mysqli, $item) && $item instanceof IMySqlUpdate) {
                $query = $item->mySqlUpdate($mysqli);
                if ($mysqli->query($query)){
                    MyLogger::log($mysqli->affected_rows . ' Row updated.<br >');
                } else {
                    try {
                        throw new \Exception('MySQL error ' . $mysqli->error . '<br> Query:<br> ' . $query, $mysqli->errno);
                    } catch(\Exception $e ) {
                        MyLogger::log('Error No: ' . $e->getCode() . ' - ' . $e->getMessage() . '<br >');
                        MyLogger::log(nl2br($e->getTraceAsString()));
                    }
                    return false;
                }
            } else {
                return self::insertItemToMySql($mysqli, $item);
            }
            return true;
        }
        return false;
    }

    /**
     * Determines if a given MySQL record already exists in the database.
     * @param \mysqli $mysqli MySQL connection to use.
     * @param IMySqlInsert $item Object containing data to insert into a MySQL table.
     * @return bool True if the record already exists.
     */
    public static function mySqlExist(\mysqli $mysqli, IMySqlInsert $item){
        $query = $item->mySqlExist($mysqli);
        MyLogger::log($query . '<br >');
        $result = $mysqli->query($query);

        if ($result->num_rows > 0){
            $data = $result->fetch_assoc();
            $numberOfRecords = $data['COUNT(*)'];
            MyLogger::log($numberOfRecords . " records <br >");
            return ($numberOfRecords != 0);
        }
        return false;
    }

    public static function clearTable(\mysqli $mysqli, $tableName, $idName = 'id'){
        $query = "SELECT * FROM $tableName";
        MyLogger::log($query . '<br />');
        $result = $mysqli->query($query);
        $ids = [];
        while ($row = $result->fetch_assoc()){
            array_push($ids, $row[$idName]);
        }
        foreach ($ids as $id){
            self::deleteFromTable($mysqli, $tableName, $id, $idName);
        }
    }

    public static function deleteFromTable(\mysqli $mysqli, $tableName, $idValue, $idName = 'id'){
        $query = "DELETE FROM $tableName WHERE $idName = $idValue";
        MySqliManager::mySQLDeleteConfirmation($mysqli, $query);
    }

    public static function mySqlIsUniqueKey(\mysqli $mysqli, $tableName, array $queryHash){
        if (count($queryHash) === 0) return false;
        $query = "SELECT * FROM $tableName WHERE $queryHash[0][0] = $queryHash[0][1]";
        for ($i = 1; $i < count($queryHash); $i++){
            $query .= " AND $queryHash[$i][0] = $queryHash[$i][1]";
        }
        $result = $mysqli->query($query);
        return ($result->num_rows === 0);
    }

    public static function mySQLDeleteConfirmation(\mysqli $mysqli, $query){
        if ($mysqli->query($query)){
            writeToConsole($mysqli->affected_rows . ' Row deleted.<br />');
        } else {
            try {
                throw new \Exception('MySQL error ' . $mysqli->error . '<br /> Query:<br /> ' . $query, $mysqli->errno);
            } catch(\Exception $e ) {
                writeToConsole('Error No: ' . $e->getCode() . ' - ' . $e->getMessage() . '<br />');
                writeToConsole(nl2br($e->getTraceAsString()));
            }
            return false;
        }
        return true;
    }

    public static function mySQLInsertConfirmation(\mysqli $mysqli, $query, $hasAutoIncrement = true){
        if ($mysqli->query($query)){
            writeToConsole($mysqli->affected_rows . ' Row inserted.<br />');
        } else {
            try {
                throw new \Exception('MySQL error ' . $mysqli->error . '<br /> Query:<br /> ' . $query, $mysqli->errno);
            } catch(\Exception $e ) {
                writeToConsole('Error No: ' . $e->getCode() . ' - ' . $e->getMessage() . '<br />');
                writeToConsole(nl2br($e->getTraceAsString()));
            }
            return false;
        }
        return $hasAutoIncrement? $mysqli->insert_id : 1;
    }

    public static function mySQLSelectConfirmation(\mysqli $mysqli, $query){
        $result = $mysqli->query($query);
        if ($result){
            writeToConsole($mysqli->affected_rows . ' Rows returned.<br />');
        } else {
            try {
                throw new \Exception('MySQL error ' . $mysqli->error . '<br /> Query: <br /> ' . $query, $mysqli->errno);
            } catch(\Exception $e ) {
                writeToConsole('Error No: ' . $e->getCode() . ' - ' . $e->getMessage() . '<br />');
                writeToConsole(nl2br($e->getTraceAsString()));
            }
            return false;
        }
        return $result;
    }

    public static function mySQLTransactionConfirmation($result){
        if (!$result){
            throw new \Exception('Transaction failed... <br />');
        }
    }
} 