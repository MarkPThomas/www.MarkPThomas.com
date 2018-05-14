<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/27/18
 * Time: 12:20 PM
 */

class ControllerLocalSite extends Controller{
    protected function loadHeadersAndNavigation(){
        // Additional model work
        require 'application/controller/_controls/loadPageControls.php';
        $navControllerStream = isset($navControllerStream)? $navControllerStream : '';

        $sidebarSearchStream = isset($sidebarSearchStream)? $sidebarSearchStream : '';
        $sidebarLoginStream = isset($sidebarLoginStream)? $sidebarLoginStream : '';
        $sidebarBlogCategoriesStream = isset($sidebarBlogCategoriesStream)? $sidebarBlogCategoriesStream : '';
        $sidebarArticlesStream = isset($sidebarArticlesStream)? $sidebarArticlesStream : '';

        // Load view
        $this->loadHeader();
        $this->loadNavigation($navControllerStream);
        // TODO: Actvate sidebar
        //$this->loadSidebar($sidebarSearchStream . $sidebarLoginStream . $sidebarBlogCategoriesStream . $sidebarArticlesStream);
    }
} 