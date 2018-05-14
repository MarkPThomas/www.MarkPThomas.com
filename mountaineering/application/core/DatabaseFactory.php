<?php

namespace markpthomas\mountaineering;

use markpthomas\library as Lib;

/**
 * Class DatabaseFactory
 *
 * Use it like this:
 * $database = DatabaseFactory::getFactory()->getConnection();
 *
 * That's my personal favourite when creating a database connection.
 * It's a slightly modified version of Jon Raphaelson's excellent answer on StackOverflow:
 * http://stackoverflow.com/questions/130878/global-or-singleton-for-database-connection
 *
 * Full quote from the answer:
 *
 * "Then, in 6 months when your app is super famous and getting dugg and slashdotted and you decide you need more than
 * a single connection, all you have to do is implement some pooling in the getConnection() method. Or if you decide
 * that you want a wrapper that implements SQL logging, you can pass a PDO subclass. Or if you decide you want a new
 * connection on every invocation, you can do do that. It's flexible, instead of rigid."
 *
 * Thanks! Big up, mate!
 */
class DatabaseFactory
{
    private static $factory;
    private $database;

    public static function getFactory()
    {
        if (!self::$factory) {
            self::$factory = new DatabaseFactory();
        }
        return self::$factory;
    }

    public function getConnection() {
        if (!$this->database) {

            /**
             * Check DB connection in try/catch block. Also when PDO is not constructed properly,
             * prevent to exposing database host, username and password in plain text as:
             * PDO->__construct('mysql:host=127....', 'root', '12345678', Array)
             * by throwing custom error message
             */
            try {
                $options = Lib\MyPDOStatement::getOptions();

                $this->database = new \PDO(
                   Config::get('DB_TYPE') .
                   ':host=' . Config::get('DB_HOST') .
                   ';dbname=' . Config::get('DB_NAME') . ';port=' . Config::get('DB_PORT') . ';charset=' . Config::get('DB_CHARSET'),
                   Config::get('DB_USER'),
                   Config::get('DB_PASS'),
                   $options
                   );

                // For below, see: https://stackoverflow.com/questions/10437423/how-can-i-pass-an-array-of-pdo-parameters-yet-still-specify-their-types
                // Also: https://stackoverflow.com/questions/10113562/pdo-mysql-use-pdoattr-emulate-prepares-or-not
                $this->database->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            } catch (\PDOException $e) {
                // For debugging only!
//                echo 'Environment: ' . getenv('APPLICATION_ENV') . '<br />';
//                echo 'Environment: ' . Environment::get() . '<br />';
//                echo '$_SERVER: ' .'<br />';
//                echo print_r($_SERVER) . '<br />';
//                echo '$_ENV: ' .'<br />';
//                echo print_r($_ENV) . '<br />';
//                echo 'Password: ' . Config::get('DB_PASS') . '<br />';

                // Echo custom message. Echo error code gives you some info.
                echo 'Database connection cannot be established. Please try again later.' . '<br>';
                echo 'Error code: ' . $e->getCode() . '<br />';
                echo 'Error description: ' . $e->getMessage() . '<br />';
                // TODO: Consider replacing above with the logger?
//                Lib\MyLogger::log('Database connection cannot be established. Please try again later.' . '<br>');
//                Lib\MyLogger::log('Error code: ' . $e->getCode() . '<br />');
//                Lib\MyLogger::log('Error description: ' . $e->getMessage() . '<br />');
//                Lib\MyLogger::log(nl2br($e->getTraceAsString()));
                // Stop application :(
                // No connection, reached limit connections etc. so no point to keep it running
                exit;
            }
        }
        return $this->database;
    }
}
