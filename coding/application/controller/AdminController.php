<?php

namespace markpthomas\coding;

use markpthomas\crawler as Crawler;

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

    public function manualDatabaseActions(){
        if(Request::postIsSet('deleteReportId')){
            TripReportsModel::deleteReportInDatabaseById(Request::post('reportId'));

        } elseif (Request::postIsSet('addSuperTopoCrawlerReports')){
            CrawlerReportModel::writeRawFileDataToDatabase('', CrawlerReportModel::superTopo);

        } elseif (Request::postIsSet('addSuperTopoReport')){
            CrawlerReportModel::writeParsedPageToDatabaseBySource(Request::post('superTopoId'), CrawlerReportModel::superTopo);

        } elseif (Request::postIsSet('addSuperTopoReportFromFile')){
            CrawlerReportModel::writeRawFileDataToDatabase(Request::post('superTopoId'), CrawlerReportModel::superTopo);
            CrawlerReportModel::writeParsedPageToDatabaseBySource(Request::post('superTopoId'), CrawlerReportModel::superTopo);

        } elseif (Request::postIsSet('addSummitPostCrawlerObjects')){
            CrawlerReportModel::writeRawFileDataToDatabase('', CrawlerReportModel::summitPost);

        } elseif (Request::postIsSet('addSummitPostReport')){
            CrawlerReportModel::writeParsedPageToDatabaseBySource(Request::post('summitPostId'), CrawlerReportModel::summitPost);

        } elseif (Request::postIsSet('addSummitPostReportFromFile')){
            CrawlerReportModel::writeRawFileDataToDatabase(Request::post('summitPostId'), CrawlerReportModel::summitPost);
            CrawlerReportModel::writeParsedPageToDatabaseBySource(Request::post('summitPostId'), CrawlerReportModel::summitPost);

        } elseif (Request::postIsSet('addSummitPostArticleFromFile')){
            CrawlerReportModel::writeRawFileDataToDatabase(Request::post('summitPostId'), CrawlerReportModel::summitPost, $isArticle = true);
            CrawlerReportModel::writeParsedPageToDatabaseBySource(Request::post('summitPostId'), CrawlerReportModel::summitPost);

        } elseif (Request::postIsSet('cleanAlbumTitles')){
            CrawlerReportModel::cleanAlbumTitles();

        } elseif (Request::postIsSet('associateReportToExternalSite')){
            CrawlerReportModel::associateCrawlerIdsWithPages();

        } elseif (Request::postIsSet('associateReportToExternalSiteByReference')){
            CrawlerReportModel::associateCrawlerIdsWithPagesByReferences();

        } elseif (Request::postIsSet('usePicasaPhotos')){
            PhotoModel::usePicasaPhotoUrls();

        } elseif (Request::postIsSet('usePiwigoPhotos')){
            PhotoModel::usePiwigoPhotoUrls();

        } elseif (Request::postIsSet('useOtherPhotos')){
            PhotoModel::useOtherPhotoUrls();

        } elseif (Request::postIsSet('usePicasaAlbums')){
            AlbumModel::usePicasaAlbumUrls();

        } elseif (Request::postIsSet('usePiwigoAlbums')){
            AlbumModel::usePiwigoAlbumUrls();

        } elseif (Request::postIsSet('setUrlOther')){
            CrawlerReportModel::addAllUrlOtherFromUrls();

        } elseif (Request::postIsSet('cleanPhotoUrlStubs')){
            CrawlerReportModel::cleanPhotoUrlStubs();

        } elseif (Request::postIsSet('cleanAlbumUrlStubs')){
            CrawlerReportModel::cleanAlbumUrlStubs();

        } elseif (Request::postIsSet('associatePhotosToAlbums')){
            TripReportsModel::associatePhotosToAlbums();

        } elseif (Request::postIsSet('associatePicasaToPiwigoPhotos')){
            CrawlerReportModel::addAllPiwigoPhotoUrlsFromPicasaUrls();

        } elseif (Request::postIsSet('associatePicasaToPiwigoAlbums')){
            CrawlerReportModel::addAllPiwigoAlbumUrlsFromPicasaAlbums();

        } elseif (Request::postIsSet('associatePiwigoAlbumsToReportsByPhotos')){
            TripReportsModel::associatePiwigoAlbumsToReportsByPhotos();

        }

        Redirect::toWithJS('admin');
    }
}
