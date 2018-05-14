<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/18/18
 * Time: 3:26 PM
 */

namespace markpthomas\mountaineering;

use markpthomas\mountaineering\dbObjects as Db;
use markpthomas\library as Lib;

// For { "item": { "key": "value", "key": value } }
// $post_data = json_encode(array('item' => $post_data), JSON_FORCE_OBJECT);
// See: https://stackoverflow.com/questions/3281354/create-json-object-the-correct-way

class TripReportsModel
{
    public static $blogCategories = [
        'trip-reports' => 'Trip Reports',
        'articles' => 'Articles'
    ];

    public static $reportCategories = [
        'alaska' => 'Alaska',
        'california' => 'California',
        'canada' => 'Canada',
        'colorado' => 'Colorado',
        'idaho' => 'Idaho',
        'utah' => 'Utah',
        'washington' => 'Washington',
        'wyoming' => 'Wyoming'
    ];

    // TODO: Rectify crawler-specific methods from ones part of site creation/update.
    public static function getTypeNameFromCrawlerData($crawlerTypeName){
        return ($crawlerTypeName === 'Trip_Report')? 'Standard' : 'Article';
    }

// =============== Create ===============
    /**
     * Creates a new trip report header.
     * @param Db\ReportTrip $header Trip report header object containing the data to be inserted.
     * @param bool $returnExisting True: If the trip report already exists (based on url), the existing trip report ID is returned. Otherwise an error occurs.
     * @return bool|int The ID of the created trip report, or either the ID of an existing trip report or false if the trip report already exists.
     */
    public static function createTripReportHeader(Db\ReportTrip $header, $returnExisting = true){
        $database = DatabaseFactory::getFactory()->getConnection();

        // If one already exists, return the id.
        if ($result = self::isExisting($header->getPage()->getId())){
            if ($returnExisting){
                return $result;
            }
            Session::add('feedback_negative', Text::get('FEEDBACK_REPORT_ALREADY_EXISTS'));
            return false;
        }

        // Otherwise, add the report
        $sql = "INSERT INTO report_trip (
                    page_id,
                    status_id,
                    report_trip_type_id)
                VALUES (
                    :page_id,
                    :status_id,
                    :report_trip_type_id)";
        $query = $database->prepare($sql);
        $query->execute([
            ':page_id' => $header->getPage()->getId(),
            ':status_id' => $header->getStatusId(),
            ':report_trip_type_id' => $header->getReportTypeId()
        ]);
        $reportId = ($query->rowCount() === 1)? (int)$database->lastInsertId() : false;
        Lib\MyLogger::log('Report ID: ' . $reportId . '<br />');

        if (!empty($reportId) && empty($header->getId())){
            $header->setId($reportId);
        }

