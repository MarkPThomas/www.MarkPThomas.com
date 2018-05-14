<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;


class MountainProjectTodoListPage
    extends Lib\KeyValueData
{
    public $pageURL;
    public $name;

    const PAGE_URL = "route_url";
    const NAME = "route_name";
    const FILE_PREFIX = "todo_list";

    function __construct()
    {
        $this->filePrefix = self::FILE_PREFIX;
    }

    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_mountainproject_todo_list (
                        name,
                        url)
                    VALUES (
                        :name,
                        :url)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':name' => $this->name,
            ':url' => $this->pageURL
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_mountainproject_todo_list
                SET
                    name = :name
                WHERE url = :url";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':name' => $this->name,
            ':url' => $this->pageURL
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_mountainproject_todo_list
                  WHERE url = :url";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':url' => $this->pageURL]);
        return ($query->fetch()->num_rows > 0);
    }

    public function readKeyValueLine($line){
        $line = $this->cleanText($line);
        $this->readKeyValue($line);
    }

    public function readKeyValue($line){
        $this->setCurrentKeyValue($line);

        switch ($this->currentKey)
        {
            case self::PAGE_URL:
                $this->pageURL = $this->currentValue;
                $this->tableIdentifier = $this->pageURL;
                break;
            case self::NAME:
                $this->name = $this->currentValue;
                break;
            default:
        }
    }

    public function isFilled(){
        return ($this->pageURL !== null &&
            $this->name !== null);
    }

    public function multipleRecordsInFile(){
        return true;
    }


    public static function factory(){
        return new MountainProjectTodoListPage();
    }
}