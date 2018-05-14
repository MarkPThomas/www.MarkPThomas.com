<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/27/18
 * Time: 12:36 PM
 */

class Contact extends ControllerLocalSite
{
    public function index()
    {
        $this->loadHeadersAndNavigation();
        require 'application/views/contact/index.php';
        $this->loadFooter();
    }
} 