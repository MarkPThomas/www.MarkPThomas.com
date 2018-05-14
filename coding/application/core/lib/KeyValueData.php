<?php
namespace markpthomas\library;

/**
 * Reads key:value data where the key:value is contained within a single line of text and parses it into appropriate properties.
 * @package markpthomas\library
 */
abstract class KeyValueData
    implements IReadKeyValueLine, IFactory, IMySqlInsert, IMySqlUpdate
{

    // ================ Public ================
    public $fileName;

    /**
     * @param string $filePrefix
     * @param string $keyValueDemarcator Character that indicates the division between the key name and value in a string.
     */
    function __construct($filePrefix, $keyValueDemarcator = ': ')
    {
        $this->filePrefix = $filePrefix;
        $this->keyValueDemarcator = $keyValueDemarcator;
    }


    public static function arrayFactory($count){
        $array = array();
        for ($i = 0; $i < $count; $i++) {
            $page = self::factory();
            array_push($array, $page);
        }
        return $array;
    }

    public abstract function readKeyValueLine($line);

    public abstract function readKeyValue($line);

    public abstract function isFilled();

    public abstract function multipleRecordsInFile();

    /**
     * @return string
     */
    public function getFilePrefix(){
        return $this->filePrefix;
    }


    // ================ Protected ================

    /**
     * @var string Character that indicates the division between the key name and value in a string.
     */
    protected $keyValueDemarcator = ': ';

    /**
     * @var string Current key name.
     */
    protected $currentKey;

    /**
     * @var string Current key:value value.
     */
    protected $currentValue;

    /**
     * @var string
     */
    protected $filePrefix;

    /**
     * @var string
     */
    protected $tableIdentifier;


    /**
     * Sets the current key and value fields of the class.
     * @param string $line String to extract the key:value data from.
     */
    protected function setCurrentKeyValue($line){
        $position = $this->getDemarcatorPosition($line);
        if ($position === -1){
            return;
        }

        $this->currentKey = self::getKey($line, $position);
        $this->currentValue = self::getValue($line, $position);
    }

    /**
     * Gets the position, if any, of the demarcator character in the key:value pair.
     * @param string $line String to get the demarcator position from.
     * @return int Position of the demarcator character, or -1 if none is found.
     */
    protected function getDemarcatorPosition($line){
        $position = strpos($line, trim($this->keyValueDemarcator));
        if ($position === false){
            return -1;
        }
        return $position;
    }

    /**
     * Gets the name of the key from the provided string.
     * @param string $line String to extract the key name from.
     * @param int $position Position of the demarcator character indicating the key:value division.
     * @return string Key name.
     */
    protected static function getKey($line, $position){
        return trim(substr($line, 0, $position));
    }

    /**
     * Gets the value from the provided string.
     * @param string $line String to extract the value from.
     * @param int $position Position of the demarcator character indicating the key:value division.
     * @return string Value.
     */
    protected static function getValue($line, $position){
        return trim(substr($line, $position + 1));
    }

    /**
     * Determines whether the line of text contains the key provided.
     * @param string $line Text to check, with the expected format of {key}: {value}.
     * @param string $key Key to check for.
     * @return bool True if the provided key exists in the text.
     */
    protected function containsKey($line, $key){
        return StringHelper::stringContains($line, $key . $this->keyValueDemarcator);
    }

    /**
     * Determines whether the line of text contains any of the keys provided.
     * @param string $line Text to check, with the expected format of {key}: {value}.
     * @param array $keys List of keys to check for.
     * @return bool True if one of the provided keys exists in the text.
     */
    protected function containsAnyKey($line, array $keys){
        foreach ($keys as $item){
            if (StringHelper::stringContains($line, $item . $this->keyValueDemarcator)){
                return true;
            }
        }
        return false;
    }

    /**
     * Cleans the text of html entities, blank spaces, etc.
     * @param string $text Text to clean.
     * @return mixed|string
     */
    protected static function cleanText($text){
        $text = htmlentities($text, null, 'utf-8');
        $text = str_replace("&nbsp;", " ", $text);
        $text = trim($text);
        return $text;
    }
} 