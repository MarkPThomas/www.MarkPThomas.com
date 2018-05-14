<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/29/18
 * Time: 9:41 AM
 */

namespace markpthomas\library;

use markpthomas\mountaineering as Core;

/**
 * Handles gathering, reading, and submitting to a database specified text files that record data in a key:value format.
 * @package markpthomas\library
 */
class KeyValueManager {
    /**
     * Reads all key:value files fitting the specified pattern in the directory path and submits their data to a database.
     * @param string $directoryPath Directory path to search within to gather all of the file names.
     * @param KeyValueData $page Data object to fill with data from the text file.
     */
    public static function insertKeyValueFilesToMySql($directoryPath, KeyValueData $page){
        $fileNames = self::readKeyValueFileNames($directoryPath, $page);
        $pages = self::fillItemsWithKeyValueData($directoryPath, $fileNames, $page);

        $database = Core\DatabaseFactory::getFactory()->getConnection();

        MyPDOManager::insertItemsToMySql($pages, $database);
    }

    /**
     * Returns all text file paths within the specified directory that match the file prefix defined in the $page object.
     * @param string $directoryPath Directory path to search within to gather all of the file names.
     * @param KeyValueData $page Data object to fill with data from the text file.
     * @return array|mixed Array of file paths.
     */
    public static function readKeyValueFileNames($directoryPath, KeyValueData $page){
        // Get list of files in directory
        $filePathAndFilter = $directoryPath . '/' . $page->getFilePrefix() . "*.txt";
        $fileNames = glob($filePathAndFilter);
        $fileNames = str_replace($directoryPath, '', $fileNames);
        return $fileNames;
    }

    /**
     * Reads all lines of all specified text files and populates the properties of the provided data object.
     * @param string $filePath Relative path to the text file.
     * @param array $fileNames File names with extension to read..
     * @param KeyValueData $page Data object to fill with data from the text file.
     * @return array Array of KeyValueData objects.
     */
    public static function fillItemsWithKeyValueData($filePath, array $fileNames, KeyValueData $page){
        // Fill data
        $pages = [];
        $numberOfItems = count($fileNames);
        for ($i = 0; $i < $numberOfItems; $i++){
            MyLogger::log('Reading data from ' . $fileNames[$i] . '<br />');
            $page = $page::factory();
            $newPages = self::readKeyValueFile($filePath, $fileNames[$i], $page);
            $pages = array_merge($pages, $newPages);
        }
        return $pages;
    }


    /**
     * Reads all lines of a text file and populates the properties of the provided data object.
     * @param string $filePath Relative path to the text file.
     * @param string $fileName File name and extension.
     * @param KeyValueData $page Data object to fill with data from the text file.
     * @return array Array of KeyValueData objects.
     */
    public static function readKeyValueFile($filePath, $fileName, KeyValueData $page){
        $file_handle = fopen($filePath . $fileName, "r") or die("Unable to open file!");
        $pages = [];
        if ($file_handle) {
            while (!feof($file_handle)) {
                $line = fgets($file_handle);
                $page->readKeyValueLine($line);
                $page->fileName = $fileName;

                // Save current page object and create a new one since there are multiple records in a single file.
                if ($page->isFilled() && $page->multipleRecordsInFile()){
                    array_push($pages, $page);
                    $page = $page::factory();
                }
            }
            fclose($file_handle);

            // Save current page object if it is the only one read for the file
            if (!$page->multipleRecordsInFile()){
                array_push($pages, $page);
            }
        } else {
            MyLogger::log("Unable to open file!");
        }
        return $pages;
    }
} 