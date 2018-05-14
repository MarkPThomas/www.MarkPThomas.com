<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 4/5/18
 * Time: 11:05 AM
 */

namespace markpthomas\mountaineering;

use markpthomas\gis\Session;
use markpthomas\library as Lib;
use markpthomas\crawler as Crawler;


class CrawlerReportModel {
    // TODO: To be deprecated. Only used for cleaning methods.
    private static $domainUrlStub = 'http://www.markpthomas.com';

    // TODO: Note in a better place that for SuperTopo URLs, all  '/tr/{name-of-report}/t{id}n.html' can work as '/tripreport/tripreport.php?articleid={id}'

    const summitPost = 'SummitPost';
    const superTopo = 'SuperTopo';
    const markPThomas = 'MarkPThomas';


// ========== Parse/Add Crawler data ============
    /**
     * Just has the most basic uses of some older refactored code, to show how to still use it and ensure everything has been reconnected.
     */
    public static function demoUseWriteParsedCrawlerDataFromFile(){
        $directoryPath = "/MarkPThomasScrape/bin/data-hold/markPThomas/";
        $page = new Crawler\MarkPThomasPage("Article_");
        CrawlerReportModel::writeAllParsedFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\MarkPThomasPage("Trip_Report_");
        CrawlerReportModel::writeAllParsedFileDataToDatabase(__DIR__ . $directoryPath, $page);


        $directoryPath = "/SummitPostScrape/bin/data-hold/summitPost/";
        $page = new Crawler\SummitPostObjectPage("my_Article_");
        CrawlerReportModel::writeAllParsedFileDataToDatabase(__DIR__ . $directoryPath, $page);

        $page = new Crawler\SummitPostObjectPage("my_Trip_Report_");
        CrawlerReportModel::writeAllParsedFileDataToDatabase(__DIR__ . $directoryPath, $page);


        $directoryPath = "/SuperTopoScrape/bin/data-hold/superTopo/";
        $page = new Crawler\SuperTopoReportPage();
        CrawlerReportModel::writeAllParsedFileDataToDatabase(__DIR__ . $directoryPath, $page);
    }

    /**
     * Just has the most basic uses, to show how to still use it and ensure everything has been reconnected.
     * @param string $source Name of the source domain that the page is located at.
     * @param bool $isArticle True if the data is for an article rather than a report.
     */
    public static function writeParsedFileDataToDatabase($source, $isArticle = false){
        /**
         * @var Crawler\ReportBase $page
         */
        $page = null;
        $directoryPath = '';
        switch ($source)
        {
            case self::superTopo:
                $directoryPath = Config::get('PATH_CRAWLER_DATA') . 'superTopo/reports';

                $page = new Crawler\SuperTopoReportPage();
                break;
            case self::summitPost:
                $directoryPath = $isArticle?
                    Config::get('PATH_CRAWLER_DATA') . 'summitPost/articles' :
                    Config::get('PATH_CRAWLER_DATA') . 'summitPost/reports';
                $page = new Crawler\SummitPostObjectPage();
                break;
            case self::markPThomas:
                $directoryPath = Config::get('PATH_CRAWLER_DATA') . 'markPThomas/reports';
                $page = new Crawler\MarkPThomasPage();
                break;
        }
        self::writeAllParsedFileDataToDatabase($directoryPath, $page);
    }

    /**
     * Parses the trip report data in the matching files within the specified directory and writes the data to MySQL.
     * @param string $directoryPath Directory path to search within to gather all of the file names.
     * @param Crawler\ReportBase $page Data object to fill with data from the text file.
     */
    public static function writeAllParsedFileDataToDatabase($directoryPath, Crawler\ReportBase $page){
        if (empty($page) || empty($directoryPath)) return;

        $fileNames = CrawlerModel::getAllFileNames($directoryPath, $page);
        $pages = CrawlerModel::loadCrawlerData($directoryPath, $fileNames, $page);
        for($i = 0; $i < count($pages); $i++){
            self::writeParsedDataToDatabase($pages[$i], $fileNames[$i]);
        }
    }

