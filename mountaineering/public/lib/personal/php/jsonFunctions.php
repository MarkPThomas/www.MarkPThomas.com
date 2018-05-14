<?php

/**
 * @param $php_version_local
 * @param $search_url
 * @return mixed|string
 */
function json_object_by_version($php_version_local, $search_url)
{
    if (phpversion() == $php_version_local){
        // Currently for WAMP
        $json = file_get_contents($search_url);   //allow_url_fopen needs to be on for this to work
    }
    else
    {
        // Currently for NetworkSolutions
        // The following is for Network Solutions as file_get_contents does not work in this way since
        // allow_url_fopen = off.
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $search_url);
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curlSession);
        curl_close($curlSession);
    }

    return $json;
}

?>