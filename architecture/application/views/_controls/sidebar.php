<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/23/18
 * Time: 8:32 AM
 */

class Sidebar {
    private $rootNode;

    public function __construct($rootNode){
        $this->rootNode = $rootNode;
    }

    public function displaySearch($title = null, $display = false){
        if (empty($title))
            $title = 'Search';

        return (empty($title) || !$display)? '' :
            '<div class="well">
                <h4>' . $title . '</h4>
                <form action="' . URL . 'search" method="post">
                    <div class="input-group">
                        <input name="search" type="text" class="form-control">
                        <span class="input-group-btn">
                            <button name="submit" class="btn btn-default" type="submit">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                    </div>
                </form>
                <!-- /.input-group -->
            </div>';
    }

    public function displayLogin($display = false){
        return !$display? '' :
            '<div class="well">'
            // TODO: Refactor login/logout control
            . loginLogoutControl() .
            '</div>';
    }

    public function displayArticle($title = null){
        if (empty($title))
            $title = 'Articles';
        $categoryItemsStream = 'TBD';
        return (empty($categoryItemsStream))? '' :
            '<div class="well">
                <h4>' . $title . '</h4>
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="list-unstyled">'
                        . $categoryItemsStream .
                        '</ul>
                    </div>
                    <!-- /.col-lg-6 -->
                </div>
                <!-- /.row -->
            </div>';
    }

    public function displayBlogCategories($title = null, $categoryParent, $categories, $current_cat_id = null){
        if (empty($title))
            $title = 'Blog Categories';

        $categoryItemsStream = $this->writeCategoryItems($categoryParent, $categories, $current_cat_id);

        return (empty($categoryItemsStream))? '' :
            '<div class="well">
                <h4>' . $title . '</h4>
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="list-unstyled">'
                           . $categoryItemsStream .
                        '<hr class="hr-thick">
                         <li><a href="' . URL . 'articles">Articles</a></li></ul>
                    </div>
                    <!-- /.col-lg-6 -->
                </div>
                <!-- /.row -->
            </div>';
        // TODO: Work out articles display better. This is temporary.
    }

    private function writeCategoryItems($categoryParent, $categories, $current_cat_id = null){
        $controlStream = '';

        $controlStream .= $this->categoryItemsRequest($categoryParent, $categories, $current_cat_id);

        return $controlStream;
    }

    private function categoryItemsRequest($categoryParent, $categories, $current_cat_id){
        $controlStream = '';
        foreach($categories as $cat_id => $category_title){
            // Set item to be selected if it is for the currently viewed page
            $selection_class = '';
            if ($current_cat_id == $cat_id){
                $selection_class = 'active';
            }
            $controlStream .=
                "<li class='$selection_class'>
                    <a href='". $this->rootNode . "{$categoryParent}/{$cat_id}'>{$category_title}</a>
                </li>";
        }
        return $controlStream;
    }
} 