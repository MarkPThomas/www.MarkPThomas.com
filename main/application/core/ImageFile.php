<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/25/18
 * Time: 1:28 PM
 */

namespace markpthomas\main;


class ImageFile {
    public static function uploadImage(array $file, $path_root = '', $sizeLimit = 0){
        $validExtensions = ['gif','jpg','jpe','jpeg','png'];
        $mimeTypes = ['image/jpeg', 'image/gif', 'image/png'];
        return File::uploadFile($file, $path_root, $validExtensions, $mimeTypes, $sizeLimit);
    }


    public static function getImageWidth(array $file){
        $image_proportions = getimagesize($file['tmp_name']);
        return $image_proportions[0];
    }

    public static function getImageHeight(array $file){
        $image_proportions = getimagesize($file['tmp_name']);
        return $image_proportions[1];
    }

    public static function getImageType(array $file){
        $image_proportions = getimagesize($file['tmp_name']);
        return $image_proportions['mime'];
    }

    public static function checkImageMimeType(array $file){
        $mimeTypes = ['image/jpeg', 'image/gif', 'image/png'];
        return File::checkFileMimeType($file, $mimeTypes);
    }
} 