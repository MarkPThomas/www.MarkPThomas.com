<?php

namespace markpthomas\gis;

class AdminController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct($hasSideBar = false);

        // special authentication check for the entire controller: Note the check-ADMIN-authentication!
        // All methods inside this controller are only accessible for admins (= users that have role type 7)
        Auth::checkAdminAuthentication();
    }

    /**
     * This method controls what happens when you move to /admin or /admin/index in your app.
     */
    public function index()
    {
        $this->View->render('admin/index', array(
                'users' => UserModel::getPublicProfilesOfAllUsers(),
                'userRoles' => UserRoleModel::getUserRoles())
        );
    }

    public function actionAccountSettings()
    {
        AdminModel::setAccountSuspensionAndDeletionStatus(
            Request::post('suspension'), Request::post('softDelete'), Request::post('user_id')
        );

        Redirect::to("admin");
    }


    public function actionAccountSuspend()
    {
        AdminModel::setAccountSuspension(
            Request::post('targetId'), Request::post('currentValue')
        );

        Redirect::to("admin");
    }

    public function actionAccountSoftDelete()
    {
        AdminModel::setAccountSoftDelete(
            Request::post('targetId'), Request::post('currentValue')
        );

        Redirect::to("admin");
    }

    public function actionAccountActivation(){
        $userStatus = (Request::post('currentValue') == 'Active')? 1 : 0;

        AdminModel::setAccountActivationStatus(Request::post('targetId'), $userStatus);

        Redirect::to("admin");
    }

    public function actionAccountChangeRole(){
        AdminModel::changeUserRole(
            Request::post('targetId'), Request::post('currentValue')
        );

        Redirect::to("admin");
    }
}
