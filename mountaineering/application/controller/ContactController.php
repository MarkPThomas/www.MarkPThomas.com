<?php

namespace markpthomas\mountaineering;
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2/27/18
 * Time: 12:36 PM
 */

class ContactController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This method controls what happens when you move to /admin or /admin/index in your app.
     */
    public function index()
    {
        $this->View->render('contact/index');
    }

    public function contact()
    {
        ContactModel::contactAdmin();

        Redirect::to('contact/index');
    }
} 