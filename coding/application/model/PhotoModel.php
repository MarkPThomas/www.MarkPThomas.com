<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/29/18
 * Time: 10:22 PM
 */

namespace markpthomas\coding;

use markpthomas\mountaineering\dbObjects as Db;
use markpthomas\library as Lib;

class PhotoModel {
    const piwigoImageUrlStub = '/photos/';

    // =============== Create ===============
    /**
     * Creates a new photo.
     * @param Db\Photo $photo Photo object containing the data to be inserted.
     * @param bool $returnExisting True: If the photo already exists (based on url), the existing photo ID is returned. Otherwise an error occurs.
     * @return bool|int The ID of the created photo, or either the ID of an existing photo or false if the page already exists.
     */
    public static function addPhoto(Db\Photo $photo, $returnExisting = true){
        $database = DatabaseFactory::getFactory()->getConnection();

        // If one already exists, return the id.
        if ($result = self::isExisting($photo->url)){
            if ($returnExisting){
                return self::setPhotoId(self::getPhotoIdFromUrl($photo->url), $photo);
            }
            Session::add('feedback_negative', Text::get('FEEDBACK_PHOTO_ALREADY_EXISTS'));
            return false;
        }

        $sql = "INSERT INTO photo (
                    url,
                    caption,
                    width,
                    height,
                    latitude,
                    longitude,
                    time_stamp,
                    is_public,
                    file_name,
                    url_piwigo,
                    id_piwigo,
                    url_picasa,
                    url_other)
                VALUES (
                    :url,
                    :caption,
                    :width,
                    :height,
                    :latitude,
                    :longitude,
                    :time_stamp,
                    :is_public,
                    :file_name,
                    :url_piwigo,
                    :id_piwigo,
                    :url_picasa,
                    :url_other)";
        $query = $database->prepare($sql);
        $query->execute([
            ':url' => $photo->url,
            ':caption' => $photo->caption,
            ':width' => $photo->width,
            ':height' => $photo->height,
            ':latitude' => $photo->latitude,
            ':longitude' => $photo->longitude,
            ':time_stamp' => $photo->time_stamp,
            ':is_public' => $photo->getIsPublic(),
            ':file_name' => $photo->file_name,
            ':url_piwigo' => $photo->url_piwigo,
            ':id_piwigo' => $photo->id_piwigo,
            ':url_picasa' => $photo->url_picasa,
            ':url_other' => $photo->url_other
        ]);

        $photoId = ($query->rowCount() === 1)? (int)$database->lastInsertId() : false;

        // This is added separately due to foreign key constraints
        if ($photoId && AlbumModel::isExistingById($photo->album_id)){
            $sql = "UPDATE photo
                    SET album_id = :album_id
                    WHERE id = :id";
            $query = $database->prepare($sql);
            $query->execute([
                ':album_id' => $photo->album_id,
                ':id' => $photoId
            ]);
        }

