<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;


class SummitPostClimberLogPage
    extends Lib\KeyValueData
{
    public $pageId;
    public $routeName;
    public $objectURL;
    public $logURL;
    public $date;
    public $title;
    public $success;
    public $message;

    const PAGE_ID = "id";
    const ROUTE_NAME = "route_name";
    const OBJECT_URL = "object_url";
    const LOG_URL = "log_url";
    const DATE = "date";
    const TITLE = "title";
    const SUCCESS = "success";
    const MESSAGE = "message";
    const FILE_PREFIX = "climber_logs";

    function __construct()
    {
        $this->filePrefix = self::FILE_PREFIX;
    }

    public function readKeyValueLine($line){
        $line = $this->cleanText($line);
        $this->readKeyValue($line);
    }

    public function readKeyValue($line){
        $this->setCurrentKeyValue($line);

        switch ($this->currentKey)
        {
            case self::PAGE_ID:
                $this->pageId = $this->currentValue;
                break;
            case self::ROUTE_NAME:
                $this->routeName = $this->currentValue;
                break;
            case self::OBJECT_URL:
                $this->objectURL = $this->currentValue;
                break;
            case self::LOG_URL:
                $this->logURL = $this->currentValue;
                break;
            case self::DATE:
                $this->date = $this->currentValue;
                break;
            case self::TITLE:
                $this->title = $this->currentValue;
                break;
            case self::SUCCESS:
                $this->success = $this->currentValue;
                break;
            case self::MESSAGE:
                $this->message = $this->currentValue;
                break;
            default:
        }
    }

    public function isFilled(){
        return ($this->pageId !== null &&
            $this->routeName !== null &&
            $this->objectURL !== null &&
            $this->logURL !== null &&
            $this->date !== null &&
            $this->title !== null &&
            $this->success !== null &&
            $this->message !== null);
    }

    public function multipleRecordsInFile(){
        return true;
    }


    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_summitpost_climber_log (
                        page_id,
                        route_name,
                        object_url,
                        log_url,
                        date,
                        title,
                        success,
                        message)
                    VALUES (
                        :pageId,
                        :routeName,
                        :objectURL,
                        :logURL,
                        :date,
                        :title,
                        :success,
                        :message)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':routeName' => $this->routeName,
            ':objectURL' => $this->objectURL,
            ':logURL' => $this->logURL,
            ':date' => $this->date,
            ':title' => $this->title,
            ':success' => $this->success,
            ':message' => $this->message
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_summitpost_climber_log
                SET
                    route_name = :routeName,
                    object_url = :objectURL,
                    log_url = :logURL,
                    date = :date,
                    title = :title,
                    success = :success,
                    message = :message
                WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':routeName' => $this->routeName,
            ':objectURL' => $this->objectURL,
            ':logURL' => $this->logURL,
            ':date' => $this->date,
            ':title' => $this->title,
            ':success' => $this->success,
            ':message' => $this->message
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_summitpost_climber_log
                  WHERE page_id = :pageId
                  AND date = :date
                  AND title = :title";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':date' => $this->date,
            ':title' => $this->title
        ]);
        return ($query->fetch()->num_rows > 0);
    }



    public static function factory(){
        return new SummitPostClimberLogPage();
    }
}