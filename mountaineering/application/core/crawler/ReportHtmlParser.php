<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/29/18
 * Time: 12:09 PM
 */

namespace markpthomas\crawler;

use markpthomas\library as Lib;

/**
 * Parses the HTML content, stripping/changing some HTML tags, and placing data into various grouped arrays.
 * @package markpthomas\crawler
 */
class ReportHtmlParser {
    // State
    /**
     * @var int Total length of the main content.
     */
    private $contentLength = 0;

    private $skipCharacters = 0;

    /**
     * @var string Current content recorded from the main content.
     */
    private $currentContent = '';

    private $recentImage = false;
    private $recentVideo = false;


    /**
     * @var int The sequence number for the coordinated repeating data of text, image, image caption, etc.
     */
    public $sequence = 0;

    private $content;
    public $url;

    // Filter/Modify
    public $tagsIgnore = [
        //'p',
        'hr',
        'div',
        'span',
        'center',
        'iframe',
        'blockquote',
        'table',
        'tbody',
        'tr',
        'td',
        'ul',
        'ol',
        'li',
        'script',
        'meta',
        'link',
        'style',
        'article',
        'noformat'
    ];


    public $tagsReplace = [
        '<li>' => '<br />',
        '</li>' => '<br />',
        '<p><br>' => '<br />',
        '<br/>' => '<br />',
        '<p>' => '<br />',
        '</p>' => '',
        '<p></p>' => '',
        '<ul>' => '',
        '</ul>' => '',
    ];


    private $headerTags = [
        ['<b><u>', '</u></b>'],
        ['<u><b>', '</b></u>'],
        ['<font color="gray"><b>', '</b></font>']
    ];


    // Media
    // Images
    public $albumNames = [];
    public $albumTriggers = [];


    // Videos
    // TODO: Remove after confirming change
//    public $baseUrlPrefixYouTube = 'www.youtube.com/embed/';
////    public $baseUrlPrefixYouTube = 'https://www.youtube.com/embed/';
//    public $baseUrlPrefixVimeo = 'player.vimeo.com/video/';
////    public $baseUrlPrefixVimeo = 'http://player.vimeo.com/video/';
//    public $videoTriggerYouTube = '<div class="sites-embed-content sites-embed-type-youtube">'; // <div class="youtube">
//    public $videoTriggerVimeo = '<div class="sites-embed-content sites-embed-type-ggs-gadget">';
//    public $videoEndTag = '</iframe>';

    /**
     * @var string Trigger for the beginning of a video. This is often a tag with a particular class attribute.
     */
    public $videoTrigger = 'iframe';

    /**
     * @var array The URL common to all videos on a particular video site.
     */
    public $baseVideoUrls = [
        'www.youtube.com/embed/',
        'http://player.vimeo.com/video/',
        'https://player.vimeo.com/video/'
    ];

    // Old
    // Hits trigger, then skips to baseUrlPrefix + baseUrl, (using $baseUrlPrefixYouTube, etc. for baseUrl)
    // $baseURLPrefix . $baseURL where for MarkPThomas was 'src=' . $baseURL for YouTube, but not Vimeo.
    // TODO: Fix for SuperTopo
    // http://www.supertopo.com/tr/The-Iota-Chimney-A-Super-Awesome-Must-Do-5-4-in-Yosemite/t11362n.html
    // <div class="youtube"><iframe  width="560" height="340" src="http://www.youtube.com/embed/wH_HWzy3ENs" frameborder="0"></iframe></div>
    // TODO: Fix for SummitPost
    // https://www.summitpost.org/tour-de-palisade-norman-clyde-peak-and-palisade-crest/854159
    // <iframe width="560" height="315" src="//www.youtube.com/embed/uGSdv97TntU" frameborder="0" allowfullscreen=""></iframe>

    // New
    // Videos in general all seem to use <iframe  ... src=
    // Consider making this more versatile by doing:
    // 1. Check for <iframe (check correct behavior w/ tagsIgnore above). Perhaps make $videoTag = 'iframe' which has opening & closing handled similar to tagsIgnore.
    // 2. Jump to $baseUrlPrefix (of any: try 1-YouTube, 2-Vimeo, 3-'src="' w/o aby prefix). Consider renaming to $baseUrls = [];
    // 3. Reassemble URL w/ base URL if it is used, or raw URL if not.
    // 4. Jump to end if iframe.

// Vimeo
// MarkPThomas
//<div class="sites-embed-content sites-embed-type-ggs-gadget">
//<iframe title="Include gadget (iframe)" width="800" height="450" scrolling="no" frameborder="0" id="437014596" name="437014596" allowtransparency="true" class="igm"
//src="//mj89sp3sau2k7lj1eg3k40hkeppguj6j-a-sites-opensocial.googleusercontent.com/gadgets/ifr?url=http://www.gstatic.com/sites-gadgets/iframe/iframe.xml&amp;container=enterprise&amp;view=default&amp;lang=en&amp;country=ALL&amp;sanitize=0&amp;v=24b446ab5dea8e75&amp;libs=core&amp;mid=199&amp;parent=http://www.markpthomas.com/mountaineering/trip-reports/california/2011-03-13-carlheller#up_scroll=auto&amp;
//
//  up_iframeURL=http://player.vimeo.com/video/21404582?title%3D0%26byline%3D0%26portrait%3D0%26color%3Dff9933&amp;st=e%3DAIHE3cAt%252FUY0eML62mleRU61KmLariq8ab5yH69Pm5yPEM74QEYmUhKjYtGbftaqtoxhe%252B6do68Ph%252FMGngdtabdh9RvdmsZ%252BQWiKMp9bx3LLUnYmDrD6SufY7S%252BRhRfPzTQ35k%252BgIubK%26c%3Denterprise&amp;rpctoken=-1881822248236537366">
//</iframe>
//
// ST
//  Does not support Vimeo or generic. Only supports YouTube.
//
// SP (Carl Heller)
// <iframe src="http://player.vimeo.com/video/21404582?title=0&amp;byline=0&amp;portrait=0&amp;color=ff9933" width="400" height="225" frameborder="0"></iframe>


// YouTube
// MarkPThomas
//<div class="sites-embed-content sites-embed-type-youtube">
//<iframe title="YouTube video player" class="youtube-player" type="text/html"
//  src="https://www.youtube.com/embed/Vpg1RMOatkk?rel=0&amp;wmode=opaque"
//      frameborder="0" allowfullscreen="true" width="425" height="355">
//</iframe>
//
// ST
//<div class="youtube">
//<iframe  width="560" height="340"
//  src="http://www.youtube.com/embed/wH_HWzy3ENs"
//      frameborder="0">
//</iframe>
//
// SP
//<iframe width="560" height="315"
//  src="//www.youtube.com/embed/uGSdv97TntU"
//      frameborder="0" allowfullscreen="">
//</iframe>


