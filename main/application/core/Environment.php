<?php

namespace markpthomas\main;

/**
 * Class Environment
 *
 * Extremely simple way to get the environment, everywhere inside your application.
 * Extend this the way you want.
 */
class Environment
{
    public static function get()
    {
        // if APPLICATION_ENV constant exists (set in Apache configs)
        // then return content of APPLICATION_ENV
        // else return "development"
        // If you don't have access to Apache configs, you can set this in the .htaccess file: https://stackoverflow.com/questions/5448943/setenv-application-env-development-htaccess-interacting-with-zend-framework

    // The above constant may not exist on the server, and the above solution may not work.
//        return (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : "development");

        if (getenv('APPLICATION_ENV')){
            return getenv('APPLICATION_ENV');
        }

        $config_file = '../application/config/config.base.php';

        if (!file_exists($config_file)) {
            return false;
        }

        $config = require $config_file;
        return (($config['SERVER_ADDR'] == $_SERVER['SERVER_ADDR'])? "production" : "development");
    }
}
