<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/29/18
 * Time: 10:22 PM
 */

namespace markpthomas\mountaineering;

use markpthomas\mountaineering\dbObjects as Db;
use markpthomas\library as Lib;

class AlbumModel {
    const piwigoCategoryUrlStub = '/photos/index.php?/category/';

    // =============== Create ===============
    /**
     * Creates a new photo/video album.
     * @param Db\Album $album Album object containing the data to be inserted.
     * @param bool $returnExisting True: If the album already exists (based on url), the existing album ID is returned. Otherwise an error occurs.
     * @return bool|int The ID of the created album, or either the ID of an existing album or false if the album already exists.
     */
    public static function addAlbum(Db\Album $album, $returnExisting = true){
        $database = DatabaseFactory::getFactory()->getConnection();

        // If one already exists, return the id.
        $result = self::isExistingByPicasa($album->url_picasa);
        if (!$result){
            $result = self::isExistingByPiwigo($album->url_piwigo);
        }
        if (!$result){
            $result = self::isExistingByOther($album->url_other);
        }
        if ($result){
            if ($returnExisting){
                return $result;
            }
            Session::add('feedback_negative', Text::get('FEEDBACK_ALBUM_ALREADY_EXISTS'));
            return false;
        }

        $sql = "INSERT INTO photo_album (
                    url,
                    title,
                    summary,
                    latitude,
                    longitude,
                    date,
                    status_id,
                    captions_status_id,
                    geotag_status_id,
                    is_public,
                    url_piwigo,
                    id_piwigo,
                    url_picasa,
                    url_other)
                VALUES (
                    :url,
                    :title,
                    :summary,
                    :latitude,
                    :longitude,
                    :date,
                    :status_id,
                    :captions_status_id,
                    :geotag_status_id,
                    :is_public,
                    :url_piwigo,
                    :id_piwigo,
                    :url_picasa,
                    :url_other)";
        $query = $database->prepare($sql);
        $query->execute([
            ':url' => $album->url,
            ':title' => $album->title,
            ':summary' => $album->summary,
            ':latitude' => $album->latitude,
            ':longitude' => $album->longitude,
            ':date' => $album->date,
            ':status_id' => $album->getAlbumStatusId(),
            ':captions_status_id' => $album->getCaptionStatusId(),
            ':geotag_status_id' => $album->getGeotagStatusId(),
            ':is_public' => $album->getIsPublic(),
            ':url_piwigo' => $album->url_piwigo,
            ':id_piwigo' => $album->id_piwigo,
            ':url_picasa' => $album->url_picasa,
            ':url_other' => $album->url_other
        ]);
        $albumId = ($query->rowCount() === 1)? (int)$database->lastInsertId() : false;
        Lib\MyLogger::log('Album ID: ' . $albumId . '<br />');

        if (!empty($albumId) && empty($album->getId())){
            $album->setId($albumId);
        }