    private  $captionTags = [
        ['<b><i>', '</i></b>'],
        ['<i><b>', '</b></i>'],
        ['<span class="caption">', '</span>'],
        ['<center>', '</center>']
    ];

    // Links
    public $linkTriggers = [];
    public $internalSiteUrlStub = '';


    // Filled Properties
    public $headerTypes = [];
    public $headerValues = [];

    public $images = [];
    public $imageFileNames = [];
    public $imageCaptions = [];

    public $videos = [];
    public $videoCaptions = [];

    public $photoAlbums = [];
    public $links = [];
    public $linksInternal = [];

    public $textBodies = [];


    function __construct($content, $skipCharacters = 0, $url = '')
    {
        $this->url = $url;
        $this->content = $content;
        $this->skipCharacters = $skipCharacters;
    }

    public function parseContent(){
        // report_trip_id
        // report_trip_summary_id
        // report_trip_introduction_id
        // article_id
        //
        // Stats included. Deal with these manually later.
        //
        // Sequence id
        //
        // Header Format: none (id 1), h1 (id 2), h2 (id 3), h3, h4.
        // Header Value: Pure text within <h1></h1>, etc. if within <hn> tags
        //
        // Text Body:
        // InnerHTML: Nonblank text
        // Ignore <p> & <br> tags, but maintain \n between photo sections.
        //
        // id (calculated by all reports, per report)
        // photo_id (overall, from photo DB)
        // Photo URL: <img src=" (ignore this)
        // Photo URL Link: preceding <a href=" (use this)
        //
        // Photo caption: Proceeding within <b><i></b></i>, ignore '-'
        // can later match URL to non-Picasa source by file name, e.g. '2011-02-12%2520-%252009%2520-%2520Peter%2520Croft%2520Ridge.jpg' = '2011-02-12 - 09 - Peter Croft Ridge.jpg'
        // as %2520 = space
        //
        // report_video_id (calculated by all reports, per report)
        // video_id (overall, from video DB)
        // Video URL: <iframe ... src="//www.youtube.com/embed/Vpg1RMOatkk where Vpg1RMOatkk is the video ID for https://www.youtube.com/watch?v=Vpg1RMOatkk
        // Video caption: Proceeding within <b><i></b></i>, ignore '-'

//Header:
//MPT: <h1>...</h2>
//ST: <b><u>...</u></b>
//SP: <font color="gray"><b>...</b></font>
//    <h2 id="first_h2">...<h2>
//    <h3>...</h3>
//
//
//Image:
//MPT:
//ST:
//SP:   src='/images/medium/42666.jpg'
//
//
//Video:
//MPT:
//ST:
//SP:
//
//
//Caption:
//MPT:
//ST: <b><i>...</i></b>
//SP: <span class="caption">...</span>



        // Modify content for parsing
        $this->content = Lib\StringHelper::decode_entities_full($this->content);
        foreach($this->tagsReplace as $search => $replace){
            $this->content = str_replace($search, $replace, $this->content);
        }
        $this->contentLength = strlen($this->content);

        // Determine initial values to support parsing
        $skipTo = -1;

        // Check character by character
        for ($i = $this->skipCharacters; $i < $this->contentLength; $i++){
            if ($i < $skipTo) continue;

            // All '<a name' tags are being ignored as they are not compatible with HTML5.
            $skipTo = $this->skipBracketedSection($i, $this->content, '<a name="', '</a>');
            if ($skipTo > $i) continue;
            $skipTo = $this->skipBracketedSection($i, $this->content, '<a href="#', '</a>');
            if ($skipTo > $i) continue;

            // Check for header
            // Checks for standard <hn></hn> header first
            $skipTo = $this->getHeaderContent($i);
            if ($skipTo > $i) continue;
            // If not found, also checks for variations on header
            foreach($this->headerTags as $headerTagSet){
                $skipTo = $this->getHeader($i, $headerTagSet[0], $headerTagSet[1]);
                if ($skipTo > $i) break;
            }
            if ($skipTo > $i) continue;

            // Check img src
            // TODO: Make the first two methods & supporting methods inherent to the calling class, which then defaults to 'getImage' after.
            $skipTo = $this->getSuperTopoImage($i);
            if ($skipTo > $i) continue;
            $skipTo = $this->getSummitPostImage($i);
            if ($skipTo > $i) continue;
            $skipTo = $this->getImage($i);
            if ($skipTo > $i) continue;

            // Check video
            // TODO: Remove after testing
//            $skipTo = $this->getYouTubeSource($i, $this->videoTriggerYouTube);
//            if ($skipTo > $i) continue;
//            $skipTo = $this->getVimeoSource($i, $this->videoTriggerVimeo);
//            if ($skipTo > $i) continue;
            $skipTo = $this->getVideoSrc($i);
            if ($skipTo > $i) continue;

            // Check caption
            foreach($this->captionTags as $captionTagSet){
                $skipTo = $this->getCaption($i, $captionTagSet[0], $captionTagSet[1]);
                if ($skipTo > $i) break;
            }
            if ($skipTo > $i) continue;

            // Remove/ignore tags:
            $skipTo = $this->ignoreTags($i, $this->tagsIgnore, $this->content);
            if ($skipTo > $i) continue;

            // Photo Album Links:
            $skipTo = $this->getPhotoAlbumLink($i, $this->albumTriggers);
            if ($skipTo > $i) continue;

            // Alternative site Links:
            $skipTo = $this->getSiteLink($i, $this->linkTriggers);
            if ($skipTo > $i) continue;

            $this->getInternalSiteLink($i);

//            $skipTo = $this->skipBracketedSection($i, $this->content, '<a name="', '</a>');
//            if ($skipTo > $i) continue;

            // Append to current content.
            $this->currentContent .= $this->content[$i];
        }

        // Record final content & balance all arrays
        $this->updateTextBodies();
        $this->syncAllArraySizes();
    }


