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

class PageModel {

    // =============== Create ===============
    /**
     * Creates a new page.
     * @param Db\Page $page Page object containing the data to be inserted.
     * @param bool $returnExisting True: If the page already exists (based on full title), the existing page ID is returned. Otherwise an error occurs.
     * @return bool|int The ID of the created page, or either the ID of an existing page or false if the page already exists.
     */
    public static function createPage(Db\Page $page, $returnExisting = true){
        $database = DatabaseFactory::getFactory()->getConnection();

        // If one already exists, return the id.
        if ($result = self::isExisting($page->title_full)){
            if ($returnExisting){
                return $result;
            }
            Session::add('feedback_negative', Text::get('FEEDBACK_PAGE_ALREADY_EXISTS'));
            return false;
        }

        $sql = "INSERT INTO page (
                    title_menu,
                    title_full,
                    description,
                    photo_id,
                    url,
                    date_created,
                    date_modified,
                    status_id,
                    tasks,
                    is_public,
                    views_count,
                    user_id)
                VALUES (
                    :title_menu,
                    :title_full,
                    :description,
                    :photo_id,
                    :url,
                    NOW(),
                    NOW(),
                    :status_id,
                    :page_tasks,
                    :is_public,
                    :views_count,
                    :user_id)";
        $query = $database->prepare($sql);
        $query->execute([
            ':title_menu' => $page->title_menu,
            ':title_full' => $page->title_full,
            ':description' => $page->description,
            ':photo_id' => $page->getPhoto()->getId(),
            ':url' => $page->url,
            ':status_id' => $page->getStatusId(),
            ':page_tasks' => $page->tasks,
            ':is_public' => $page->getIsPublic(),
            ':views_count' => $page->views_count,
            ':user_id' => $page->user_id,
        ]);
        $pageId = ($query->rowCount() === 1)? (int)$database->lastInsertId() : false;
        Lib\MyLogger::log('Page ID: ' . $pageId . '<br />');

        if (!empty($pageId) && empty($page->getId())){
            $page->setId($pageId);
        }