    /**
     * Reads the specified file and writes the data to the corresponding crawler table.
     * If $pageID is blank, all files within the appropriate site & type directory will be read and written.
     * @param string $pageId Page ID corresponding with the page for a given source.
     * @param string $source Name of the source domain that the page is located at.
     * @param bool $isArticle True if the data is for an article rather than a report.
     */
    public static function writeRawFileDataToDatabase($pageId, $source, $isArticle = false){
        /**
         * @var Crawler\ReportBase $page
         */
        $page = null;
        $directory = '';
        $fileNamePrefix = '';
        switch ($source)
        {
            case self::superTopo:
                $page = new Crawler\SuperTopoReportPage();
                $directory = 'superTopo/reports';
                $fileNamePrefix = 'report_';
                break;
            case self::summitPost:
                $page = new Crawler\SummitPostObjectPage();
                $directory = $isArticle? 'summitPost/articles' : 'summitPost/reports';
                $fileNamePrefix = $isArticle? 'my_Article_' : 'my_Trip Report_';
                break;
            case self::markPThomas:
                $page = new Crawler\MarkPThomasPage();
                $directory = $isArticle? 'markPThomas/articles' : 'markPThomas/reports';
                $fileNamePrefix = $isArticle? 'Article_' : 'Trip_Report_';
                break;
        }

        if (empty($pageId)){
            CrawlerModel::writeAllRawFileDataToDatabase(Config::get('PATH_CRAWLER_DATA') . $directory, $page);
        } else {
            $fileName = $fileNamePrefix . $pageId . '.txt';
            CrawlerModel::writeRawFileDataToDatabase(Config::get('PATH_CRAWLER_DATA') . $directory, $fileName, $page);
        }
    }


    /**
     * Just has the most basic uses, to show how to still use it and ensure everything has been reconnected.
     * @param string $pageId Page ID corresponding with the page for a given source.
     * @param string $source Name of the source domain that the page is located at.
     * @return bool|int Returns the page ID if successful, false otherwise.
     */
    public static function writeParsedPageToDatabaseBySource($pageId, $source){
        /**
         * @var Crawler\ReportBase $page
         */
        $page = null;
        switch ($source)
        {
            case self::superTopo:
                $page = new Crawler\SuperTopoReportPage();
                break;
            case self::summitPost:
                $page = new Crawler\SummitPostObjectPage();
                break;
            case self::markPThomas:
                $page = new Crawler\MarkPThomasPage();
                break;
        }

        if (self::writeParsedPageToDatabase($pageId, $page)){
            $database = DatabaseFactory::getFactory()->getConnection();

            // Get the recently created page ID
            $sql = 'SELECT max(id) AS latest_id FROM page';
            $query = $database->prepare($sql);
            $query->execute();
            $newPageID = $query->fetch()->latest_id;

            // Write the crawler page ID into the page table in order to associate the two.
            $page->insertCrawlerId($database, $newPageID);

            return $newPageID;
        }
        return false;
    }

    /**
     * Matches pages by name to any crawled pages and associates the two via the crawler's ID.
     */
    public static function associateCrawlerIdsWithPages(){
        $database = DatabaseFactory::getFactory()->getConnection();

        // For each crawler type:
        Crawler\SuperTopoReportPage::associateCrawlerIdsWithPages($database);
        Crawler\SummitPostObjectPage::associateCrawlerIdsWithPages($database);
    }