        return $reportId;
    }

    /**
     * Adds the current trip report body sequence to the trip report body set.
     * @param Db\ReportTripBody $body
     * @return null
     */
    public static function addTripReportBody(Db\ReportTripBody $body){
        if (!self::insertTripReportBody($body)){
            return null;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO report_trip_body (
                        report_trip_id,
                        sequence,
                        header_type_id,
                        header_value,
                        report_photo_id,
                        report_video_id,
                        text_body)
                    VALUES (
                        :report_trip_id,
                        :sequence,
                        :header_type_id,
                        :header_value,
                        :report_photo_id,
                        :report_video_id,
                        :text_body)";
        $query = $database->prepare($sql);
        $query->execute([
            ':report_trip_id' => $body->report_trip_id,
            ':sequence' => $body->sequence,
            ':header_type_id' => $body->getHeader()->getHeaderTypeId(),
            ':header_value' => empty($body->getHeader()->header_value)? null : $body->getHeader()->header_value,
            ':report_photo_id' => $body->getReportPhoto()->getId(),
            ':report_video_id' => $body->getReportVideo()->getId(),
            ':text_body' => empty($body->text_body)? null : $body->text_body]);
        return ($query->rowCount() === 1)? (int)$database->lastInsertId() : false;
    }

    /**
     * Adds an n:m relation between the report and photo album.
     * @param $reportId
     * @param $albumId
     * @return bool
     */
    public static function addAlbumRelation($reportId, $albumId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $values = [
            ':reportId' => $reportId,
            ':albumId' => $albumId
        ];

        $sql = "SELECT report_trip_id
                  FROM report_trip_has_photo_album
                  WHERE report_trip_id = :reportId
                  AND photo_album_id = :albumId";
        $query = $database->prepare($sql);
        $query->execute($values);
        if ($query->rowCount() === 1){
            return true;
        } else {
            $sql = "INSERT INTO report_trip_has_photo_album (
                                    report_trip_id,
                                    photo_album_id)
                                VALUES (
                                    :reportId,
                                    :albumId)";
            $query = $database->prepare($sql);
            $query->execute($values);
            return ($query->rowCount() === 1);
        }
    }

    /**
     * Adds an n:m relation between the report and reference.
     * @param $reportId
     * @param $referenceId
     * @return bool
     */
    public static function addReferenceRelation($reportId, $referenceId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $values = [
            ':reportId' => $reportId,
            ':referenceId' => $referenceId
        ];

        $sql = "SELECT report_trip_id
                  FROM report_trip_has_reference
                  WHERE report_trip_id = :reportId
                  AND reference_id = :referenceId";
        $query = $database->prepare($sql);
        $query->execute($values);
        if ($query->rowCount() === 1){ // Relations do not return unique IDs.
            return false;
        } else {
            $sql = "INSERT INTO report_trip_has_reference (
                            report_trip_id,
                            reference_id)
                        VALUES (
                            :reportId,
                            :referenceId)";
            $query = $database->prepare($sql);
            $query->execute($values);
            return ($query->rowCount() === 1)? true : false;
        }
    }


    /**
     * Writes the trip report JSON object to the database.
     * @param $data
     * @return bool  True: The report was successfully created.
     */
    public static function createReportFromJson($data){
        $report = Lib\JsonHandler::decode($data, $assoc = true);
        $report = Report::factoryStdClass($report);
        return self::createTripReport($report);
    }

    /**
     * Writes the trip report object to the database.
     * @param Report $report
     * @return bool  True: The report was successfully created.
     */
    public static function createTripReport(Report $report){
        $database = DatabaseFactory::getFactory()->getConnection();

        try {
            $database->beginTransaction();

            // 1. Insert Data to page
            $pageId = PageModel::createPage($report->getHeader()->getPage());
            Lib\MyPDOManager::confirmTransaction($pageId);

            // 2. Insert data to report_trip

            // 2b. Insert data
            $reportId = self::createTripReportHeader($report->getHeader());
            Lib\MyPDOManager::confirmTransaction($reportId);

            // 3. Insert data to report_trip_body
            for ($i = 0; $i < $report->getMaxSequence(); $i++){
                $reportBody = $report->getBody($i);
                if (empty($reportBody->report_trip_id)){
                    $reportBody->report_trip_id = $reportId;
                }

                Lib\MyLogger::log('___________________________<br />');
                Lib\MyLogger::log('Sequence ' . $reportBody->sequence . '<br />');
                Lib\MyLogger::log('---------------------------<br />');

                // 3b. Add photo
                if (!empty($reportBody->getReportPhoto()->getPhoto()->url)){
                    $photoId = PhotoModel::addPhoto($reportBody->getReportPhoto()->getPhoto());
                    Lib\MyPDOManager::confirmTransaction($photoId);

                    $reportPhotoId = PhotoModel::addReportPhoto($reportBody->getReportPhoto());
                    Lib\MyPDOManager::confirmTransaction($reportPhotoId);
                }

                // 3c. Add video
                if (!empty($reportBody->getReportVideo()->getVideo()->url)){
                    $videoId = VideoModel::addVideo($reportBody->getReportVideo()->getVideo());
                    Lib\MyPDOManager::confirmTransaction($videoId);

                    $reportVideoId = VideoModel::addReportVideo($reportBody->getReportVideo());
                    Lib\MyPDOManager::confirmTransaction($reportVideoId);
                }

                // Skip adding body if all relevant values are null
                if (!self::insertTripReportBody($reportBody)) continue;

                $reportTextBodyId = self::addTripReportBody($reportBody);
                Lib\MyPDOManager::confirmTransaction($reportTextBodyId);
            }

            // 4. Add photo albums
            for ($i = 0; $i < $report->countAlbums(); $i++){
                $albumId = AlbumModel::addAlbum($report->getAlbum($i));
                Lib\MyPDOManager::confirmTransaction($albumId);

                $result = self::addAlbumRelation($reportId, $albumId);
                Lib\MyPDOManager::confirmTransaction($result);
            }

            // 5. Add links
            for ($i = 0; $i < $report->countReferenceLinkExternal(); $i++){
                $referenceId = ReferenceModel::addReference($report->getReferenceLinkExternal($i));
                Lib\MyPDOManager::confirmTransaction($referenceId);

                $result = self::addReferenceRelation($reportId, $referenceId);
                Lib\MyPDOManager::confirmTransaction($result);
            }

            for ($i = 0; $i < $report->countReferenceLinkInternal(); $i++){
                $referenceId = ReferenceModel::addReference($report->getReferenceLinkInternal($i));
                Lib\MyPDOManager::confirmTransaction($referenceId);

                $result = self::addReferenceRelation($reportId, $referenceId);
                Lib\MyPDOManager::confirmTransaction($result);
            }

            for ($i = 0; $i < $report->countReference(); $i++){
                $referenceId = ReferenceModel::addReference($report->getReference($i));
                Lib\MyPDOManager::confirmTransaction($referenceId);

                $result = self::addReferenceRelation($reportId, $referenceId);
                Lib\MyPDOManager::confirmTransaction($result);
            }

            $database->commit();
        } catch (\Exception $e){
            $database->rollback();
            Lib\MyLogger::log('Error No: ' . $e->getCode() . ' - ' . $e->getMessage() . '<br />');
            Lib\MyLogger::log(nl2br($e->getTraceAsString()));
            Session::add('feedback_negative', Text::get('FEEDBACK_REPORT_CREATION_FAILED'));
            return false;
        }
        Session::add('feedback_positive',
            Text::get('FEEDBACK_REPORT_SUCCESSFULLY_CREATED') .
                ' Report_ID: ' . $report->getHeader()->getId() .
                ' ~ Name: ' . $report->getHeader()->getPage()->title_full .
                ' ~ URL: ' . $report->getHeader()->getPage()->url);
        return true;
    }


    /**
     * Only insert if at least one of the critical data is not null.
     * @param Db\ReportTripBody $body
     * @return bool True: The body object should be added.
     */
    public static function insertTripReportBody(Db\ReportTripBody $body){
        return !($body->getHeader()->getHeaderTypeId() === null &&
            $body->getHeader()->header_value === null &&
            $body->getReportPhoto()->getId() === null &&
            $body->getReportVideo()->getId() === null &&
            $body->text_body === null);
    }



