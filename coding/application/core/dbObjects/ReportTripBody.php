<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 5:51 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\library as Lib;


class ReportTripBody extends MountaineeringBase
{
// ========== Properties ===========
    public $report_trip_id;
    public $sequence;
    public $text_body;

    protected $header;
    /**
     * @return Header
     */
    public function getHeader(){
        if (!$this->header){
            return new Header();
        }
        return $this->header;
    }
    /**
     * @param \stdClass $data
     */
    public function setHeader(\stdClass $data){
        if (!$data) return;

        $this->header = Header::factoryStdClass($data);
    }


    protected $reportPhoto;
    /**
     * @return ReportPhoto
     */
    public function getReportPhoto(){
        if (!$this->reportPhoto){
            return new ReportPhoto();
        }
        return $this->reportPhoto;
    }
    /**
     * @param \stdClass $data/**
     */
    public function setReportPhoto(\stdClass $data){
        if (!$data) return;

        $this->reportPhoto = ReportPhoto::factoryStdClass($data);
    }

    protected $reportVideo;
    /**
     * @return ReportVideo
     */
    public function getReportVideo(){
        if (!$this->reportVideo){
            return new ReportVideo();
        }
        return $this->reportVideo;
    }
    /**
     * @param \stdClass $data
     */
    public function setReportVideo(\stdClass $data){
        if (!$data) return;

        $this->reportVideo = ReportVideo::factoryStdClass($data);
    }


// ========== Initialization ===========
    public function __construct(\stdClass $data = null){
        if ($data){
            $this->loadStdClass($data);
        }
    }

    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new ReportTripBody();
        if ($data){
            $object->loadStdClass($data, $strict);
        }
        return $object;
    }


// ========== I/O ===========

    public function loadStdClass(\stdClass $data, $strict = true){
        if ($data){
            parent::loadStdClass($data, $strict);
            $this->initializeProperties($data, $strict);
        }
    }

    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = parent::toStdClass($isVerbose, $isJoin);
        $object->report_trip_id = $this->report_trip_id;
        $object->sequence = $this->sequence;
        $object->text_body = $this->text_body;
        $object->header_value = $this->getHeader()->header_value;

        if ($isVerbose){
            $object->header_type = $this->getHeader()->getHeaderType();
        } else {
            $object->header_type_id = $this->getHeader()->getHeaderTypeId();
        }

        if ($isJoin){
            $object->report_photo = $this->getReportPhoto()->toStdClass($isVerbose, $isJoin);
            $object->report_video = $this->getReportVideo()->toStdClass($isVerbose, $isJoin);
        } else {
            $object->report_photo_id = $this->getReportPhoto()->getId();
            $object->report_video_id = $this->getReportVideo()->getId();
        }

        return $object;
    }


// ========== Methods ===========
    protected function initializeProperties(\stdClass $data, $strict = true){
        $this->setHeader($data);

        if (isset($data->report_photo)){    // Load sub-object directly
            $this->reportPhoto = ReportPhoto::factoryStdClass($data->report_photo, $strict);
        } else {    // Data might be differentiated by prefix. Allow class to check for this in parsing.
            $this->setReportPhoto($data);
        }

        if (isset($data->report_video)){    // Load sub-object directly
            $this->reportVideo = ReportVideo::factoryStdClass($data->report_video, $strict);
        } else {    // Data might be differentiated by prefix. Allow class to check for this in parsing.
            $this->setReportVideo($data);
        }

        $this->castData();
        self::finalizeProperties( $this);
    }

    protected function castData(){
        parent::castData();

        $this->report_trip_id = (int) $this->report_trip_id;
        $this->sequence = (int) $this->sequence;
    }

    protected static function finalizeProperties(ReportTripBody $object){
        // Set properties that don't have matching names

        unset($object->id);
    }
} 