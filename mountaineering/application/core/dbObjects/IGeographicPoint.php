<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/22/18
 * Time: 8:41 PM
 */

namespace markpthomas\mountaineering\dbObjects;


interface IGeographicPoint {
    public function getLatitude();
    public function getLongitude();
} 