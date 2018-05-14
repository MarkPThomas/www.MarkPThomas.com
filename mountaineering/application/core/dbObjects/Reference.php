<?php

/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 5:50 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\mountaineering as core;
use markpthomas\library as Lib;

class Reference extends MountaineeringBasePrivacy
{
// ========== Properties ===========
    public $name;
    public $description;
    public $book_title;
    public $book_author;
    public $book_URL;
    public $private_file_URL;

    const externalLink = 'External Link';
    const internalLink = 'Internal Link';
    public static $domainName = 'http://www.markpthomas.com/';    // TODO: get the domain name programmatically

    protected $website_URL;
    public function getWebsiteURL(){
        return $this->website_URL;
    }
    public function setWebsiteURL($website_url){
        if ($website_url){
            if (Lib\StringHelper::stringContains($this->description, self::internalLink)){
                // Strip site domain from URL
                $website_url = str_replace(self::$domainName, '', $website_url);
            } // Check if this is an internal link and adjust as such
            elseif (Lib\StringHelper::stringContains($website_url, self::$domainName)) {
                // Strip site domain from URL
                $website_url = str_replace(self::$domainName, '', $website_url);
                $this->description = $this->description .= ' (' . self::internalLink . ')';
            }
            $this->website_URL = $website_url;
        }
    }


    protected $status_id;
    /**
     * @return int
     */
    public function getStatusId(){
        return (!empty($this->status_id)) ?
            $this->status_id :
            1;
    }
    /**
     * @param int $typeId
     */
    public function setStatusId($typeId = 1){
        if ($typeId){
            $this->status_id = $typeId;
        }
    }

    /**
     * @return null|string
     */
    public function getStatus(){
        return StatusEnum::getFullName($this->status_id);
    }
    /**
     * @param string $status
     */
    public function setStatus($status){
        if (StatusEnum::isValidName($status)){
            $this->status_id = StatusEnum::getValue($status);
        }
    }



// ========== Initialization ===========
    public function __construct(\stdClass $data = null){
        if ($data){
            $this->loadStdClass($data);
        }
    }


    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new Reference();
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
            core\Privacy::enforcePrivacyOfEntry($this, 'private_file_URL');
        }
    }


    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = parent::toStdClass($isVerbose, $isJoin);
        $object->website_URL = $this->getWebsiteURL();
        $object->name = $this->name;
        $object->description = $this->description;

        // Enforce privacy
        $object = parent::toStdClassPrivate($object);

        return $object;
    }
} 