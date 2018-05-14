<?php

namespace markpthomas\coding;
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/18/18
 * Time: 1:07 AM
 */

/**
 * Class Home
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 */
class HomeController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handles what happens when user moves to URL/home/index - or - as this is the default controller, also
     * when user moves to /home or enter your application at base level
     */
    public function index()
    {
        $this->View->render('home/index');
    }
}