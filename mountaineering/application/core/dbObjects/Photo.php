<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 5:49 PM
 */

namespace markpthomas\mountaineering\dbObjects;

class Photo extends Media
{
// ========== Properties ===========
    public $url_picasa;
    public $file_name;


// ========== Initialization ===========
    public function __construct(){
        $this->objectPrefix = 'photo';
    }


    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new Photo();
        if ($data){
            $object->loadStdClass($data, $strict);
        }
        return $object;
    }


// ========== I/O ===========
    public function loadStdClass(\stdClass $data, $strict = true){
        parent::loadStdClass($data); // Load object directly
        if ($data){
            if(!isset($data->id)){ // Load object by parsing prefix first. This is often done with joined select queries meant for viewing.
                parent::loadStdClassByPrefix($data, $this->objectPrefix, $strict);
            }
            $this->castData();
        }
    }


    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = parent::toStdClass($isVerbose, $isJoin);
        $object->url_picasa = $this->url_picasa;
        $object->file_name = $this->file_name;

        // Enforce privacy
        $object = parent::toStdClassPrivate($object);

        return $object;
    }
} 