// =============== Read ===============
    /**
     * Returns the trip report id if the parent page exists. Otherwise, returns false.
     * @param string $pageId
     * @return int|bool
     */
    public static function isExisting($pageId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id FROM report_trip WHERE page_id = :pageId";
        $query = $database->prepare($sql);
        $query->execute([':pageId' => $pageId]);

        return ($query->rowCount() !== 0)? (int)$query->fetch()->id : false;
    }


    /**
     * Returns a list of trip reports that are children of the supplied URL.
     * @param $pageUrl string URL used as a unique index for looking up the reports.
     * @return array|string
     */
    public static function getReportsList($pageUrl)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    r.id as report_trip_id,
                    r.page_id,
                    r.status_id AS report_status_id,
                    r.report_trip_type_id,
                    p.title_menu,
                    p.title_full,
                    p.description,
                    p.url,
                    photo.url AS photo_url,
                    photo.caption AS photo_caption,
                    p.date_created,
                    p.status_id AS page_status_id,
                    p.tasks,
                    p.is_public,
                    p.views_count,
                    p.user_id
                FROM
                    report_trip r
                LEFT JOIN
                    page p ON (r.page_id = p.id)
                LEFT JOIN
                    photo ON (p.photo_id = photo.id)
                WHERE p.url LIKE :url';
        $query = $database->prepare($sql);
        $query->execute([':url' => $pageUrl . '%']);

        $reportPages = [];
        while ($row = $query->fetch()){
            $reportPages[] = $row;
        }

        $urlElements = explode('/', $pageUrl);
        $pageGroup = end($urlElements);

        $reportsList = [];
        foreach($reportPages as $page){
            // Strip base URL from the page URL to get the stub
            $pageUrlStub = str_replace($pageUrl, '', $page->url);
            $reportPageUrl = Lib\PathHelper::Combine([$pageGroup, $pageUrlStub], $isRoot=false);
            $reportsList[$reportPageUrl] = $page->title_menu;
        }
        $reportsList = Lib\JsonHandler::encode($reportsList);
        return $reportsList;
    }

    /**
     * Returns a trip report as a JSON string.
     * @param $pageUrl string URL used as a unique index for looking up the reports.
     * @return array
     */
    public static function getReportByUrlAsJSON($pageUrl){
        $report = self::getReportByUrl($pageUrl);
        $jsonReport = $report->convertToStdClassView();
        $jsonReport = Lib\JsonHandler::encode($jsonReport);
        return $jsonReport;
    }

    /**
     * Returns a report object based on the page URL.
     * @param string $pageUrl URL used as a unique index for looking up the reports.
     * @return Report
     */
    public static function getReportByUrl($pageUrl){
        $page = PageModel::getPageByUrl($pageUrl);
        if (!$page) return null;

        $reportPage = new Db\Page($page);

        $header = self::getTripReportHeaderByPageId($reportPage->getId());
        $reportId = $header->id;

        return self::getReportById($reportId);
    }

    /**
     * Returns a report object based on the report ID.
     * @param $reportId Report ID used to look up the report and associated page data.
     * @return Report
     */
    public static function getReportById($reportId){
        $header = self::getTripReportHeader($reportId);

        $pageId = $header->page_id;
        $reportPage = PageModel::getReportPageById($pageId);
        if (!$reportPage) return null;

        $report = new Report();
        $report->setHeaderAndPage($header, $reportPage);

        // Validate that the basic report read correctly. Otherwise, abort.
        $reportId = $report->getHeader()->getId();
        if (empty($reportId))
            return new Report();

        // Add report body elements
        $reportBodies = self::getReportBodies($reportId);
        $report->setBodies($reportBodies);

        // Add albums
        $photoAlbums = AlbumModel::getReportAlbumsByReportId($reportId);
        $report->setAlbums($photoAlbums);

        // Add references
        $references = ReferenceModel::getReportReferencesByReportId($reportId);
        $report->setReferences($references);

        return $report;
    }


    /**
     * Gets the report header data for all reports.
     * @return array
     */
    public static function getAllTripReportHeaders(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    id,
                    page_id,
                    status_id,
                    report_trip_type_id
                FROM report_trip';

        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Gets the report header data for the report ID provided.
     * @param $reportId
     * @return mixed
     */
    public static function getTripReportHeader($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    id,
                    page_id,
                    status_id,
                    report_trip_type_id
                FROM report_trip
                WHERE id = :id';

        $query = $database->prepare($sql);
        $query->execute([':id' => $reportId]);
        return $query->fetch();
    }

    /**
     * Gets the first report header data for the page ID provided.
     * Note that currently if more than one report is associated with a page, the additional pages will not be included.
     * @param $pageId
     * @return mixed
     */
    public static function getTripReportHeaderByPageId($pageId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    id,
                    page_id,
                    status_id,
                    report_trip_type_id
                FROM report_trip
                WHERE page_id = :page_id';

        $query = $database->prepare($sql);
        $query->execute([':page_id' => $pageId]);
        return $query->fetch();
    }

    /**
     * @param $reportId
     * @return array
     */
    public static function getReportBodies($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        // Note: Using a view. Current host does not allow this. Might implement in future hosting.
//        $sql = 'SELECT * FROM report_trip_body_view
//                WHERE report_trip_id = :reportId';

        $sql = 'SELECT
                    b.report_trip_id AS report_trip_id,
                    b.sequence AS sequence,
                    header.name AS header_type,
                    b.header_value,
                    b.text_body AS text_body,
                    rp.suppress_caption AS reportPhoto_suppress_caption,
                    rp.custom_caption AS reportPhoto_custom_caption,
                    p.album_id AS photo_album_id,
                    p.url AS photo_url,
                    p.width AS photo_width,
                    p.height AS photo_height,
                    p.caption AS photo_caption,
                    p.latitude AS photo_latitude,
                    p.longitude AS photo_longitude,
                    p.time_stamp AS photo_time_stamp,
                    p.is_public AS photo_is_public,
                    rv.suppress_caption AS reportVideo_suppress_caption,
                    rv.custom_caption AS reportVideo_custom_caption,
                    v.album_id AS video_album_id,
                    v.url AS video_url,
                    v.width AS video_width,
                    v.height AS video_height,
                    v.caption AS video_caption,
                    v.latitude AS video_latitude,
                    v.longitude AS video_longitude,
                    v.time_stamp AS video_time_stamp,
                    v.is_public AS video_is_public
                FROM
                    (((((report_trip_body b
                    LEFT JOIN header_type header ON ((b.header_type_id = header.id)))
                    LEFT JOIN report_photo rp ON ((b.report_photo_id = rp.id)))
                    LEFT JOIN photo p ON ((rp.photo_id = p.id)))
                    LEFT JOIN report_video rv ON ((b.report_video_id = rv.id)))
                    LEFT JOIN video v ON ((rv.video_id = v.id)))
                WHERE b.report_trip_id = :reportId
                ORDER BY b.report_trip_id , b.sequence';

        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);

        return $query->fetchAll();
    }

    /**
     * @param $pageUrl
     * @return null
     */
    public static function getReportIdFromPageUrl($pageUrl){
        $pageId = PageModel::getPageIdByUrl($pageUrl);

        $database = DatabaseFactory::getFactory()->getConnection();
        $sql= 'SELECT id FROM report_trip WHERE page_id = :page_id';
        $query = $database->prepare($sql);
        $query->execute([':page_id' => $pageId]);

        return ($query->rowCount() === 1)? $query->fetch()->id : null;
    }

    /**
     * @param $reportId
     * @return int|null
     */
    public static function getPageIdFromReportId($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql= 'SELECT page_id FROM report_trip WHERE id = :reportId';
        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);

        return ($query->rowCount() === 1)? $query->fetch()->page_id : null;
    }

    /**
     * @param $reportId
     * @return int|null
     */
    public static function getReportPhotoAndVideoIds($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT report_photo_id, report_video_id
                    FROM report_trip_body
                    WHERE report_trip_id = :reportId';
        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);
        if ($query->rowCount() === 0){
            return false;
        }
        return $query->fetchAll();
    }

    /**
     * @param $typeName
     * @return null
     */
    public static function getReportTypeId($typeName){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id FROM report_trip_type WHERE name = :typeName";
        $query = $database->prepare($sql);
        $query->execute([':typeName' => $typeName]);

        return ($query->rowCount() === 1)? $query->fetch()->id : null;
    }


