<?php
namespace markpthomas\library;
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 12/16/17
 * Time: 7:41 PM
 */

/**
 * Interface IReadKeyValueLine
 */
interface IReadKeyValueLine
{
    /**
     * Reads the line of text and parses it into the appropriate fields in the data object.
     * @param string $line Line of text to parse.
     * @return void
     */
    public function readKeyValueLine($line);

    /**
     * Reads the key:value pair from the provided line of text.
     * @param string $line
     * @return void
     */
    public function readKeyValue($line);

    /**
     * Returns 'true' if all of the properties have been filled.
     * @return boolean 'True' if all of the properties have been filled.
     */
    public function isFilled();

    /**
     * Returns 'true' if there are multiple recurring key-value pair sets in a given file.
     * @return boolean 'True' if there are multiple key:value records in the file.
     */
    public function multipleRecordsInFile();
}