<?php

namespace markpthomas\mountaineering;

use markpthomas\library as Lib;

/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/18/18
 * Time: 3:25 PM
 */

class Trip_ReportsController extends ControllerReport
{
    function __construct() {
        parent::__construct();
        $this->topicDirectory = 'trip-reports';
    }


    public function index()
    {
        $fileName = Lib\PathHelper::Combine([$this->topicDirectory, 'index'], $isRoot = false);
        $this->View->render($fileName, ['categories' => TripReportsModel::$reportCategories]);
    }

    /**
     * PAGE: alaska
     * This method handles what happens when you move to http://yourproject/mountaineering/trip-reports/alaska
     * The camelCase writing is just for better readability. The method name is case insensitive.
     */
    public function alaska($reportName = null, $subReportName = null)
    {
        $this->renderPage('alaska', $reportName, $subReportName);
    }


    public function california($reportName = null, $subReportName = null)
    {
        $this->renderPage('california', $reportName, $subReportName);
    }

    public function canada($reportName = null, $subReportName = null)
    {
        $this->renderPage('canada', $reportName, $subReportName);
    }

    public function colorado($reportName = null, $subReportName = null)
    {
        $this->renderPage('colorado', $reportName, $subReportName);
    }

    public function idaho($reportName = null, $subReportName = null)
    {
        $this->renderPage('idaho', $reportName, $subReportName);
    }

    public function utah($reportName = null, $subReportName = null)
    {
        $this->renderPage('utah', $reportName, $subReportName);
    }

    public function washington($reportName = null, $subReportName = null)
    {
        $this->renderPage('washington', $reportName, $subReportName);
    }

    public function wyoming($reportName = null, $subReportName = null)
    {
        $this->renderPage('wyoming', $reportName, $subReportName);
    }

    public function viewAlbums()
    {
        $this->View->render($this->topicDirectory . '/viewAlbums', ['albums' => PiwigoModel::getAlbums()]);
    }

    public function album($album_id)
    {
        $album = PiwigoModel::getAlbum($album_id);
        $this->View->render($this->topicDirectory . '/album',
            [
                'album_id' => $album_id,
                'album_title' => $album->title,
                'photos' => PiwigoModel::getAlbumPhotos($album_id)
            ]);
    }

    public function createReport()
    {
        $album_id 	= Request::post("album_id");

        $album = PiwigoModel::getAlbum($album_id);
        $comment = $album? $album->comment : null;

        $data = PiwigoModel::createReportFromAlbum($album_id, $comment);

//        $photosDisplaySize = PiwigoModel::getAlbumPhotosDisplaySize($album_id);
//
//        $this->View->render($this->topicDirectory . '/editReport',
//            [
//                'subtitle' => $comment,
//                'photosDisplaySize' => $photosDisplaySize,
//                'photosFullSize' => PiwigoModel::getAlbumPhotos($album_id),
//                'picIdsSelection' => PiwigoModel::getSelectedPhotos($photosDisplaySize)
//            ]);

        if (!empty($data)) $data = Lib\JsonHandler::decode($data, $assoc = true);

        $this->View->render($this->topicDirectory . '/editReport', $data);
    }

    public function editReport($reportId = 0)
    {
        if ($reportId)
        {
            $data = TripReportsModel::getReportById($reportId);
        } else {
            $data = TripReportsModel::getReportByUrlAsJSON(URL::getPreviousUrl());
        }

        if (!empty($data)) $data = Lib\JsonHandler::decode($data, $assoc = true);

        $this->View->render($this->topicDirectory . '/editReport', $data);
    }
}