<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;


class PeakBaggerRangePage
    implements Lib\IMySqlUpdate, Lib\IMySqlInsert, Lib\IReadCsvLine, Lib\IFactory
{
    public $pageId;
    public $pageURL;
    public $name;
    public $level;
    public $type;
    public $latitudeCenter;
    public $longitudeCenter;
    public $extentNS;
    public $extentEW;
    public $description;
    public $parent;
    protected $tableIdentifier;

    public function readCsvLine($line)
    {
        $values = str_getcsv($line, ",", '"');

        $this->pageId = trim($values[1]);
        $this->pageURL = trim($values[2]);
        $this->name = trim($values[3]);
        $this->level = trim($values[4]);
        $this->type = trim($values[5]);
        $this->latitudeCenter = trim($values[6]);
        $this->longitudeCenter = trim($values[7]);
        $this->extentNS = trim($values[8]);
        $this->extentEW = trim($values[9]);
        $this->description = trim($values[10]);
        $this->parent = trim($values[11]);

        $this->tableIdentifier = $this->pageId;
        return true;
    }

    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_peakbagger_range (
                        page_id,
                        url,
                        name,
                        level,
                        type,
                        latitude_center,
                        longitude_center,
                        extent_NS,
                        extent_EW,
                        description,
                        parent,
                        is_page_scraped)
                    VALUES (
                        :pageId,
                        :url,
                        :name,
                        :level,
                        :type,
                        :latitudeCenter,
                        :longitudeCenter,
                        :extentNS,
                        :extentEW,
                        :description,
                        :parent,
                        'yes')";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':level' => $this->level,
            ':type' => $this->type,
            ':latitudeCenter' => $this->latitudeCenter,
            ':longitudeCenter' => $this->longitudeCenter,
            ':extentNS' => $this->extentNS,
            ':extentEW' => $this->extentEW,
            ':description' => $this->description,
            ':parent' => $this->parent
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_peakbagger_range
                SET
                    url = :url,
                    name = :name,
                    level = :level,
                    type = :type,
                    latitude_center = :latitudeCenter,
                    longitude_center = :longitudeCenter,
                    extent_NS = :extentNS,
                    extent_EW = :extentEW,
                    description = :description,
                    parent = :parent
                WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':level' => $this->level,
            ':type' => $this->type,
            ':latitudeCenter' => $this->latitudeCenter,
            ':longitudeCenter' => $this->longitudeCenter,
            ':extentNS' => $this->extentNS,
            ':extentEW' => $this->extentEW,
            ':description' => $this->description,
            ':parent' => $this->parent
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_peakbagger_range
                  WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $this->pageId]);
        return ($query->fetch()->num_rows > 0);
    }


    public static function factory(){
        return new PeakBaggerRangePage();
    }

    public static function arrayFactory($count){
        $array = array();
        for ($i = 0; $i < $count; $i++) {
            $page = PeakBaggerRangePage::factory();
            array_push($array, $page);
        }
        return $array;
    }
}