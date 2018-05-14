<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;


class SuperTopoReportPage
    extends ReportBase
{
    const PAGE_ID = "id";
    const PAGE_NAME = "name";
    const PAGE_URL = "url";
    const FILE_PREFIX = "report_t";

    function __construct()
    {
        $this->externalSiteStub = 'http://www.supertopo.com';
        $this->filePrefix = self::FILE_PREFIX;
        $this->keysForSingleLineValues =
            [
                SuperTopoReportPage::PAGE_ID,
                SuperTopoReportPage::PAGE_NAME,
                SuperTopoReportPage::PAGE_URL,
            ];
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
            case self::PAGE_NAME:
                $this->name = $this->currentValue;
                break;
            case self::PAGE_URL:
                $this->pageURL = $this->currentValue;
                break;
            default:
        }
    }

    public function isFilled(){
        return ($this->pageId !== null &&
            $this->name !== null &&
            $this->pageURL !== null);
    }

    public function multipleRecordsInFile(){
        return false;
    }

    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_supertopo_report (
                        page_id,
                        url,
                        name,
                        content)
                    VALUES (
                        :pageId,
                        :url,
                        :name,
                        :content)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':content' => $this->content
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_supertopo_report
                SET
                    url = :url,
                    name = :name,
                    content = :content
                WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':content' => $this->content
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_supertopo_report
                  WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $this->pageId]);
        return ($query->fetch()->num_rows > 0);
    }

    public function mySqlFill(\PDO $mysqlPdo, $pageId){
        $sql = "SELECT *
                FROM scraper_supertopo_report
                WHERE page_id=:pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $pageId]);
        $result = $query->fetch();

        if ($result){
            $this->pageId = $pageId;
            $this->name = $result->name;
            $this->content = $result->content;
            $this->pageURL = $result->url;
            return true;
        }
        return false;
    }

    public function insertCrawlerId(\PDO $mysqlPdo, $pageId){
        if (empty($this->pageId)) return;

        $sql = "UPDATE page
                SET id_superTopo = :id
                WHERE id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $pageId,
            ':id' => $this->pageId
        ]);
    }

    public static function associateCrawlerIdsWithPages(\PDO $mysqlPdo){
        // 1. Get all crawler IDs and names
        $crawlerIds = [];
        $crawlerNames = [];

        $sql = 'SELECT page_id, name FROM scraper_supertopo_report';
        $query = $mysqlPdo->prepare($sql);
        $query->execute();
        $results = $query->fetchAll();
        foreach ($results as $result){
            $crawlerIds[] = $result->page_id;
            $crawlerNames[] = $result->name;
        }

        // 2. Get any matching page by name
        for ($j = 0; $j < count($crawlerIds); $j++){
            $sql = 'SELECT * FROM page WHERE title_full = :name';
            $query = $mysqlPdo->prepare($sql);
            $query->execute([':name' => $crawlerNames[$j]]);

            // 3. If there is only one match, write the crawler ID
            if ($query->rowCount() == 1){
                $pageId = $query->fetch()->id;

                $sql = "UPDATE page
                        SET id_superTopo = :id
                        WHERE id = :pageId";
                $query = $mysqlPdo->prepare($sql);
                $query->execute([
                    ':pageId' => $pageId,
                    ':id' => $crawlerIds[$j]
                ]);
            }
        }
    }

    public function toReport($keepOldUrl = false){
        if ($keepOldUrl){
            $pageUrl = $this->pageURL;
        } else {
            $urlComponents = explode('/', $this->pageURL);
            $pageUrl = '/mountaineering/trip-reports/' . $urlComponents[2];
        }

        $tripReport = new Report($this->name, 'Standard', $this->content, $this->name, $pageUrl, $this->externalSiteStub);

        return $tripReport;
    }

    public static function factory(){
        return new SuperTopoReportPage();
    }
}