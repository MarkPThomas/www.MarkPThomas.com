<?php

namespace markpthomas\gis;
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/22/18
 * Time: 10:27 PM
 */

class Navigation {

    public static function writeNavBarItems($filename, $categories, $hasLogin = true){
        $controlStream = '';

        $controlStream .= self::categoryItemsRequest($filename, $categories);

        // Set item to be selected if it is for the currently viewed page
        if(Session::userIsLoggedIn()){

        } elseif ($hasLogin) {
            $controlStream .= self::loginItemRequest($filename);
            $controlStream .= self::registrationItemRequest($filename);
        }

        $controlStream .= self::contactItemRequest($filename);

        return $controlStream;
    }

    // TODO: Finish generic dropdown control?
    public static function dropDownControl($customButton = null, $label = ''){
        $controlStream = "<div class='dropdown'>";
        $controlStream .= $customButton ?
                            $customButton :
                            "<button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>{$label}
                                                <span class='caret'></span></button>";
        $controlStream .= "    <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>";
        $controlStream .= "         <li role='presentation'><a role='menuitem' tabindex='-1' href='#'>Pending</a></li>";
        $controlStream .= "    </ul>
                            </div>";
        
        return $controlStream;
    }

    public static function loginDropDownControl($username){
        $url = Config::get('URL');

        if ($username === null)
            $username = 'Username';

        $controlStream = "<li>
                             <div class='btn-group'>
                                <button class='btn btn-default user-options rounded-left' type='button' id='user-profile'>
                                    <span><img width='32px' class='img-rounded' src='{$url}favicon-32x32.png' /></span>
                                {$username}
                                </button>
                                <button class='btn btn-default dropdown-toggle user-options rounded-right' type='button' id='user-options' data-toggle='dropdown'>
                                    <span class='caret'></span>
                                </button>
                                <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>
                                    <li role='presentation'>
                                        <a role='menuitem' tabindex='-1' href='{$url}user'><i class='fas fa-fw fa-user'></i> Profile</a>
                                    </li>
                                    <li role='presentation'>
                                        <a role='menuitem' tabindex='-1' href='{$url}profile'><i class='fas fa-fw fa-users'></i> Users</a>
                                    </li>
                                    ";
        if (Session::userIsAdmin()){
            $controlStream .= "     <li role='presentation' class='divider'></li>
                                    <li role='presentation'>
                                        <a role='menuitem' tabindex='-1' href='{$url}admin'><i class='fas fa-fw fa-signal'></i> Admin</a>
                                    </li>";
        }
        $controlStream .= "         <li role='presentation' class='divider'></li>
                                    <li role='presentation'>
                                        <a role='menuitem' tabindex='-1' href='{$url}login/logout'><i class='fas fa-fw fa-power-off'></i> Logout</a>
                                    </li>
                                </ul>
                            </div>
                          </li>";
        return $controlStream;
    }
    
    
    public static function categoryItemsRequest($filename, $categories){
        $controlStream = '';
        if (empty($categories)) return $controlStream;
        foreach($categories as $cat_id => $category_title){
            // Set item to be selected if it is for the currently viewed page
            $selection_class = (View::checkForActiveController($filename, $cat_id))? 'active' : '';
            $controlStream .=
                "<li class='$selection_class'>
                    <a href='". Config::get('URL') . $cat_id ."'>{$category_title}</a>
                </li>";
        }
        return $controlStream;
    }

    public static function contactItemRequest($filename){
        // Set item to be selected if it is for the currently viewed page
        $selection_class = (View::checkForActiveController($filename, 'contact'))? 'active' : '';
        return
        "<li class='$selection_class'>
                <a href='" . Config::get('URL') . "contact'>Contact</a>
            </li>";
    }

    public static function usersRequest($filename){
        // Set item to be selected if it is for the currently viewed page
        $selection_class = (View::checkForActiveController($filename, 'profile'))? 'active' : '';
        return
            "<li class='$selection_class'>
                <a href='" . Config::get('URL') . "profile'>Users</a>
            </li>";
    }

    public static function profileRequest($filename){
        // Set item to be selected if it is for the currently viewed page
        $selection_class = (View::checkForActiveController($filename, 'user'))? 'active' : '';
        return
            "<li class='$selection_class'>
                <a href='" . Config::get('URL') . "user'>Profile</a>
            </li>";
    }