    /**
     * Gets the type and content of the headers and returns the index for the first character after the header if successful.
     * @param int $i Character index to begin check at.
     * @return int Index for the first character after the header if successful, otherwise returns the current index.
     */
    private function getHeaderContent($i){
        $contentLength = $this->contentLength;
        if ($this->content[$i] === '<' &&
            $i + 1 < $contentLength && $this->content[$i + 1] === 'h' &&
            $i + 2 < $contentLength && ctype_digit ($this->content[$i + 2])){

            $this->updateTextBodies();

            // Get header type
            $headerType = $this->content[$i + 1] . $this->content[$i + 2];

            // Skip to end of opening tag:
            $startPosition = $i + 3;
            while($this->content[$startPosition] != '>'){
                $startPosition++;
            }
            // Increment once more to start after the closing bracket.
            $startPosition++;

            // Get content:
            $j = $startPosition;
            $tagBalance = 1;
            $isInTag = false;
            $headerContent = '';
            $stringToCheck = '/';
            $stringToCheckLength = strlen($stringToCheck);
            $skipTo = -1;

            // Limit possible skipping to be within current header tag
            $skipToMax = strpos($this->content, '</' . $headerType . '>', $j) - 4;
            $skipTag = '<a name="';
            do {
                // Skip single undesirable anchor tag by checking for it once
                $skipTo = ($skipTo === -1)? $skipTo = strpos($this->content, $skipTag, $j) + strlen($skipTag): false;
                if ($j < $skipTo && $skipTo < $skipToMax) {
                    for ($k = $j; $k < $skipTo; $k++){
                        $j++;
                    }
                    $j = Lib\StringHelper::offsetByNextString($j, '</a>', $this->content);
                }
                $currentMaxJ = $j + $stringToCheckLength;

                if ($this->content[$j] === '<' &&
                    $currentMaxJ < $contentLength &&
                    $this->content[$currentMaxJ] !== $stringToCheck &&
                    !$this->tagIsSelfClosing($j, $this->content)){
                    // Entering opening tag
                    $tagBalance++;
                    $isInTag = true;
                } elseif ($this->content[$j] === '<' &&
                    $currentMaxJ < $contentLength &&
                    $this->content[$currentMaxJ] === $stringToCheck){
                    // Entering closing tag
                    $tagBalance--;
                    $isInTag = true;
                } elseif ($this->content[$j] === '>'){
                    // Leaving tag
                    $isInTag = false;
                }

                // Records all text that is not in the closing header tag
                if (!($tagBalance === 0 && $isInTag)){
                    $headerContent .= $this->content[$j];
                }
                $j++;
            } while (!($tagBalance === 0 && !$isInTag) && $j < $contentLength);

            if (!empty($headerContent)){
                $this->updateFilteredHeaders($headerType, $headerContent, $startPosition);
            }
            return $j;
        }
        return $i;
    }


    /**
     * Gets the content of the headers based on a set of tags that indicate a header and returns the index for the first character after the header tags if successful.
     * @param int $i Character index to begin check at.
     * @param string $tagsOpen Set of opening tags that indicate the beginning of a header.
     * @param string $tagsClose Set of closing tags that indicate the ending of a header.
     * @param string $defaultHeader Default header level to use for matching tags.
     * @return int Index for the first character after the header if successful, otherwise returns the current index.
     */
    private function getHeader($i, $tagsOpen, $tagsClose, $defaultHeader = 'H2'){
        if (count($this->headerTypes) == $this->sequence + 1 &&
            $this->headerTypes[$this->sequence]) return $i; // Header was already located. Characters are to be recorded as body text.

        // Get header content:
        if (Lib\StringHelper::isMatching($i, $tagsOpen, $this->content)){
            $this->updateTextBodies();
            $headerContent = '';
            $startPosition = $i + strlen($tagsOpen);
            $j = $startPosition;
            $tagsCloseLength = strlen($tagsClose);
            do{
                $headerContent .= $this->content[$j];
                $j++;
                if ($j + $tagsCloseLength >= $this->contentLength)
                    return $j - 1;
            } while(!Lib\StringHelper::isMatching($j, $tagsClose, $this->content));
            $j += $tagsCloseLength;

            if (!empty($headerContent)){
                $this->updateFilteredHeaders($defaultHeader, $headerContent, $startPosition);
            }
            return $j;
        }
        return $i;
    }



    /**
     * Filter text of unwanted tags and update header content.
     * @param string $headerType The header type, eg. H1.
     * @param string $headerContent The content within the header tags.
     * @param int $startPosition Position of first character within the header tags.
     */
    private function updateFilteredHeaders($headerType, $headerContent, $startPosition){
        // Filter text of unwanted tags
        $filteredHeaderContent = '';
        for ($k = $startPosition; $k < $startPosition + strlen($headerContent) - 1; $k++){
            $skipTo = $this->ignoreTags($k, $this->tagsIgnore, $this->content);
            if ($skipTo > $k) continue;
            $filteredHeaderContent .= $headerContent[$k - $startPosition];
            $filteredHeaderContent = str_replace('<b>', '', $filteredHeaderContent);
            $filteredHeaderContent = str_replace('</b>', '', $filteredHeaderContent);
        }

        if (!empty($filteredHeaderContent)){
            $this->updateHeaders($headerType, $filteredHeaderContent);
        }
    }

    /**
     * Sets the image rom a SuperTopo image div and returns the index offset to resume beyond the image tags (also any bounding tags).
     * Also gets captions since these are included within the div.
     * Photos that are stored on SuperTopo, rather than merely referenced from there, are what this handles.
     * @param int $i Character index to begin check at.
     * @return int Index offset to resume beyond the image tags (also any bounding tags).
     */
    private function getSuperTopoImage($i){
        // Check for special photo div set. Abort if not found.
        $j = $this->offsetByTag($i, '<div class="photonormal"', $this->content);
        if ($j === $i) return $i;

        // Jump to section containing the desired image to avoid picking up auxiliary images
        $j = Lib\StringHelper::offsetByNextString($j, '<td class="sdw_body"', $this->content);
        if ($j >=  $this->contentLength) return $i;
        $j = Lib\StringHelper::offsetByNextString($j, '<img ', $this->content);
        if ($j >=  $this->contentLength) return $i;

        $this->updateTextBodies();

        // Get src
        $this->recentImage = true;
        $imgSrc = '';
        $j = Lib\StringHelper::offsetByNextString($j, 'src="', $this->content);
        while ($j <= $this->contentLength &&
            $this->content[$j] !== '"'){
            $imgSrc .= $this->content[$j];
            $j++;
        }
        $img_file_name = Lib\StringHelper::getFileNameFromUrl($imgSrc);
        $this->updateImages($imgSrc, $img_file_name);

        // Skip to end of tag
        while ($j <= $this->contentLength &&
            $this->content[$j] !== '>'){
            $j++;
        }
        $j++;

        // Skip beyond </a> tag if exists
        $closeTag = '</a>';
        if ($this->tagReached($j, $closeTag, $this->content)){
            $j = $this->indexSkipToIgnoreTag($j, $closeTag);
        }

        // Get SuperTopo caption
        return $this->getSuperTopoCaption($j);
    }

