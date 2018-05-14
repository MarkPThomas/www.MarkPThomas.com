<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/17/18
 * Time: 10:16 AM
 */

namespace markpthomas\mountaineering;

use markpthomas\library as Lib;
use markpthomas\crawler as Crawler;

class CrawlerModel {
    /**
     * Just has the most basic uses of some older refactored code, to show how to still use it and ensure everything has been reconnected.
     */
    public static function demoUseWriteCrawlerDataFromFile(){
        $directoryPath = "/MarkPThomasScrape/bin/data-hold/markPThomas/";
        $page = new Crawler\MarkPThomasPage("Article_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\MarkPThomasPage("Trip_Report_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $directoryPath = "/MtnProjScrape/bin/data-hold/mountainProject/";
        $page = new Crawler\MountainProjectAreaPage();
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\MountainProjectRouteOverwritePage();
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\MountainProjectRoutePage();
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\MountainProjectTickListPage();
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\MountainProjectTodoListPage();
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);



        $directoryPath = "/SummitPostScrape/bin/data-hold/summitPost/";
        $page = new Crawler\SummitPostClimberLogPage();
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SummitPostObjectPage("Canyon_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SummitPostObjectPage("my_Canyon_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SummitPostObjectPage("Mountain-Rock_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SummitPostObjectPage("my_Mountain-Rock_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SummitPostObjectPage("Route_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SummitPostObjectPage("my_Route_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SummitPostObjectPage("my_Area-Range_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SummitPostObjectPage("my_Article_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SummitPostObjectPage("my_Trip_Report_");
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);



        $directoryPath = "/SuperTopoScrape/bin/data-hold/superTopo/";
        $page = new Crawler\SuperTopoReportPage();
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SuperTopoReportSummaryPage();
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SuperTopoRoutePage();
        CrawlerModel::writeAllRawFileDataToDatabase(__DIR__ . $directoryPath, $page);


        $filePath = "/PeakBaggerScrape/";
        $bufferCount = 2000;
        $fileName = "scraper_peakbagger_peak.csv";
        $pageCsv = new Crawler\PeakBaggerPeakPage();
        Lib\CsvManager::insertCsvFileToMySql(__DIR__ . $filePath, $fileName, $pageCsv, $bufferCount);

        $fileName = "scraper_peakbagger_range.csv";
        $pageCsv = new Crawler\PeakBaggerRangePage();
        Lib\CsvManager::insertCsvFileToMySql(__DIR__ . $filePath, $fileName, $pageCsv, $bufferCount);
    }


    /**
     * Parses the trip report data in the matching files within the specified directory and writes the data to MySQL.
     * @param string $directoryPath Directory path to search within to gather all of the file names.
     * @param Lib\KeyValueData $page Data object to fill with data from the text file.
     */
    public static function writeAllRawFileDataToDatabase($directoryPath, Lib\KeyValueData $page){
        $fileNames = self::getAllFileNames($directoryPath, $page);
        $pages = self::loadCrawlerData($directoryPath, $fileNames, $page);
        for($i = 0; $i < count($pages); $i++){
            self::writeRawDataToDatabase($pages[$i]);
        }
    }

    /**
     * Parses the trip report data in the matching files within the specified directory and writes the data to MySQL.
     * @param string $directoryPath Directory path to search within to gather all of the file names.
     * @param string $fileNameTarget Name of the file name to target for writing.
     * @param Lib\KeyValueData $page Data object to fill with data from the text file.
     */
    public static function writeRawFileDataToDatabase($directoryPath, $fileNameTarget, Lib\KeyValueData $page){
        $fileNames = self::getAllFileNames($directoryPath, $page);
        foreach ($fileNames as $fileName){
            if ($fileName === '/'. $fileNameTarget){
                $page = self::loadCrawlerData($directoryPath, [$fileName], $page)[0];
                self::writeRawDataToDatabase($page);
                return;
            }
        }
    }


    /**
     * Writes the raw crawler page data to the appropriate database table.
     * @param Lib\KeyValueData $page
     */
    public static function writeRawDataToDatabase(Lib\KeyValueData $page){
        Lib\MyLogger::log("================================<br />");
        Lib\MyLogger::log("================================<br />");
        Lib\MyLogger::log('Writing to database...<br />');
        Lib\MyLogger::log("================================<br />");
        if (!empty($page->fileName)){
            Lib\MyLogger::log('File: ' . $page->fileName . "<br />");
        }
        if (!empty($page->name)){
            Lib\MyLogger::log('Name: ' . $page->name . "<br />");
        }
        if (!empty($page->pageURL)){
            Lib\MyLogger::log('URL: ' . $page->pageURL . "<br />");
        } elseif (!empty($page->objectURL)){
            Lib\MyLogger::log('URL: ' . $page->objectURL . "<br />");
        }
        Lib\MyLogger::log("================================<br />");

        $database = DatabaseFactory::getFactory()->getConnection();
        Lib\MyPDOManager::updateItemInMySQL($database, $page);

        Lib\MyLogger::log("================================<br />");
        Lib\MyLogger::log('Writing complete. Connection is closed. <br />');
        Lib\MyLogger::log("================================<br /><br /><br />");
    }



    /**
     * @param string $directoryPath
     * @param Lib\KeyValueData $page
     * @return array|mixed
     */
    public static function getAllFileNames($directoryPath, Lib\KeyValueData $page){
        Lib\MyLogger::log('Getting filenames... <br />');
        return Lib\KeyValueManager::readKeyValueFileNames($directoryPath, $page);
    }

    /**
     * @param string $directoryPath
     * @param array $fileNames
     * @param Lib\KeyValueData $page
     * @return array|mixed
     */
    public static function loadCrawlerData($directoryPath, array $fileNames, Lib\KeyValueData $page){
        Lib\MyLogger::log('Reading data to pages... <br />');
        return Lib\KeyValueManager::fillItemsWithKeyValueData($directoryPath, $fileNames, $page);
    }
} 