    public static function associateCrawlerIdsWithPagesByReferences(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id
                FROM report_trip";
        $query = $database->prepare($sql);
        $query->execute();
        $results = $query->fetchAll();

        // For each trip report
        foreach($results as $result){
            if (!isset($result->id)) continue;
            $urls = ReferenceModel::getReportExternalReferenceUrlsByReportId($result->id);
            if (!$urls || count($urls) == 0) continue;

            foreach($urls as $url){
                $pageId = '';

                // ArticleId Case
                $urlComponents = explode('?', $url);
                if (count($urlComponents) > 1){
                    $pageId = end($urlComponents);
                    $pageId = str_replace('articleid=', '', $pageId);
                    $pageId = 't' . $pageId . 'n';
                }

                // General Case
                if (empty($pageId)){
                    $urlComponents = explode('/', $url);
                    if (count($urlComponents) > 1){
                        $pageId = end($urlComponents);
                        $pageId = str_replace('.html', '', $pageId);
                    }
                }

                if (empty($pageId)) continue;

                // Get page ID to update
                $sql = "SELECT page_id
                                FROM report_trip
                                WHERE id = :id";
                $query = $database->prepare($sql);
                $query->execute([':id' => $result->id]);

                $pageUpdateId = $query->fetch()->page_id;

                // Add appropriate external page reference
                if ($pageId[0] === 't' && substr($pageId, -1) == 'n'){
                    // Check if submitted to SuperTopo
                    $sql = "SELECT page_id
                            FROM scraper_supertopo_report
                            WHERE page_id = :pageId";
                    $query = $database->prepare($sql);
                    $query->execute([':pageId' => $pageId]);

                    $results = $query->fetchAll();
                    if (count($results) > 0){
                        $sql = "UPDATE page
                                SET id_superTopo = :pageId
                                WHERE id = :id";
                        $query = $database->prepare($sql);
                        $query->execute([
                            ':pageId' => $pageId,
                            ':id' => $pageUpdateId
                        ]);
                    }
                } else {
                    // Check if submitted to SummitPost
                    $sql = "SELECT page_id
                            FROM scraper_summitpost_object
                            WHERE page_id = :pageId";
                    $query = $database->prepare($sql);
                    $query->execute([':pageId' => $pageId]);

                    $results = $query->fetchAll();
                    if (count($results) > 0){
                        $sql = "UPDATE page
                                SET id_summitPost = :pageId
                                WHERE id = :id";
                        $query = $database->prepare($sql);
                        $query->execute([
                            ':pageId' => $pageId,
                            ':id' => $pageUpdateId
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Writes the crawler page specified to a standard format for the web site.
     * @param string $pageId Page ID of the page to write.
     * @param Crawler\ReportBase $page Page object that corresponds to the crawler data to convert from.
     * This will be modified to contain the data used to create the page.
     * @return bool True if a new page was successfully created.
     */
    public static function writeParsedPageToDatabase($pageId, Crawler\ReportBase &$page){
        if (empty($page) || empty ($pageId)) return false;

        $database = DatabaseFactory::getFactory()->getConnection();

        /**
         * @var Crawler\ReportBase $page
         */
        $page = $page::factory();
        if ($page->mySqlFill($database, $pageId))
        {
            $report = $page->toReport();
            $parsedReport = $report->parseContent();
            return TripReportsModel::createTripReport($parsedReport);
        }
        Session::add('feedback_negative', Text::get('FEEDBACK_SCRAPER_FILL_FAIL') . ' ' . $pageId);
        return false;
    }

    /**
     * Parses the trip report data in the page objects and adds them to a new class.
     * @param Crawler\ReportBase $page Page object containing data to be parsed.
     * @return Report Report object that is filled with the parsed data.
     */
    public static function parseData(Crawler\ReportBase $page){
        Lib\MyLogger::log("================================<br />");
        Lib\MyLogger::log('Parsing pages data... <br />');
        Lib\MyLogger::log("================================<br />");
        if (!empty($page->fileName)){
            Lib\MyLogger::log('File: ' . $page->fileName . "<br />");
        }
        Lib\MyLogger::log('Name: ' . $page->name . "<br />");
        Lib\MyLogger::log("================================<br />");

        $pageMenuName = isset($page->pageMenuName)? $page->pageMenuName: '';
        $tripReport = new Crawler\Report($page->name, $page->type, $page->content, $pageMenuName, $page->pageURL);
        return $tripReport->parseContent();
    }

    /**
     * Parses the trip report data in the page object and writes the data to the database.
     * @param Crawler\ReportBase $page Page object containing data to be parsed and written.
     */
    public static function writeParsedDataToDatabase(Crawler\ReportBase $page){
        $tripReportParsed = self::parseData($page);
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

        TripReportsModel::createTripReport($tripReportParsed);

        Lib\MyLogger::log('Writing complete. Connection is closed. <br />');
        Lib\MyLogger::log("================================<br /><br /><br />");
    }


 // ======== Post-Processing of Crawler Data =========
    // TODO: Post-process font color content <font color="Green"></font>. In some TRs for different text color to differentiate multiple authors. Consider stripping & replacing with some other marker? e.g. see Trip_Report_33.txt (Carl Heller)


// Photos

    public static function cleanPhotoUrlStubs(){
        $urls = PhotoModel::getPhotoUrlsPiwigo();

        $newUrls = [];
        foreach ($urls as $id => $url){
            $newUrl = str_replace(self::$domainUrlStub, '', $url);
            $newUrls[$id] = $newUrl;
        }

        $newUrlIds = array_keys($newUrls);
        $newUrlValues = array_values($newUrls);
        for($i = 0; $i < count($newUrls); $i++){
            if($newUrlValues[$i] !== $urls[$newUrlIds[$i]]){
                PhotoModel::updatePhotoUrlPiwigo($newUrlIds[$i], 0, $newUrlValues[$i]);
            }
        }
    }

    /**
     * Looks up all photo URLs in the database, and if Picasa size data is present, changes it to the specified size.
     * @param int $photoSize New maximum photo dimension to use.
     */
    public static function updateAllPicasaPhotoSizes($photoSize){
        $urls = PhotoModel::getPhotoUrls();

        $newUrls = [];
        foreach ($urls as $id => $url){
            $urlComponents = explode('/', $url);
            $newUrl = '';
            for($i = 0; $i < count($urlComponents); $i++){
                if ($i === 0){
                    $newUrl = $urlComponents[$i];
                } elseif (preg_match('/^[s]{1}\d+[^a-zA-Z]/', $urlComponents[$i])){
                    $newUrl .= '/' . 's' . $photoSize;
                } else {
                    $newUrl .= '/' . $urlComponents[$i];
                }
            }
            $newUrls[$id] = $newUrl;
        }

        PhotoModel::updatePhotoUrls(array_keys($newUrls), array_values($newUrls));
    }


    public static function addAllUrlOtherFromUrls(){
        $urls = PhotoModel::getPhotoUrls();

        foreach ($urls as $id => $url){
            if (!Crawler\Report::isPicasaUrl($url) &&
                !Lib\StringHelper::stringContains($url, Config::get('URL'))){
                PhotoModel::updatePhotoUrlOther($id, $url);
            }
        }
    }


    /**
     * Copies all potential Picasa photo URLs to the appropriate column.
     */
    public static function addAllPicasaPhotoUrlsFromUrls(){
        $urls = PhotoModel::getPhotoUrls();

        foreach ($urls as $id => $url){
            if (Crawler\Report::isPicasaUrl($url)){
                PhotoModel::updatePhotoUrlPicasa($id, $url);
            }
        }
    }


    public static function addAllPiwigoPhotoUrlsFromPicasaUrls(){
        $database = DatabaseFactory::getFactory()->getConnection();

        // Get all non-matched images that have derived file names
        $sql = "SELECT id, file_name
                FROM photo
                WHERE url_piwigo IS NULL
                    AND file_name IS NOT NULL";
        $query = $database->prepare($sql);
        $query->execute();

        $photoIds = [];
        $fileNames = [];
        $results = $query->fetchAll();
        foreach($results as $result){
            $photoIds[] = $result->id;
            $fileNames[] = $result->file_name;
        }

        // Match images
        $urlsPiwigo = [];
        $idsPiwigo = [];
        $idsPhotoMatching = [];
        $databasePiwigo = Lib\MyPDOManager::mysqlPdoObject(
            Config::get('DB_PIWIGO_HOST'),
            Config::get('DB_PIWIGO_USER'),
            Config::get('DB_PIWIGO_PASS'),
            Config::get('DB_PIWIGO_NAME'),
            Lib\MyPDOStatement::getOptions());

        for ($i = 0; $i < count($fileNames); $i++){
            // Get Piwigo data of all matching file names
            $sql = "SELECT id, path
                    FROM piwigo_images
                    WHERE file = :fileName";
            $query = $databasePiwigo->prepare($sql);

            // Clean filename of characters not found in how Piwigo records data
            $fileNameCleaned = $fileNames[$i];
            $fileNameCleaned = str_replace('&', '_', $fileNameCleaned);
            $fileNameCleaned = str_replace('‘', '_', $fileNameCleaned);

            // Set all extensions to be lower case
            $fileNameComponents = explode('.', $fileNameCleaned);
            $extension = $fileNameComponents[count($fileNameComponents) - 1];
            $fileNameBase = str_replace($extension, '', $fileNameCleaned);
            $fileNameCleaned = $fileNameBase . strtolower($extension);

            $query->execute([':fileName' => $fileNameCleaned]);

            $result = $query->fetch();

            if ($result && !empty($result->path)){
                $photoPath = substr($result->path, 2);  // Removes the './' from './uploads/....'
                $urlsPiwigo[] = PhotoModel::piwigoImageUrlStub . $photoPath;
                $idsPiwigo[] = $result->id;
                $idsPhotoMatching[] = $photoIds[$i];
            } else {
                // When files w/ .psd equivalents were uploaded, initially both were converted to jpeg & uploaded, leaving the original jpeg as -2.jpg.
                // Check for this
                $fileNameCleaned = substr($fileNameBase,0,strlen($fileNameBase) - 1) . '-2.' . strtolower($extension);

                $query->execute([':fileName' => $fileNameCleaned]);

                $result = $query->fetch();

                if ($result && !empty($result->path)){
                    $photoPath = substr($result->path, 2);  // Removes the './' from './uploads/....'
                    $urlsPiwigo[] = PhotoModel::piwigoImageUrlStub . $photoPath;
                    $idsPiwigo[] = $result->id;
                    $idsPhotoMatching[] = $photoIds[$i];
                }
            }
        }

        // Add Piwigo Urls & Ids
        for($i = 0; $i < count($idsPhotoMatching); $i++){
            PhotoModel::updatePhotoUrlPiwigo($idsPhotoMatching[$i], $idsPiwigo[$i], $urlsPiwigo[$i]);
        }
    }


// Albums

    public static function cleanAlbumUrlStubs(){
        $urls = AlbumModel::getAlbumUrlsPiwigo();

        $newUrls = [];
        foreach ($urls as $id => $url){
            $newUrl = str_replace(self::$domainUrlStub, '', $url);
            $newUrls[$id] = $newUrl;
        }

        $newUrlIds = array_keys($newUrls);
        $newUrlValues = array_values($newUrls);
        for($i = 0; $i < count($newUrls); $i++){
            if($newUrlValues[$i] !== $urls[$newUrlIds[$i]]){
                AlbumModel::updateAlbumUrlPiwigo($newUrlIds[$i], 0, $newUrlValues[$i]);
            }
        }
    }

    public static function cleanAlbumTitles(){
        $stripTitles = [
            'Picasa Album: ',
            'Picasa Album - ',
            'Picasa Albumm - ',
            'Picasa Album.',
            'Picasa Album',
            'Picasa Photos',
            'Picasa Photo Album',
            'Picasa - '
        ];

        // 0. Get album titles
        $titles = AlbumModel::getAlbumTitles();
        $newTitles = [];

        foreach ($titles as $id => $title){
            $newTitle = $title;
            // 1. Strip all of the following:
            $newTitle = str_replace($stripTitles, '', $newTitle);
            for ($i = 0; $i < 11; $i++){
                $newTitle = str_replace('Picasa Album ' . $i . ' - ', '', $newTitle);
            }

            // 2. For all titles not beginning with '20', derive a new title from the album URL if possible.
            if (empty($newTitle) || !self::titleStartsAsDate($newTitle)){
                $url = AlbumModel::getAlbumUrlById($id);
                $newTitle = Crawler\ReportHtmlParser::deriveNameFromUrl($url);
            }

            // 3. Keep only titles beginning with '20'
            if (!self::titleStartsAsDate($newTitle)){
                continue;
            }

            // 4. Replace the following:
            $newTitle = str_replace('t-o', 'to', $newTitle);
            $newTitle = str_replace(' W ', ' w ', $newTitle);

            // Add spaces betweenCamelCaseLetters including w/ as W & KIATo7500 as KIA To 7500, etc.
            $newTitle = preg_replace('/([a-z])([A-Z])/s','$1 $2', $newTitle);
            $newTitle = preg_replace('/([A-Z0-9])(To)([A-Z0-9])/s','$1 $2 $3', $newTitle);
            $newTitle = preg_replace('/([A-Z])([A-Z])/s','$1 $2', $newTitle);

            // Add ' - ' between any dates and names
            $newTitle = preg_replace('/([0-9])([A-Z])/s','$1 - $2', $newTitle);
            $newTitle = preg_replace('/([0-9])-([A-Z])/s','$1 - $2', $newTitle);

            // 6. Add only if unique
            if ($title != $newTitle){
                $newTitles[$id] = $newTitle;
            }
        }

        // 7. Update album titles
        foreach($newTitles as $id => $newTitle){
            AlbumModel::updateAlbumTitle($id, $newTitle);
        }
    }


    private static function titleStartsAsDate($newTitle){
        return (substr($newTitle, 0, 2) == '19' ||
            substr($newTitle, 0, 2) == '20');
    }

    /**
     * Copies all potential Picasa album URLs to the appropriate column.
     */
    public static function addAllPicasaAlbumUrlsFromUrls(){
        $database = DatabaseFactory::getFactory()->getConnection();

        // Get URLS
        $sql = "SELECT id, url FROM photo_album";
        $query = $database->prepare($sql);
        $query->execute();

        $urls = [];
        $urlIds = [];
        $results = $query->fetchAll();
        foreach($results as $result){
            $urls[] = $result->url;
            $urlIds[] = $result->id;
        }

        // Update Urls
        for($i = 0; $i < count($urlIds); $i++){
            if (Crawler\Report::isPicasaUrl($urls[$i])){
                $sql = "UPDATE photo_album
                        SET url_picasa = :url_picasa
                        WHERE id = :urlId";
                $query = $database->prepare($sql);
                $query->execute([
                    ':url_picasa' => $urls[$i],
                    ':urlId' => $urlIds[$i]
                ]);
            }
        }
    }

    public static function addAllPiwigoAlbumUrlsFromPicasaAlbums(){
        $database = DatabaseFactory::getFactory()->getConnection();

        // Get all non-matched albums
        $sql = "SELECT id, title
                FROM photo_album
                WHERE url_piwigo IS NULL";
        $query = $database->prepare($sql);
        $query->execute();

        $albumIds = [];
        $albumTitles = [];
        $results = $query->fetchAll();
        foreach($results as $result){
            $albumIds[] = $result->id;
            $albumTitles[] = $result->title;
        }

        // Match albums
        $idsPiwigo = [];
        $urlsPiwigo = [];
        $idsAlbumMatching = [];
        $databasePiwigo = Lib\MyPDOManager::mysqlPdoObject(
            Config::get('DB_PIWIGO_HOST'),
            Config::get('DB_PIWIGO_USER'),
            Config::get('DB_PIWIGO_PASS'),
            Config::get('DB_PIWIGO_NAME'),
            Lib\MyPDOStatement::getOptions());

        for ($i = 0; $i < count($albumTitles); $i++){
            // Get Piwigo data of all matching album names
            $sql = "SELECT id
                    FROM piwigo_categories
                    WHERE name = :name";
            $query = $databasePiwigo->prepare($sql);
            $query->execute([':name' => $albumTitles[$i]]);

            $result = $query->fetch();
            if ($result){
                $idsPiwigo[] = $result->id;
                $urlsPiwigo[] = AlbumModel::piwigoCategoryUrlStub . $result->id;
                $idsAlbumMatching[] = $albumIds[$i];
            }
        }

        // Add Piwigo Urls & Ids
        for($i = 0; $i < count($idsAlbumMatching); $i++){
            AlbumModel::updateAlbumUrlPiwigo($idsAlbumMatching[$i], $idsPiwigo[$i], $urlsPiwigo[$i]);
        }
    }


} 