        return self::setPhotoId($photoId, $photo);
    }

    private static function setPhotoId($photoId, Db\Photo $photo){
        Lib\MyLogger::log('Photo ID: ' . $photoId . '<br />');

        if (!empty($photoId) && empty($photo->getId())){
            $photo->setId($photoId);
        }

        return $photoId;
    }

    /**
     * Creates a new report photo association.
     * @param Db\ReportPhoto $reportPhoto Photo association object containing the data to be inserted.
     * @return bool|int The ID of the created photo association.
     */
    public static function addReportPhoto(Db\ReportPhoto $reportPhoto){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO report_photo (
                    photo_id,
                    suppress_caption,
                    custom_caption)
                VALUES (
                    :photo_id,
                    :suppress_caption,
                    :custom_caption)";
        $query = $database->prepare($sql);
        $query->execute([
            ':photo_id' => $reportPhoto->getPhoto()->getId(),
            ':suppress_caption' => (int) $reportPhoto->suppress_caption,    // Note: Type is automatically converted to string unless PHP can tell otherwise. This is needed for correct boolean integer results when false.
            ':custom_caption' => $reportPhoto->custom_caption
        ]);
        $reportPhotoId = ($query->rowCount() === 1)? (int)$database->lastInsertId() : false;

        Lib\MyLogger::log('Report Photo ID: ' . $reportPhotoId . '<br />');

        if (!empty($reportPhotoId) && empty($reportPhoto->getId())){
            $reportPhoto->setId($reportPhotoId);
        }

        return $reportPhotoId;
    }



    // =============== Read ===============
    /**
     * Returns true if the photo exists. Otherwise, returns false.
     * @param string $url
     * @return int|bool
     */
    public static function isExisting($url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id FROM photo WHERE url = :url";
        $query = $database->prepare($sql);
        $query->execute([':url' => $url]);

        return ($query->rowCount() !== 0);
    }

    /**
     * Gets the photo data for all photos.
     * @return array
     */
    public static function getAllPhotos(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    id,
                    album_id,
                    url,
                    caption,
                    width,
                    height,
                    latitude,
                    longitude,
                    time_stamp,
                    is_public,
                    file_name,
                    url_piwigo,
                    id_piwigo,
                    url_picasa,
                    url_other
                FROM photo';

        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Gets the photo data based on the photo id.
     * @param $photoId
     * @return mixed
     */
    public static function getPhotoById($photoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    id,
                    album_id,
                    url,
                    caption,
                    width,
                    height,
                    latitude,
                    longitude,
                    time_stamp,
                    is_public,
                    file_name,
                    url_piwigo,
                    id_piwigo,
                    url_picasa,
                    url_other
                FROM photo
                WHERE id = :id';

        $query = $database->prepare($sql);
        $query->execute([':id' => $photoId]);
        return $query->fetch();
    }

    /**
     * Gets the photo data for all photos associated with the given report ID.
     * @param $reportId
     * @return array
     */
    public static function getAllPhotosByReportId($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    p.id,
                    p.album_id,
                    p.url,
                    p.caption,
                    p.width,
                    p.height,
                    p.latitude,
                    p.longitude,
                    p.time_stamp,
                    p.is_public,
                    p.file_name,
                    p.url_other,
                    p.url_piwigo,
                    p.id_piwigo,
                    p.url_picasa
                FROM photo p
                    JOIN report_photo rp ON (rp.photo_id = p.id)
                    JOIN report_trip_body rtb ON (rp.id = rtb.report_photo_id)
                WHERE rtb.report_trip_id = :reportId';

        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);
        return $query->fetchAll();
    }

    /**
     * Gets the photo report data for all report photos associated with the given report ID.
     * @param $reportId
     * @return array
     */
    public static function getAllReportPhotosByReportId($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    rp.id,
                    rp.photo_id,
                    rp.suppress_caption,
                    rp.custom_caption
                FROM report_photo rp
                    JOIN report_trip_body rtb ON (rp.id = rtb.report_photo_id)
                WHERE rtb.report_trip_id = :reportId';

        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);
        return $query->fetchAll();
    }

    /**
     * Gets the photo report data for a photo associated with the given report ID.
     * @param $reportPhotoId
     * @return mixed
     */
    public static function getReportPhotoById($reportPhotoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    id,
                    photo_id,
                    suppress_caption,
                    custom_caption
                FROM report_photo
                WHERE id = :id';

        $query = $database->prepare($sql);
        $query->execute([':id' => $reportPhotoId]);
        return $query->fetch();
    }

    /**
     * Gets the photo ID associated with the report photo ID.
     * @param int $reportPhotoId
     * @return int
     */
    public static function getPhotoIdFromReportPhotoId($reportPhotoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT photo_id
                FROM report_photo
                WHERE id = :reportPhotoId';
        $query = $database->prepare($sql);
        $query->execute([':reportPhotoId' => $reportPhotoId]);

        return $query->fetch()->photo_id;
    }

    /**
     * Gets the photo ID associated with the photo URL.
     * @param $url
     * @return int
     */
    public static function getPhotoIdFromUrl($url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT id
                FROM photo
                WHERE url = :url';
        $query = $database->prepare($sql);
        $query->execute([':url' => $url]);

        return $query->fetch()->id;
    }

    /**
     * Returns the number of times a photo is referenced by a report.
     * @param int $photoId
     * @return int
     */
    public static function countPhotoReferencedByReports($photoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT photo_id
                FROM report_photo
                WHERE photo_id = :id';

        $query = $database->prepare($sql);
        $query->execute([':id' => $photoId]);
        return $query->rowCount();
    }


    public static function getPhotoUrls(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                    id,
                    url
                FROM photo
                WHERE url IS NOT NULL";
        $query = $database->prepare($sql);
        $query->execute();

        $urls = [];
        $results = $query->fetchAll();
        foreach($results as $result){
            if (!empty($result->url)){
                $urls[$result->id] = $result->url;
            }
        }
        return $urls;
    }


    public static function getPhotoUrlsOther(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                    id,
                    url_other
                FROM photo
                WHERE url_other IS NOT NULL";
        $query = $database->prepare($sql);
        $query->execute();

        $urls = [];
        $results = $query->fetchAll();
        foreach($results as $result){
            if (!empty($result->url_other)){
                $urls[$result->id] = $result->url_other;
            }
        }
        return $urls;
    }


    public static function getPhotoUrlsPicasa(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                    id,
                    url_picasa
                FROM photo
                WHERE url_picasa IS NOT NULL";
        $query = $database->prepare($sql);
        $query->execute();

        $urls = [];
        $results = $query->fetchAll();
        foreach($results as $result){
            if (!empty($result->url_picasa)){
                $urls[$result->id] = $result->url_picasa;
            }
        }
        return $urls;
    }


    public static function getPhotoUrlsPiwigo(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                    id,
                    url_piwigo
                FROM photo
                WHERE url_piwigo IS NOT NULL";
        $query = $database->prepare($sql);
        $query->execute();

        $urls = [];
        $results = $query->fetchAll();
        foreach($results as $result){
            if (!empty($result->url_piwigo)){
                $urls[$result->id] = $result->url_piwigo;
            }
        }
        return $urls;
    }

    // =============== Update ===============
    public static function updatePhotoUrls(array $ids, array $urls){
        for($i = 0; $i < count($ids); $i++){
            self::updatePhotoUrl($ids[$i], $urls[$i]);
        }
    }

    public static function updatePhotoUrl($id, $url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE photo
                    SET url = :url
                    WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute([
            ':url' => $url,
            ':id' => $id
        ]);
    }


    public static function updatePhotoUrlOther($id, $urlOther){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE photo
                    SET url_other = :urlOther
                    WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute([
            ':urlOther' => $urlOther,
            ':id' => $id
        ]);
    }

    public static function updatePhotoUrlPicasa($id, $picasaUrl){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE photo
                    SET url_picasa = :picasaUrl
                    WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute([
            ':picasaUrl' => $picasaUrl,
            ':id' => $id
        ]);
    }

    public static function updatePhotoUrlPiwigo($id, $piwigoId = 0, $piwigoUrl){
        $database = DatabaseFactory::getFactory()->getConnection();

        if ($piwigoId){
            $sql = "UPDATE photo
                    SET
                      url_piwigo = :urlPiwigo,
                      id_piwigo = :idPiwigo
                    WHERE id = :id";
            $query = $database->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':urlPiwigo' => $piwigoUrl,
                ':idPiwigo' => $piwigoId,
            ]);
        } else {
            $sql = "UPDATE photo
                    SET
                      url_piwigo = :urlPiwigo
                    WHERE id = :id";
            $query = $database->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':urlPiwigo' => $piwigoUrl
            ]);
        }
    }


    /**
     * Copies all Picasa photo URLs back to the main URL column.
     */
    public static function usePicasaPhotoUrls(){
        $urls = self::getPhotoUrlsPicasa();
        self::updatePhotoUrls(array_keys($urls), array_values($urls));
    }

    /**
     * Copies all Piwigo photo URLs back to the main URL column.
     */
    public static function usePiwigoPhotoUrls(){
        $urls = self::getPhotoUrlsPiwigo();
        self::updatePhotoUrls(array_keys($urls), array_values($urls));
    }

    /**
     * Copies all Other photo URLs back to the main URL column.
     */
    public static function useOtherPhotoUrls(){
        $urls = self::getPhotoUrlsOther();
        self::updatePhotoUrls(array_keys($urls), array_values($urls));
    }

    // =============== Destroy ===============
    /**
     * Deletes the photo.
     * @param int $photoId
     * @return bool True if deletion was successful.
     */
    public static function deletePhoto($photoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'DELETE FROM photo WHERE id = :id';
        $query = $database->prepare($sql);
        $query->execute([':id' => $photoId]);

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_PHOTO_DELETION_FAILED'));
        return false;
    }

    /**
     * Deletes the photo association with the report.
     * @param int $reportPhotoId ID of the photo as it was used in the report.
     * @return bool True if deletion was successful.
     */
    public static function deleteReportPhoto($reportPhotoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'DELETE FROM report_photo WHERE id = :id';
        $query = $database->prepare($sql);
        $query->execute([':id' => $reportPhotoId]);

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_REPORT_PHOTO_DELETION_FAILED'));
        return false;
    }

    /**
     * Deletes the report photo. If this orphans the base photo, then that is deleted as well.
     * @param $reportPhotoId
     * @return bool
     */
    public static function deleteReportAndBasePhoto($reportPhotoId){
        // 1. Get base photo id
        $photoId = self::getPhotoIdFromReportPhotoId($reportPhotoId);

        // 2. Remove photo
        $numberOfReferences = self::countPhotoReferencedByReports($photoId);
        if ($numberOfReferences == 1){ // If only one base photo, remove base photo first, which cascades
            $result = self::deletePhoto($photoId);
        } else { // Remove report photo
            $result = self::deleteReportPhoto($reportPhotoId);
        }
        return $result;
    }



    /**
     * Deletes all photos in the photo tables.
     * THIS DOES A COMPLETE WIPE!!
     * Only use this method for resetting all data cleanly without dropping tables.
     */
    public static function deleteAllPhotos(){
        $database = DatabaseFactory::getFactory()->getConnection();

        // Delete all entries in photo
        Lib\MyPDOManager::clearTable($database, 'photo');
    }
} 