    /**
     * Sets the caption from a SuperTopo image div and returns the index offset to resume beyond the image tags (also any bounding tags)..
     * @param int $i Character index to begin check at.
     * @return int  Index offset to resume beyond the caption tags (also any bounding tags).
     */
    private function getSuperTopoCaption($i){
        $j = Lib\StringHelper::offsetByNextString($i, '<span class="small">', $this->content);
        if ($j >=  $this->contentLength) return $i;

        $creditTag = '<div class="photo-credit">';
        $caption = '';
        while ($j <= $this->contentLength &&
            !$this->tagReached($j, $creditTag, $this->content)){
            $caption .= $this->content[$j];
            $j++;
        }

        // Get photo credit if it exists.
        if ($this->tagReached($j, $creditTag, $this->content)){
            $j += strlen($creditTag);

            $endOfCreditTag = '</div>';
            $endOfCredit = Lib\StringHelper::offsetByNextString($j, $endOfCreditTag, $this->content) - strlen($endOfCreditTag);
            if ($endOfCredit >=  $this->contentLength) return $j;

            $caption .= ' (';
            while ($j <= $this->contentLength &&
                $j < $endOfCredit){
                $caption .= $this->content[$j];
                $j++;
            }
            $caption .= ')';
        }
        $caption = str_replace('<br>', '', $caption);
        $caption = str_replace('<br/>', '', $caption);

        if ($this->recentImage) {
            $this->updateImageCaptions($caption);
        }

        // Jump to end of div item to avoid picking up auxiliary images
        $k = Lib\StringHelper::offsetByNextString($j, '</table>', $this->content);
        return ($k >=  $this->contentLength)? $j : $k;
    }

    /**
     * Sets the image rom a getSummitPostImage image div and returns the index offset to resume beyond the image tags (also any bounding tags).
     * Also gets captions since these are included within the div.
     * @param int $i Character index to begin check at.
     * @return int Index offset to resume beyond the image tags (also any bounding tags).
     */
    private function getSummitPostImage($i){
        // Check for special photo div set. Abort if not found.
        $j = $this->offsetByTag($i, '<div align="center" class="image"', $this->content);
        if ($j === $i) return $i;

        // Jump to section containing the desired image
        $j = Lib\StringHelper::offsetByNextString($j, '<img ', $this->content);
        if ($j >=  $this->contentLength) return $i;

        $this->updateTextBodies();

        // Get src
        $this->recentImage = true;
        $imgSrc = '';
        $j = Lib\StringHelper::offsetByNextString($j, 'src="', $this->content);
        while ($j <= $this->contentLength &&
            $this->content[$j] !== '"'){
            $imgSrc .= $this->content[$j];
            $j++;
        }
        $img_file_name = Lib\StringHelper::getFileNameFromUrl($imgSrc);
        $this->updateImages($imgSrc, $img_file_name);

        // Skip to end of tag
        while ($j <= $this->contentLength &&
            $this->content[$j] !== '>'){
            $j++;
        }
        $j++;

        // Skip beyond </a> tag if exists
        $closeTag = '</a>';
        if ($this->tagReached($j, $closeTag, $this->content)){
            $j = $this->indexSkipToIgnoreTag($j, $closeTag);
        }

        // Get SuperTopo caption
        return $this->getSummitPostCaption($j);
    }

    /**
     * Sets the caption from a SummitPost image div and returns the index offset to resume beyond the image tags (also any bounding tags)..
     * @param int $i Character index to begin check at.
     * @return int  Index offset to resume beyond the caption tags (also any bounding tags).
     */
    private function getSummitPostCaption($i){
        $j = Lib\StringHelper::offsetByNextString($i, '<span class="caption">', $this->content);
        if ($j >=  $this->contentLength) return $i;

        $caption = '';
        while ($j <= $this->contentLength &&
            !$this->tagReached($j, '</span>', $this->content)){
            $caption .= $this->content[$j];
            $j++;
        }

        $caption = str_replace('<br>', '', $caption);
        $caption = str_replace('<br/>', '', $caption);

        if ($this->recentImage) {
            $this->updateImageCaptions($caption);
        }

        // Jump to end of div item to avoid picking up auxiliary images
        $k = Lib\StringHelper::offsetByNextString($j, '</div>', $this->content);
        return ($k >=  $this->contentLength)? $j : $k;
    }


    /**
     * Sets the image URL source and returns the index offset to resume beyond the image tags (also any bounding anchor tags).
     * @param int $i Character index to begin check at.
     * @return int Index offset to resume beyond the image tags (also any bounding anchor tags).
     */
    private function getImage($i){
        // Filter out any preceding <a href
        $j = $this->offsetByTag($i, '<a href="', $this->content);

        // Check <img tag
        if (Lib\StringHelper::isMatching($j, '<img ', $this->content)){
            $this->updateTextBodies();

            // Get src
            $this->recentImage = true;
            $imgSrc = '';
            $j = Lib\StringHelper::offsetByNextString($j, 'src="', $this->content);
            while ($j <= $this->contentLength &&
                $this->content[$j] !== '"'){
                $imgSrc .= $this->content[$j];
                $j++;
            }
            $img_file_name = Lib\StringHelper::getFileNameFromUrl($imgSrc);
            $this->updateImages($imgSrc, $img_file_name);

            // Skip to end of tag
            while ($j <= $this->contentLength &&
                $this->content[$j] !== '>'){
                $j++;
            }
            $j++;

            // Skip beyond </a> tag if exists
            $closeTag = '</a>';
            if ($this->tagReached($j, $closeTag, $this->content)){
                return $this->indexSkipToIgnoreTag($j, $closeTag);
            }

            return $j;
        }
        return $i;
    }

