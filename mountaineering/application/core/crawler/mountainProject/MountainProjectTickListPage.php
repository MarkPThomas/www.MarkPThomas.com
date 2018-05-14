<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;


class MountainProjectTickListPage
    extends Lib\KeyValueData
{
    public $pageURL;
    public $name;
    public $tickDate;
    public $comments;

    const PAGE_URL = "route_url";
    const NAME = "route_name";
    const TICK_DATE = "route_tick_date";
    const COMMENTS = "route_comments";
    const FILE_PREFIX = "tick_list";

    function __construct()
    {
        $this->filePrefix = self::FILE_PREFIX;
    }

    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_mountainproject_tick_list (
                        url,
                        name,
                        tick_date,
                        comments)
                    VALUES (
                        :url,
                        :name,
                        :tickDate,
                        :comments)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':tickDate' => $this->tickDate,
            ':comments' => $this->comments
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_mountainproject_tick_list
                SET
                    name = :name,
                    tick_date = :tickDate,
                    comments = :comments
                WHERE url = :url";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':tickDate' => $this->tickDate,
            ':comments' => $this->comments
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_mountainproject_tick_list
                  WHERE url = :url
                  AND tick_date = :tickDate";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':url' => $this->pageURL,
            ':tickDate' => $this->tickDate]);
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
            case self::TICK_DATE:
                $this->tickDate = $this->currentValue;
                break;
            case self::COMMENTS:
                $this->comments = $this->currentValue;
                break;
            default:
        }
    }

    public function isFilled(){
        return ($this->pageURL !== null &&
            $this->name !== null &&
            $this->tickDate !== null &&
            $this->comments !== null);
    }

    public function multipleRecordsInFile(){
        return true;
    }


    public static function factory(){
        return new MountainProjectTickListPage();
    }
}