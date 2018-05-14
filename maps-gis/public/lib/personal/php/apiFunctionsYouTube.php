<?php

// Generate the first part of the URL
function youtube_api_url_prefix($type)
{
    $url = 'https://www.googleapis.com/youtube/v3/' . $type;

    return $url;
}

// Add the parts to add to the results
function youtube_api_url_part($parts)
{
    $urlStub = $parts[0];
    if (count($parts) > 1)
    {
        for ($i=1; $i < count($parts); $i++)
        {
            $urlStub .= '%2C+' . $parts[$i];
        }
    }

    return '?part=' . $urlStub;
}

// Add search limits if different from the default
function youtube_api_url_search_limits($limits)
{
    if (!$limits == 5)
    {
        Return "&maxResults=" . $limits;
    }
}

// Add the video id
function youtube_api_url_video_id($videoID)
{
    return '&id=' . $videoID;
}

// Add the search items
function youtube_api_url_search_items($searchItems)
{
    $urlStub = "";
    if (count($searchItems) > 0)
    {
        $urlStub .=  "&q=" . $searchItems[0];
    }
    for ($i=1; $i<count($searchItems); $i++)
    {
        $urlStub .=  '%2C' . $searchItems[$i];
    }

    return $urlStub;
}

// Add the result items to limit the search to
function youtube_api_url_limit_items($limitItems)
{
    if (count($limitItems) == 1)
    {
        return "&fields=items%2F" . $limitItems[0];
    }
    elseIf(count($limitItems) > 1)
    {
        $urlStub = ("&fields=items(" . $limitItems[0]);
        for ($i=1; $i<count($limitItems); $i++)
        {
            $urlStub .= '%2C' . $limitItems[$i];
        }
        $urlStub .= ")";

        return $urlStub;
    }
}

// Add the API key
function youtube_api_url_key($apiKey)
{
    return '&key=' . $apiKey;
}

// Format the time
function youtube_api_video_time_format($video_time)
{
    // Get seconds
    $intStart = 0;
    if (strrpos($video_time, 'M'))
    {
        $intStart = strrpos($video_time, 'M') + 1;
    }
    elseif (strrpos($video_time, 'T'))
    {
        $intStart = strrpos($video_time, 'T') + 1;
    }

    $length_sec = '';
    if (!$intStart == 0){
        $length_sec = substr($video_time, $intStart, strrpos($video_time, 'S')-$intStart);

        if ($length_sec == 1)
        {
            $length_sec .= ' second';
        }
        elseif ($length_sec > 1)
        {
            $length_sec .= ' seconds';
        }
    }

    // Get minutes
    $intStart = 0;
    if (strrpos($video_time, 'H'))
    {
        $intStart = strrpos($video_time, 'H') + 1;
    }
    elseif (strrpos($video_time, 'M'))
    {
        $intStart = strrpos($video_time, 'PT') + 2;
    }

    $length_min = '';
    if (!$intStart == 0){
        $length_min = substr($video_time, $intStart, strrpos($video_time, 'M')-$intStart);
        if ($length_min == 1)
        {
            $length_min .= ' minute, ';
        }
        elseif ($length_min > 1)
        {
            $length_min .= ' minutes, ';
        }
    }

    // Get hours
    $intStart = 0;
    if (strrpos($video_time, 'H'))
    {
        $intStart = strrpos($video_time, 'PT') + 2;
    }

    $length_hrs = '';
    if (!$intStart == 0){
        $length_hrs = substr($video_time, $intStart, strrpos($video_time, 'H')-$intStart);
        if ($length_hrs == 1)
        {
            $length_hrs .= ' hour, ';
        }
        elseif ($length_hrs > 1)
        {
            $length_hrs .= ' hours, ';
        }
    }

    // Get the formatted duration
    $length_formatted = $length_hrs . $length_min .$length_sec;

    return $length_formatted;
}

?>