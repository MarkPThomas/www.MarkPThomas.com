<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/22/18
 * Time: 11:03 PM
 */

// Navigation
$pageControlsModel = $this->loadModel('PageControlsModel');

// ===== Navigation Ribbon Controller =====
$currentCategoryId = $pageControlsModel->getCurrentCategoryId();
$pageName = $pageControlsModel->getPageName();
$pageId = $pageControlsModel->getPageId();

if (empty($currentCategoryId))
{
    $currentCategoryId = $pageName;
}

$navigationController = new NavigationList(URL, $isLoggedIn = false);
$navControllerStream = $navigationController->writeNavListItems(
    ['structural' => 'Structural'],
    $currentCategoryId, $pageName, $pageId);

// ===== Sidebar Controller =====
// TODO: Design GIS site sidebar, if present
//$sidebarController = new Sidebar(URL);
//$sidebarSearchStream = $sidebarController->displaySearch();
//$sidebarLoginStream = $sidebarController->displayLogin();
//
//require_once 'application/controller/trip_reports.php';
//$listCategories = Trip_Reports::$categories;
//$sidebarBlogCategoriesStream = $sidebarController->displayBlogCategories('Report Regions', 'trip-reports', $listCategories, $currentCategoryId);
//$sidebarArticlesStream = $sidebarController->displayArticle();