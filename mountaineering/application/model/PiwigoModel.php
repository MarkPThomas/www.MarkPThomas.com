<?php
/**
 * Created by PhpStorm.
 * User: marku
 * Date: 5/25/2018
 * Time: 6:17 PM
 */

namespace markpthomas\mountaineering;


class PiwigoModel
{

    public static function getAlbums()
    {
        // Build feed URL
        $feedURL = Config::get('URL') . "../photos/ws.php?format=json&method=pwg.categories.getList&public=true";

        // GET LIST OF ALBUMS AVAILABLE ONLINE
        // read feed into JSON object
        $data = file_get_contents($feedURL); // put the contents of the file into a variable
        $json = json_decode($data); // decode the JSON feed
        if ($json->stat !== "ok") {
            return [];
        }

        $albums = [];
        foreach ($json->result->categories as $category) {
            $album = new \stdClass();
            $date = $category->date_last;
            $unixtime = strtotime($date);
            $album->formatted_date = date('j F Y', $unixtime);

            $album->title = $category->name;
            $album->comment = $category->comment;
            $album->id = $category->id;
            $album->numphotos = $category->total_nb_images;
            $album->url = $category->url;
            $album->thumb = $category->tn_url;

            $albums[] = $album;
        }
        return $albums;
    }


    public static function getAlbum($album_id)
    {
        // Build feed URL
        $feedURL = Config::get('URL') .
            "../photos/ws.php?format=json&method=pwg.categories.getList&cat_id=" . $album_id . "&public=true";

        // GET LIST OF ALBUMS AVAILABLE ONLINE
        // read feed into JSON object
        $data = file_get_contents($feedURL); // put the contents of the file into a variable
        $json = json_decode($data); // decode the JSON feed
        if ($json->stat !== "ok" || count($json->result->categories) < 1) {
            return null;
        }

        $category = $json->result->categories[0];
        $album = new \stdClass();

        $date = $category->date_last;
        $unixtime = strtotime($date);
        $album->formatted_date = date('j F Y', $unixtime);

        $album->title = $category->name;
        $album->comment = $category->comment;
        $album->id = $category->id;
        $album->numphotos = $category->total_nb_images;
        $album->url = $category->url;
        $album->thumb = $category->tn_url;

        return $album;
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
        $feedURL = Config::get('URL') .
            "../photos/ws.php?format=json&method=pwg.categories.getImages&cat_id=" . $album_id . "&per_page=1000&order=name";

        // GET LIST OF ALBUMS AVAILABLE ONLINE
        // read feed into JSON object
        $data = file_get_contents($feedURL); // put the contents of the file into a variable
        $json = json_decode($data); // decode the JSON feed
        if ($json->stat !== "ok") {
            return [];
        }

        // iterate over entries in album
        // print each entry's title, size, dimensions, tags, and thumbnail image
        $i = 0;
        $photos = [];
        foreach ($json->result->images as $image) {
            $pic = [];

            $pic["title"] = $image->file;
            $pic["caption"] = $image->comment;
            $pic["thumb_url"] = $image->derivatives->thumb->url;
            $pic["img_url"] = $image->element_url;
            $pic["img_page_url"] = $image->page_url;
            $pic["pic_id"] = $image->id;

            $photos[$i] = $pic;

            $i++;
        }

        return $photos;
    }


// Return the set of photos associated with the specified Picasa user/album id.
    public static function getAlbumPhotosDisplaySize($album_id)
    {
        return self::getAlbumPhotos($album_id);
    }

    public static function createReportFromAlbum($album_id, $comment = '')
    {
        $photosDisplaySize = PiwigoModel::getAlbumPhotosDisplaySize($album_id);
        $photosFullSize = PiwigoModel::getAlbumPhotos($album_id);
        $picIdsSelection = PiwigoModel::getSelectedPhotos($photosDisplaySize);
    }
}