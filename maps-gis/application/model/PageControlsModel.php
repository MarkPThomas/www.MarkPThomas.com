<?php

namespace markpthomas\gis;
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/22/18
 * Time: 10:47 PM
 */

class PageControlsModel {

    public function getCurrentCategoryId(){
        return isset($_GET['category'])? $_GET['category'] : '';
    }

    public function getPageName(){
        return basename($_SERVER['PHP_SELF']);
    }

    public function getPageId(){
        return isset($_GET['p_id'])? $_GET['p_id'] : '';
    }
} 