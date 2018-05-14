<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;


class MountainProjectRoutePage
    extends Lib\KeyValueData
{
    public $pageId;
    public $pageURL;
    public $name;
    public $description;
    public $routeQuality;
    public $rating;
    public $ratingOriginal;
    public $routeType;
    public $parentName;
    public $parentURL;

    const PAGE_ID = "id";
    const PAGE_URL = "route_url";
    const NAME = "route_name";
    const DESCRIPTION = "route_description";
    const ROUTE_QUALITY = "route_quality";
    const RATING = "route_rating";
    const RATING_ORIGINAL = "route_rating_original";
    const ROUTE_TYPE = "route_type";
    const PARENT_NAME = "parent_name";
    const PARENT_URL = "parent_url";
    const FILE_PREFIX = "Route_";

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
            case self::ROUTE_QUALITY:
                $this->routeQuality = $this->currentValue;
                break;
            case self::RATING:
                $this->rating = $this->currentValue;
                break;
            case self::RATING_ORIGINAL:
                $this->ratingOriginal = $this->currentValue;
                break;
            case self::ROUTE_TYPE:
                $this->routeType = $this->currentValue;
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
            $this->routeQuality !== null &&
            $this->rating !== null &&
            $this->ratingOriginal !== null &&
            $this->routeType !== null &&
            $this->parentName !== null &&
            $this->parentURL !== null);
    }

    public function multipleRecordsInFile(){
        return false;
    }

    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_mountainproject_route (
                        page_id,
                        url,
                        name,
                        description,
                        route_quality,
                        rating,
                        rating_original,
                        route_type,
                        parent_name,
                        parent_url)
                    VALUES (
                        :pageId,
                        :url,
                        :name,
                        :description,
                        :routeQuality,
                        :rating,
                        :ratingOriginal,
                        :routeType,
                        :parentName,
                        :parentURL)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':description' => $this->description,
            ':routeQuality' => $this->routeQuality,
            ':rating' => $this->rating,
            ':ratingOriginal' => $this->ratingOriginal,
            ':routeType' => $this->routeType,
            ':parentName' => $this->parentName,
            ':parentURL' => $this->parentURL
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_mountainproject_route
                SET
                    url = :url,
                    name = :name,
                    description = :description,
                    route_quality = :routeQuality,
                    rating = :rating,
                    rating_original = :ratingOriginal,
                    route_type = :routeType,
                    parent_name = :parentName,
                    parent_url = :parentURL
                WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':description' => $this->description,
            ':routeQuality' => $this->routeQuality,
            ':rating' => $this->rating,
            ':ratingOriginal' => $this->ratingOriginal,
            ':routeType' => $this->routeType,
            ':parentName' => $this->parentName,
            ':parentURL' => $this->parentURL
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_mountainproject_route
                  WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $this->pageId]);
        return ($query->fetch()->num_rows > 0);
    }

    public static function factory(){
        return new MountainProjectRoutePage();
    }
}