    /**
     * Sets the video URL source and returns the index offset to resume beyond the video tags.
     * @param int $i Character index to begin check at.
     * @return int Index offset to resume beyond the video tags.
     */
    private function getVideoSrc($i){
        if (Lib\StringHelper::isMatching($i, '<' . $this->videoTrigger . ' ', $this->content)){
            // Get the end of the video tags
            $jEnd = Lib\StringHelper::offsetByNextString($i, '</' . $this->videoTrigger . '>', $this->content);

            $this->updateTextBodies();

            // Get src
            $this->recentVideo = true;

            $baseUrl = '';
            $j = $i;
            for ($k = 0; $k < count($this->baseVideoUrls); $k++){
                $baseUrl = $this->baseVideoUrls[$k];
                $j = Lib\StringHelper::offsetByNextString($i, $baseUrl, $this->content);
                if ($j < $jEnd){    // Match found
                    break;
                }

                // Reset values
                $baseUrl = '';
                $j = $i;
            }
            if ($j === $i){ // No base URLs were valid, so do check without
                $j = Lib\StringHelper::offsetByNextString($i, 'src="', $this->content);
                if ($j >= $jEnd){ // Error. Abort.
                    return $i;
                }
            }

            // Get URL
            $src = '';
            do{
                $src .= $this->content[$j];
                $j++;
                if ($j >= $this->contentLength)
                    return $j - 1;
            } while($this->content[$j] !== '?' &&
                $this->content[$j] !== '"');

            $this->updateVideos($baseUrl . $src);
            return $jEnd;
        }
        return $i;
    }

    // TODO: Remove next 3 methods after testing above method
//    /**
//     * Sets the video URL source for a YouTube video and returns the index offset to resume beyond the video tags.
//     * @param int $i Character index to begin check at.
//     * @param string $videoTrigger Trigger for the beginning of a YouTube video. This is often a tag with a particular class attribute.
//     * @return int Index offset to resume beyond the video tags.
//     */
//    private function getYouTubeSource($i, $videoTrigger){
//        return $this->getVideoWithCodeSource($i, $videoTrigger, $this->baseUrlPrefixYouTube, 'src="');
//    }
//
//    /**
//     * Sets the video URL source for a Vimeo video and returns the index offset to resume beyond the video tags.
//     * @param int $i Character index to begin check at.
//     * @param string $videoTrigger Trigger for the beginning of a Vimeo video. This is often a tag with a particular class attribute.
//     * @return int Index offset to resume beyond the video tags.
//     */
//    private function getVimeoSource($i, $videoTrigger){
//        return $this->getVideoWithCodeSource($i, $videoTrigger, $this->baseUrlPrefixVimeo);
//    }
//
//
//    /**
//     * Sets the video URL source and returns the index offset to resume beyond the video tags
//     * @param int $i Character index to begin check at.
//     * @param array $baseUrls The URL common to all videos on a particular video site.
//     * @return int Index offset to resume beyond the video tags.
//     */
//    private function getVideoWithCodeSource($i, array $baseUrls){//, $baseURLPrefix = ''){ * @param string $baseURLPrefix Trigger for the beginning of the video URL.
//        if (Lib\StringHelper::isMatching($i, '<' . $this->videoTrigger . ' ', $this->content)){
//            // Get the end of the video tags
//            $jEnd = Lib\StringHelper::offsetByNextString($i, '</' . $this->videoTrigger . '>', $this->content);
//
//            $this->updateTextBodies();
//
//            // Get src
//            $this->recentVideo = true;
//
//            $baseUrl = '';
//            $j = $i;
//            for ($k = 0; $k < count($baseUrls); $k++){
//                $baseUrl = $baseUrls[$k];
//                $j = Lib\StringHelper::offsetByNextString($i, $baseUrl, $this->content);
//                if ($j < $jEnd){    // Match found
//                    break;
//                }
//
//                // Reset values
//                $baseUrl = '';
//                $j = $i;
//            }
//            if ($j === $i){ // No base URLs were valid, so do check without
//                $j = Lib\StringHelper::offsetByNextString($i, 'src="', $this->content);
//                if ($j >= $jEnd){ // Error. Abort.
//                    return $i;
//                }
//            }
//
//            // Get URL
//            $src = '';
//            do{
//                $src .= $this->content[$j];
//                $j++;
//                if ($j >= $this->contentLength)
//                    return $j - 1;
//            } while($this->content[$j] !== '?' &&
//                $this->content[$j] !== '"');
//
//            $this->updateVideos($baseUrl . $src);
//            return $jEnd;
//        }
//        return $i;
//
////        if (Lib\StringHelper::isMatching($i, $videoTrigger, $this->content)){
////            $this->updateTextBodies();
////
////            // Get src:
////            $this->recentVideo = true;
////            $src = '';
////            $j = $i + strlen($videoTrigger);
////            $j = Lib\StringHelper::offsetByNextString($j, $baseURLPrefix . $baseURL, $this->content);
////            do{
////                $src .= $this->content[$j];
////                $j++;
////                if ($j >= $this->contentLength)
////                    return $j - 1;
////            } while($this->content[$j] !== '?' &&
////                $this->content[$j] !== '"');
////            $this->updateVideos($baseURL . $src);
////
////            // Skip to the end of the video tags
////            $j = Lib\StringHelper::offsetByNextString($j, $this->videoEndTag, $this->content);
////            return $j; // + 1;
////        }
////        return $i;
//    }

    /**
     * Sets the caption for the current image or video.
     * @param int $i Character index to begin check at.
     * @param string $tagsOpen Set of opening tags that indicate the beginning of a photo caption.
     * @param string $tagsClose Set of closing tags that indicate the ending of a photo caption.
     * @return int Index offset to resume beyond the caption and closing tags.
     */
    private function getCaption($i, $tagsOpen, $tagsClose){
        if (!$this->recentImage && !$this->recentVideo) return $i; // Characters are to be recorded as body text.

        // Get caption:
        if (Lib\StringHelper::isMatching($i, $tagsOpen, $this->content)){
            $this->updateTextBodies();
            $caption = '';
            $j = $i + strlen($tagsOpen);
            $tagsCloseLength = strlen($tagsClose);
            do{
                $caption .= $this->content[$j];
                $j++;
                if ($j + $tagsCloseLength >= $this->contentLength)
                    return $j - 1;
            } while(!Lib\StringHelper::isMatching($j, $tagsClose, $this->content));
            $j += $tagsCloseLength;

            if ($caption === '-'){
                $this->resetImageAndVideoCaptions();
            }
            elseif ($this->recentImage) {
                $this->updateImageCaptions($caption);
            }
            elseif ($this->recentVideo) {
                $this->updateVideoCaptions($caption);
            }
            return $j;
        }
        return $i;
    }

