<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 9:42 PM
 */

namespace markpthomas\library;

/**
 * Class Object
 * @package markpthomas\library
 */
class Object implements IObject{
    /**
     * Auto loads properties from the data into a new object.
     * @param \stdClass $data
     * @param bool $strict True: Property will only be added if it already exists in the class.
     * False: The property will be dynamically created if it does not exist.
     * @return Object
     */
    public static function factoryStdClass(\stdClass $data, $strict = true){
        $object = new Object();
        $object->loadStdClass($data, $strict);
        return $object;
    }


    /**
     * Auto loads properties from the data into a new object.
     * @param array $data
     * @param bool $strict True: Property will only be added if it already exists in the class.
     * False: The property will be dynamically created if it does not exist.
     * @return Object
     */
    public static function factoryArray(array $data, $strict = true){
        $object = new Object();
        $object->loadArray($data, $strict);
        return $object;
    }

    /**
     * Auto loads properties from the data into the current object.
     * @param IObject $data
     * @param bool $strict True: Property will only be added if it already exists in the class.
     * False: The property will be dynamically created if it does not exist.
     */
    protected function loadClass(IObject $data, $strict = true){
        foreach ($data as $key => $value){
            if ($strict){
                if (property_exists($this, $key)){
                    $this->{$key} = $value;
                }
            }
            else {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Auto loads properties from the data into the current object.
     * @param \stdClass $data
     * @param bool $strict True: Property will only be added if it already exists in the class.
     * False: The property will be dynamically created if it does not exist.
     * @see  https://stackoverflow.com/questions/18576762/php-stdclass-to-array
     */
    protected function loadStdClass(\stdClass $data, $strict = true){
        if (count((array)$data) === 0) return;

        $array = JsonHandler::decode(JsonHandler::encode($data), $assoc = true);
        $this->loadArray($array, $strict);
    }

    /**
     * Auto loads properties from the data into the current object.
     * @param array $data
     * @param bool $strict True: Property will only be added if it already exists in the class.
     * False: The property will be dynamically created if it does not exist.
     */
    protected function loadArray(array $data, $strict = true){
        foreach ($data as $key => $value){
            if ($strict){
                if (property_exists($this, $key)){
                    $this->{$key} = $value;
                }
            }
            else {
                $this->{$key} = $value;
            }
        }
    }

// TODO: Finish
    protected function loadArrayEnumNames(array $data, $property){
        if ($property) return;

        // First round of loading matches if property is {property}_id
        // On this round, '_id' is stripped to see if there is a match.

        foreach ($data as $key => $value){
            $keyName = str_replace('_id', '', $key);
            if (property_exists($this, $keyName)){
                $this->{$keyName} = $value;
            }
        }

    }

    /**
     * Returns the current object as an stdClass object.
     * @param bool $isVerbose True: Enumerations will be represented by name. False: Enumeration ordinal values are used.
     * @param bool $isJoin True: Properties of object will be represented as the object. False: Object ID used.
     * @return \stdClass
     */
    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = new \stdClass();

        if ($isVerbose){
            $object->propertyEnumName = '';
        } else {
            $object->propertyEnum_id = '';
        }

        if ($isJoin){
            $object->propertyObject = '';
        } else {
            $object->propertyObject_id = '';
        }

        return $object;
    }


    /**
     * Sets any numeric value to the property, casting if necessary.
     * @param $field
     * @param $value
     * @return bool
     */
    protected function setInt($field, $value){
        if (is_integer($value)){
            $this->{$field} = $value;
            return true;
        } elseif (is_numeric($value)){
            $this->{$field} = (int)$value;
            return true;
        }
        return false;
    }

    /**
     * Sets any numeric value to the property, casting if necessary.
     * @param $field
     * @param $value
     * @return bool
     */
    protected function setBool($field, $value){
        if (is_bool($value)){
            $this->{$field} = $value;
            return true;
        } elseif (is_integer($value)){ // Choosing a rough truthiness, where any value but 0 is true.
            $this->{$field} = (bool)($value != 0);
            return true;
        }elseif (is_numeric($value)){ // Choosing a rough truthiness, where any value but 0 is true.
            $this->{$field} = (bool)((int)$value != 0);
            return true;
        }
        return false;
    }


    // Magic Getters/Setters
    // These might not be good to use as:
    // 1. Type hinting doesn't work
    // 2. Cannot assign null to a property without being inconsistent
    // 3. Jetbrains IDE throws up errors when using this, e.g. $object->id = 5, or $value = $object->id
//    /**
//     * @param $name
//     * @param $value
//     * @see http://www.beaconfire-red.com/epic-stuff/better-getters-and-setters-php
//     */
//    public function __set($name, $value){
//        if(method_exists($this, $name)){
//            $this->$name($value);
//        }
//        else{
//            // Getter/Setter not defined so set as property of object
//            $this->$name = $value;
//        }
//    }
//
//    /**
//     * @param $name
//     * @return null
//     * @see http://www.beaconfire-red.com/epic-stuff/better-getters-and-setters-php
//     */
//    public  function __get($name){
//        if(method_exists($this, $name)){
//            return $this->$name();
//        }
//        elseif(property_exists($this,$name)){
//            // Getter/Setter not defined so return property if it exists
//            return $this->$name;
//        }
//        return null;
//    }

    // Implementation in a class for properties:
//    private $_id;
//    public function id($value = null){
//        // If value was provided, set the value
//        if($value){
//            $this->_id = $value;
//        }
//        // If no value was provided return the existing value
//        else {
//            return $this->_id;
//        }
//    }
} 