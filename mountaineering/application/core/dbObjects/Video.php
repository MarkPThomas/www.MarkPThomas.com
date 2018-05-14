<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 5:49 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;

class Video extends Media
{
// ========== Properties ===========
    public $id_youtube;
    public $id_vimeo;


// ========== Initialization ===========
    public function __construct(){
        $this->objectPrefix = 'video';
    }


    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new Video();
        if ($data){
            $object->loadStdClass($data, $strict);
        }
        return $object;
    }


// ========== I/O ===========
    public function loadStdClass(\stdClass $data, $strict = true){
        if ($data){
            parent::loadStdClass($data); // Load object directly
            if ($data){
                if(!isset($data->id)){ // Load object by parsing prefix first. This is often done with joined select queries meant for viewing.
                    parent::loadStdClassByPrefix($data, $this->objectPrefix, $strict);
                }
                $this->castData();
            }
            $this->castData();
        }
    }


    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = parent::toStdClass($isVerbose, $isJoin);
        $object->id_youtube = $this->id_youtube;
        $object->id_vimeo = $this->id_vimeo;

        // Enforce privacy
        $object = parent::toStdClassPrivate($object);

        return $object;
    }
} 