// This works, but for CSS that ust load before the rest of the page, results will not be as desired.
<!-- Load Project CSS -->
<div id="css-loader"
data-root-url="<?= Config::get('URL'); ?>"
data-style-sheets="<?php
        $clientPath = 'client/lib/';
        $customPath = 'client';
        $alwaysUpdate = '?' . time();
        $cssPaths = [
             $clientPath .'leaflet.css' . $alwaysUpdate,
             $clientPath .'Control.FullScreen.css' .  $alwaysUpdate,
             $clientPath .'Control.MiniMap.css' .  $alwaysUpdate,
             $clientPath .'leaflet.draw.css' .  $alwaysUpdate,
             $clientPath .'leaflet.label.css' .  $alwaysUpdate,
             $customPath . 'map.css' .  $alwaysUpdate
            ];
        echo implode(';', $cssPaths);
     ?>"
>
</div>
<script src="<?= Config::get('URL'); ?>client/styles.js"></script>

/**
 * Created by Mark on 3/26/18.
 */

//$(document).ready(function() {
//    $('.service-container').each(function() {
//        var container = $(this);
//        var rootUrl = container.data('rootUrl');
//        console.log(rootUrl);
//        var cssPathsData = container.data('styleSheets');
//        console.log(cssPathsData);
//        var cssPaths = cssPathsData.split(";");
//        console.log(cssPaths);
//
//        $.each(cssPaths, function(index, value){
//            createCssLink(rootUrl + value);
//        });
//    });
//});

createCssLinksFromDOM();

function createCssLinksFromDOM()
{
    var cssLoader = document.getElementById('css-loader');
    var rootUrl = cssLoader.dataset.rootUrl;
    console.log(rootUrl);
    var cssPathsData = cssLoader.dataset.styleSheets;
    console.log(cssPathsData);
    var cssPaths = cssPathsData.split(";");
    console.log(cssPaths);

    var key = 0;
    var value;
    while (value = cssPaths[key++]) {
        console.log(value);
        createCssLink(rootUrl + value);
    }

}

function createCssLink(path)
{
    var linkElement = document.createElement("link");
    linkElement.rel = "stylesheet";
    linkElement.href = path; //Replace here
    console.log(linkElement);

    document.head.appendChild(linkElement);
}