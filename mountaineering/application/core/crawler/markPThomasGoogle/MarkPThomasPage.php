<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;

/**
 * Reads & writes raw crawler data from files to the scraper_markpthomas_page table.
 * @package markpthomas\crawler\markPThomasGoogle
 */
class MarkPThomasPage
    extends ReportBase
{
    const PAGE_ID = "page_id";
    const PAGE_MENU_NAME = "page_menu_name";
    const TITLE = "title";
    const PAGE_URL = "page_url";
    const TYPE = "type";

    /**
     * @param string $filePrefix First part of the text filename used to identify the text files to read.
     * <br /> Example:
     * <br /> Article_
     * <br /> Trip_Report_
     */
    function __construct($filePrefix = "")
    {
        $this->filePrefix = $filePrefix;
        $this->keysForSingleLineValues =
            [
                self::PAGE_ID,
                self::PAGE_MENU_NAME,
                self::PAGE_URL,
                self::TITLE,
                self::TYPE,
            ];
    }

    public static function factory(){
        return new MarkPThomasPage();
    }

    public function readKeyValueLine($line){
        $line = $this->cleanText($line);
        $this->readKeyValueLineAndContent($line);
    }


    public function readKeyValue($line){
        $this->setCurrentKeyValue($line);

        switch ($this->currentKey)
        {
            case self::PAGE_ID:
                $this->pageId = $this->currentValue;
                break;
            case self::PAGE_MENU_NAME:
                $this->pageMenuName = $this->currentValue;
                break;
            case self::PAGE_URL:
                $this->pageURL = $this->currentValue;
                break;
            case self::TITLE:
                $this->name = $this->currentValue;
                break;
            case self::TYPE:
                $this->type = $this->currentValue;
                break;
            default:
        }
    }


    public function isFilled(){
        return ($this->pageId !== null &&
            $this->pageMenuName !== null &&
            $this->pageURL !== null &&
            $this->name !== null &&
            $this->type !== null);
    }


    public function multipleRecordsInFile(){
        return false;
    }


    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_markpthomas_page (
                        page_id,
                        page_menu_name,
                        url,
                        title,
                        type,
                        content)
                    VALUES (
                        :page_id,
                        :page_menu_name,
                        :url,
                        :title,
                        :type,
                        :content)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':page_menu_name' => $this->pageMenuName,
            ':url' => $this->pageURL,
            ':title' => $this->name,
            ':type' => $this->type,
            ':content' => $this->content
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_markpthomas_page
                SET
                    page_menu_name = :page_menu_name,
                    url = :url,
                    title = :title,
                    type = :type,
                    content = :content
                WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':page_menu_name' => $this->pageMenuName,
            ':url' => $this->pageURL,
            ':title' => $this->name,
            ':type' => $this->type,
            ':content' => $this->content
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_markpthomas_page
                  WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $this->pageId]);
        return ($query->fetch()->num_rows > 0);
    }

    public function mySqlFill(\PDO $mysqlPdo, $pageId){
        $sql = "SELECT *
                FROM scraper_markpthomas_page
                WHERE page_id=:pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $pageId]);
        $result = $query->fetch();

        if ($result){
            $this->pageId = $pageId;
            $this->name = $result->title;
            $this->pageMenuName = $result->page_menu_name;
            $this->content = $result->content;
            $this->type = $result->type;
            $this->pageURL = $result->url;
            return true;
        }
        return false;
    }

    public function insertCrawlerId(\PDO $mysqlPdo, $pageId){
        // No action taken for this page.
    }

    public function toReport($keepOldUrl = false){
        $tripReport = new Report($this->name, $this->type, $this->content, $this->pageMenuName, $this->pageURL);

        return $tripReport;
    }
}