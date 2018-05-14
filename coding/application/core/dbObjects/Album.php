<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 5:50 PM
 */

namespace markpthomas\mountaineering\dbObjects;
use markpthomas\library as Lib;


class Album extends MountaineeringBasePrivacy
{
// ========== Properties ===========
    public $url;
    public $title;
    public $summary;
    public $latitude;
    public $longitude;
    public $date;
    public $url_piwigo;
    public $id_piwigo;
    public $url_picasa;
    public $url_other;

    protected $status_id;
    /**
     * @return int
     */
    public function getAlbumStatusId(){
        return (!empty($this->status_id)) ?
            $this->status_id :
            1;
    }
    /**
     * @param int $typeId
     */
    public function setAlbumStatusId($typeId = 1){
        if ($typeId){
            $this->status_id = $typeId;
        }
    }

    /**
     * @return null|string
     */
    public function getAlbumStatus(){
        return StatusEnum::getFullName($this->status_id);
    }
    /**
     * @param string $status
     */
    public function setAlbumStatus($status){
        if (StatusEnum::isValidName($status)){
            $this->status_id = StatusEnum::getValue($status);
        }
    }


    protected $captions_status_id;
    /**
     * @return int
     */
    public function getCaptionStatusId(){
        return (!empty($this->captions_status_id)) ?
            $this->captions_status_id :
            1;
    }
    /**
     * @param int $typeId
     */
    public function setCaptionStatusId($typeId = 1){
        if ($typeId){
            $this->captions_status_id = $typeId;
        }
    }

    /**
     * @return null|string
     */
    public function getCaptionStatus(){
        return StatusEnum::getFullName($this->captions_status_id);
    }
    /**
     * @param string $status
     */
    public function setCaptionStatus($status){
        if (StatusEnum::isValidName($status)){
            $this->captions_status_id = StatusEnum::getValue($status);
        }
    }


    protected $geotag_status_id;
    /**
     * @return int
     */
    public function getGeotagStatusId(){
        return (!empty($this->geotag_status_id)) ?
            $this->geotag_status_id :
            1;
    }
    /**
     * @param int $typeId
     */
    public function setGeotagStatusId($typeId = 1){
        if ($typeId){
            $this->geotag_status_id = $typeId;
        }
    }

    /**
     * @return null|string
     */
    public function getGeotagStatus(){
        return StatusEnum::getFullName($this->geotag_status_id);
    }
    /**
     * @param string $status
     */
    public function setGeotagStatus($status){
        if (StatusEnum::isValidName($status)){
            $this->geotag_status_id = StatusEnum::getValue($status);
        }
    }



// ========== Initialization ===========
    public function __construct(\stdClass $data = null){
        if ($data){
            $this->loadStdClass($data);
        }
    }

    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new Album();
        if ($data){
            $object->loadStdClass($data, $strict);
        }
        return $object;
    }


// ========== I/O ===========
    public function loadStdClass(\stdClass $data, $strict = true){
        if ($data){
            parent::loadStdClass($data, $strict);
            $this->castData();
        }
    }



    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = parent::toStdClass($isVerbose, $isJoin);
        $object->title = $this->title;
        $object->summary = $this->summary;
        $object->latitude = $this->latitude;
        $object->longitude = $this->longitude;
        $object->date = $this->date;
        $object->url = $this->url;
        $object->url_piwigo = $this->url_piwigo;
        $object->id_piwigo = $this->id_piwigo;
        $object->url_picasa = $this->url_picasa;
        $object->url_other = $this->url_other;

        if ($isVerbose){
            $object->status = $this->getAlbumStatus();
            $object->captions_status = $this->getCaptionStatus();
            $object->geotag_status = $this->getGeotagStatus();
        } else {
            $object->status = $this->getAlbumStatusId();
            $object->captions_status = $this->getCaptionStatusId();
            $object->geotag_status = $this->getGeotagStatusId();
        }

        // Enforce privacy
        $object = parent::toStdClassPrivate($object);

        return $object;
    }


// ========== Methods ===========
    protected function castData(){
        parent::castData();
        $this->id_piwigo = (int) $this->id_piwigo;
        if ($this->id_piwigo == 0) {$this->id_piwigo = null;}

        $this->latitude = (float) $this->latitude;
        if ($this->latitude == 0) {$this->latitude = null;}

        $this->longitude = (float) $this->longitude;
        if ($this->longitude == 0) {$this->longitude = null;}
    }
} 