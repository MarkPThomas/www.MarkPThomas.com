<?php
namespace markpthomas\library;

/**
 * Class KeyValueComplexContentData reads key:value data where the value may be either single or multi-line content and parses it into appropriate properties..
 * @package markpthomas\library
 */
abstract class KeyValueMultiLineData extends KeyValueData
{
    // ================ Public ================
    /**
     * @var string Complete value content read from the key.
     */
    public $content;

    /**
     * @param string $contentKey Name of the key corresponding with the desired content.
     * @param string $keyValueDemarcator Character that indicates the division between the key name and value in a string.
     */
    function __construct($contentKey = "content", $keyValueDemarcator = ': ')
    {
        parent::__construct($keyValueDemarcator);
        $this->contentKey = $contentKey;
    }


    // ================ Protected ================
    /**
     * @var string Name of the key corresponding with the desired content.
     */
    protected $contentKey = "content";

    /**
     * @var bool True: The current line of text is within multi-line content and should be appended to the current content field.
     * False: The current line of text is within single-line content and should constitute the entirety of the content field.
     */
    protected $isInContent = false;

    /**
     * @var array List of key names corresponding to single lines of text content.
     * Keys not in this list are assumed to be multi-line.
     */
    protected $keysForSingleLineValues = [];

    /**
     * Reads a line of text and assigns or appends it to the content field, depending on the type of key:value expected (single line or multi-line).
     * @param string $line Line of text to read.
     */
    protected function readKeyValueLineAndContent($line){
        if ($this->containsKey($line, $this->contentKey)){
            $this->isInContent = true;
        }
        elseif ($this->containsAnyKey($line, $this->keysForSingleLineValues)){
            $this->isInContent = false;
        }

        if ($this->isInContent){
            $this->appendContent($line);
        }
        else {
            $this->readKeyValue($line);
        }
    }

    /**
     * Appends the provided line of text to the content field.
     * @param string $line Line of text to append.
     */
    protected function appendContent($line){
        $this->content .= $line;
    }
} 