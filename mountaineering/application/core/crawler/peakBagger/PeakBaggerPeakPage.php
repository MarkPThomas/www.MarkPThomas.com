<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;

/**
 * A sample class
 *
 * This class is just random php used as a {@link www.markpthomas.com phpdoc} example
 *
 * @version 1.0
 * @author Mark Thomas <markums@gmail.com>
 * @project test
 */
class PeakBaggerPeakPage
    implements Lib\IMySqlUpdate, Lib\IMySqlInsert, Lib\IReadCsvLine, Lib\IFactory
{
    public $pageId;
    public $pageURL;
    public $name;
    public $nameOther;
    public $elevation;
    public $latitude;
    public $longitude;
    public $peakType;
    public $land;
    public $wildernessSpecialArea;
    public $stateProvince1;
    public $stateProvince2;
    public $range1;
    public $range2;
    public $range3;
    public $range4;
    public $description;
    protected $tableIdentifier;

    public function readCsvLine($line)
    {
        $values = str_getcsv($line, ",", '"');

        $this->pageId = trim($values[1]);
        $this->pageURL = trim($values[2]);
        $this->name = trim($values[3]);
        $this->nameOther = trim($values[4]);
        $this->elevation = trim($values[5]);
        $this->latitude = trim($values[6]);
        $this->longitude = trim($values[7]);
        $this->peakType = trim($values[8]);
        $this->land = trim($values[9]);
        $this->wildernessSpecialArea = trim($values[10]);
        $this->stateProvince1 = trim($values[11]);
        $this->stateProvince2 = trim($values[12]);
        $this->range1 = trim($values[13]);
        $this->range2 = trim($values[14]);
        $this->range3 = trim($values[15]);
        $this->range4 = trim($values[16]);
        $this->description = trim($values[17]);

        $this->tableIdentifier = $this->pageId;
        return true;
    }

    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_peakbagger_peak (
                        page_id,
                        url,
                        name,
                        name_other,
                        elevation,
                        latitude,
                        longitude,
                        peak_type,
                        land,
                        wilderness_special_area,
                        state_province1,
                        state_province2,
                        range_1,
                        range_2,
                        range_3,
                        range_4,
                        description)
                    VALUES (
                        :pageId,
                        :url,
                        :name,
                        :nameOther,
                        :elevation,
                        :latitude,
                        :longitude,
                        :peakType,
                        :land,
                        :wildernessSpecialArea,
                        :stateProvince1,
                        :stateProvince2,
                        :range1,
                        :range2,
                        :range3,
                        :range4,
                        :description)";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':nameOther' => $this->nameOther,
            ':elevation' => $this->elevation,
            ':latitude' => $this->latitude,
            ':longitude' => $this->longitude,
            ':peakType' => $this->peakType,
            ':land' => $this->land,
            ':wildernessSpecialArea' => $this->wildernessSpecialArea,
            ':stateProvince1' => $this->stateProvince1,
            ':stateProvince2' => $this->stateProvince2,
            ':range1' => $this->range1,
            ':range2' => $this->range2,
            ':range3' => $this->range3,
            ':range4' => $this->range4,
            ':description' => $this->description
        ]);
        return $query;
    }

    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_peakbagger_peak
                SET
                    url = :url,
                    name = :name,
                    name_other = :nameOther,
                    elevation = :elevation,
                    latitude = :latitude,
                    longitude = :longitude,
                    peak_type = :peakType,
                    land = :land,
                    wilderness_special_area = :wildernessSpecialArea,
                    state_province1 = :stateProvince1,
                    state_province2 = :stateProvince2,
                    range_1 = :range1,
                    range_2 = :range2,
                    range_3 = :range3,
                    range_4 = :range4,
                    description = :description
                WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $this->pageId,
            ':url' => $this->pageURL,
            ':name' => $this->name,
            ':nameOther' => $this->nameOther,
            ':elevation' => $this->elevation,
            ':latitude' => $this->latitude,
            ':longitude' => $this->longitude,
            ':peakType' => $this->peakType,
            ':land' => $this->land,
            ':wildernessSpecialArea' => $this->wildernessSpecialArea,
            ':stateProvince1' => $this->stateProvince1,
            ':stateProvince2' => $this->stateProvince2,
            ':range1' => $this->range1,
            ':range2' => $this->range2,
            ':range3' => $this->range3,
            ':range4' => $this->range4,
            ':description' => $this->description
        ]);
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_peakbagger_peak
                  WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $this->pageId]);
        return ($query->fetch()->num_rows > 0);
    }


    public static function factory(){
        return new PeakBaggerPeakPage();
    }

    public static function arrayFactory($count){
        $array = array();
        for ($i = 0; $i < $count; $i++) {
            $page = PeakBaggerPeakPage::factory();
            array_push($array, $page);
        }
        return $array;
    }
}
