<?php

namespace markpthomas\library;

/**
 * Class CsvImporter Reads data from comma-separated value (*.csv) files.
 * From comment at: http://php.net/manual/en/function.fgetcsv.php
 * this is slightly more flexible in that you can import huge files without running out of memory, you just have to use a limit on the get() method
 *
 * Sample usage for small files:-
 * -------------------------------------
 *
 * $importer = new CsvImporter("small.txt",true);
 * $data = $importer->get();
 * print_r($data);
 *
 * Sample usage for large files:
 *
 * $importer = new CsvImporter("large.txt",true);
 * while($data = $importer->get(2000))
 * {
 *     print_r($data);
 * }
 */
class CsvImporter {
    private $fp;
    private $parseHeader;
    private $header;
    private $delimiter;
    private $length;

    /**
     * @param string $fileName Path and name of the file.
     * @param bool $parseHeader If true, the first line will be treated as a header and ignored.
     * @param string $delimiter The string or character to use as the line delimiter. Tab is the default.
     * @param int $length The number of characters expected in one line.
     */
    function __construct($fileName, $parseHeader=false, $delimiter="\t", $length=8000)
    {
        $this->fp = fopen($fileName, "r");
        $this->parseHeader = $parseHeader;
        $this->delimiter = $delimiter;
        $this->length = $length;

        if ($this->parseHeader)
        {
            $this->header = fgetcsv($this->fp, $this->length, $this->delimiter);
        }

    }
    //--------------------------------------------------------------------
    function __destruct()
    {
        if ($this->fp)
        {
            fclose($this->fp);
        }
    }


    /**
     * Reads the *.csv file line by line and returns an array of each line of data.
     * @param int $maxLines The maximum number of lines to read from the file. If set to 0 (default), then get all of the data.
     * @param int $startLine The first line to begin reading from in the file.
     * @return array Array of the *.csv data.
     */
    public function Get($maxLines=0, $startLine=0)
    {
        $data = array();

        $maxLines > 0 ? $lineCount = 0 : $lineCount = -1; // so loop limit is ignored

        if ($startLine === 0)
            $startLine = -1; // so start line is ignored

        $currentLine = 0;
        while ($lineCount < $maxLines && ($row = fgetcsv($this->fp, $this->length, $this->delimiter)) !== FALSE)
        {
            if ($currentLine < $startLine)
            {
                $currentLine++;
                continue;
            }

            if ($this->parseHeader)
            {
                $row_new = array();
                foreach ($this->header as $i => $heading_i)
                {
                    $row_new[$heading_i] = $row[$i];
                }
                $data[] = $row_new;
            }
            else
            {
                $data[] = $row;
            }

            if ($maxLines > 0)
                $lineCount++;
        }
        return $data;
    }
} 