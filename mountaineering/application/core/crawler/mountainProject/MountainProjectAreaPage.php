<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;

class MountainProjectAreaPage
    extends Lib\KeyValueData
{
    public $pageId;
    public $pageURL;
    public $name;
    public $description;
    public $gettingThere;
    public $latitude;
    public $longitude;
    public $parentName;
    public $parentURL;

    const PAGE_ID = "id";
    const PAGE_URL = "area_url";
    const NAME = "area_name";
    const DESCRIPTION = "description";
    const GETTING_THERE = "getting_there";
    const LATITUDE = "latitude";
    const LONGITUDE = "longitude";
    const PARENT_NAME = "parent_name";
    const PARENT_URL = "parent_url";
    const FILE_PREFIX = "Area_";

    function __construct()
    {
        $this->filePrefix = self::FILE_PREFIX;
    }

    public function readKeyValueLine($line){
        $line = $this->cleanText($line);
        $this->readKeyValue($line);
    }

    public function readKeyValue($line){
        $this->setCurrentKeyValue($line);

        switch ($this->currentKey)
        {
            case self::PAGE_ID:
                $this->pageId = $this->currentValue;
                break;
            case self::PAGE_URL:
                $this->pageURL = $this->currentValue;
                break;
            case self::NAME:
                $this->name = $this->currentValue;
                break;
            case self::DESCRIPTION:
                $this->description = $this->currentValue;
                break;
            case self::GETTING_THERE:
                $this->gettingThere = $this->currentValue;
                break;
            case self::LATITUDE:
                $this->latitude = $this->currentValue;
                break;
            case self::LONGITUDE:
                $this->longitude = $this->currentValue;
                break;
            case self::PARENT_NAME:
                $this->parentName = $this->currentValue;
                break;
            case self::PARENT_URL:
                $this->parentURL = $this->currentValue;
                break;
            default:
        }
    }

    public function isFilled(){
        return ($this->pageId !== null &&
            $this->pageURL !== null &&
            $this->name !== null &&
            $this->description !== null &&
            $this->gettingThere !== null &&
            $this->latitude !== null &&
            $this->longitude !== null &&
            $this->parentName !== null &&
            $this->parentURL !== null);
    }

    public function multipleRecordsInFile(){
        return false;
    }


    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_mountainproject_area (
                        page_id,
                        url,
                        name,
                        description,
                        getting_there,
                        latitude,
                        longitude,
                        parent_name,
                        parent_url)
                    VALUES (
                        :pageId,
                        :pageURL,
                        :name,
                        :description,
                        :gettingThere,
                        :latitude,
                        :longitude,
                        :parentName,
                        :parentURL)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':pageURL' => $this->pageURL,
            ':name' => $this->name,
            ':description' => $this->description,
            ':gettingThere' => $this->gettingThere,
            ':latitude' => $this->latitude,
            ':longitude' => $this->longitude,
            ':parentName' => $this->parentName,
            ':parentURL' => $this->parentURL
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_mountainproject_area
                SET
                    url = :pageURL,
                    name = :name,
                    description = :description,
                    getting_there = :gettingThere,
                    latitude = :latitude,
                    longitude = :longitude,
                    parent_name = :parentName,
                    parent_url = :parentURL
                WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':pageURL' => $this->pageURL,
            ':name' => $this->name,
            ':description' => $this->description,
            ':gettingThere' => $this->gettingThere,
            ':latitude' => $this->latitude,
            ':longitude' => $this->longitude,
            ':parentName' => $this->parentName,
            ':parentURL' => $this->parentURL
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_mountainproject_area
                  WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $this->pageId]);
        return ($query->fetch()->num_rows > 0);
    }

    public static function factory(){
        return new MountainProjectAreaPage();
    }
}