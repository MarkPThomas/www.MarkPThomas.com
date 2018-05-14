<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 1:28 PM
 */

namespace markpthomas\mountaineering\dbObjects;
use markpthomas\library as Lib;

/**
 * Class Header represents a header in the website.
 * @package markpthomas\mountaineering\dbObjects
 */
class Header extends MountaineeringBase
{
// ========== Properties ===========
    public $header_value;

    protected  $header_type_id;
    /**
     * Get the name of the header.
     * @return string|null
     */
    public function getHeaderType(){
        return HeaderEnum::getFullName($this->header_type_id);
    }
    /**
     * @param $status
     */
    public function setHeaderType($status){
        if (HeaderEnum::isValidName($status)){
            $this->header_type_id = HeaderEnum::getValue($status);
        }
    }
    /**
     * @return int
     */
    public function getHeaderTypeId(){
        return (!empty($this->header_type_id)) ?
            $this->header_type_id :
            null;
    }
    /**
     * @param int $typeId
     */
    public function setHeaderTypeId($typeId = 1){
        if ($typeId){
            $this->header_type_id = $typeId;
        }
    }



// ========== Initialization ===========
    public function __construct(){
    }


    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new Header();
        if ($data){
            $object->loadStdClass($data, $strict);
        }
        return $object;
    }


// ========== I/O ===========

    public function loadStdClass(\stdClass $data, $strict = true){
        if ($data){
            parent::loadStdClass($data, $strict);
            self::finalizeProperties($data, $this);
            $this->castData();
        }
    }


    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = parent::toStdClass($isVerbose, $isJoin);

        if ($isVerbose) {
            $object->header_type = $this->getHeaderType();
        } else {
            $object->header_type_id = $this->getHeaderType();
        }
        $object->header_value = $this->header_value;

        return $object;
    }


// ========== Methods ===========

    protected static function finalizeProperties($data, Header $object){
        // Set properties that don't have matching names
        if (isset($data->header_type)) $object->setHeaderType($data->header_type);

        unset($object->id);
    }
}