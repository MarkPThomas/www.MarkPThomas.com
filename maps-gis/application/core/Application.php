<?php

namespace markpthomas\gis;

/**
 * Class Application
 * The heart of the application
 */
class Application
{
    /** @var mixed Instance of the controller */
    private $controller;

    /** @var array URL parameters, will be passed to used controller-method */
    private $parameters = array();

    /** @var string Just the name of the controller, useful for checks inside the view ("where am I ?") */
    private $controller_name;

    /** @var string Just the name of the controller's method, useful for checks inside the view ("where am I ?") */
    private $action_name;

    /**
     * Start the application, analyze URL elements, call according controller/method or relocate to fallback location.
     */
    public function __construct()
    {
        // create array with URL parts in $url
        $this->splitUrl();

        // creates controller and action names (from URL input)
        $this->createControllerAndActionNames();

        // does such a controller exist ?
        if (file_exists(Config::get('PATH_CONTROLLER') . $this->controller_name . '.php')) {

            // load this file and create this controller
            // example: if controller would be "car", then this line would translate into: $this->car = new car();
            require Config::get('PATH_CONTROLLER') . $this->controller_name . '.php';
            $controllerName = __NAMESPACE__ . '\\' . $this->controller_name;
            $this->controller = new $controllerName();
//            $this->controller = new namespace\$this->controller_name();

            // check for method: does such a method exist in the controller ?
            if (method_exists($this->controller, $this->action_name)) {
                if (!empty($this->parameters)) {
                    // call the method and pass arguments to it
                    call_user_func_array(array($this->controller, $this->action_name), $this->parameters);
                } else {
                    // if no parameters are given, just call the method without parameters, like $this->index->index();
                    $this->controller->{$this->action_name}();
                }
            } elseif(!empty($this->action_name)){
                // URL action is just the next page down. Try passing the value as a parameter to the index.
                $this->controller->index($this->action_name);
            } else {
                // load 404 error page
                require Config::get('PATH_CONTROLLER') . 'ErrorController.php';
                $this->controller = new ErrorController;
                $this->controller->error404();
            }
        } else {
            // load 404 error page
            require Config::get('PATH_CONTROLLER') . 'ErrorController.php';
            $this->controller = new ErrorController;
            $this->controller->error404();
        }
    }

    /**
     * Get and split the URL.
     */
    private function splitUrl()
    {
        if (Request::get('url')) {

            // split URL
            $url = trim(Request::get('url'), '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            // put URL parts into according properties
            $this->controller_name = self::parseSpinalToSnakeCase((isset($url[0]) ? $url[0] : null));
            $this->action_name = self::parseSpinalToSnakeCase((isset($url[1]) ? $url[1] : null));

            // remove controller name and action name from the split URL
            unset($url[0], $url[1]);

            // rebase array keys and store the URL parameters
            $this->parameters = array_values($url);
//             for debugging. uncomment this if you have problems with the URL
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
    private static function parseSpinalToSnakeCase($urlPart)
    {
//        return $urlPart;
        return (isset($urlPart)) ? str_replace('-', '_', $urlPart) : null;
    }

    /**
     * Checks if controller and action names are given. If not, default values are put into the properties.
     * Also renames controller to usable name.
     */
    private function createControllerAndActionNames()
    {
        // check for controller: no controller given ? then make controller = default controller (from config)
        if (!$this->controller_name) {
            $this->controller_name = Config::get('DEFAULT_CONTROLLER');
        }

        // check for action: no action given ? then make action = default action (from config)
        if (!$this->action_name OR (strlen($this->action_name) == 0)) {
            $this->action_name = Config::get('DEFAULT_ACTION');
        }

        // rename controller name to real controller class/file name ("index" to "IndexController")
//        $this->controller_name = ucwords($this->controller_name) . 'Controller';
        $this->controller_name = self::capitalizeSnakeCase($this->controller_name) . 'Controller';
    }

    private static function capitalizeSnakeCase($name){
        $parts = explode('_', $name);
        for($i = 0; $i < count($parts); $i++){
            $parts[$i] = ucwords($parts[$i]);
        }
        return implode('_', $parts);
    }
}
