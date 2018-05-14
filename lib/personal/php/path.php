<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/1/18
 * Time: 10:31 AM
 */

/**
 * Contains utility methods for working with paths.
 */
class Path {

    /**
     * Combines all provided components into a valid path without redundant slashes.
     * @param array $urlComponents Each individual URL component to combine.
     * @param bool $isRoot If true, a preceding slash is left on. Otherwise, the path starts with no slash.
     * @param string $demarcator The slash or other single character demarcator to use between the components.
     * @return string The combined URL.
     */
    public static function Combine(array $urlComponents, $isRoot = true, $demarcator = '/'){
        $combinedUrl = '';
        $demarcator = $demarcator[0];

        foreach($urlComponents as $urlComponent){
            if (empty($urlComponent)) continue;

            if ($urlComponent[0] === $demarcator) $urlComponent = substr($urlComponent, 1);
            if ($urlComponent[strlen($urlComponent) - 1] === $demarcator) $urlComponent = substr($urlComponent, 0, strlen($urlComponent) - 1);
            $combinedUrl .= $demarcator . $urlComponent;
        }

        return $isRoot? $combinedUrl : substr($combinedUrl, 1);
    }
}