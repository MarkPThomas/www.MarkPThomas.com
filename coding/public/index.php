<?php namespace markpthomas\coding; ?>
<?php
/**
 * A simple PHP MVC skeleton
 *
 * @package php-mvc
 * @author Panique
 * @link http://www.php-mvc.net
 * @link https://github.com/panique/php-mvc/
 * @license http://opensource.org/licenses/MIT MIT License
 */

// load the (optional) Composer auto-loader
if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
}

// start the application
$app = new Application();