// =============== Update ===============

    public static function updateReportInDatabaseFromJSON($data){
        $report = Lib\JsonHandler::decode($data, $assoc = true);
        self::updateReportInDatabase($report);
    }

    public static function updateReportInDatabase(Report $report){
        $database = DatabaseFactory::getFactory()->getConnection();

        // 1. Update page & report_trips page
        // 2. Clear current report (remove all report body entries)
        // 3. Re-add report bodies, photos, etc.
        // 4. Add albums, links, etc. & references if new


    }


    public static function associatePhotosToAlbums(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id
                FROM report_trip";
        $query = $database->prepare($sql);
        $query->execute();
        $reports = $query->fetchAll();

        // For each trip report
        foreach($reports as $report){
            if (!isset($report->id)) continue;
            // Get all report photo_ids associated with the report
            $sql = "SELECT p.id AS id
                    FROM report_trip_body rtb
                      JOIN report_photo rp ON (rtb.report_photo_id = rp.id)
                      JOIN photo p ON (rp.photo_id = p.id)
                    WHERE report_trip_id = :reportTripId";
            $query = $database->prepare($sql);
            $query->execute([':reportTripId' => $report->id]);
            $photos = $query->fetchAll();

            // For each photo id
            foreach($photos as $photo){
                if (!isset($photo->id)) continue;

                // Get piwigo id
                $sql = "SELECT id_piwigo
                        FROM photo
                        WHERE id = :id";
                $query = $database->prepare($sql);
                $query->execute([':id' => $photo->id]);
                $result = $query->fetch();

                $piwigoId = isset($result->id_piwigo)? $result->id_piwigo : null;

                // For all photos with piwigo ids...
                $piwigoAlbumId = '';
                if (!empty($piwigoId)){
                    $databasePiwigo = Lib\MyPDOManager::mysqlPdoObject(
                        Config::get('DB_PIWIGO_HOST'),
                        Config::get('DB_PIWIGO_USER'),
                        Config::get('DB_PIWIGO_PASS'),
                        Config::get('DB_PIWIGO_NAME'),
                        Lib\MyPDOStatement::getOptions());

                    // Look up corresponding album ids
                    $sql = "SELECT category_id
                            FROM piwigo_image_category
                            WHERE image_id = :imageId";
                    $query = $databasePiwigo->prepare($sql);
                    $query->execute([':imageId' => $piwigoId]);
                    $piwigoAlbumId = $query->fetch()->category_id;
                }

                // Get album ID from piwigo album ID
                $albumId = '';
                if (!empty($piwigoAlbumId)){
                    $sql = "SELECT id
                            FROM photo_album
                            WHERE id_piwigo = :id";
                    $query = $database->prepare($sql);
                    $query->execute([':id' => $piwigoAlbumId]);
                    $albumId = $query->fetch()->id;
                }

                // Set Album ID
                if (!empty($albumId)){
                    $sql = "UPDATE photo
                            SET album_id = :album_id
                            WHERE id = :id";
                    $query = $database->prepare($sql);
                    $query->execute([
                        ':album_id' => $albumId,
                        ':id' => $photo->id
                    ]);
                }
            }
        }
    }

    public static function associatePiwigoAlbumsToReportsByPhotos(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id
                FROM report_trip";
        $query = $database->prepare($sql);
        $query->execute();
        $reports = $query->fetchAll();

        // For each trip report
        foreach($reports as $report){
            if (!isset($report->id)) continue;
            // Get all report photo_ids associated with the report
            $sql = "SELECT p.id AS id
                    FROM report_trip_body rtb
                      JOIN report_photo rp ON (rtb.report_photo_id = rp.id)
                      JOIN photo p ON (rp.photo_id = p.id)
                    WHERE report_trip_id = :reportTripId";
            $query = $database->prepare($sql);
            $query->execute([':reportTripId' => $report->id]);
            $photos = $query->fetchAll();

            $piwigoAlbums = [];
            $piwigoAlbumIds = [];

            // For each photo id
            foreach($photos as $photo){
                if (!isset($photo->id)) continue;

                // Get piwigo id
                $sql = "SELECT id_piwigo
                        FROM photo
                        WHERE id = :id";
                $query = $database->prepare($sql);
                $query->execute([':id' => $photo->id]);
                $result = $query->fetch();

                $piwigoId = isset($result->id_piwigo)? $result->id_piwigo : null;

                // For all photos with piwigo ids...
                if (!empty($piwigoId)){
                    $databasePiwigo = Lib\MyPDOManager::mysqlPdoObject(
                        Config::get('DB_PIWIGO_HOST'),
                        Config::get('DB_PIWIGO_USER'),
                        Config::get('DB_PIWIGO_PASS'),
                        Config::get('DB_PIWIGO_NAME'),
                        Lib\MyPDOStatement::getOptions());

                    // Look up corresponding album ids
                    $sql = "SELECT category_id
                            FROM piwigo_image_category
                            WHERE image_id = :imageId";
                    $query = $databasePiwigo->prepare($sql);
                    $query->execute([':imageId' => $piwigoId]);
                    $piwigoAlbumId = $query->fetch()->category_id;

                    // Get piwigo album id if unique, and piwigo url, piwigo title
                    if ($piwigoAlbumId && !in_array($piwigoAlbumId, $piwigoAlbumIds)){
                        $piwigoAlbumIds[] = $piwigoAlbumId;

                        $piwigoAlbum = new \stdClass();
                        $piwigoAlbum->url = AlbumModel::piwigoCategoryUrlStub . $piwigoAlbumId;
                        $piwigoAlbum->url_piwigo = $piwigoAlbum->url;
                        $piwigoAlbum->id_piwigo = $piwigoAlbumId;

                        $sql = "SELECT name
                                FROM piwigo_categories
                                WHERE id = :albumId";
                        $query = $databasePiwigo->prepare($sql);
                        $query->execute([':albumId' => $piwigoAlbumId]);
                        $piwigoAlbum->title = $query->fetch()->name;

                        $piwigoAlbums[] = $piwigoAlbum;
                    }
                }
            }

            // For each album id:
            foreach($piwigoAlbums as $piwigoAlbum){
                // Add new album reference
                $album = new Db\Album($piwigoAlbum);

                try {
                    $database->beginTransaction();

                    $albumId = AlbumModel::addAlbum($album);
                    Lib\MyPDOManager::confirmTransaction($albumId);

                    $result = self::addAlbumRelation($report->id, $albumId);
                    Lib\MyPDOManager::confirmTransaction($result);

                    $database->commit();
                } catch (\Exception $e){
                    $database->rollback();
                    Lib\MyLogger::log('Error No: ' . $e->getCode() . ' - ' . $e->getMessage() . '<br />');
                    Lib\MyLogger::log(nl2br($e->getTraceAsString()));
                    Session::add('feedback_negative', Text::get('FEEDBACK_REPORT_CREATION_FAILED'));
                    return false;
                }
            }
        }
        return true;
    }

