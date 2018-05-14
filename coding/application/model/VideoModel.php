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

class VideoModel {
    // =============== Create ===============
    /**
     * Creates a new video.
     * @param Db\Video $video Video object containing the data to be inserted.
     * @param bool $returnExisting True: If the video already exists (based on url), the existing video ID is returned. Otherwise an error occurs.
     * @return bool|int The ID of the created video, or either the ID of an existing video or false if the page already exists.
     */
    public static function addVideo(Db\Video $video, $returnExisting = true){
        $database = DatabaseFactory::getFactory()->getConnection();

        // If one already exists, return the id.
        if ($result = self::isExisting($video->url)){
            if ($returnExisting){
                return self::setVideoId(self::getVideoIdFromUrl($video->url), $video);
            }
            Session::add('feedback_negative', Text::get('FEEDBACK_VIDEO_ALREADY_EXISTS'));
            return false;
        }

        $sql = "INSERT INTO video (
                    url,
                    caption,
                    width,
                    height,
                    latitude,
                    longitude,
                    time_stamp,
                    is_public,
                    id_youtube,
                    id_vimeo,
                    url_piwigo)
                VALUES (
                    :url,
                    :caption,
                    :width,
                    :height,
                    :latitude,
                    :longitude,
                    :time_stamp,
                    :is_public,
                    :id_youtube,
                    :id_vimeo,
                    :url_piwigo)";
        $query = $database->prepare($sql);
        $query->execute([
            ':url' => $video->url,
            ':caption' => $video->caption,
            ':width' => $video->width,
            ':height' => $video->height,
            ':latitude' => $video->latitude,
            ':longitude' => $video->longitude,
            ':time_stamp' => $video->time_stamp,
            ':is_public' => $video->getIsPublic(),
            ':id_youtube' => $video->id_youtube,
            ':id_vimeo' => $video->id_vimeo,
            ':url_piwigo' => $video->url_piwigo
        ]);

        $videoId = ($query->rowCount() === 1)? (int)$database->lastInsertId() : false;

        // This is added separately due to foreign key constraints
        if ($videoId && AlbumModel::isExistingById($video->album_id)){
            $sql = "UPDATE video
                    SET album_id = :album_id
                    WHERE id = :id";
            $query = $database->prepare($sql);
            $query->execute([
                ':album_id' => $video->album_id,
                ':id' => $videoId
            ]);
        }

