<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/18/18
 * Time: 12:13 AM
 */

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 */
class Controller
{
    /**
     * @var null Database Connection
     */
    public $db = null;

    /**
     * Whenever a controller is created, open a database connection too. The idea behind is to have ONE connection
     * that can be used by multiple models (there are frameworks that open one connection per model).
     */
    function __construct()
    {
        $this->openDatabaseConnection();
    }

    /**
     * Open the database connection with the credentials from application/config/config.php
     */
    private function openDatabaseConnection()
    {
        // Set the (optional) options of the PDO connection. in this case, we set the fetch mode to
        // "objects", which means all results will be objects, like this: $result->user_name !
        // For example, fetch mode FETCH_ASSOC would return results like this: $result["user_name] !
        // @see http://www.php.net/manual/en/pdostatement.fetch.php
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                         PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);

        // Generate a database connection, using the PDO connector.
        // @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
        $this->db = new PDO(ConnectDbOutdoors::DB_TYPE .
                            ':host=' . ConnectDbOutdoors::DB_HOST .
                            ';dbname=' . ConnectDbOutdoors::DB_NAME .
//                            ';dbname=php-mvc' .
                            ';charset=utf8',
                            ConnectDbOutdoors::DB_USER,
                            ConnectDbOutdoors::DB_PASSWORD,
                            $options);
    }

    /**
     * Load the model with the given name.
     * loadModel("SongModel") would include models/songmodel.php and create the object in the controller, like this:
     * $songs_model = $this->loadModel('SongsModel');
     * Note that the model class name is written in "CamelCase", the model's filename is the same in lowercase letters
     * @param string $model_name The name of the model
     * @return object model
     */
    public function loadModel($model_name)
    {
        $firstChar = strtolower($model_name[0]);
        $camelCaseModelName = $firstChar . substr($model_name, 1);

        require 'application/models/' . $camelCaseModelName . '.php';

        // return new model (and pass the database connection to the model)
        return new $model_name($this->db);
    }

    protected function loadHeader(){
        require 'application/views/_templates/header.php';
    }

    protected function loadNavigation($navControllerStream){
        require 'application/views/_templates/navigation.php';
    }

    protected function loadSideBar($sidebarControllerStream){
        require 'application/views/_templates/sidebar.php';
    }

    protected function loadFooter(){
        require 'application/views/_templates/footer.php';
    }
}