<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 8:05 PM
 */

namespace markpthomas\coding;

use markpthomas\library as Lib;
use markpthomas\mountaineering\dbObjects as Db;

/**
 * Data object representing the trip reports (and other reports) used in the application.
 * @package markpthomas\mountaineering
 */
class Report implements Lib\IObject
{
// ========== Properties ===========
    private $header;
    /**
     * @return Db\ReportTrip
     */
    public function getHeader(){
        if (empty($this->header)){
            return new Db\ReportTrip();
        }
        return $this->header;
    }
    /**
     * @param \stdClass $data
     */
    public function setHeader(\stdClass $data){
        $this->header = Db\ReportTrip::factoryStdClass($data);
    }

    /**
     * @param \stdClass $header
     * @param \stdClass $page
     */
    public function setHeaderAndPage(\stdClass $header, \stdClass $page){
        $this->setHeader($header);
        $this->setPage($page);
    }

    /**
     * @return Db\Page
     */
    public function getPage(){
        if (empty($this->header)){
            return new Db\Page();
        }
        return $this->getHeader()->getPage();
    }
    /**
     * @param \stdClass $data
     */
    public function setPage(\stdClass $data){
        $this->getHeader()->setPage($data);
    }

    private $bodies = [];
    /**
     * @param array $reportBodies
     */
    public function setBodies(array $reportBodies){
        foreach ($reportBodies as $reportBody){
            $this->addBody($reportBody);
        }
    }

    public function clearBodies(){
        $this->bodies = [];
    }

    /**
     * @param \stdClass $data
     */
    public function addBody(\stdClass $data){
        $this->bodies[] = Db\ReportTripBody::factoryStdClass($data);
    }
    /**
     * @param $index
     * @return Db\ReportTripBody
     */
    public function getBody($index){
        return ($index < count($this->bodies))?
            $this->bodies[$index] : null;
    }



    /**
     * @return int
     */
    public function getMaxSequence(){
        return count($this->bodies);
    }


    private $albums = [];
    /**
     * @param array $albums
     */
    public function setAlbums(array $albums){
        foreach ($albums as $album){
            self::addAlbum($album);
        }
    }

    public function clearAlbums(){
        $this->albums = [];
    }

    /**
     * @param \stdClass $data
     */
    public function addAlbum(\stdClass $data){
        $album = Db\Album::factoryStdClass($data);
        $this->albums[] = $album;
    }

    /**
     * @param $index
     * @return Db\Album
     */
    public function getAlbum($index){
        return ($index < count($this->albums))?
            $this->albums[$index] : null;
    }

    /**
     * @return int
     */
    public function countAlbums(){
        return count($this->albums);
    }



    private $externalLinks = [];
    private $internalLinks = [];
    private $references = [];
    /**
     * @param array $references
     */
    public function setReferences(array $references){
        foreach ($references as $reference){
            self::addReference($reference);
        }
    }

    public function clearReferences(){
        self::clearReferenceLinkExternal();
        self::clearReferenceLinkInternal();
        self::clearReference();
    }

    public function clearReferenceLinkExternal(){
        $this->externalLinks = [];
    }

    public function clearReferenceLinkInternal(){
        $this->internalLinks = [];
    }

    public function clearReference(){
        $this->references = [];
    }


    /**
     * @param \stdClass $data
     */
    public function addReference(\stdClass $data){
        $reference = Db\Reference::factoryStdClass($data);

        if ($reference->description == Db\Reference::externalLink){
            $this->externalLinks[] = $reference;
        } elseif ($reference->description == Db\Reference::internalLink){
            $this->internalLinks[] = $reference;
        } else {
            $this->references[] = $reference;
        }
    }

    /**
     * @param $index
     * @return Db\Reference
     */
    public function getReferenceLinkExternal($index){
        return ($index < count($this->externalLinks))?
            $this->externalLinks[$index] : null;
    }

    /**
     * @return int
     */
    public function countReferenceLinkExternal(){
        return count($this->externalLinks);
    }

    /**
     * @param $index
     * @return Db\Reference
     */
    public function getReferenceLinkInternal($index){
        return ($index < count($this->internalLinks))?
            $this->internalLinks[$index] : null;
    }

    /**
     * @return int
     */
    public function countReferenceLinkInternal(){
        return count($this->internalLinks);
    }

    /**
     * @param $index
     * @return Db\Reference
     */
    public function getReference($index){
        return ($index < count($this->references))?
            $this->references[$index] : null;
    }

