<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/8/18
 * Time: 12:31 AM
 */

namespace markpthomas\mountaineering;

use markpthomas\library as Lib;

class ControllerReport extends Controller
{
    protected $topicDirectory = '';

    public function __construct()
    {
        parent::__construct();
    }

    protected function renderPage($groupName = '', $reportName = null, $subReportName = null){
        if (empty($reportName)){
            if (empty($groupName)){
                $fileName = Lib\PathHelper::Combine([$this->topicDirectory, 'index'], $isRoot = false);
            } else {
                $fileName = Lib\PathHelper::Combine([$this->topicDirectory, $groupName], $isRoot = false);
            }

            $reportUrlStub = Lib\PathHelper::Combine(['mountaineering', $this->topicDirectory, $groupName]);
            $data = TripReportsModel::getReportsList($reportUrlStub);
            if (!empty($data)) {
                $reports = Lib\JsonHandler::decode($data, $assoc = true);
                natsort($reports);
                $data = ['categories' => $reports];
            }
        } else {
            $fileName = Lib\PathHelper::Combine([$this->topicDirectory, 'trip_report'], $isRoot = false);

            // TODO: Rework this later to use better key.... maybe w/o /mountaineering in the report name?
            // $reportUrlStub is currently used as a key to fetch the correct trip report based on '/mountaineering/trip-reports/alaska/reportName/subReportName
            $reportUrlStub = Lib\PathHelper::Combine(['mountaineering', $this->topicDirectory, $groupName, $reportName, $subReportName]);
            $reportUrlStub = str_replace('_', '-', $reportUrlStub);
            $data = TripReportsModel::getReportByUrlAsJSON($reportUrlStub);

            if (!empty($data)) $data = Lib\JsonHandler::decode($data, $assoc = true);
        }

        $this->View->render($fileName, $data);
    }
} 