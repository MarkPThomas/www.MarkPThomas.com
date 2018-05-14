<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/25/18
 * Time: 12:20 PM
 */

namespace markpthomas\coding;


class File {

    /**
     * Removes the file from the filesystem.
     *
     * @param string $filePath Path to the file to delete.
     * @return bool
     */
    public static function deleteFile($filePath)
    {
        // Check if file exists
        if (!file_exists($filePath)) {
            Session::add("feedback_negative", Text::get("FEEDBACK_FILE_DELETE_NO_FILE") . $filePath);
            return false;
        }

        // Delete avatar file
        if (!unlink($filePath)) {
            Session::add("feedback_negative", Text::get("FEEDBACK_FILE_DELETE_FAILED") . $filePath);
            return false;
        }

        return true;
    }

    public static function uploadFile($file, $path_root = '', array $validExtensions = null, array $validMimeTypes = null, $sizeLimit = 0){
        $result = (self::validateFileForUpload($file, $path_root, $validExtensions, $validMimeTypes, $sizeLimit) &&
                   self::moveFile($file, $path_root));
        return $result;
    }

    public static function moveFile(array $file, $path_root = ''){
        $destinationPath = $path_root . $file['name'];
        if (!move_uploaded_file($file['tmp_name'], $destinationPath)){
            Session::add('feedback_negative',
                Text::get('FEEDBACK_FILE_MOVE_FAILED') . $file['tmp_name'] . ' to ' . $destinationPath );
            return false;
        }
        return true;
    }

    public static function validateFileForUpload(array $file, $path_root = '', array $validExtensions = null, array $validMimeTypes = null, $sizeLimit = 0){
        if ($file['error'] != 0){
            Session::add('feedback_negative',
                Text::get('FEEDBACK_FILE_UPLOAD_ERROR'). $file['error'] . ' - ' . self::fileErrorToText($file['error']));
            return false;
        }

        if (!self::fileIsNotEmpty($file)){
            Session::add('feedback_negative', Text::get('FEEDBACK_FILE_IS_EMPTY'));
            return false;
        }

        if (!self::checkFileUploadedName ($file['name'])){
            Session::add('feedback_negative', Text::get('FEEDBACK_FILE_NAME_INVALID_CHARACTERS'). $file['name']);
            return false;
        }

        if (!self::checkFilePathLength ($path_root . $file['name'])){
            Session::add('feedback_negative', Text::get('FEEDBACK_FILE_NAME_TOO_LONG'). $file['name']);
            return false;
        }

        if ($validExtensions && !self::checkFileExtensions($file['name'], $validExtensions)){
            Session::add('feedback_negative',
                Text::get('FEEDBACK_FILE_EXTENSION_INVALID') . $file['type'] . ' is not of ' . implode (", ", $validExtensions));
            return false;
        }

        if ($validMimeTypes && !self::checkFileMimeType($file['name'], $validMimeTypes)){
            Session::add('feedback_negative', Text::get('FEEDBACK_UPLOAD_WRONG_TYPE') . implode (", ", $validMimeTypes));
            return false;
        }

        if (!self::checkFileSize($file, $sizeLimit)){
            Session::add('feedback_negative',
                Text::get('FEEDBACK_FILE_SIZE_TOO_BIG') . $file['size'] . ' > ' . $sizeLimit);
            return false;
        }
        return true;
    }

    /**
     * Returns the description associated with the file upload error code/enum provided.
     * @param int $code File upload error code/enum.
     * @return string Corresponding error description.
     */
    public static function fileErrorToText($code){
        switch ($code) {
            case UPLOAD_ERR_OK:
                $message = 'There is no error, the file uploaded with success.';
                break;
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    /**
     * Make sure file is not empty.
     * @param $file
     * @return bool
     */
    public static function fileIsNotEmpty(array $file){
        return ($file['size'] > 0);
    }

    /**
     * Make sure the file name in English characters, numbers and (_-.) symbols.
     * Check $_FILES[][name]
     * @param $fileName
     * @return bool
     */
    public static function checkFileUploadedName ($fileName)
    {
        return (!preg_match('/[^A-Za-z0-9\-\.\_&\s]+/', $fileName));
    }

    /**
     * Make sure that the file name not bigger than 250 characters.
     * Check $_FILES[][name] length.
     * @param $fileName
     * @return bool
     */
    public static function checkFilePathLength ($fileName)
    {
        return (mb_strlen($fileName, "UTF-8") < 225);
    }

    /**
     * Check File extensions and Mime Types that you want to allow in your project.
     * @param $fileName
     * @param array $validExtensions
     * @return bool
     */
    public static function checkFileExtensions($fileName, array $validExtensions){
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        foreach ($validExtensions as $validExtension){
            if ($fileExtension === strtolower($validExtension)){
                return true;
            }
        }
        return false;
    }


    public static function checkFileMimeType(array $file, array $mimeTypes){
        $image_data = getimagesize($file['tmp_name']);
        return in_array($image_data['mime'], $mimeTypes);
    }


    /**
     * Ensures that the file size is less than the specified limit, or that it is greater than 0 if no limit is specified.
     *
     * See 'upload_max_filesize' in php.ini for the max upload size. This can be changed. Likely 2MB or 10MB.
     * See http://www.php.net/manual/en/ini.core.php#ini.file-uploads
     * @param $file
     * @param int $sizeLimit
     * @return bool
     */
    public static function checkFileSize(array $file, $sizeLimit = 0){
        if ($sizeLimit === 0){
            return ($file['size'] > 0);
        } else {
            return ($file['size'] <= $sizeLimit);
        }

    }

// Last: Check the file content if have a bad codes or something like this function http://php.net/manual/en/function.file-get-contents.php

    /**
     * Checks if the folder exists and is writable.
     *
     * @param $path
     * @return bool success status
     */
    public static function isFolderWritable($path)
    {
        if (is_dir($path) && is_writable($path)) {
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_FOLDER_DOES_NOT_EXIST_OR_NOT_WRITABLE'));
        return false;
    }
} 