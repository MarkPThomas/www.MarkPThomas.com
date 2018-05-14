<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 7:07 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;
use markpthomas\mountaineering as core;

/**
 * Base class that most of the mountaineering database objects inherit from if they have privacy restrictions.
 * @package markpthomas\mountaineering\dbObjects
 */
class MountaineeringBasePrivacy extends MountaineeringBase{
// ========== Properties ===========
    protected $is_public = true;
    /**
     *
     * @param int $value
     */
    public function setIsPublic($value){
        if($value){
            parent::setBool('is_public', $value);
        }
    }
    /**
     *
     * @return int
     */
    public function getIsPublic(){
        return $this->is_public;
    }


// ========== I/O ===========
    /**
     * Determines the privacy access of the class based on privacy settings and admin or owner viewership.
     * If access to the class is unauthorized, the class provided is cleared and an empty one is returned.
     * If access is authorized, the object is returned with the public status property appended.
     * @param \stdClass $data
     * @return null|\stdClass
     */
    protected function toStdClassPrivate(\stdClass $data){
        if (core\Privacy::isPrivate($this)){
            $data = null;
            return new \stdClass();
        }

        $data->is_public = $this->getIsPublic();

        return $data;
    }


// ========== Methods ===========
    protected function castData(){
        parent::castData();
        $this->setIsPublic($this->is_public);
    }
} 