    public static function deriveNameFromUrl($hrefAttributeCore){
        if (!$hrefAttributeCore) return null;

        $hrefValues = explode('/', $hrefAttributeCore);
        $hrefValue = end($hrefValues);

        $name = '';
        $date = '';
        for ($i = 0; $i < strlen($hrefValue); $i++){
            // If not numeric, not recording name, etc.
            if (empty($name)
                && (preg_match('/[^a-zA-Z]/', $hrefValue[$i]) ||
                    strtolower(substr($hrefValue, $i, 2)) === 'to'||
                    $i - 1 > 0 && strtolower(substr($hrefValue, $i - 1, 2)) === 'to')){
                $date .= $hrefValue[$i];
                continue;
            }
            $name .= $hrefValue[$i];
        }

        $dateWithDashes = '';
        for ($i = 0; $i < strlen($date); $i++){
            $dateWithDashes .= $date[$i];

            // Add dashes for yyyy-mm-dd{to{dd}}
            if ($i === 3 ||
                $i === 5){
                $dateWithDashes .= '-';
            }
        }

        // Add spaces betweenCamelCaseLetters including w/ as W
        $name = preg_replace('/([a-z])([A-Z])/s','$1 $2', $name);
        $name = preg_replace('/([A-Z])([W])([A-Z])/s','$1 $2 $3', $name);

        return $dateWithDashes . ' - ' . $name;
    }

    /**
     * Sets the URL to a photo album.
     * @param int $i Character index to begin check at.
     * @param array $albumTriggers
     * @return int Index offset to resume beyond the photo album link and closing tags.
     */
    private function getPhotoAlbumLink($i, array $albumTriggers){
        // <li><a href="https://picasaweb.google.co
        // photo_album_id
        // url
        // Title from within URL: https://picasaweb.google.com/105894936663116565516/20110211to13MtHumphreysEArete02?feat=directlink = 2011-02-11to13MtHumphreysEArete
        // https://picasaweb.google.com/105894936663116565516/20110211to13MtHumphreysEArete
        // Taken from last segment after / before ?
        // Summary: Use 'Summary' for now for easier find & replace later.

        // Get href attribute value
        $hrefAttribute = $this->getHrefAttribute($i);
        if ($hrefAttribute === '')
            return $i;

        $hrefAttributeCore = explode('?', $hrefAttribute)[0];
        $j = 0;
        while (ctype_digit($hrefAttributeCore[strlen($hrefAttributeCore) - 1 - $j])){
            $j++;
        }
        if ($j > 0){
            $hrefAttributeCore = substr($hrefAttributeCore, 0, -$j);
        }

        // Get href element value
        $hrefValue = $this->getHrefValue($i);

        foreach($albumTriggers as $albumTrigger){
            if (strpos($hrefValue, $albumTrigger) !== false){

                // Derive the name from the URL if the value is a generic name (they must be unique in the database)
                if(in_array($hrefValue, $this->albumNames))
                {
                    $hrefValue = self::deriveNameFromUrl($hrefAttributeCore);
                }

                $this->updateTextBodies();
                $this->updatePhotoAlbums($hrefAttributeCore, $hrefValue);
                return $this->getMaxHrefIndex($i);
            }
        }
        return $i;
    }

    /**
     * Sets the URL to a link off site.
     * @param int $i Character index to begin check at.
     * @param array $linkTriggers
     * @return int Index offset to resume beyond the site link and closing tags.
     */
    private function getSiteLink($i, array $linkTriggers){
        // <a name="TOC-Links"></a>Links</h2>
        // <li><a href="http://www.summitpost.org/ (SummitPost)
        // <li><a href="http://www.supertopo.com/tr/ (SuperTopo)
        // <li><a href="http://www.snwburd.com/ (Bob Burd site)
        // For other links (e.g. multi-part reports, e.g. Trip_Report_71.txt), keep them as text body to remove later, and see about manually working these for multi-part reports.
        // Reference_id
        // Name: TR Title (site)
        // Description: TR on (site)
        // Website_URL

        // Get href attribute value
        $hrefAttribute = $this->getHrefAttribute($i);
        if ($hrefAttribute === '')
            return $i;

        // Get href element value
        $hrefValue = $this->getHrefValue($i);

        // If href value contains any of the following, record data:
        foreach($linkTriggers as $linkTrigger){
            if (strpos($hrefValue, $linkTrigger) !== false){
                $this->updateTextBodies();
                $this->updateLinks($hrefAttribute, $hrefValue);
                return $this->getMaxHrefIndex($i);
            }
        }
        return $i;
    }

    /**
     * Sets the URL to a link on site.
     * @param int $i Character index to begin check at.
     * @return int Index offset to resume beyond the site link and closing tags.
     */
    private function getInternalSiteLink($i){

        // Get href attribute value
        $hrefAttribute = $this->getHrefAttribute($i);
        if ($hrefAttribute === '')
            return $i;

        if (strpos($hrefAttribute, $this->internalSiteUrlStub) !== false){
            // Get href element value
            $hrefValue = $this->getHrefValue($i);
            $this->updateLinksInternal($hrefAttribute, $hrefValue);
            return $this->getMaxHrefIndex($i);
        }
        return $i;
    }


    // Update Values ============================================================

    /**
     * Adds header types and content to their respective arrays and writes the results to the console.
     * @param $headerType
     * @param $headerContent
     */
    private function updateHeaders($headerType, $headerContent){
        array_push($this->headerTypes, '');
        $this->updateArray($this->headerTypes, $headerType);
        Lib\MyLogger::log('<b>Header Type: </b>' . $headerType . '<br />');

        $headerContent = trim($headerContent);
        $headerContent = utf8_encode($headerContent);
        $this->updateArray($this->headerValues, $headerContent);
        Lib\MyLogger::log('<b>Header: </b>' . $headerContent . '<br />');
    }

