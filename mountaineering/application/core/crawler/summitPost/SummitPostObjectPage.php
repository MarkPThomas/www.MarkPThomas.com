<?php
namespace markpthomas\crawler;

use markpthomas\library as Lib;


class SummitPostObjectPage
    extends ReportBase
{
    public $parentURL;
    public $activities;
    public $date;
    public $difficulty;
    public $elevation;
    public $grade;
    public $hits;
    public $latitude;
    public $longitude;
    public $location;
    public $myRouteQuality;
    public $numberOfPitches;
    public $parentCode;
    public $parentName;
    public $rockDifficulty;
    public $routeQuality;
    public $routeType;
    public $seasons;
    public $timeRequired;

    const PAGE_ID = "id";
    const PAGE_URL = "page_url";
    const PARENT_URL = "parent_url";
    const NAME = "name";
    const ACTIVITIES = "activities";
    const DATE = "date";
    const DIFFICULTY = "difficulty";
    const ELEVATION = "elevation";
    const GRADE = "grade";
    const HITS = "hits";
    const LATITUDE = "latitude";
    const LONGITUDE = "longitude";
    const LOCATION = "location";
    const MY_ROUTE_QUALITY = "my_route_quality";
    const NUMBER_OF_PITCHES = "number_of_pitches";
    const PARENT_CODE = "parent_code";
    const PARENT_NAME = "parent_name";
    const ROCK_DIFFICULTY = "rock_difficulty";
    const ROUTE_QUALITY = "route_quality";
    const ROUTE_TYPE = "route_type";
    const SEASONS = "seasons";
    const TIME_REQUIRED = "time_required";
    const TYPE = "type";

    /**
     * @param string $filePrefix First part of the text filename used to identify the text files to read.
     * <br /> Example:
     * <br /> Canyon_
     * <br /> Mountain-Rock_
     * <br /> Route_
     * <br /> my_Canyon_
     * <br /> my_Mountain-Rock_
     * <br /> my_Route_
     * <br /> my_Area-Range_
     * <br /> my_Article_
     * <br /> my_Trip_Report_
     */
    function __construct($filePrefix = "")
    {
        $this->externalSiteStub = 'https://www.summitpost.org';
        $this->filePrefix = $filePrefix;
        $this->keysForSingleLineValues =
            [
                self::PAGE_ID,
                self::PAGE_URL,
                self::PARENT_URL,
                self::NAME,
                self::ACTIVITIES,
                self::DATE,
                self::DIFFICULTY,
                self::ELEVATION,
                self::GRADE,
                self::HITS,
                self::LATITUDE,
                self::LONGITUDE,
                self::LOCATION,
                self::MY_ROUTE_QUALITY,
                self::NUMBER_OF_PITCHES,
                self::PARENT_CODE,
                self::PARENT_NAME,
                self::ROCK_DIFFICULTY,
                self::ROUTE_QUALITY,
                self::ROUTE_TYPE,
                self::SEASONS,
                self::TIME_REQUIRED,
                self::TYPE,
            ];
    }

    public function readKeyValueLine($line){
        $line = $this->cleanText($line);
        $this->readKeyValueLineAndContent($line);
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
            case self::PARENT_URL:
                $this->parentURL = $this->currentValue;
                break;
            case self::NAME:
                $this->name = $this->currentValue;
                break;
            case self::ACTIVITIES:
                $this->activities = $this->currentValue;
                break;
            case self::DATE:
                $this->date = $this->currentValue;
                break;
            case self::DIFFICULTY:
                $this->difficulty = $this->currentValue;
                break;
            case self::ELEVATION:
                $this->elevation = $this->currentValue;
                break;
            case self::GRADE:
                $this->grade = $this->currentValue;
                break;
            case self::HITS:
                $this->hits = $this->currentValue;
                break;
            case self::LATITUDE:
                $this->latitude = $this->currentValue;
                break;
            case self::LONGITUDE:
                $this->longitude = $this->currentValue;
                break;
            case self::LOCATION:
                $this->location = $this->currentValue;
                break;
            case self::MY_ROUTE_QUALITY:
                $this->myRouteQuality = $this->currentValue;
                break;
            case self::NUMBER_OF_PITCHES:
                $this->numberOfPitches = $this->currentValue;
                break;
            case self::PARENT_CODE:
                $this->parentCode = $this->currentValue;
                break;
            case self::PARENT_NAME:
                $this->parentName = $this->currentValue;
                break;
            case self::ROCK_DIFFICULTY:
                $this->rockDifficulty = $this->currentValue;
                break;
            case self::ROUTE_QUALITY:
                $this->routeQuality = $this->currentValue;
                break;
            case self::ROUTE_TYPE:
                $this->routeType = $this->currentValue;
                break;
            case self::SEASONS:
                $this->seasons = $this->currentValue;
                break;
            case self::TIME_REQUIRED:
                $this->timeRequired = $this->currentValue;
                break;
            case self::TYPE:
                $this->type = $this->currentValue;
                break;
            default:
        }
    }

    public function multipleRecordsInFile(){
        return false;
    }

    public function isFilled(){
        return ($this->pageId !== null &&
            $this->pageURL !== null &&
            $this->parentURL !== null &&
            $this->name !== null &&
            $this->activities !== null &&
            $this->date !== null &&
            $this->difficulty !== null &&
            $this->elevation !== null &&
            $this->grade !== null &&
            $this->hits !== null &&
            $this->latitude !== null &&
            $this->longitude !== null &&
            $this->location !== null &&
            $this->myRouteQuality !== null &&
            $this->numberOfPitches !== null &&
            $this->parentCode !== null &&
            $this->parentName !== null &&
            $this->rockDifficulty !== null &&
            $this->routeQuality !== null &&
            $this->routeType !== null &&
            $this->seasons !== null &&
            $this->timeRequired !== null&&
            $this->type !== null);
    }


    public function mySqlInsert(\PDO $mysqlPdo)
    {
        $sql = "INSERT INTO scraper_summitpost_object (
                        page_id,
                        page_url,
                        parent_url,
                        name,
                        activities,
                        date,
                        difficulty,
                        elevation,
                        grade,
                        hits,
                        latitude,
                        longitude,
                        location,
                        my_route_quality,
                        number_of_pitches,
                        parent_code,
                        parent_name,
                        rock_difficulty,
                        route_quality,
                        route_type,
                        seasons,
                        time_required,
                        type,
                        content)
                    VALUES (
                        :pageId,
                        :pageURL,
                        :parentURL,
                        :name,
                        :activities,
                        :date,
                        :difficulty,
                        :elevation,
                        :grade,
                        :hits,
                        :latitude,
                        :longitude,
                        :location,
                        :myRouteQuality,
                        :numberOfPitches,
                        :parentCode,
                        :parentName,
                        :rockDifficulty,
                        :routeQuality,
                        :routeType,
                        :seasons,
                        :timeRequired,
                        :type,
                        :content)";
        $query = $mysqlPdo->prepare($sql);
        $properties = $this->getPropertiesArray();
        $query->execute($properties);
        return $query;
    }


    public function mySqlUpdate(\PDO $mysqlPdo)
    {
        $sql = "UPDATE scraper_summitpost_object
                SET
                    page_url = :pageURL,
                    parent_url = :parentURL,
                    name = :name,
                    activities = :activities,
                    date = :date,
                    difficulty = :difficulty,
                    elevation = :elevation,
                    grade = :grade,
                    hits = :hits,
                    latitude = :latitude,
                    longitude = :longitude,
                    location = :location,
                    my_route_quality = :myRouteQuality,
                    number_of_pitches = :numberOfPitches,
                    parent_code = :parentCode,
                    parent_name = :parentName,
                    rock_difficulty = :rockDifficulty,
                    route_quality = :routeQuality,
                    route_type = :routeType,
                    seasons = :seasons,
                    time_required = :timeRequired,
                    type = :type,
                    content = :content
                WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute($this->getPropertiesArray());
        return $query;
    }

    public function mySqlExist(\PDO $mysqlPdo){
        $sql = "SELECT COUNT(*) AS num_rows FROM scraper_summitpost_object
                  WHERE page_id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $this->pageId]);
        return ($query->fetch()->num_rows > 0);
    }

    public function mySqlFill(\PDO $mysqlPdo, $pageId){
        $sql = "SELECT *
                FROM scraper_summitpost_object
                WHERE page_id=:pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([':pageId' => $pageId]);
        $result = $query->fetch();

        if ($result){
            $this->pageId = $pageId;
            $this->name = $result->name;
            $this->content = $result->content;
            $this->type = $result->type;
            $this->pageURL = $result->page_url;
            $this->parentURL = $result->parent_url;
            $this->activities = $result->activities;
            $this->date = $result->date;
            $this->difficulty = $result->difficulty;
            $this->elevation = $result->elevation;
            $this->grade = $result->grade;
            $this->hits = $result->hits;
            $this->latitude = $result->latitude;
            $this->longitude = $result->longitude;
            $this->location = $result->location;
            $this->myRouteQuality = $result->my_route_quality;
            $this->numberOfPitches = $result->number_of_pitches;
            $this->parentCode = $result->parent_code;
            $this->parentName = $result->parent_name;
            $this->rockDifficulty = $result->rock_difficulty;
            $this->routeQuality = $result->route_quality;
            $this->routeType = $result->route_type;
            $this->seasons = $result->seasons;
            $this->timeRequired = $result->time_required;

            return true;
        }
        return false;
    }

    public function insertCrawlerId(\PDO $mysqlPdo, $pageId){
        if (empty($this->pageId)) return;

        $sql = "UPDATE page
                SET id_summitPost = :id
                WHERE id = :pageId";
        $query = $mysqlPdo->prepare($sql);
        $query->execute([
            ':pageId' => $pageId,
            ':id' => $this->pageId
        ]);
    }


    public static function associateCrawlerIdsWithPages(\PDO $mysqlPdo){
        // 1. Get all crawler IDs and names
        $crawlerIds = [];
        $crawlerNames = [];

        $sql = 'SELECT page_id, name FROM scraper_summitpost_object';
        $query = $mysqlPdo->prepare($sql);
        $query->execute();
        $results = $query->fetchAll();
        foreach ($results as $result){
            $crawlerIds[] = $result->page_id;
            $crawlerNames[] = $result->name;
        }

        // 2. Get any matching page by name
        for ($j = 0; $j < count($crawlerIds); $j++){
            $sql = 'SELECT * FROM page WHERE title_full = :name';
            $query = $mysqlPdo->prepare($sql);
            $query->execute([':name' => $crawlerNames[$j]]);

            // 3. If there is only one match, write the crawler ID
            if ($query->rowCount() == 1){
                $pageId = $query->fetch()->id;

                $sql = "UPDATE page
                        SET id_summitPost = :id
                        WHERE id = :pageId";
                $query = $mysqlPdo->prepare($sql);
                $query->execute([
                    ':pageId' => $pageId,
                    ':id' => $crawlerIds[$j]
                ]);
            }
        }
    }

    public function toReport($keepOldUrl = false){
        if ($keepOldUrl){
            $pageUrl = $this->pageURL;
        } else {
            $urlComponents = explode('/', $this->pageURL);
            $pageUrl = '/mountaineering/';
            $pageUrl .= ($this->type === 'Article')? 'articles/' : 'trip-reports/california/'; // TODO: Remove 'california' after adding reports.
            if (count($urlComponents) > 1) $pageUrl .= $urlComponents[1];
        }

        $tripReport = new Report($this->name, $this->type, $this->content, $this->name, $pageUrl, $this->externalSiteStub);

        return $tripReport;
    }


    public static function factory(){
        return new SummitPostObjectPage();
    }



    private function getPropertiesArray(){
        return [
            ':pageId' => $this->pageId,
            ':pageURL' => $this->pageURL,
            ':parentURL' => empty($this->parentURL)? null : $this->parentURL,
            ':name' => empty($this->name)? null : $this->name,
            ':activities' => empty($this->activities)? null : $this->activities,
            ':date' => empty($this->date)? null : $this->date,
            ':difficulty' => empty($this->difficulty)? null : $this->difficulty,
            ':elevation' => empty($this->elevation)? null : $this->elevation,
            ':grade' => empty($this->grade)? null : $this->grade,
            ':hits' => empty($this->hits)? null : $this->hits,
            ':latitude' => empty($this->latitude)? null : $this->latitude,
            ':longitude' => empty($this->longitude)? null : $this->longitude,
            ':location' => empty($this->location)? null : $this->location,
            ':myRouteQuality' => empty($this->myRouteQuality)? null : $this->myRouteQuality,
            ':numberOfPitches' => empty($this->numberOfPitches)? null : $this->numberOfPitches,
            ':parentCode' => empty($this->parentCode)? null : $this->parentCode,
            ':parentName' => empty($this->parentName)? null : $this->parentName,
            ':rockDifficulty' => empty($this->rockDifficulty)? null : $this->rockDifficulty,
            ':routeQuality' => empty($this->routeQuality)? null : $this->routeQuality,
            ':routeType' => empty($this->routeType)? null : $this->routeType,
            ':seasons' => empty($this->seasons)? null : $this->seasons,
            ':timeRequired' => empty($this->timeRequired)? null : $this->timeRequired,
            ':type' => empty($this->type)? null : $this->type,
            ':content' => empty($this->content)? null : $this->content
        ];
    }
}