// =============== Destroy ===============
    /**
     * Deletes all trips reports.
     * THIS DOES A COMPLETE WIPE!
     * Only use this method for resetting all trip reports cleanly without dropping tables.
     */
    public static function deleteAllTripReports(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id FROM report_trip";
        $query = $database->prepare($sql);
        $query->execute();
        $results = $query->fetchAll();

        $reportIds = [];
        foreach($results as $result)
        {
            $reportIds[] = (int)$result->id;
        }

        foreach($reportIds as $reportId)
        {
            self::deleteReportInDatabaseById($reportId);
        }
    }

    /**
     * Deletes the trip report in the database identified by the provided URL.
     * @param string $pageUrl URL of the report to delete.
     * @return bool True: The trip report was successfully deleted.
     */
    public static function deleteReportInDatabaseByUrl($pageUrl){
        $reportId = self::getReportIdFromPageUrl($pageUrl);
        $result = self::deleteReportInDatabaseById($reportId);
        return $result;
    }

    /**
     * Deletes the trip report in the database identified by the provided ID.
     * @param int $reportId
     * @return bool
     */
    public static function deleteReportInDatabaseById($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        try {
            $database->beginTransaction();


            // Delete page
            $pageId = self::getPageIdFromReportId($reportId);
//            $pagePhotoId = PageModel::getPagePhotoId($pageId);    // Not sure if page photo should be deleted. May still be referenced elsewhere.
            $result = PageModel::deletePage($pageId);
            Lib\MyPDOManager::confirmTransaction($result);

            // Delete orphaned albums
            $albumIdsToDelete = AlbumModel::getAlbumIdsOrphanedByReportDelete($reportId);
            foreach ($albumIdsToDelete as $albumId){
                $result = AlbumModel::deleteAlbum($albumId);
                Lib\MyPDOManager::confirmTransaction($result);
            }

            // Delete orphaned references
            $referenceIdsToDelete = ReferenceModel::getReferenceIdsOrphanedByReportDelete($reportId);
            foreach ($referenceIdsToDelete as $referenceId){
                $result = ReferenceModel::deleteReference($referenceId);
                Lib\MyPDOManager::confirmTransaction($result);
            }

            // Delete photos and videos
            $reportPhotoAndVideoIds = self::getReportPhotoAndVideoIds($reportId);
            foreach ($reportPhotoAndVideoIds as $reportPhotoAndVideoId){
                // Delete photos
                $reportPhotoId = $reportPhotoAndVideoId->report_photo_id;
                if (!empty($reportPhotoId)){
                    $result = PhotoModel::deleteReportAndBasePhoto($reportPhotoId);
                    Lib\MyPDOManager::confirmTransaction($result);
                }

                // Delete videos
                $reportVideoId = $reportPhotoAndVideoId->report_video_id;
                if (!empty($reportVideoId)){
                    $result = VideoModel::deleteReportAndBaseVideo($reportVideoId);
                    Lib\MyPDOManager::confirmTransaction($result);
                }
            }

            // Delete report header (cascades to report bodies)
            $result = self::deleteReportHeader($reportId);
            Lib\MyPDOManager::confirmTransaction($result);

            $database->commit();
        } catch (\Exception $e){
            $database->rollback();
            Lib\MyLogger::log('Error No: ' . $e->getCode() . ' - ' . $e->getMessage() . '<br />');
            Lib\MyLogger::log(nl2br($e->getTraceAsString()));
            Session::add('feedback_negative', Text::get('FEEDBACK_REPORT_DELETION_FAILED'));
            return false;
        }

        Session::add('feedback_positive', Text::get('FEEDBACK_REPORT_DELETION_SUCCESSFUL'));
        return true;
    }


    /**
     * Deletes the trip report header object.
     * @param int $reportId ID of the report to delete.
     * @return bool True: The trip report was successfully deleted.
     */
    public static function deleteReportHeader($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'DELETE FROM report_trip WHERE id = :id';
        $query = $database->prepare($sql);
        $query->execute([':id' => $reportId]);

        return ($query->rowCount() == 1);
    }

    /**
     * Deletes all report bodies associated with the specified report.
     * @param int $reportId
     * @return bool
     */
    public static function deleteReportBodies($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'DELETE FROM report_trip_body
                WHERE report_trip_id = :report_trip_id';
        $query = $database->prepare($sql);
        $query->execute([':report_trip_id' => $reportId]);

        return ($query->rowCount() == 1);
    }
} 