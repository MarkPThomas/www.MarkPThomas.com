<?php

namespace markpthomas\mountaineering;
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/19/18
 * Time: 1:40 AM
 */

class ArticlesController extends ControllerReport
{
    function __construct() {
        parent::__construct();
        $this->topicDirectory = 'articles';
    }

    public function index($reportName = null)
    {
        $this->renderPage('', $reportName);
    }
} 