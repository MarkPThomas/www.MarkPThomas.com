<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/29/18
 * Time: 9:45 AM
 */

namespace markpthomas\library;

use markpthomas\mountaineering as Core;

class CsvManager {
    public static function insertCsvFileToMySql($filePath, $fileName, IFactory $page, $bufferCount = 2000){
        $currentLine = 0;
        do{
            $data = self::readCSVFile($filePath, $fileName, $bufferCount, $currentLine);
            $pages = $page::arrayFactory(count($data));
            self::fillItemsWithCsvData($data, $pages, $currentLine);

            $database = Core\DatabaseFactory::getFactory()->getConnection();

            MyPDOManager::insertItemsToMySql($pages, $database);
            $currentLine += $bufferCount;
        } while (count($data) === $bufferCount);
    }

    /**
     * Reads data from a *.csv file and returns an array of all lines.
     * @param string $filePath Relative path to the *.csv file.
     * @param string $fileName File name and extension.
     * @param int $numberOfLines
     * @param int $startLine
     * @return array
     */
    public static function readCSVFile($filePath, $fileName, $numberOfLines = 0, $startLine = 0){
        $importer = new CsvImporter($filePath . $fileName, false, "\t", 10000);
        $fullData = Array();
        while($data = $importer->Get($numberOfLines, $startLine))
        {
            $fullData += $data;
        }

        return $fullData;
    }

    /**
     * Fills the array of data objects with the comma-separated values.
     * @param array $data Array of lines read from a *.csv file.
     * @param array $items Array of objects that implement IReadCsvLine.
     * @param int $rowStart The first row number of the CSV file that was read.
     */
    public static function fillItemsWithCsvData(array $data, array $items, $rowStart = 0){
        MyLogger::log('Reading Lines from CSV file...<br >');
        $numberOfItems = count($data);
        for($i = 0; $i < $numberOfItems; $i++){
            $line = array_values($data[$i]);
            if (self::fillItemWithCsvData($line[0], $items[$i])){
                $j = $rowStart + $i + 1;
                MyLogger::log('Line # ' . $j . ' read from the file. <br >');
            }
        }
    }

    /**
     * Fills the data object with the comma-separated values.
     * @param string $line Line read from a *.csv file.
     * @param IReadCsvLine $item Data object to fill with values from the *.csv file.
     * @return bool
     */
    public static function fillItemWithCsvData($line, IReadCsvLine $item){
        return ($item->readCsvLine($line));
    }
} 