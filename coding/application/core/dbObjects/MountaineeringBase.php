<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 8:37 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;

/**
 * Base class that most of the mountaineering database objects inherit from.
 * @package markpthomas\mountaineering\dbObjects
 */
abstract class MountaineeringBase extends Lib\Object {
// ========== Properties ===========
    protected $id;
    /**
     * Database ID.
     * @param int $value
     */
    public function setId($value){
        if($value){
            parent::setInt('id', $value);
        }
    }
    /**
     * Database ID.
     * @return int
     */
    public function getId(){
        return $this->id;
    }


// ========== I/O ===========
    /**
     * Only loads class properties matching object properties starting with the given prefix.
     * For example, $this->property = $data->objectPrefix_property.
     * @param \stdClass $data
     * @param $objectPrefix
     * @param bool $strict True: Property will only be added if it already exists in the class.
     * False: The property will be dynamically created if it does not exist.
     */
    protected function loadStdClassByPrefix(\stdClass $data, $objectPrefix, $strict = true){
        if (count((array)$data) === 0) return;

        $array = Lib\JsonHandler::decode(Lib\JsonHandler::encode($data), $assoc = true);
        $this->loadArrayByPrefix($array, $objectPrefix, $strict);
    }

    /**
     * Only loads class properties matching array items starting with the given prefix.
     * For example, $this->property = $data[objectPrefix_property].
     * @param array $data
     * @param $objectPrefix
     * @param bool $strict True: Property will only be added if it already exists in the class.
     * False: The property will be dynamically created if it does not exist.
     */
    protected function loadArrayByPrefix(array $data, $objectPrefix, $strict = true){
        $filteredData = [];
        foreach ($data as $key => $value){
            $nameComponents = explode('_', $key);
            if (strtolower($nameComponents[0]) == strtolower($objectPrefix)){
                // New key strips out object prefix name
                $newKey = substr($key, strlen($objectPrefix) + 1);
                $filteredData[$newKey] = $value;
            }
        }
        if ($filteredData){
            $this->loadArray($filteredData, $strict);
        }
    }


    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = new \stdClass();
        if (isset($this->id)){
            $object->id = $this->getId();
        }
        return $object;
    }

//    /**
//     * Returns the object as a JSON string.
//     * However, some numerical data such as ids for enumerations will have the verbose value written instead.
//     * @return string
//     */
//    public function toJsonVerbose(){
//        return '';
//    }
//
//    /**
//     * Returns the object as a JSON string.
//     * @return string
//     */
//    public function toJson(){
//        return '';
//    }
//
//
//    /**
//     * Auto loads data from the JSON string into the current object.
//     * @param string $data
//     * @param bool $strict
//     */
//    public function loadJson($data, $strict = true){
//        $array = Lib\JsonHandler::decode($data, $assoc = true);
//        $this->loadArray($array, $strict);
//    }
//
//
//
//    /**
//     * Returns the JSON string representation of the object as an associative array.
//     * @param MountaineeringBase $object
//     * @return mixed
//     */
//    public static function getFromJson(MountaineeringBase $object){
//        return Lib\JsonHandler::decode($object->toJsonVerbose());
//    }




// ========== Methods ===========
    /**
     * OVERRIDE THIS METHOD.
     * Casts any class properties to the intended type.
     * This ensures correct type, such as after reading from databases where numbers may come as strings, or booleans as 1/0.
     */
    protected function castData(){
        if (isset($this->id)) $this->setId($this->id);
    }
} 