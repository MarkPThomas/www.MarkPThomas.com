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
} 