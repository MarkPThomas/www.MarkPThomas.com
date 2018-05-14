<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;


class MountainProjectRouteOverwritePage
    extends Lib\KeyValueData
{
    public $pageURL;
    public $myRating;
    public $myStars;

    const PAGE_URL = "route_url";
    const MY_RATING = "my_rating";
    const MY_STARS = "my_stars";
    const FILE_PREFIX = "route_overwrites";

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
            case self::PAGE_URL:
                $this->pageURL = $this->currentValue;
                $this->tableIdentifier = $this->pageURL;
                break;
            case self::MY_RATING:
                $this->myRating = $this->currentValue;
                break;
            case self::MY_STARS:
                $this->myStars = $this->currentValue;
                break;
            default:
        }
    }

    public function isFilled(){
        return ($this->pageURL !== null &&
                $this->myRating !== null &&
                $this->myStars !== null);
    }

    public function multipleRecordsInFile(){
        return true;
    }


    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_mountainproject_route_overwrite (
                        url,
                        my_rating,
                        my_stars)
                    VALUES (
                        :url,
                        :myRating,
                        :myStars)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':url' => $this->pageURL,
            ':myRating' => $this->myRating,
            ':myStars' => $this->myStars
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_mountainproject_route_overwrite
                SET
                    my_rating = :my_rating,
                    my_stars = :my_stars
                WHERE url = :url";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageURL' => $this->pageURL,
            ':myRating' => $this->myRating,
            ':myStars' => $this->myStars
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_mountainproject_route_overwrite
                  WHERE url = :url";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':url' => $this->pageURL]);
        return ($query->fetch()->num_rows > 0);
    }


    public static function factory(){
        return new MountainProjectRouteOverwritePage();
    }
}