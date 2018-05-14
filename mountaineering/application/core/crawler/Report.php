<?php

namespace markpthomas\crawler;

use markpthomas\library as Lib;
use markpthomas\mountaineering as Core;

/**
 * Represents the page-specific crawler data as a generic report.
 * @package markpthomas\crawler
 */
class Report
    implements Lib\IFactory
{

    private $contentKey = "content";
    private $keyValueDemarcator = ': ';

    private $internalSiteUrlStub = 'http://www.markpthomas.com/mountaineering/';
    private $albumNames = [
        'Picasa Album' ,
        'Picasa Photo Album' ,
        'Picasa Photos' ,
        'Picasa'
    ];

    private $albumTriggers = [
        'Picasa',
        'Album'
    ];
    
    private $linkTriggers = [
        'SummitPost Article',
        'SummitPost Page',
        'SummitPost TR',
        'SummitPost',
        'SuperTopo TR',
        'Bob Burd',
        'Bob Burd\'s',
        'Steph TR',
        'Steph\'s TR',
        'Steph Abegg\'s TR',
        'Steph Abegg\'s Trip Report'
    ];

    public $externalSiteStub = '';

    public $title;
    public $menuTitle;
    public $type;

    /* @var ReportHtmlParser Parser that contains the raw and parsed HTML content. */
    public $content;

    /**
     * @param string $title
     * @param string $type
     * @param string $content
     * @param string $menuTitle
     * @param string $url
     * @param string $externalSiteStub Website URL of the external site. This is the portion that precedes any relative path in the site.
     */
    function __construct($title = '', $type = '', $content = '', $menuTitle = '', $url = '', $externalSiteStub = '')
    {
        $this->title = $title;
        $this->type = $type;
        $this->menuTitle = $menuTitle;
        $this->externalSiteStub = $externalSiteStub;

        // Load HTML parser
        $this->content = new ReportHtmlParser($content,
                                $skipCharacters = strlen($this->contentKey . $this->keyValueDemarcator),
                                $url);
        $this->content->internalSiteUrlStub = $this->internalSiteUrlStub;
        $this->content->albumNames = $this->albumNames;
        $this->content->albumTriggers = $this->albumTriggers;
        $this->content->linkTriggers = $this->linkTriggers;
    }

    public static function arrayFactory($count){
        $array = array();
        for ($i = 0; $i < $count; $i++) {
            $page = self::factory();
            array_push($array, $page);
        }
        return $array;
    }

    public static function factory(){
        return new Report();
    }

    /**
     * Parses the content and returns a new version of the data in a standardized non-crawler Report object.
     * @return Core\Report
     */
    public function parseContent(){
        $this->content->parseContent();

        // Create Header
        $newPage = new \stdClass();
        $newPage->title_menu = $this->menuTitle;
        $newPage->title_full = $this->title;
        $newPage->url = $this->content->url;
        $newPage->user_id = Core\Session::get('user_id');
        $newPage->is_public = true;

        $newHeader = new \stdClass();
        $newHeader->report_trip_type = self::getReportTypeName($this->type);
        $newHeader->report_trip_status_id = 4;
        $newHeader->page = $newPage;

        // Create body
        $body = [];
        for ($i = 0; $i < count($this->content->textBodies); $i++){
            $newBody = new \stdClass();
            $newBody->sequence = $i;
            $newBody->header_type = $this->content->headerTypes[$i];
            $newBody->header_value = $this->content->headerValues[$i];
            $newBody->text_body = $this->content->textBodies[$i];

            $photo = $this->content->images[$i];
            $newReportPhoto = new \stdClass();
            if ($photo){
                $newPhoto = new \stdClass();
                $newPhoto->url = $this->getFullUrl($this->content->images[$i]);
                $newPhoto->url_picasa = (self::isPicasaUrl($newPhoto->url)) ? $newPhoto->url : null;
                $newPhoto->caption = $this->content->imageCaptions[$i];
                $newPhoto->file_name = $this->content->imageFileNames[$i];
                $newPhoto->is_public = true;

                $newReportPhoto->custom_caption = $this->content->imageCaptions[$i];

                // It is assumed that if there is no caption here, it is meant to be suppressed.
                // This is because the original likely has a caption.
                $newReportPhoto->suppress_caption = empty($newReportPhoto->custom_caption);
                $newReportPhoto->photo = $newPhoto;
            }
            $newBody->report_photo = $newReportPhoto;

            $video = $this->content->videos[$i];
            $newReportVideo = new \stdClass();
            if ($video){
                $newVideo = new \stdClass();
                $newVideo->url = $this->getFullUrl($this->content->videos[$i]);
                $newVideo->url_picasa = (self::isPicasaUrl($newVideo->url)) ? $newVideo->url : null;
                $newVideo->id_youtube = self::getYouTubeId($newVideo->url);
                $newVideo->id_vimeo = self::getVimeoId($newVideo->url);
                $newVideo->caption = $this->content->videoCaptions[$i];
                $newVideo->is_public = true;

                $newReportVideo->custom_caption = $this->content->videoCaptions[$i];
                // It is assumed that if there is no caption here, it is meant to be suppressed.
                // This is because the original likely has a caption.
                $newReportVideo->suppress_caption = empty($newReportVideo->custom_caption);;
                $newReportVideo->video = $newVideo;
            }
            $newBody->report_video = $newReportVideo;

            array_push($body, $newBody);
        }

        // Create albums
        $albums = [];
        foreach($this->content->photoAlbums as $album){
            $newAlbum = new \stdClass();
            $newAlbum->url = $this->getFullUrl($album[0]);
            $newAlbum->url_picasa = (self::isPicasaUrl($newAlbum->url)) ? $newAlbum->url : null;
            $newAlbum->title = $album[1];
            $newAlbum->is_public = true;
            array_push($albums, $newAlbum);
        }

        // Create links
        $internalLinks = [];
        foreach($this->content->linksInternal as $link){
            $newLink = new \stdClass();
            $newLink->website_URL = $link[0];
            $newLink->name = $link[1];
            $newLink->description = "Internal Link";
            $newLink->is_public = true;
            array_push($internalLinks, $newLink);
        }

        $externalLinks = [];
        foreach($this->content->links as $link){
            $newLink = new \stdClass();
            $newLink->website_URL = $this->getFullUrl($link[0]);
            $newLink->name = $link[1];
            $newLink->description = "External Link";
            $newLink->is_public = true;
            array_push($externalLinks, $newLink);
        }

        $references = [];


        // Assemble report
        $newReport = new \stdClass();
        $newReport->header = $newHeader;
        $newReport->body = $body;
        $newReport->albums = $albums;
        $newReport->internalLinks = $internalLinks;
        $newReport->externalLinks = $externalLinks;
        $newReport->references = $references;

        $report = Core\Report::factoryStdClass($newReport);
        return $report;
    }

    private function getFullUrl($url){
        $rootUrl = 'http';
        if (!empty($externalSiteStub) ||
            strtolower(substr($url, 0, strlen($rootUrl))) === $rootUrl){
            return $url;
        }
        $url = ($url[0] === '/')? $url : '/' . $url;
        $this->externalSiteStub = (substr($this->externalSiteStub, -1) === '/')?
                                    substr($this->externalSiteStub, 0, strlen($this->externalSiteStub) - 1) :
                                    $this->externalSiteStub;
        return $this->externalSiteStub . $url;
    }

    public static function getReportTypeName($crawlerReportTypeName){
        return ($crawlerReportTypeName === 'Trip_Report' ||
                $crawlerReportTypeName === 'Trip Report' ||
                $crawlerReportTypeName === 'Standard' ||
                empty($crawlerReportTypeName))? 'Standard' : 'Article';
    }

    public static function getYouTubeId($url){
        $youTubeId = null;
        $urlComponents = explode('/',$url );
        if (strpos($url, 'youtube')){
            $youTubeId = $urlComponents[count($urlComponents) - 1];
        }
        return $youTubeId;
    }

    public static function getVimeoId($url){
        $vimeoId = null;
        $urlComponents = explode('/',$url );
        if (strpos($url, 'vimeo')){
            $vimeoId = $urlComponents[count($urlComponents) - 1];
        }
        return $vimeoId;
    }

    public static function isPicasaUrl($url){
        return ((strpos($url, 'ggpht') !== false) ||
                (strpos($url, 'googleusercontent') !== false) ||
                (strpos($url, 'picasaweb') !== false) ||
                preg_match('/^[s]{1}\d+/', $url));
    }
} 