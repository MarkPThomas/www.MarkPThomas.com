<?php

namespace markpthomas\coding;
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/23/18
 * Time: 8:32 AM
 */

class Sidebar {
    public static function displayBlogCategories($filename, $title = null, $categoryParent, $categories){
        if (empty($title)) { $title = 'Blog Categories'; }

        $categoryItemsStream = self::categoryItemsRequest($filename, $categoryParent, $categories);

        return (empty($categoryItemsStream))? '' :
            '<div class="well">
                <h4>' . $title . '</h4>
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="list-unstyled">'
                           . $categoryItemsStream .
                            '<hr class="hr-thick">
                             <li>
                                <a href="' . Config::get('URL') . 'articles">Articles</a>
                             </li>
                        </ul>
                    </div>
                    <!-- /.col-lg-6 -->
                </div>
                <!-- /.row -->
            </div>';
        // TODO: Work out articles display better. This is temporary.
    }

    public static function categoryItemsRequest($filename, $categoryParent, $categories){
        $controlStream = '';
        if (empty($categories)) return $controlStream;

        foreach($categories as $cat_id => $category_title){
            // Set item to be selected if it is for the currently viewed page
            $selection_class = (View::checkForActiveController($filename, $cat_id))? 'active' : '';
            $controlStream .=
                "<li class='$selection_class'>
                    <a href='". Config::get('URL') . "{$categoryParent}/{$cat_id}'>{$category_title}</a>
                </li>";
        }
        return $controlStream;
    }

    public static function displaySearch($title = null, $display = false){
        if (empty($title)) { $title = 'Search'; }

        return ($display)?
            '<div class="well">
                <h4>' . $title . '</h4>
                <form action="' . Config::get('URL') . 'search" method="post">
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
            </div>'
            : '';
    }

    // TODO: The methods below might be appropriate for removal
    public static function displayLogin($display = false){
        return $display?
            '<div class="well">'
            // TODO: Refactor login/logout control
            . loginLogoutControl() .
            '</div>'
            : '';
    }

    public static function displayArticle($title = null){
        if (empty($title)) { $title = 'Articles'; }
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
} 