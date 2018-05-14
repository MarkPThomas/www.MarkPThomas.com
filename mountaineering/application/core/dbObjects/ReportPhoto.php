<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 11:24 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class ReportPhoto extends ReportMedia
{
// ========== Properties ===========
    protected $photo;
    /**
     * @return Photo
     */
    public function getPhoto(){
        if (!$this->photo){
            return new Photo();
        }
        return $this->photo;
    }
    /**
     * @param \stdClass $data
     */
    public function setPhoto(\stdClass $data = null){
        if ($data){
            $this->photo = Photo::factoryStdClass($data);
        }
    }


// ========== Initialization ===========
    public function __construct(){
        $this->objectPrefix = 'reportPhoto';
    }

    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new ReportPhoto();
        if ($data){
            $object->loadStdClass($data, $strict);
        }
        return $object;
    }


// ========== I/O ===========
    public function loadStdClass(\stdClass $data, $strict = true){
        if ($data){
            if (isset($data->photo)){   // Load sub-object directly
                $this->setPhoto($data->photo);
                unset($data->photo);
            } else {    // Data might be differentiated by prefix. Allow class to check for this in parsing.
                $this->setPhoto($data);
            }

            parent::loadStdClass($data);  // Load object directly
            if(!isset($data->id)){ // Load object by parsing prefix first. This is often done with joined select queries meant for viewing.
                parent::loadStdClassByPrefix($data, $this->objectPrefix, $strict);
            }

            $this->castData();
        }
    }


    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = parent::toStdClass($isVerbose, $isJoin);

        if ($isJoin){
            $object->photo = $this->getPhoto()->toStdClass($isVerbose, $isJoin);
        } else {
            $object->photo_id = $this->getPhoto()->getId();
        }
        return $object;
    }

// ========== Methods ===========
    /**
     * @return string
     */
    public function getReportCaption(){
        $currentMedia = $this->getPhoto();
        return parent::getCaptionFromMedia($currentMedia);
    }


} 