    /**
     * @return int
     */
    public function countReference(){
        return count($this->references);
    }



// ========== Initialization ===========
    /**
     * Auto loads properties from the data into a new object.
     * @param \stdClass $data
     * @param bool $strict True: Property will only be added if it already exists in the class.
     * False: The property will be dynamically created if it does not exist.
     * @return Report
     */
    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new Report();
        if ($data){
            $object->loadStdClass($data, $strict);
        }
        return $object;
    }


// ========== I/O ===========
    /**
     * Auto loads properties from the data into the current object.
     * @param \stdClass $data
     * @param bool $strict True: Property will only be added if it already exists in the class.
     * False: The property will be dynamically created if it does not exist.
     * @see  https://stackoverflow.com/questions/18576762/php-stdclass-to-array
     */
    public function loadStdClass(\stdClass $data, $strict = true){
        if ($data){
            if (isset($data->header)){
                $this->header = Db\ReportTrip::factoryStdClass($data->header, $strict);
            }

            if (isset($data->body)){
                foreach ($data->body as $datum){
                    $this->bodies[] = Db\ReportTripBody::factoryStdClass($datum, $strict);
                }
            }

            if (isset($data->albums)){
                foreach ($data->albums as $datum){
                    $this->albums[] = Db\Album::factoryStdClass($datum, $strict);
                }
            }

            if (isset($data->internalLinks)){
                foreach ($data->internalLinks as $datum){
                    $this->internalLinks[] = Db\Reference::factoryStdClass($datum, $strict);
                }
            }

            if (isset($data->externalLinks)){
                foreach ($data->externalLinks as $datum){
                    $this->externalLinks[] = Db\Reference::factoryStdClass($datum, $strict);
                }
            }

            if (isset($data->references)){
                foreach ($data->references as $datum){
                    $this->references[] = Db\Reference::factoryStdClass($datum, $strict);
                }
            }
        }
    }

    /**
     * Returns the current object as an stdClass object.
     * @param bool $isVerbose True: Enumerations will be represented by name. False: Enumeration ordinal values are used.
     * @param bool $isJoin True: Properties of object will be represented as the object. False: Object ID used.
     * @return \stdClass
     */
    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = new \stdClass();
        // While individual objects will not render if private, this early additional check avoids wasting computation time.
        if (Privacy::isPrivate($this->getHeader()->getPage())){
            return $object;
        }

        $object->header = $this->getHeader()->toStdClass($isVerbose, $isJoin);
        $object->bodies = $this->toStdClassArray($this->bodies, $isVerbose, $isJoin);
        $object->albums = $this->toStdClassArray($this->albums, $isVerbose, $isJoin);
        $object->internalLinks = $this->toStdClassArray($this->internalLinks, $isVerbose, $isJoin);
        $object->externalLinks = $this->toStdClassArray($this->externalLinks, $isVerbose, $isJoin);
        $object->references = $this->toStdClassArray($this->references, $isVerbose, $isJoin);

        return $object;
    }

    /**
     * Returns an array of Db\MountaineeringBase objects as an array of \stdClass objects.
     * @param array $array Array of Db\MountaineeringBase objects.
     * @param bool $isVerbose True: Enumerations will be represented by name. False: Enumeration ordinal values are used.
     * @param bool $isJoin True: Properties of object will be represented as the object. False: Object ID used.
     * @return array Array of \stdClass
     */
    public static function toStdClassArray(array $array, $isVerbose = false, $isJoin = false){
        $stdArray = [];
        if (!isset($array) || count($array) == 0 ||
            !is_a($array[0], 'markpthomas\mountaineering\dbObjects\MountaineeringBase')){
            return $stdArray;
        }

        foreach ($array as $item){
            /* @var $item Db\MountaineeringBase*/
            $stdArray[] = $item->toStdClass($isVerbose, $isJoin);
        }
        return $stdArray;
    }

    /**
     * Renders the object to an /stdClass object meant for display in the View of the application.
     * @return \stdClass
     */
    public function convertToStdClassView(){
        // Consolidate all captions
        /* @var $body Db\ReportTripBody*/
        foreach ($this->bodies as $body){
            // Photo
            $reportPhoto = $body->getReportPhoto();
            $caption = $reportPhoto->getReportCaption();

            $reportPhoto->getPhoto()->caption = $caption;
            unset($reportPhoto->suppress_caption);
            unset($reportPhoto->custom_caption);

            // Video
            $reportVideo = $body->getReportVideo();
            $caption = $reportVideo->getReportCaption();

            $reportVideo->getVideo()->caption = $caption;
            unset($reportVideo->suppress_caption);
            unset($reportVideo->custom_caption);
        }

        $object = $this->toStdClass($isVerbose = true, $isJoin = true);
        return $object;
    }
} 