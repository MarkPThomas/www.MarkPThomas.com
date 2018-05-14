<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;

/**
 * Class SuperTopoRoutePage
 * @package markpthomas\crawler\superTopo
 */
class SuperTopoRoutePage extends Lib\KeyValueData
{
    public $pageURL;
    public $title;
    public $climbingArea;
    public $formation;
    public $quality;
    public $rating;
    public $pitchNumber;
    public $imgURL;

    const PAGE_URL = "result_url";
    const TITLE = "result_title";
    const CLIMBING_AREA = "result_climbing_area";
    const FORMATION = "result_formation";
    const QUALITY = "result_quality";
    const RATING = "result_rating";
    const PITCH_NUMBER = "pitch_number";
    const IMG_URL = "img_url";

    const FILE_PREFIX = "routes";

    function __construct()
    {
        $this->filePrefix = self::FILE_PREFIX;
    }


    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_supertopo_route (
                        url,
                        title,
                        climbing_area,
                        formation,
                        quality,
                        rating,
                        pitch_number,
                        img_url)
                    VALUES (
                        :url,
                        :title,
                        :climbingArea,
                        :formation,
                        :quality,
                        :rating,
                        :pitchNumber,
                        :imgURL)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':url' => $this->pageURL,
            ':title' => $this->title,
            ':climbingArea' => $this->climbingArea,
            ':formation' => $this->formation,
            ':quality' => $this->quality,
            ':rating' => $this->rating,
            ':pitchNumber' => $this->pitchNumber,
            ':imgURL' => $this->imgURL
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_supertopo_route
                SET
                    title = :title,
                    climbing_area = :climbingArea,
                    formation = :formation,
                    quality = :quality,
                    rating = :rating,
                    pitch_number = :pitchNumber,
                    img_url = :imgURL
                WHERE url = :url";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':url' => $this->pageURL,
            ':title' => $this->title,
            ':climbingArea' => $this->climbingArea,
            ':formation' => $this->formation,
            ':quality' => $this->quality,
            ':rating' => $this->rating,
            ':pitchNumber' => $this->pitchNumber,
            ':imgURL' => $this->imgURL
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_supertopo_route
                  WHERE url = :url";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':url' => $this->pageURL]);
        return ($query->fetch()->num_rows > 0);
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
                break;
            case self::TITLE:
                $this->title = $this->currentValue;
                break;
            case self::CLIMBING_AREA:
                $this->climbingArea = $this->currentValue;
                break;
            case self::FORMATION:
                $this->formation = $this->currentValue;
                break;
            case self::QUALITY:
                $this->quality = $this->currentValue;
                break;
            case self::RATING:
                $this->rating = $this->currentValue;
                break;
            case self::PITCH_NUMBER:
                $this->pitchNumber = $this->currentValue;
                break;
            case self::IMG_URL:
                $this->imgURL = $this->currentValue;
                break;
            default:
        }
    }

    public function isFilled(){
        return ($this->pageURL !== null &&
            $this->title !== null &&
            $this->climbingArea !== null &&
            $this->formation !== null &&
            $this->quality !== null &&
            $this->rating !== null &&
            $this->pitchNumber !== null &&
            $this->imgURL !== null);
    }

    public function multipleRecordsInFile(){
        return true;
    }

    public static function factory(){
        return new SuperTopoRoutePage();
    }
}