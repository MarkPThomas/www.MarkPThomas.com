<?php
namespace markpthomas\library;
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 12/16/17
 * Time: 8:21 PM
 */

/**
 * Interface IReadCsvLine is for objects that read CSV files.
 */
interface IReadCsvLine{
    public function readCsvLine($line);
}