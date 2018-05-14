<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 8:58 PM
 */

namespace markpthomas\mountaineering\dbObjects;


class Page extends MountaineeringBasePrivacy
{
// ========== Properties ===========
    public $title_menu;
    public $title_full;
    public $description;
    public $url;
    public $date_created;
    public $date_modified;
    public $tasks;
    public $views_count;
    public $user_id;

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
     * @param Photo $photo
     */
    public function setPhoto(Photo $photo){
        if ($photo){
            $this->photo = $photo;
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
        $this->is_public = true;
        if ($data){
            $this->loadStdClass($data);
        }
    }

    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new Page();
        if ($data){
            $object->loadStdClass($data, $strict);
            Page::finalizeProperties($data, $object);
            $object->castData();
        }
        return $object;
    }


// ========== I/O ===========

    public function loadStdClass(\stdClass $data, $strict = true){
        if ($data){
            parent::loadStdClass($data, $strict);
            if (isset($data->photo)){
                $this->getPhoto()->loadStdClass($data->photo, $strict);
            }
            Page::finalizeProperties($data, $this);
            $this->castData();
        }
    }


    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = parent::toStdClass($isVerbose, $isJoin);
        $object->title_menu = $this->title_menu;
        $object->title_full = $this->title_full;
        $object->description = $this->description;
        $object->url = $this->url;
        $object->date_created = $this->date_created;
        $object->date_modified = $this->date_modified;
        $object->tasks = $this->tasks;
        $object->description = $this->description;

        if ($isVerbose){
            $object->status = $this->getStatus();
        } else {
            $object->status_id = $this->getStatusId();
        }

        if ($isJoin){
            $object->photo = $this->getPhoto()->toStdClass($isVerbose, $isJoin);
        } else {
            $object->photo_id = $this->getPhoto()->getId();
        }

        // Enforce privacy
        $object->user_id = $this->user_id;
        $object = parent::toStdClassPrivate($object);

        return $object;
    }

// ========== Methods ===========

    protected static function finalizeProperties($data, Page $object){
        if ($object->id == null && isset($data->page_id)){
            $object->id = $data->page_id;
        }

        if ($object->status_id == null && isset($data->page_status)){
            $object->setStatus($data->page_status);
        }
    }


    protected function castData(){
        parent::castData();
        $this->views_count = (int) $this->views_count;
        $this->user_id = (int) $this->user_id;
    }
} 