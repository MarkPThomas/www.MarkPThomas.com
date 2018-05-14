<?php
namespace markpthomas\library;
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 12/16/17
 * Time: 9:33 PM
 */

/**
 * Interface IFactory is for objects that contain factory methods.
 */
interface IFactory{
    /**
     * Creates a new instance of the specified object.
     * @return mixed
     */
    public static function factory();

    /**
     * Creates an array of the number of specified instances of the object.
     * @param int $count Number of objects to generate.
     * @return mixed
     */
    public static function arrayFactory($count);
}