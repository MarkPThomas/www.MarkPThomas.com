<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;


class SuperTopoReportSummaryPage
    extends Lib\KeyValueData
{
    public $pageId;
    public $pageURL;
    public $name;
    public $hits;
    public $messages;

    const PAGE_ID = "id";
    const PAGE_URL = "report_url";
    const NAME = "report_name";
    const HITS = "hits";
    const MESSAGES = "messages";
    const FILE_PREFIX = "reports_summaries";

    function __construct()
    {
        $this->filePrefix = self::FILE_PREFIX;
    }


    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_supertopo_report_summary (
                        page_id,
                        url,
                        name,
                        hits,
                        messages)
                    VALUES (
                        :pageId,
                        :url,
                        :name,
                        :hits,
                        :messages)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':hits' => $this->hits,
            ':messages' => $this->messages
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_supertopo_report_summary
                SET
                    url = :url,
                    name = :name,
                    hits = :hits,
                    messages = :messages
                WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':hits' => $this->hits,
            ':messages' => $this->messages
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_supertopo_report_summary
                  WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $this->pageId]);
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
            case self::PAGE_ID:
                $this->pageId = $this->currentValue;
                break;
            case self::PAGE_URL:
                $this->pageURL = $this->currentValue;
                break;
            case self::NAME:
                $this->name = $this->currentValue;
                break;
            case self::HITS:
                $this->currentValue = str_replace(",", "", $this->currentValue);
                $this->hits = $this->currentValue;
                break;
            case self::MESSAGES:
                $this->currentValue = str_replace(",", "", $this->currentValue);
                $this->messages = $this->currentValue;
                break;
            default:
        }
    }

    public function isFilled(){
        return ($this->pageId !== null &&
            $this->pageURL !== null &&
            $this->name !== null &&
            $this->hits !== null &&
            $this->messages !== null);
    }

    public function multipleRecordsInFile(){
        return true;
    }

    public static function factory(){
        return new SuperTopoReportSummaryPage();
    }
}