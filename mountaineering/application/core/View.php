<?php

namespace markpthomas\mountaineering;

/**
 * Class View
 * The part that handles all the output.
 */
class View
{
    protected $hasSideBar = true;
    protected $hasLogin = true;

    public function __construct($dataNavigation = null, $hasSideBar = true, $hasLogin = true)
    {
        $this->hasSideBar = $hasSideBar;
        $this->hasLogin = $hasLogin;
        $this->loadData($dataNavigation);
    }

    /**
     * Simply includes (=shows) the view. This is done from the controller. In the controller, you usually say
     * $this->view->render('help/index'); to show (in this example) the view index.php in the folder help.
     * Usually the Class and the method are the same like the view, but sometimes you need to show different view.
     * @param string $fileName Path of the to-be-rendered view, usually folder/file(.php).
     * @param array $data Data to be used in the view.
     */
    public function render($fileName, $data = null)
    {
        $this->loadData($data);

        $this->loadHeadersAndNavigation($fileName);
        require Config::get('PATH_VIEW') . $fileName . '.php';
        $this->loadFooter();
    }

    /**
     * Similar to render, but accepts an array of separate view to render between the header and footer. Use like
     * the following: $this->view->renderMulti(array('help/index', 'help/banner'));
     * @param array $fileNames Array of the paths of the to-be-rendered view, usually folder/file(.php) for each.
     * @param array $data Data to be used in the view.
     * @return bool
     */
    public function renderMulti($fileNames, $data = null)
    {
        if (!is_array($fileNames)) {
            self::render($fileNames, $data);
            return false;
        }

        $this->loadData($data);

        $this->loadHeadersAndNavigation($fileNames[0]);
        foreach($fileNames as $fileName) {
            require Config::get('PATH_VIEW') . $fileName . '.php';
        }
        $this->loadFooter();
    }

    /**
     * Same like render(), but does not include header and footer.
     * @param string $fileName Path of the to-be-rendered view, usually folder/file(.php).
     * @param mixed $data Data to be used in the view.
     */
    public function renderWithoutHeaderAndFooter($fileName, $data = null)
    {
        $this->loadData($data);

        require Config::get('PATH_VIEW') . $fileName . '.php';
    }

    /**
     * Renders pure JSON to the browser, useful for API construction.
     * @param $data
     */
    public function renderJSON($data)
    {
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    /**
     * Renders the feedback messages into the view.
     */
    public function renderFeedbackMessages()
    {
        // echo out the feedback messages (errors and success messages etc.),
        // they are in $_SESSION["feedback_positive"] and $_SESSION["feedback_negative"]
        require Config::get('PATH_VIEW') . '_templates/feedback.php';

        // delete these messages (as they are not needed anymore and we want to avoid showing them twice
        Session::set('feedback_positive', null);
        Session::set('feedback_negative', null);
    }

    /**
     * Checks if the passed string is the currently active controller.
     * Useful for handling the navigation's active/non-active link.
     *
     * @param string $filename
     * @param string $navigation_controller
     *
     * @return bool Shows if the controller is used or not.
     */
    public static function checkForActiveController($filename, $navigation_controller)
    {
        $split_filename = explode("/", $filename);
        $active_controller = $split_filename[0];

        if ($active_controller == $navigation_controller) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the passed string is the currently active controller-action (=method).
     * Useful for handling the navigation's active/non-active link.
     *
     * @param string $filename
     * @param string $navigation_action
     *
     * @return bool Shows if the action/method is used or not.
     */
    public static function checkForActiveAction($filename, $navigation_action)
    {
        $split_filename = explode("/", $filename);
        $active_action = $split_filename[1];

        if ($active_action == $navigation_action) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the passed string is the currently active controller and controller-action.
     * Useful for handling the navigation's active/non-active link.
     *
     * @param string $filename
     * @param string $navigation_controller_and_action
     *
     * @return bool
     */
    public static function checkForActiveControllerAndAction($filename, $navigation_controller_and_action)
    {
        $split_filename = explode("/", $filename);
        $active_controller = $split_filename[0];
        $active_action = $split_filename[1];

        $split_filename = explode("/", $navigation_controller_and_action);
        $navigation_controller = $split_filename[0];
        $navigation_action = $split_filename[1];

        if ($active_controller == $navigation_controller AND $active_action == $navigation_action) {
            return true;
        }

        return false;
    }

    /**
     * Converts characters to HTML entities.
     * This is important to avoid XSS attacks, and attempts to inject malicious code in your page.
     *
     * @param  string $str The string.
     * @return string
     */
    public function encodeHTML($str)
    {
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

    protected function loadHeadersAndNavigation($fileName){
        $data['navItemsStream'] = '';
        $data['navLoginStream'] = '';
        if (!empty($this->navCategories)){
            require_once Config::get('PATH_VIEW') . '_controls/Navigation.php';
            $data['navItemsStream'] = Navigation::writeNavBarItems(
                                            $fileName,
                                            $this->navCategories,
                                            $this->hasLogin);
            if($this->hasLogin && Session::userIsLoggedIn()){
                $data['navLoginStream'] = Navigation::loginDropDownControl(Session::get('user_name'));
            }
        }

        $data['sidebarControllerStream'] = '';
        if ($this->hasSideBar && !empty($this->sidebarCategories)){
            require_once Config::get('PATH_VIEW') . '_controls/Sidebar.php';
            $sidebarBlogCategoriesStream = Sidebar::displayBlogCategories(
                                            $fileName,
                                            'Report Regions', 'trip-reports',
                                            $this->sidebarCategories);

            $data['sidebarControllerStream'] =
                Sidebar::displaySearch() .
                Sidebar::displayLogin() .
                $sidebarBlogCategoriesStream;
            // . Sidebar::displayArticle());
        }

        $this->loadData($data);

        // Load view
        $this->loadHeader();
        $this->loadNavigation();
        $this->loadSidebar();
    }

    protected function loadHeader(){
        require Config::get('PATH_VIEW') . '_templates/header.php';
    }

    protected function loadNavigation(){
        require Config::get('PATH_VIEW') . '_templates/navigation.php';
    }

    protected function loadSideBar(){
        require Config::get('PATH_VIEW') . '_templates/sidebar.php';
    }

    protected function loadFooter(){
        require Config::get('PATH_VIEW') . '_templates/footer.php';
    }

    private function loadData($data){
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
}
