<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 11:28 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class ReportVideo extends ReportMedia
{
// ========== Properties ===========
    protected $video;
    /**
     * @return Video
     */
    public function getVideo(){
        if (!$this->video){
            return new Video();
        }
        return $this->video;
    }
    /**
     * @param \stdClass $data
     */
    public function setVideo(\stdClass $data = null){
        if ($data){
            $this->video = Video::factoryStdClass($data);
        }
    }


// ========== Initialization ===========
    public function __construct(){
        $this->objectPrefix = 'reportVideo';
    }

    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new ReportVideo();
        if ($data){
            $object->loadStdClass($data, $strict);
        }
        return $object;
    }


// ========== I/O ===========
    public function loadStdClass(\stdClass $data, $strict = true){
        if ($data){
            if (isset($data->video)){  // Load sub-object directly
                $this->setVideo($data->video);
                unset($data->video);
            } else { // Data might be differentiated by prefix. Allow class to check for this in parsing.
                $this->setVideo($data);
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
            $object->video = $this->getVideo()->toStdClass($isVerbose, $isJoin);
        } else {
            $object->video_id = $this->getVideo()->getId();
        }
        return $object;
    }


// ========== Methods ===========
    /**
     * @return string
     */
    public function getReportCaption(){
        $currentMedia = $this->getVideo();
        return parent::getCaptionFromMedia($currentMedia);
    }
} 