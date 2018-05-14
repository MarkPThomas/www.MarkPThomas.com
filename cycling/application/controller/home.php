<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/18/18
 * Time: 1:07 AM
 */

class Home extends ControllerLocalSite
{
    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/home/index (which is the default page btw)
     */
    public function index()
    {
        $this->loadHeadersAndNavigation();
        require 'application/views/home/index.php';
        $this->loadFooter();
    }
}