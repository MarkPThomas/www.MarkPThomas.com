<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/18/18
 * Time: 12:04 AM
 */

class Application
{
    /** @var null The controller */
    private $url_controller = null;

    /** @var null The method (of the above controller), often also named "action" */
    private $url_action = null;

    /** @var null Parameter one */
    private $url_parameter_1 = null;

    /** @var null Parameter two */
    private $url_parameter_2 = null;

    /** @var null Parameter three */
    private $url_parameter_3 = null;

    /**
     * "Start" the application:
     * Analyze the URL elements and calls them according controller/method or the fallback
     */
    public function __construct($rootURL = './application/controller/')
    {
        // create array with URL parts in $url
        $this->splitUrl();

        // check for controller: does such a controller exist ?
        if (file_exists($rootURL . $this->url_controller . '.php')) {
            // if so, then load this file and create this controller
            // example: if controller would be "car", then this line would translate into: $this->car = new car();
            require $rootURL . $this->url_controller . '.php';
            $this->url_controller = new $this->url_controller();

            // check for method: does such a method exist in the controller ?
            if (method_exists($this->url_controller, $this->url_action)) {
                // call the method and pass the arguments to it
                if (isset($this->url_parameter_3)) {
                    // will translate to something like $this->home->method($param_1, $param_2, $param_3);
                    $this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2, $this->url_parameter_3);
                } elseif (isset($this->url_parameter_2)) {
                    // will translate to something like $this->home->method($param_1, $param_2);
                    $this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2);
                } elseif (isset($this->url_parameter_1)) {
                    // will translate to something like $this->home->method($param_1);
                    $this->url_controller->{$this->url_action}($this->url_parameter_1);
                } else {
                    // if no parameters given, just call the method without parameters, like $this->home->method();
                    $this->url_controller->{$this->url_action}();
                }
            } elseif(!empty($this->url_action)){
            // URL action is just the next page down. Try passing the value as a parameter to the index.
                $this->url_controller->index($this->url_action);
            } else {
                // default/fallback: call the index() method of a selected controller
                $this->url_controller->index();
            }
        } else {
//             invalid URL, so simply show home/index
//            echo 'invalid URL, so simply show home/index <br />';
//            echo 'Root URL: ' . $rootURL . '<br />';
//            echo 'Controller: ' . $this->url_controller . '<br />';
            require $rootURL . 'home.php';
            $home = new Home();
            $home->index();
        }
    }

    /**
     * Get and split the URL
     */
    private function splitUrl()
    {
        if (isset($_GET['url'])) {
//             for debugging. uncomment this if you have problems with the URL
//            echo 'URL IS set <br />: ' . $_GET['url'] . '</br />';
            // split URL
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            // Put URL parts into according properties
            $this->url_controller = $this->parseSpinalToSnakeCase(isset($url[0]) ? $url[0] : null);
            $this->url_action = $this->parseSpinalToSnakeCase(isset($url[1]) ? $url[1] : null);
            $this->url_parameter_1 = $this->parseSpinalToSnakeCase(isset($url[2]) ? $url[2] : null);
            $this->url_parameter_2 = $this->parseSpinalToSnakeCase(isset($url[3]) ? $url[3] : null);
            $this->url_parameter_3 = $this->parseSpinalToSnakeCase(isset($url[4]) ? $url[4] : null);

////             for debugging. uncomment this if you have problems with the URL
//             echo 'Controller: ' . $this->url_controller . '<br />';
//             echo 'Action: ' . $this->url_action . '<br />';
//             echo 'Parameter 1: ' . $this->url_parameter_1 . '<br />';
//             echo 'Parameter 2: ' . $this->url_parameter_2 . '<br />';
//             echo 'Parameter 3: ' . $this->url_parameter_3 . '<br />';
        }
        else
        {
            //             for debugging. uncomment this if you have problems with the URL
//            echo 'URL not set: <br /> ' . $_SERVER['REQUEST_URI'] . '<br />';
        }
    }

    /**
     * This handles the translation of URLs representing spaces as spinal-case to class name-appropriate snake_case.
     * @param string $urlPart URL to parse. If null, method returns null.
     * @return string|null
     */
    private function parseSpinalToSnakeCase($urlPart)
    {
        return (isset($urlPart)) ? str_replace('-', '_', $urlPart) : null;
    }
}