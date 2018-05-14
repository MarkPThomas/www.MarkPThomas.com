<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/22/18
 * Time: 10:27 PM
 */

class NavigationList {
    private $rootNode;
    private $isLoggedIn;

    public function __construct($rootNode, $isLoggedIn = false){
        $this->rootNode = $rootNode;
        $this->isLoggedIn = $isLoggedIn;
    }

    public function writeNavListItems($categories, $current_cat_id = null, $pageName = null, $p_id = null){
        $controlStream = '';

        $controlStream .= $this->categoryItemsRequest($categories, $current_cat_id);

        // Set item to be selected if it is for the currently viewed page
        if($this->isLoggedIn){
            $controlStream .= $this->adminPageRequest();
        } else {
            // TODO: Implement & then unhide login & registration
//            $controlStream .= $this->loginItemRequest($pageName);
//            $controlStream .= $this->registrationItemRequest($pageName);
        }

        $controlStream .= $this->contactItemRequest($pageName);

        $controlStream .= $this->editPostItemRequest($p_id);

        return $controlStream;
    }

    private function categoryItemsRequest($categories, $current_cat_id){
        $controlStream = '';
        foreach($categories as $cat_id => $category_title){
            // Set item to be selected if it is for the currently viewed page
            $selection_class = '';
            if ($current_cat_id == $cat_id){
                $selection_class = 'active';
            }
            $controlStream .=
                "<li class='$selection_class'>
                    <a href='". $this->rootNode . "{$cat_id}'>{$category_title}</a>
                </li>";
        }
        return $controlStream;
    }

    private function contactItemRequest($pageName){
        $targetPage = 'contact.php';
        $selection_class = ($pageName === $targetPage)? 'active' : '';

        return
            "<li class='$selection_class'>
                <a href='" . $this->rootNode . "contact'>Contact</a>
            </li>";
    }

    private function registrationItemRequest($pageName){
        $targetPage = 'registration.php';
        $selection_class = ($pageName === $targetPage)? 'active' : '';

        return
            "<li class='$selection_class'>
                <a href='" . $this->rootNode . "registration'>Registration</a>
            </li>";
    }

    private function adminPageRequest(){
        return "<li>
                    <a href='" . $this->rootNode . "admin'>Admin</a>
                </li>";
    }

    private function loginItemRequest($pageName){
        $targetPage = 'login.php';
        $selection_class = ($pageName === $targetPage)? 'active' : '';

        return
            "<li class='$selection_class'>
                <a href='" . $this->rootNode . "login'>Login</a>
            </li>";
    }

    private function editPostItemRequest($p_id){
        if ($this->isLoggedIn && !empty($p_id)){
            return
                "<li>
                    <a href='" . $this->rootNode . "admin/posts/edit_post/{$p_id}'>Edit Post</a>
                </li>";
        }
        return '';
    }
} 