        return self::setVideoId($videoId, $video);
    }

    private static function setVideoId($videoId, Db\Video $video){
        Lib\MyLogger::log('Video ID: ' . $videoId . '<br />');

        if (!empty($videoId) && empty($video->getId())){
            $video->setId($videoId);
        }

        return $videoId;
    }


    /**
     * Creates a new report video association.
     * @param Db\ReportVideo $reportVideo Video association object containing the data to be inserted.
     * @return bool|int The ID of the created video association.
     */
    public static function addReportVideo(Db\ReportVideo $reportVideo){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO report_video (
                    video_id,
                    suppress_caption,
                    custom_caption)
                VALUES (
                    :video_id,
                    :suppress_caption,
                    :custom_caption)";
        $query = $database->prepare($sql);
        $query->execute([
            ':video_id' => $reportVideo->getVideo()->getId(),
            ':suppress_caption' => (int) $reportVideo->suppress_caption,
            ':custom_caption' => $reportVideo->custom_caption
        ]);
        $reportVideoId = ($query->rowCount() === 1)? (int)$database->lastInsertId() : false;

        Lib\MyLogger::log('Report Video ID: ' . $reportVideoId . '<br />');

        if (!empty($reportVideoId) && empty($reportVideo->getId())){
            $reportVideo->setId($reportVideoId);
        }

        return $reportVideoId;
    }



    // =============== Read ===============
    /**
     * Returns true if the video exists. Otherwise, returns false.
     * @param string $url
     * @return int|bool
     */
    public static function isExisting($url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id FROM video WHERE url = :url";
        $query = $database->prepare($sql);
        $query->execute([':url' => $url]);

        return ($query->rowCount() !== 0);
    }

    /**
     * Gets the video data for all videos.
     * @return array
     */
    public static function getAllVideos(){
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
                    id_youtube,
                    id_vimeo,
                    url_piwigo
                FROM video';

        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Gets the video data based on the video id.
     * @param $videoId
     * @return mixed
     */
    public static function getVideoById($videoId){
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
                    id_youtube,
                    id_vimeo,
                    url_piwigo
                FROM video
                WHERE id = :id';

        $query = $database->prepare($sql);
        $query->execute([':id' => $videoId]);
        return $query->fetch();
    }

    /**
     * @param $reportVideoId
     * @return int|null
     */
    public static function getBaseVideoFromReportVideo($reportVideoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT video_id
                FROM report_video
                WHERE id = :reportVideoId';
        $query = $database->prepare($sql);
        $query->execute([':reportVideoId' => $reportVideoId]);
        return $query->fetch();
    }

    /**
     * Gets the video data for all videos associated with the given report ID.
     * @param $reportId
     * @return array
     */
    public static function getAllVideosByReportId($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    v.id,
                    v.album_id,
                    v.url,
                    v.caption,
                    v.width,
                    v.height,
                    v.latitude,
                    v.longitude,
                    v.time_stamp,
                    v.is_public,
                    v.id_youtube,
                    v.id_vimeo,
                    v.url_piwigo
                FROM video v
                    JOIN report_video rv ON (rv.photo_id = v.id)
                    JOIN report_trip_body rtb ON (rv.id = rtb.report_photo_id)
                WHERE rtb.report_trip_id = :reportId';

        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);
        return $query->fetchAll();
    }

    /**
     * Gets the video report data for all report videos associated with the given report ID.
     * @param $reportId
     * @return array
     */
    public static function getAllReportVideosByReportId($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    rv.id,
                    rv.photo_id,
                    rv.suppress_caption,
                    rv.custom_caption
                FROM report_video rv
                    JOIN report_trip_body rtb ON (rv.id = rtb.report_video_id)
                WHERE rtb.report_trip_id = :reportId';

        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);
        return $query->fetchAll();
    }

    /**
     * Gets the video report data for a video associated with the given report ID.
     * @param $reportVideoId
     * @return mixed
     */
    public static function getReportVideoById($reportVideoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    id,
                    video_id,
                    suppress_caption,
                    custom_caption
                FROM report_video
                WHERE id = :id';

        $query = $database->prepare($sql);
        $query->execute([':id' => $reportVideoId]);
        return $query->fetch();
    }

    /**
     * Gets the video ID associated with the report video ID.
     * @param int $reportVideoId
     * @return int
     */
    public static function getVideoIdFromReportVideoId($reportVideoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT video_id
                FROM report_video
                WHERE id = :reportVideoId';
        $query = $database->prepare($sql);
        $query->execute([':reportVideoId' => $reportVideoId]);

        return $query->fetch()->video_id;
    }

    /**
     * Gets the photo ID associated with the photo URL.
     * @param $url
     * @return int
     */
    public static function getVideoIdFromUrl($url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT id
                FROM video
                WHERE url = :url';
        $query = $database->prepare($sql);
        $query->execute([':url' => $url]);

        return $query->fetch()->id;
    }

    /**
     * Returns the number of times a video is referenced by a report.
     * @param int $videoId
     * @return int
     */
    public static function countVideoReferencedByReports($videoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT video_id
                FROM report_video
                WHERE video_id = :id';

        $query = $database->prepare($sql);
        $query->execute([':id' => $videoId]);
        return $query->rowCount();
    }

    // =============== Update ===============




    // =============== Destroy ===============
    /**
     * Deletes the video.
     * @param int $videoId
     * @return bool True if deletion was successful.
     */
    public static function deleteVideo($videoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'DELETE FROM video WHERE id = :id';
        $query = $database->prepare($sql);
        $query->execute([':id' => $videoId]);

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_VIDEO_DELETION_FAILED'));
        return false;
    }

    /**
     * Deletes the video association with the report.
     * @param int $reportVideoId ID of the video as it was used in the report.
     * @return bool True if deletion was successful.
     */
    public static function deleteReportVideo($reportVideoId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'DELETE FROM report_video WHERE id = :id';
        $query = $database->prepare($sql);
        $query->execute([':id' => $reportVideoId]);

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_REPORT_VIDEO_DELETION_FAILED'));
        return false;
    }

    /**
     * Deletes the report video. If this orphans the base video, then that is deleted as well.
     * @param $reportVideoId
     * @return bool
     */
    public static function deleteReportAndBaseVideo($reportVideoId){
        // 1. Get base video id
        $photoId = self::getVideoIdFromReportVideoId($reportVideoId);

        // 2. Remove video
        $numberOfReferences = self::countVideoReferencedByReports($photoId);
        if ($numberOfReferences == 1){ // If only one base video, remove base video first, which cascades
            $result = self::deleteVideo($photoId);
        } else { // Remove report video
            $result = self::deleteReportVideo($reportVideoId);
        }
        return $result;
    }



    /**
     * Deletes all all videos in the video table.
     * THIS DOES A COMPLETE WIPE!!
     * Only use this method for resetting all data cleanly without dropping tables.
     */
    public static function deleteAllVideos(){
        $database = DatabaseFactory::getFactory()->getConnection();

        // Delete all entries in video
        Lib\MyPDOManager::clearTable($database, 'video');
    }
} 