    /**
     * Adds image URL to the array property and writes the result to the console.
     * @param $imgSrc
     * @param $img_file_name
     */
    private function updateImages($imgSrc, $img_file_name){
        $this->updateArray($this->images, $imgSrc);
        $this->updateArray($this->imageFileNames, $img_file_name);
        Lib\MyLogger::log('<b>Image: </b>' . $imgSrc . '<br />');
        Lib\MyLogger::log('<b>Filename: </b>' . $img_file_name . '<br />');
    }

    /**
     * Adds image caption to the array property and writes the result to the console.
     * @param $caption
     */
    private function updateImageCaptions($caption){
        $caption = trim($caption);
        $caption = utf8_encode($caption);
        $this->updateArray($this->imageCaptions, $caption);
        Lib\MyLogger::log('<b>Image Caption: </b>' . $caption . '<br />');
        $this->resetImageAndVideoCaptions();
    }

    /**
     * Adds video URL to the array property and writes the result to the console.
     * @param $videoUrl
     */
    private function updateVideos($videoUrl){
        $this->updateArray($this->videos, $videoUrl);
        Lib\MyLogger::log('<b>Video: </b>' . $videoUrl . '<br />');
    }

    /**
     * Adds video caption to the array property and writes the result to the console.
     * @param $caption
     */
    private function updateVideoCaptions($caption){
        $caption = trim($caption);
        $caption = utf8_encode($caption);
        $this->updateArray($this->videoCaptions, $caption);
        Lib\MyLogger::log('<b>Video Caption: </b>' . $caption . '<br >');
        $this->resetImageAndVideoCaptions();
    }

    /**
     * Resets the recent image & recent video boolean flags.
     */
    private function resetImageAndVideoCaptions(){
        $this->recentImage = false;
        $this->recentVideo = false;
    }

    /**
     * Adds the current text content to the array of text content and resets the variable for further recording.
     */
    private function updateTextBodies(){
        // If text is only line breaks, ignore it.
        $tempCurrentContent = Lib\StringHelper::trimTextBodies($this->currentContent);
        if ($tempCurrentContent !== ''){
            $tempCurrentContent = trim($tempCurrentContent);
            $tempCurrentContent = utf8_encode($tempCurrentContent);
            $this->updateArray($this->textBodies, $tempCurrentContent);
            Lib\MyLogger::log('<b>Text: </b>' . $tempCurrentContent . '<br />');
        }
        $this->currentContent = '';
    }

    /**
     * Adds photo album URL and respective title to the array property and writes the result to the console.
     * @param $url
     * @param $title
     */
    private function updatePhotoAlbums($url, $title){
        $title = trim($title);
        $title = utf8_encode($title);
        Lib\MyLogger::log('<b>Album: </b>' . $title . ': ' . $url . '<br />');
        array_push($this->photoAlbums, [$url, $title]);
    }

    /**
     * Adds link and respective title to the array property and writes the result to the console.
     * @param $url
     * @param $title
     */
    private function updateLinks($url, $title){
        $title = trim($title);
        $title = utf8_encode($title);
        Lib\MyLogger::log('<b>Link: </b>' . $title . ': ' . $url . '<br />');
        array_push($this->links, [$url, $title]);
    }

    /**
     * Adds link and respective title of pages within the website to the array property and writes the result to the console.
     * @param $url
     * @param $title
     */
    private function updateLinksInternal($url, $title){
        $title = trim($title);
        $title = utf8_encode($title);
        Lib\MyLogger::log('<b>Link (Internal): </b>' . $title . ': ' . $url . '<br />');
        array_push($this->linksInternal, [$url, $title]);
    }


    // Arrays ============================================================
    /**
     * Syncs all other arrays to be at least within one count of number size of the largest array.
     * @param bool $totalBalance True: Arrays will all be sized to the exact same size as the largest array.
     */
    private function syncAllArraySizes($totalBalance = false){
        $this->syncArraySize($this->headerTypes, $totalBalance);
        $this->syncArraySize($this->headerValues, $totalBalance);
        $this->syncArraySize($this->images, $totalBalance);
        $this->syncArraySize($this->imageFileNames, $totalBalance);
        $this->syncArraySize($this->imageCaptions, $totalBalance);
        $this->syncArraySize($this->videos, $totalBalance);
        $this->syncArraySize($this->videoCaptions, $totalBalance);
        $this->syncArraySize($this->textBodies, $totalBalance);
    }


    /**
     * Syncs the array to be at least within one count of the size of the largest array.
     * @param bool $totalBalance True: Arrays will all be sized to the exact same size as the largest array.
     * @param array $array
     */
    private function syncArraySize(array &$array, $totalBalance = false){
        // Determine difference between sequence & number of elements in array.
        $lengthDifference = $this->sequence - sizeof($array); // + 1;

        if ($totalBalance) {
            $lengthDifference++;
        }

        // Add array value to synced sequence position
        if ($lengthDifference > 0){
            // Add buffer spaces to array
            for ($i = 0; $i < $lengthDifference; $i++){
                array_push($array, '');
            }
        }
    }

    /**
     * Adds item to the array.
     * This is done by incrementing the sequence number if necessary and adding the item to the correct index of the array.
     * Since not every array has content for every sequence, this skips some array indices, leaving some blank entries.
     * @param array $array Array to scale and add the value to.
     * @param string $value Value to add to the array.
     */
    private function updateArray(array &$array, $value){
        $lengthDifference = $this->sequence - sizeof($array);

        // Add array value to synced sequence position
        if ($lengthDifference > 0){
            // Add buffer spaces to array & assign value to last position
            for ($i = 0; $i < $lengthDifference; $i++){
                array_push($array, '');
            }
            $array[$this->sequence - 1] = $value;
        } elseif(($lengthDifference === -1 || $lengthDifference === 0)
                  && !empty($value)) {
            // Increment sequence and add space in array
            Lib\MyLogger::log("~~~~~~~~~~~~~~~~~~~~~~~~~<br />");
            Lib\MyLogger::log("<b>Sequence #" . $this->sequence . "</b><br />");
            if ($lengthDifference === 0)
                array_push($array, '');

            $array[$this->sequence] = $value;
            $this->syncAllArraySizes();
            $this->sequence++;
        }
    }


    // Helper Functions ============================================================


