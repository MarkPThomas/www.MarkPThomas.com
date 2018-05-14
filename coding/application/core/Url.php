<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 5/2/18
 * Time: 8:34 AM
 */

namespace markpthomas\coding;

use markpthomas\library as Lib;

class Url {

    public static function getAbsolute($url, $urlStub = ''){
        if (empty($urlStub)){
            $urlStub = Config::get('URL');
        }

        if ($url[0] == '/'){
            return $urlStub . '..' . $url;
        } elseif (!Lib\StringHelper::stringContains($url, 'http')){
            return $urlStub . '../' . $url;
        } else {
            return $url;
        }
    }
} 