    public static function adminRequest($filename){
        // Set item to be selected if it is for the currently viewed page
        $selection_class = (View::checkForActiveController($filename, 'admin'))? 'active' : '';
        return
            "<li class='$selection_class'>
                <a href='" . Config::get('URL') . "admin'>Admin</a>
            </li>";
    }

    public static function dashboardRequest($filename){
        // Set item to be selected if it is for the currently viewed page
        $selection_class = (View::checkForActiveController($filename, 'dashboard'))? 'active' : '';
        return
            "<li class='$selection_class'>
                <a href='" . Config::get('URL') . "dashboard'>Dashboard</a>
            </li>";
    }

    public static function noteRequest($filename){
        // Set item to be selected if it is for the currently viewed page
        $selection_class = (View::checkForActiveController($filename, 'note'))? 'active' : '';
        return
            "<li class='$selection_class'>
                <a href='" . Config::get('URL') . "note'>My Notes</a>
            </li>";
    }

    public static function loginItemRequest($filename){
        // Set item to be selected if it is for the currently viewed page
        $selection_class = (View::checkForActiveController($filename, 'login'))? 'active' : '';

        return
            "<li class='$selection_class'>
                <a href='" . Config::get('URL') . "login'>Login</a>
            </li>";
    }

    public static function logoutItemRequest(){
        return
            "<li>
                <a href='" . Config::get('URL') . "login/logout'>Logout</a>
            </li>";
    }

    public static function registrationItemRequest($filename){
        // Set item to be selected if it is for the currently viewed page
        $selection_class = (View::checkForActiveController($filename, 'register'))? 'active' : '';

        return
            "<li class='$selection_class'>
                <a href='" . Config::get('URL') . "register'>Register</a>
            </li>";
    }

    public static function editPostItemRequest($filename, $p_id){
        // Set item to be selected if it is for the currently viewed page
        $selection_class = (View::checkForActiveController($filename, 'register'))? 'active' : '';

        return
            "<li>
                <a href='" . Config::get('URL') . "admin/posts/edit_post/{$p_id}'>Edit Post</a>
            </li>";
    }

// TODO: Finish integrating pagination, next/previous controls
    function numberOfPages($number_of_posts_per_page, $post_status = ''){
        $count = numberOfPrepared('cms.post', ['post_status' => $post_status]);
        return ceil($count / $number_of_posts_per_page);
    }


    function paginationControl($page_min, $number_of_pages, $page_name = 'index'){
        for($i = 1; $i <= $number_of_pages; $i++){
            if ($i == $page_min){
                echo "<li><a class='active_link' href='{$page_name}/page/{$i}'>{$i}</a></li>";
            } else {
                echo "<li><a href='{$page_name}/page/{$i}'>{$i}</a></li>";
            }
        }
    }

    function nextPreviousControl($tableName, $idName, $idValue, $pageName){
        global $db;

        // Previous Item
        $previousQuery = "SELECT {$idName} FROM {$tableName}
                            WHERE {$idName} =
                                (SELECT MIN({$idName}) FROM {$tableName} WHERE {$idName} < {$idValue})";
        $previousItem = $db->query($previousQuery);
        confirmQuery($previousItem);

        $idPrevious = '';
        while($row = $previousItem->fetch_array()){
            $idPrevious = $row[$idName];
        }
        $hrefPrevious = (!empty($idPrevious))? ROOT_NODE . '/' . $pageName . '/' .$idPrevious : '';
        $classDisabledPrevious = empty($hrefPrevious)? 'disabled' : '';



        // Next Item
        $nextQuery = "SELECT {$idName} FROM {$tableName}
                        WHERE {$idName} =
                            (SELECT MIN({$idName}) FROM {$tableName} WHERE {$idName} > {$idValue})";
        $nextItem = $db->query($nextQuery);
        confirmQuery($nextItem);

        $idNext = '';
        while($row = $nextItem->fetch_array()){
            $idNext = $row[$idName];
        }
        $hrefNext = (!empty($idNext))? ROOT_NODE . '/' . $pageName . '/' .$idNext : '';
        $classDisabledNext = empty($hrefNext)? 'disabled' : '';

        echo "<ul class='pager'>
                <li class='previous {$classDisabledPrevious}'>
                    <a href='{$hrefPrevious}'>&larr; Older</a>
                </li>
                <li class='next {$classDisabledNext}'>
                    <a href='{$hrefNext}'>Newer &rarr;</a>
                </li>
            </ul>";
    }

} 