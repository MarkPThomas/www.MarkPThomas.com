<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 7:51 PM
 */

namespace markpthomas\mountaineering\dbObjects;

use markpthomas\mountaineering as core;
use markpthomas\library as Lib;


class ReportTrip extends MountaineeringBase
{
// ========== Properties ===========
    protected $page;
    /**
     * @return Page
     */
    public function getPage(){
        if (!$this->page){
            return new Page();
        }

        return $this->page;
    }
    /**
     * @param \stdClass $data
     */
    public function setPage(\stdClass $data){
        if ($data){
            $this->page = Page::factoryStdClass($data);
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

    protected $report_trip_type_id;
    /**
     * @return int
     */
    public function getReportTypeId(){
        return (!empty($this->report_trip_type_id)) ?
            $this->report_trip_type_id :
            3;
    }
    /**
     * @param int $typeId
     */
    public function setReportTypeId($typeId = 3){
        if ($typeId){
            $this->report_trip_type_id = $typeId;
        }
    }
    /**
     * @return null|string
     */
    public function getReportType(){
        return ReportTypeEnum::getFullName($this->report_trip_type_id);
    }
    /**
     * @param string $reportType
     */
    public function setReportType($reportType){
        if (ReportTypeEnum::isValidName($reportType)){
            $this->report_trip_type_id = ReportTypeEnum::getValue($reportType);
        }
    }


// ========== Initialization ===========
    public function __construct(\stdClass $data = null){
        if ($data){
            $this->loadStdClass($data);
        }
    }

    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new ReportTrip();
        if ($data){
            $object->loadStdClass($data, $strict);
        }
        return $object;
    }


// ========== I/O ===========

    public function loadStdClass(\stdClass $data, $strict = true){
        if ($data){
            if (isset($data->page)){
                $this->setPage($data->page);
                unset($data->page);
            }

            if (isset($data->report_trip_type)){
                $this->setReportType($data->report_trip_type);
                unset($data->report_trip_type);
            }

            if (isset($data->report_trip_status_id)){
                $this->setStatusId($data->report_trip_status_id);
                unset($data->report_trip_status_id);
            }

            if (isset($data->report_trip_status)){
                $this->setStatus($data->report_trip_status);
                unset($data->report_trip_status);
            }

            parent::loadStdClass($data, $strict);
            $this->castData();
        }
    }

    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = parent::toStdClass($isVerbose, $isJoin);
        $object->report_trip_id = $this->getId();

        if ($isVerbose){
            $object->status = $this->getStatus();
            $object->report_trip_type = $this->getReportType();
        } else {
            $object->status_id = $this->getStatusId();
            $object->report_trip_type_id = $this->getReportTypeId();
        }

        if ($isJoin){
            $object->page = $this->getPage()->toStdClass($isVerbose, $isJoin);
        } else {
            $object->page_id = $this->getPage()->getId();
        }

        return $object;
    }


// ========== Methods ===========
    protected function castData(){
        parent::castData();
        $this->report_trip_type_id = (int) $this->report_trip_type_id;
        $this->status_id = (int) $this->status_id;
    }
} 