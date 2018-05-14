<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 9:42 PM
 */

namespace markpthomas\gis;


class Object implements IObject{

    protected function loadClass(IObject $query){
        foreach ($query as $key => $value){
            $this->{$key} = $value;
        }
    }


    public static function FactoryStd(\stdClass $data){
        $object = new Object();
        $object->loadStdClass($data);
        return $object;
    }

    protected function loadStdClass(\stdClass $data){
        foreach ($data as $key => $value){
            $this->{$key} = $value;
        }
    }



    public static function FactoryArray(array $data){
        $object = new Object();
        $object->loadArray($data);
        return $object;
    }

    protected function loadArray(array $data){
        foreach ($data as $key => $value){
            $this->{$key} = $value;
        }
    }
} 