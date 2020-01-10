<?php
/**
 * Created by PhpStorm.
 * User: marku
 * Date: 5/25/2018
 * Time: 6:17 PM
 */

namespace markpthomas\mountaineering;


class PicasaModel
{

    public static function getAlbums()
    {
        // Build feed URL
        $feedURL = "http://picasaweb.google.com/data/feed/api/user/$userid?kind=album";

        // GET LIST OF ALBUMS AVAILABLE ONLINE
        // read feed into SimpleXML object
        set_time_limit(0);
        $xml = simplexml_load_file($feedURL);

        foreach ($xml->entry as $entry) {
            $title = $entry->title;
            $date = $entry->published;
            $unixtime = strtotime($date);
            $formatted_date = date('j F Y', $unixtime);

            $gphoto = $entry->children('http://schemas.google.com/photos/2007');
            $album_id = $gphoto->id; // used as: album.php?album_id=<?= $album->album_id ...
            $numphotos = $gphoto->numphotos;

            $media = $entry->children('http://search.yahoo.com/mrss/');
            $thumb = $media->group->thumbnail[0]->attributes()->{'url'};
        }
    }


    public static function getAlbumInfo($album_id)
    {
        $album_info = array();

        // build feed URL
        $feedURL = "https://picasaweb.google.com/data/feed/api/user/" . $user_id . "/albumid/" . $album_id . "?thumbsize=" . $thumb_size . "c&imgmax=512";

        // read feed into SimpleXML object
        $xml = simplexml_load_file($feedURL);
        set_time_limit(0);

        $subtitle = $xml->subtitle;

        return $subtitle;
    }

    public static function getSelectedPhotos($photos)
    {
        $num_photos = count($photos);
        $picIdsSelection = [];
        for ($i = 0; $i < $num_photos; $i++) {
            $pic_id = $photos[$i]["pic_id"];
            $picIdsSelection[] = Request::post(strval($pic_id));
        }
        return $picIdsSelection;
    }


    // Return the set of photos associated with the specified Picasa user/album id.
    public static function getAlbumPhotos($album_id)
    {

        // build feed URL
        $feedURL = "https://picasaweb.google.com/data/feed/api/user/" . $user_id . "/albumid/" . $album_id . "?thumbsize=" . $thumb_size . "&imgmax=" . $img_max;
        // Sample feed URL for debugging:
        //https://picasaweb.google.com/data/feed/api/user/foundinthemountains/albumid/5773225414897424801?thumbsize=64c&imgmax=512

        // read feed into SimpleXML object
        $xml = simplexml_load_file($feedURL);
        set_time_limit(0);

        $gphoto = $xml->children('http://schemas.google.com/photos/2007');
        $total = $gphoto->numphotos;

        $photos = array();


        // iterate over entries in album
        // print each entry's title, size, dimensions, tags, and thumbnail image
        $i = 0;
        foreach ($xml->entry as $entry) {
            $pic = array();

            $media = $entry->children('http://search.yahoo.com/mrss/');
            $gp = $entry->children('http://schemas.google.com/photos/2007');

            $pic["title"] = $entry->title;
            $pic["caption"] = $entry->summary;
            $pic["thumb_url"] = $media->group->thumbnail[0]->attributes()->{'url'};
            $pic["img_url"] = $media->group->content->attributes()->{'url'};
            $pic["pic_id"] = $gp->id;

            $photos[$i] = $pic;

            $i++;
        }

        return $photos;
    }


    public static function getAlbumPhotosThumbnails($album_id)
    {
        // [
        //  'img_url' => '',
        //  'caption' => '',
        //  'pic_id' => ''
        //  ]
    }


// Return the set of photos associated with the specified Picasa user/album id.
    public static function getAlbumPhotosDisplaySize($album_id)
    {
        // [
        //  'img_url' => '',
        //  'caption' => '',
        //  'pic_id' => ''
        //  ]
    }
}