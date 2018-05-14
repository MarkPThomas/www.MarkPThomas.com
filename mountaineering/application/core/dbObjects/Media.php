<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 7:36 PM
 */

namespace markpthomas\mountaineering\dbObjects;
use markpthomas\library as Lib;

abstract class Media extends MountaineeringBasePrivacy
{
// ========== Properties ===========
    protected $objectPrefix;

    public $album_id;
    public $url;
    public $caption;
    public $width;
    public $height;
    public $latitude;
    public $longitude;
    public $time_stamp;
    public $url_piwigo;
    public $id_piwigo;
    public $url_other;


// ========== I/O ===========
    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = parent::toStdClass($isVerbose, $isJoin);
        $object->album_id = $this->album_id;
        $object->caption = $this->caption;
        $object->width = $this->width;
        $object->height = $this->height;
        $object->latitude = $this->latitude;
        $object->longitude = $this->longitude;
        $object->time_stamp = $this->time_stamp;
        $object->url = $this->url;
        $object->url_piwigo = $this->url_piwigo;
        $object->id_piwigo = $this->id_piwigo;
        $object->url_other = $this->url_other;

        // Enforce privacy
        $object = parent::toStdClassPrivate($object);

        return $object;
    }


// ========== Methods ===========
    protected function castData(){
        parent::castData();

        $this->album_id = (int) $this->album_id;
        if ($this->album_id == 0) {$this->album_id = null;}

        $this->id_piwigo = (int) $this->id_piwigo;
        if ($this->id_piwigo == 0) {$this->id_piwigo = null;}

        $this->height = (int) $this->height;
        if ($this->height == 0) {$this->height = null;}

        $this->width = (int) $this->width;
        if ($this->width == 0) {$this->width = null;}

        $this->latitude = (float) $this->latitude;
        if ($this->latitude == 0) {$this->latitude = null;}

        $this->longitude = (float) $this->longitude;
        if ($this->longitude == 0) {$this->longitude = null;}
    }
} 