        return $pageId;
    }



    // =============== Read ===============
    /**
     * Returns the page id if the page exists. Otherwise, returns false.
     * @param string $titleFull
     * @return int|bool
     */
    public static function isExisting($titleFull){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id
                FROM page
                WHERE title_full = :title_full";
        $query = $database->prepare($sql);
        $query->execute([':title_full' => $titleFull]);

        return ($query->rowCount() !== 0)? (int)$query->fetch()->id : false;
    }

    /**
     * Returns the page id if the page exists only once. Otherwise, returns false.
     * @param string $titleFull
     * @return int|bool
     */
    public static function isUnique($titleFull){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id
                FROM page
                WHERE title_full = :title_full";
        $query = $database->prepare($sql);
        $query->execute([':title_full' => $titleFull]);

        return ($query->rowCount() === 1)? (int)$query->fetch()->id : false;
    }

    /**
     * Gets the page id by looking up the unique URL.
     * @param string $pageUrl
     * @return int|bool
     */
    public static function getPageIdByUrl($pageUrl){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql= 'SELECT id FROM page WHERE url = :url';
        $query = $database->prepare($sql);
        $query->execute([':url' => $pageUrl]);

        return ($query->rowCount() === 1)? (int)$query->fetch()->id : false;
    }

    /**
     * Gets the page URL by looking up the unique id.
     * @param string $pageId
     * @return int|bool
     */
    public static function getPageUrlById($pageId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql= 'SELECT url FROM page WHERE id = :id';
        $query = $database->prepare($sql);
        $query->execute([':id' => $pageId]);

        return ($query->rowCount() === 1)? (int)$query->fetch()->url : false;
    }

    /**
     * Gets the page data for all pages.
     * @param int $userId User ID to limit the pages by.
     * @return array
     */
    public static function getAllPages($userId = 0){
        $database = DatabaseFactory::getFactory()->getConnection();
        $userIdArray = [];

        $sql = 'SELECT
                    id,
                    title_menu,
                    title_full,
                    description,
                    photo_id,
                    url,
                    date_created,
                    date_modified,
                    status_id,
                    tasks,
                    is_public,
                    views_count,
                    user_id
                FROM page';
        if ($userId){
            $sql .= 'WHERE user_id = :user_id';
            $userIdArray = [':user_id' => $userId];
        }

        $query = $database->prepare($sql);
        $query->execute($userIdArray);

        $result = null;
        if ($query->rowCount() === 1){
            $result = $query->fetchAll();
        }

        // Remove private pages.
        $pages =[];
        foreach ($result as $page){
            if (!Privacy::isPrivate($page)){
                $pages[] = $page;
            }
        }
        return $pages;
    }

    /**
     * Gets the page data based on the page ID.
     * @param int $pageId
     * @return mixed|null
     */
    public static function getPageById($pageId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    id,
                    title_menu,
                    title_full,
                    description,
                    photo_id,
                    url,
                    date_created,
                    date_modified,
                    status_id,
                    tasks,
                    is_public,
                    views_count,
                    user_id
                FROM page
                WHERE id = :pageId';

        $query = $database->prepare($sql);
        $query->execute([':pageId' => $pageId]);

        $result = null;
        if ($query->rowCount() === 1){
            $result = $query->fetch();
        }

        // Abort if the page is private
        return (Privacy::enforcePrivacyStd($result));
    }

    /**
     * Gets the page data based on the page URL.
     * @param int $pageUrl
     * @return mixed|null
     */
    public static function getPageByUrl($pageUrl){
        $pageId = self::getPageIdByUrl($pageUrl);
        return self::getPageById($pageId);
    }


    /**
     * Gets the page report data, tailored for trip report views, based on the page ID.
     * @param int $pageId
     * @return mixed|null
     */
    public static function getReportPageById($pageId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                    r.id as report_trip_id,
                    r.page_id,
                    r_status.name AS report_status,
                    r_type.name AS report_trip_type,
                    p.title_menu,
                    p.title_full,
                    p.description,
                    p.url,
                    photo.url AS photo_url,
                    photo.caption AS photo_caption,
                    p.date_created,
                    p_status.name AS page_status,
                    p.tasks,
                    p.is_public,
                    p.views_count,
                    p.user_id
                FROM
                    report_trip r
                    LEFT JOIN page p ON (r.page_id = p.id)
                    LEFT JOIN photo ON (p.photo_id = photo.id)
                    LEFT JOIN report_trip_type r_type ON (r.report_trip_type_id = r_type.id)
                    LEFT JOIN status r_status ON (r.status_id = r_status.id)
                    LEFT JOIN status p_status ON (p.status_id = p_status.id)
                WHERE r.page_id = :pageId';
        $query = $database->prepare($sql);
        $query->execute([':pageId' => $pageId]);

        $result = null;
        if ($query->rowCount() === 1){
            $result = $query->fetch();
        }

        // Abort if the page is private
        return (Privacy::enforcePrivacyStd($result));
    }

    /**
     * Returns the photo ID associated with the page.
     * @param int $pageId
     * @return int|null
     */
    public static function getPagePhotoId($pageId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT photo_id
                FROM page
                WHERE id = :pageId';

        $query = $database->prepare($sql);
        $query->execute([':pageId' => $pageId]);

        return $query->fetch();
    }

    // =============== Update ===============




    // =============== Destroy ===============
    /**
     * Deletes the page.
     * @param int $pageId
     * @return bool True if deletion was successful.
     */
    public static function deletePage($pageId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'DELETE FROM page WHERE id = :pageId';
        $query = $database->prepare($sql);
        $query->execute([':pageId' => $pageId]);

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_PAGE_DELETION_FAILED'));
        return false;
    }

    // =============== Header ===============
    public static function getHeaderKeywords($pageData){
        // 1. Get value from page data header
        $headerKeywords = '';
        // TODO: Add keywords to database

        // 2. Append values to default values from config
        $headerKeywords .= Config::get('HEADER_DEFAULT_KEYWORDS');

        return $headerKeywords;
    }

    public static function getHeaderTitle($pageData){
        // 1. Get value from page data header
        $headerTitle = null;
        if ($pageData !== null && isset($pageData->header)) {
            $headerTitle = $pageData->header['page']['title_full'];
        }

        // 2. If not present, get default value from config
        if ($headerTitle === null || $headerTitle === '') {
            $headerTitle = Config::get('HEADER_DEFAULT_TITLE');
        }

        return $headerTitle;
    }


    public static function getHeaderDescription($pageData){
        // 1. Get value from page data header
        $headerDescription = null;
        if ($pageData !== null && isset($pageData->header)) {
            $headerDescription = $pageData->header['page']['description'];
        }

        // 2. If not present, get value from page data body
        if (($headerDescription === null || $headerDescription === '') && isset($pageData->bodies)) {
            $bodies = $pageData->bodies;
            $numberOfItems = count($bodies);
            for ($i = 0; $i < $numberOfItems; $i++) {
                $value = $bodies[$i]['text_body'];
                if ($value !== null && $value !== '')
                {
                    $headerDescription = $value;
                    break;
                }
            }
        }

        // 3. If not present, get default value from config
        if ($headerDescription === null || $headerDescription === '') {
            $headerDescription = Config::get('HEADER_DEFAULT_DESCRIPTION');
        }

        return $headerDescription;
    }


    public static function getHeaderImage($pageData){
        // 1. Get value from page data header
        $headerImage = null;
        if ($pageData !== null && isset($pageData->header)) {
            $headerImage = $pageData->header['page']['photo']['url'];
        }

        // 2. If not present, get value from page data body
        if (($headerImage === null || $headerImage === '') && isset($pageData->bodies)) {
            $bodies = $pageData->bodies;
            $numberOfItems = count($bodies);
            for ($i = 0; $i < $numberOfItems; $i++) {
                $value = $bodies[$i]['report_photo']['photo']['url'];
                if ($value !== null && $value !== '')
                {
                    $headerImage = $value;
                    break;
                }
            }
        }

        // 3. If not present, get default value from config
        if ($headerImage === null || $headerImage === '') {
            $headerImage = Config::get('HEADER_DEFAULT_IMAGE');
        }

        if (substr($headerImage, 0, 4) !== 'http' && substr($headerImage, 0, 4) !== 'www.') {
            $headerImage = Config::get('URL') . $headerImage;
        }

        return $headerImage;
    }

} 