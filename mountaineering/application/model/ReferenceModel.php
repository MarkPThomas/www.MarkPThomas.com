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

class ReferenceModel {

    // =============== Create ===============
    /**
     * Creates a new reference.
     * @param Db\Reference $reference Reference object containing the data to be inserted.
     * @param bool $returnExisting True: If the reference already exists (based on url), the existing reference ID is returned. Otherwise an error occurs.
     * @return bool|int The ID of the created reference, or either the ID of an existing reference or false if the reference already exists.
     */
    public static function addReference(Db\Reference $reference, $returnExisting = true){
        $database = DatabaseFactory::getFactory()->getConnection();

        // If one already exists, return the id.
        if ($result = self::isExisting($reference->getWebsiteURL())){
            if ($returnExisting){
                return $result;
            }
            Session::add('feedback_negative', Text::get('FEEDBACK_REFERENCE_ALREADY_EXISTS'));
            return false;
        }

        $sql = "INSERT INTO reference (
                  name,
                  description,
                  website_URL,
                  book_title,
                  book_author,
                  book_URL,
                  status_id,
                  private_file_URL,
                  is_public)
                VALUES (
                  :name,
                  :description,
                  :website_URL,
                  :book_title,
                  :book_author,
                  :book_URL,
                  :status_id,
                  :private_file_URL,
                  :is_public)";
        $query = $database->prepare($sql);
        $query->execute([
              ':name' => $reference->name,
              ':description' => $reference->description,
              ':website_URL' => $reference->getWebsiteURL(),
              ':book_title' => $reference->book_title,
              ':book_author' => $reference->book_author,
              ':book_URL' => $reference->book_URL,
              ':status_id' => $reference->getStatusId(),
              ':private_file_URL' => $reference->private_file_URL,
              ':is_public' => $reference->getIsPublic()
        ]);
        $referenceId = ($query->rowCount() === 1)? (int)$database->lastInsertId() : false;
        Lib\MyLogger::log('Reference ID: ' . $referenceId . '<br />');

        if (!empty($referenceId) && empty($reference->getId())){
            $reference->setId($referenceId);
        }

        return $referenceId;
    }

    // =============== Read ===============
    /**
     * Returns the reference id if the reference exists. Otherwise, returns false.
     * @param string $url
     * @return int|bool
     */
    public static function isExisting($url){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id FROM reference WHERE website_URL = :website_URL";
        $query = $database->prepare($sql);
        $query->execute([':website_URL' => $url]);

        return ($query->rowCount() !== 0)? (int)$query->fetch()->id : false;
    }

    /**
     * Gets the reference data for all references.
     * @return array
     */
    public static function getAllReferences(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                  id,
                  name,
                  description,
                  website_URL,
                  book_title,
                  book_author,
                  book_URL,
                  status_id,
                  private_file_URL,
                  is_public
                FROM reference';

        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Gets the reference data based on the reference id.
     * @param $referenceId
     * @return mixed
     */
    public static function getReferenceById($referenceId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                  id,
                  name,
                  description,
                  website_URL,
                  book_title,
                  book_author,
                  book_URL,
                  status_id,
                  private_file_URL,
                  is_public
                FROM reference
                WHERE id = :id';

        $query = $database->prepare($sql);
        $query->execute([':id' => $referenceId]);
        return $query->fetch();
    }

    /**
     * Gets the reference data for all references associated with a given report ID.
     * @param $reportId
     * @return array
     */
    public static function getReferencesByReportId($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = 'SELECT
                  r.id,
                  r.name,
                  r.description,
                  r.website_URL,
                  r.book_title,
                  r.book_author,
                  r.book_URL,
                  r.status_id,
                  r.private_file_URL,
                  r.is_public
                FROM reference r
                  JOIN report_trip_has_reference repToRef ON (r.id = repToRef.reference_id)
                WHERE repToRef.report_trip_id = :reportId';
        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);

        return $query->fetchAll();
    }

    /**
     * Gets the reference data, tailored for trip report views, for all references associated with a given report ID.
     * @param $reportId
     * @return array
     */
    public static function getReportReferencesByReportId($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                  r.id,
                  r.name,
                  r.description,
                  r.website_URL,
                  r.book_title,
                  r.book_author,
                  r.book_URL,
                  s.name AS status,
                  r.private_file_URL,
                  r.is_public
                FROM reference r
                  JOIN report_trip_has_reference repToRef ON (r.id = repToRef.reference_id)
                  LEFT JOIN status s ON (r.status_id = s.id)
                WHERE repToRef.report_trip_id = :reportId';
        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);

        return $query->fetchAll();
    }

    /**
     * Gets the reference urls for all external references associated with a given report ID.
     * @param $reportId
     * @return array
     */
    public static function getReportExternalReferenceUrlsByReportId($reportId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'SELECT
                  r.website_URL AS url
                FROM reference r
                  JOIN report_trip_has_reference repToRef ON (r.id = repToRef.reference_id)
                  LEFT JOIN status s ON (r.status_id = s.id)
                WHERE repToRef.report_trip_id = :reportId
                  AND r.website_URL IS NOT NULL
                  AND r.description = "External Link"';
        $query = $database->prepare($sql);
        $query->execute([':reportId' => $reportId]);

        $results = $query->fetchAll();
        if (isset($results[0]->url)){
            $urls = [];
            foreach($results as $result){
                $urls[] = $result->url;
            }
            return $urls;
        } else {
            return null;
        }
    }

    /**
     * Returns the number of reports referencing the reference specified.
     * @param int $referenceId
     * @return int
     */
    public static function countReportsAssociatedWithReference($referenceId){
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT report_trip_id
                FROM report_trip_has_reference
                WHERE reference_id = :referenceId";
        $query = $database->prepare($sql);
        $query->execute([':referenceId' => $referenceId]);

        return $query->rowCount();
    }

    /**
     * Returns the list of album ids that will be orphaned if the report of the ID provided is deleted.
     * @param int $reportId
     * @return array
     */
    public static function getReferenceIdsOrphanedByReportDelete($reportId){
        $references = self::getReportReferencesByReportId($reportId);
        $referenceIdsToDelete = [];
        foreach ($references as $reference){
            if (self::countReportsAssociatedWithReference($reference->id) == 1){
                $referenceIdsToDelete[] = $reference->id;
            }
        }
        return $referenceIdsToDelete;
    }

    // =============== Update ===============




    // =============== Destroy ===============
    /**
     * Deletes the reference.
     * @param int $referenceId
     * @return bool True if deletion was successful.
     */
    public static function deleteReference($referenceId){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = 'DELETE FROM reference WHERE id = :id';
        $query = $database->prepare($sql);
        $query->execute([':id' => $referenceId]);

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_REFERENCE_DELETION_FAILED'));
        return false;
    }

} 