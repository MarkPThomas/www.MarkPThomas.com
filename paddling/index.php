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
if (file_exists('../../vendor/autoload.php')) {
    require '../../vendor/autoload.php';
}

// Load application config (error reporting etc.)
// Local
require 'application/config/config.php';
// Global
require '../../cgi-bin/config.php';
require '../../cgi-bin/connections/connectDbOutdoors.php';

// Load application class
// Local implementation
require 'application/libs/application.php';
require 'application/libs/controller.php';
require 'application/libs/controllerLocalSite.php';
require 'application/views/_controls/navigationList.php';
require 'application/views/_controls/sidebar.php';

// Global implementation
//require '../application/libs/application.php';
//require '../application/libs/controller.php';

// start the application
$app = new Application();