    /**
     * Returns the index to skip the specified bracketed section if the supplied index is at the start.
     * @param $i
     * @param $content
     * @param $fromBeforeCurrentString
     * @param $toAfterString
     * @return int
     */
    private function skipBracketedSection($i, $content, $fromBeforeCurrentString, $toAfterString){
        $stringToCheckLength = strlen($fromBeforeCurrentString);
        if ($i + $stringToCheckLength < strlen($content) &&
            substr($content, $i, $stringToCheckLength) === $fromBeforeCurrentString){
            return Lib\StringHelper::offsetByNextString($i, $toAfterString, $this->content);
        }
        return $i;
    }

    /**
     * Returns the index offset to skip the provided tag, if the tag is at the current index.
     * @param int $i Character index to begin check at.
     * @param string $tagName Tag to check.
     * @param string $content
     * @return int The index offset to skip the provided tag, if the tag is at the current index.
     */
    private function offsetByTag($i, $tagName, $content){
        if (Lib\StringHelper::isMatching($i, $tagName, $this->content)){
            return $this->indexSkipToIgnoreTagWithAttributes($i, $tagName, $content);
        }
        return $i;
    }



    private function tagIsSelfClosing($i, $content){
        if ($content[$i] !== '<') return false;

        $endOfTag = Lib\StringHelper::offsetByNextString($i, '>', $this->content) - 1;
        return (($content[$endOfTag - 1] === '/' && $content[$endOfTag - 2] === ' '));
    }

    private function offsetOfCurrentTag($i){
        return Lib\StringHelper::offsetByNextString($i, '>', $this->content);
    }

    private function isAnchorTagWithHrefAttribute($i){
        if (!(Lib\StringHelper::isMatching($i, '<a ', $this->content))) {
            return false;
        }

        $firstAttributeLetter = $this->content[$i + 3];
        if ($firstAttributeLetter === 'n'){
            return false;
        }

        $j = Lib\StringHelper::offsetByNextString($i, 'href="', $this->content);
        $jMax = $this->offsetOfCurrentTag($i);
        return (($j < $jMax) &&
                ($j < $this->contentLength));
    }

    /**
     * Gets the value of the hyperlink href attribute.
     * @param int $i Character index to begin check at.
     * @return string The value of the hyperlink href attribute.
     */
    private function getHrefAttribute($i){
        $attribute = '';
        if ($this->isAnchorTagWithHrefAttribute($i)){
            $j = Lib\StringHelper::offsetByNextString($i, 'href="', $this->content);
            while ($j < $this->contentLength &&
                $this->content[$j] !== '"'){
                $attribute .= $this->content[$j];
                $j++;
            }
        }
        return $attribute;
    }

    /**
     * Gets the value of the hyperlink tag.
     * @param int $i Character index to begin check at.
     * @return string The value of the hyperlink tag.
     */
    private function getHrefValue($i){
        $value = '';
        if ($this->isAnchorTagWithHrefAttribute($i)){
            $j = Lib\StringHelper::offsetByNextString($i, 'href="', $this->content);
            while ($j < $this->contentLength &&
                $this->content[$j] !== '>'){
                $j++;
            }
            $j++;
            while ($j < $this->contentLength &&
                $this->content[$j] !== '<'){
                $value .= $this->content[$j];
                $j++;
            }
        }
        return $value;
    }

    /**
     * Gets the index for the first character after the hyperlink tags.
     * @param int $i Character index to begin check at.
     * @return int The index for the first character after the hyperlink tags.
     */
    private function getMaxHrefIndex($i){
        if ($this->isAnchorTagWithHrefAttribute($i)){
            $j = Lib\StringHelper::offsetByNextString($i, 'href="', $this->content);
            while ($j < $this->contentLength &&
                $this->content[$j] !== '>'){
                $j++;
            }
            while ($j < $this->contentLength &&
                $this->content[$j] !== '<'){
                $j++;
            }
            while ($j < $this->contentLength &&
                $this->content[$j] !== '>'){
                $j++;
            }
            return $j + 1;
        }
        return $i;
    }


    /**
     * Returns the index for the first character that lies beyond the tag to be ignored. Opening and closing variations are checked, and at most only one tag can be skipped per method invokation.
     * @param int $i Current character index in the content.
     * @param array $tags Array of tags to ignore
     * @param string $content
     * @return int Character index to skip to in order to ignore the tag.
     */
    private function ignoreTags($i, array $tags, $content){
        foreach($tags as $tag){
            $openTagWithAttributes = '<' . $tag . ' ';
            $openTag = '<' . $tag . '>';
            if ($this->tagReached($i, $openTagWithAttributes, $content)){
                return $this->indexSkipToIgnoreTagWithAttributes($i, $openTagWithAttributes, $content);
            } elseif ($this->tagReached($i, $openTag, $content)){
                return $this->indexSkipToIgnoreTag($i, $openTag);
            }

            $closeTag = '</' . $tag . '>';
            if ($this->tagReached($i, $closeTag, $content)){
                return $this->indexSkipToIgnoreTag($i, $closeTag);
            }
        }
        return $i;
    }

    /**
     * Determines if a tag to be ignored has been reached at the current index in the content.
     * @param int $i Index for the starting character of the tag.
     * @param string $tagName Tag to check.
     * @param string $content
     * @return bool True if a tag to be ignored has been reached at the current index in the content.
     */
    private function tagReached($i, $tagName, $content){
        $tagLength = strlen($tagName);
        return  ($i + $tagLength <= strlen($content) &&
            substr($content, $i, $tagLength) === $tagName);

    }

    /**
     * Returns the index for the end of the tag.
     * This function assumes the tag has no attributes, and the full tag is supplied.
     * @param int $i Index for the starting character of the tag.
     * @param string $tagName string Tag to check.
     * @return int Character index to skip to in order to ignore the tag.
     */
    private function indexSkipToIgnoreTag($i, $tagName){
        return $i + strlen($tagName);
    }

    /**
     * Returns the index for the end of the tag that contains attributes.
     * @param int $i Index for the starting character of the tag.
     * @param string $tagName Tag to check.
     * @param string $content
     * @return int Character index to skip to in order to ignore the tag.
     */
    private function indexSkipToIgnoreTagWithAttributes($i, $tagName, $content){
        $j = $i + strlen($tagName);
        $contentLength = strlen($content);
        while ($j < $contentLength &&
            $content[$j] !== '>'){
            $j++;
        }
        $j++;
        return $j;
    }
} 