        return $albumId;
    }


    // =============== Read ===============
    /**
     * Returns the album id if the album exists. Otherwise, returns false.
     * @param string $url
     * @return int|bool
     */
    public static function isExisting($url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id FROM photo_album WHERE url = :url";
        $query = $database->prepare($sql);
        $query->execute([':url' => $url]);

        return ($query->rowCount() !== 0)? (int)$query->fetch()->id : false;
    }

    /**
     * Returns the album id if the album exists. Otherwise, returns false.
     * @param string $url URL of the Picasa album
     * @return int|bool
     */
    public static function isExistingByPicasa($url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id FROM photo_album WHERE url_picasa = :url";
        $query = $database->prepare($sql);
        $query->execute([':url' => $url]);

        return ($query->rowCount() !== 0)? (int)$query->fetch()->id : false;
    }

    /**
     * Returns the album id if the album exists. Otherwise, returns false.
     * @param string $url URL of the piwigo album
     * @return int|bool
     */
    public static function isExistingByPiwigo($url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id FROM photo_album WHERE url_piwigo = :url";
        $query = $database->prepare($sql);
        $query->execute([':url' => $url]);

        return ($query->rowCount() !== 0)? (int)$query->fetch()->id : false;
    }

    /**
     * Returns the album id if the album exists. Otherwise, returns false.
     * @param string $url URL that is neither Picasa nor Piwigo.
     * @return int|bool
     */
    public static function isExistingByOther($url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id FROM photo_album WHERE url_other = :url";
        $query = $database->prepare($sql);
        $query->execute([':url' => $url]);

        return ($query->rowCount() !== 0)? (int)$query->fetch()->id : false;
    }

    /**
     * Returns the album id if the album exists. Otherwise, returns false.
     * @param int $id
     * @return int|bool
     */
    public static function isExistingById($id){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT COUNT(*) AS numberOfMatches FROM photo_album WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute([':id' => $id]);
        $numberOfMatches = $query->fetch()->numberOfMatches;
        return ($numberOfMatches === 1);
    }

    /**
     * Gets the album data for all albums.
     * @return array
     */
    public static function getAllAlbums(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    id,
                    url,
                    title,
                    summary,
                    latitude,
                    longitude,
                    date,
                    status_id,
                    captions_status_id,
                    geotag_status_id,
                    is_public,
                    url_piwigo,
                    id_piwigo,
                    url_picasa,
                    url_other
                FROM photo_album';

        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Gets the album data based on the album id.
     * @param $albumId
     * @return mixed
     */
    public static function getAlbumById($albumId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    id,
                    url,
                    title,
                    summary,
                    latitude,
                    longitude,
                    date,
                    status_id,
                    captions_status_id,
                    geotag_status_id,
                    is_public,
                    url_piwigo,
                    id_piwigo,
                    url_picasa,
                    url_other
                FROM photo_album
                WHERE id = :id';

        $query = $database->prepare($sql);
        $query->execute([':id' => $albumId]);
        return $query->fetch();
    }

    /**
     * Gets the album data for all albums associated with a given report ID.
     * @param $reportId
     * @return array
     */
    public static function getAlbumsByReportId($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = 'SELECT
                  p.id,
                  p.url,
                  p.title,
                  p.summary,
                  p.latitude,
                  p.longitude,
                  p.date,
                  p.status_id,
                  p.captions_status_id,
                  p.geotag_status_id,
                  p.is_public,
                  p.url_piwigo,
                  p.id_piwigo,
                  p.url_picasa,
                  p.url_other
                FROM photo_album p
                    JOIN report_trip_has_photo_album repToAlb ON (p.id = repToAlb.photo_album_id)
                WHERE repToAlb.report_trip_id = :reportId';
        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);

        return $query->fetchAll();
    }

    /**
     * Gets the album data, tailored for trip report views, for all albums associated with a given report ID.
     * @param $reportId
     * @return array
     */
    public static function getReportAlbumsByReportId($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT
                  photo_album.id,
                  photo_album.url,
                  photo_album.title,
                  photo_album.summary,
                  photo_album.latitude,
                  photo_album.longitude,
                  photo_album.date,
                  status.name AS album_status,
                  c_status.name AS captions_status,
                  g_status.name AS geotag_status,
                  photo_album.is_public,
                  photo_album.url_piwigo,
                  photo_album.id_piwigo,
                  photo_album.url_picasa,
                  photo_album.url_other
                FROM photo_album
                    JOIN report_trip_has_photo_album repToAlb ON (photo_album.id = repToAlb.photo_album_id)
                    LEFT JOIN status ON (photo_album.status_id = status.id)
                    LEFT JOIN status c_status ON (photo_album.captions_status_id = c_status.id)
                    LEFT JOIN status g_status ON (photo_album.geotag_status_id = g_status.id)
                WHERE repToAlb.report_trip_id = :reportId";
        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);

        return $query->fetchAll();
    }

    /**
     * Returns the number of reports referencing the album specified.
     * @param int $albumId
     * @return int
     */
    public static function countReportsAssociatedWithAlbum($albumId){
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT report_trip_id
                FROM report_trip_has_photo_album
                WHERE photo_album_id = :albumId";
        $query = $database->prepare($sql);
        $query->execute([':albumId' => $albumId]);

        return $query->rowCount();
    }

    /**
     * Returns the list of album ids that will be orphaned if the report of the ID provided is deleted.
     * @param int $reportId
     * @return array
     */
    public static function getAlbumIdsOrphanedByReportDelete($reportId){
        $albums = self::getAlbumsByReportId($reportId);
        $albumIdsToDelete = [];
        foreach ($albums as $album){
            if (self::countReportsAssociatedWithAlbum($album->id) == 1){
                $albumIdsToDelete[] = $album->id;
            }
        }
        return $albumIdsToDelete;
    }

    public static function getAlbumUrlById($id){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT url
                FROM photo_album
                WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute([':id' => $id]);

        $result = $query->fetch();

        return (isset($result->url))? $result->url : null;
    }

    public static function getAlbumUrls(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                    id,
                    url
                FROM photo_album
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

    public static function getAlbumUrlsPicasa(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                    id,
                    url_picasa
                FROM photo_album
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


    public static function getAlbumUrlsPiwigo(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                    id,
                    url_piwigo
                FROM photo_album
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

    public static function getAlbumUrlsOther(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                    id,
                    url_other
                FROM photo_album
                WHERE url_piwigo IS NOT NULL";
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

    public static function getAlbumTitles(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT
                    id,
                    title
                FROM photo_album";
        $query = $database->prepare($sql);
        $query->execute();

        $titles = [];
        $results = $query->fetchAll();
        foreach($results as $result){
            if (!empty($result->title)){
                $titles[$result->id] = $result->title;
            }
        }
        return $titles;
    }

    // =============== Update ===============
    public static function updateAlbumUrls(array $ids, array $urls){
        for($i = 0; $i < count($ids); $i++){
            self::updateAlbumUrl($ids[$i], $urls[$i]);
        }
    }

    public static function updateAlbumUrl($id, $url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE photo_album
                    SET url = :url
                    WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute([
            ':url' => $url,
            ':id' => $id
        ]);
    }

    public static function updateAlbumUrlOther($id, $urlOther){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE photo_album
                    SET url_other = :urlOther
                    WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute([
            ':urlOther' => $urlOther,
            ':id' => $id
        ]);
    }

    public static function updateAlbumUrlPicasa($id, $picasaUrl){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE photo_album
                    SET url_picasa = :urlPicasa
                    WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute([
            ':urlPicasa' => $picasaUrl,
            ':id' => $id
        ]);
    }

    public static function updateAlbumUrlPiwigo($id, $piwigoId = 0, $piwigoUrl){
        $database = DatabaseFactory::getFactory()->getConnection();

        if ($piwigoId){
            $sql = "UPDATE photo_album
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
            $sql = "UPDATE photo_album
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


    public static function updateAlbumTitle($id, $title){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE photo_album
                    SET title = :title
                    WHERE id = :id";
        $query = $database->prepare($sql);
        $query->execute([
            ':title' => $title,
            ':id' => $id
        ]);
    }

    /**
     * Copies all Picasa album URLs back to the main URL column.
     */
    public static function usePicasaAlbumUrls(){
        $urls = self::getAlbumUrlsPicasa();
        self::updateAlbumUrls(array_keys($urls), array_values($urls));
    }

    /**
     * Copies all Piwigo album URLs back to the main URL column.
     */
    public static function usePiwigoAlbumUrls(){
        $urls = self::getAlbumUrlsPiwigo();
        self::updateAlbumUrls(array_keys($urls), array_values($urls));
    }

    /**
     * Copies all Other photo URLs back to the main URL column.
     */
    public static function useOtherAlbumUrls(){
        $urls = self::getAlbumUrlsOther();
        self::updateAlbumUrls(array_keys($urls), array_values($urls));
    }


    // =============== Destroy ===============
    /**
     * Deletes the album.
     * @param int $albumId
     * @return bool True if deletion was successful.
     */
    public static function deleteAlbum($albumId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'DELETE FROM photo_album WHERE id = :albumId';
        $query = $database->prepare($sql);
        $query->execute([':albumId' => $albumId]);

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_ALBUM_DELETION_FAILED'));
        return false;
    }


}