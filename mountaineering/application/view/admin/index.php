<?php namespace markpthomas\mountaineering; ?>
<?php
        require_once(Config::get('PATH_VIEW') . '_controls/Form.php');
        require_once(Config::get('PATH_VIEW') . '_templates/delete_modal.php');
?>
<div  class="col-lg-12">
    <h1>Admin Control Panel</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>Users</h3>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>
                        <?php
                        echo Form::checkBoxArrayMasterControl();
                        ?>
                    </th>
                    <th>Id</th>
                    <th>Avatar</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Days Suspended</th>
                    <th>Soft Delete</th>
                    <th>Delete</th>
<!--                    <th>Submit</th>-->
                </tr>
                </thead>
                <?php foreach ($this->users as $user) { ?>
                    <tr class="<?= ($user->is_active == 0 ? 'inactive' : 'active'); ?>">
                        <td>
                            <?php
                            echo Form::checkBoxArrayChildControl($user->id)
                            ?>
                        </td>
                        <td><?= $user->id; ?></td>
                        <td class="avatar">
                            <?php if (isset($user->user_avatar_link)) { ?>
                                <img src="<?= $user->user_avatar_link; ?>"/>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="<?= Config::get('URL') . 'profile/showProfile/' . $user->id; ?>"><?= $user->username; ?></a>
                        </td>
                        <td><?= $user->email; ?></td>
                        <td><?php
                            $statusActive = 'Active';
                            $statusInactive = 'Inactive';
                            $userStatus = ($user->is_active == 0) ? $statusInactive : $statusActive;
                            echo Form::sliderControl(
                                            $target = $user->id,
                                            $currentValue = $userStatus,
                                            $statusActive,
                                            $statusInactive,
                                            $post = Config::get('URL') . "admin/actionAccountActivation",
                                            $dataOffStyle = 'info',
                                            $isDisabled = (bool)$user->is_deleted);
                            ?>
                        </td>
                        <td><?php
                            echo Form::selectionControlFuelUx(
                                            $target = $user->id,
                                            $values = $this->userRoles,
                                            $selected = $user->user_account_type_id,
                                            $control_id = '',
                                            $post = Config::get('URL') . 'admin/actionAccountChangeRole',
                                            $isCurrentKey = true);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo Form::spinBoxControlFuelUx(
                                            $target = $user->id,
                                            $post = Config::get("URL"). 'admin/actionAccountSuspend',
                                            $currentValue = $user->suspension_timestamp);
                            ?>
                        </td>
                        <td>
                            <?php
                            echo Form::checkBoxControl(
                                $target = $user->id,
                                $isChecked = $user->is_deleted,
                                $post = config::get("URL"). 'admin/actionAccountSoftDelete');
                            ?>
                        </td>
                        <td>
                            <input type='submit' class='btn btn-danger' name='delete'
                                   data-item-id='<?= $user->id ?>'
                                   data-item-type='user'
                                   data-post-Url='<?= Config::get("URL"). 'admin/actionAccountSoftDelete' ?>'
                                   value='Delete'>
                        </td>
<!--                        <td><input type="checkbox" name="softDelete" --><?php //if ($user->is_deleted) { ?><!-- checked --><?php //} ?><!-- /></td>-->
                    </tr>
                <?php } ?>
            </table>
        </div>

        <h3>Manual Database Operations</h3>
        <form action="<?php echo Config::get('URL'); ?>admin/manualDatabaseActions" method="post">
            <div class="form-group">
                <label for="reportId">Report ID:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="reportId" name="reportId" placeholder="report ID" />
                    <button type="submit" class="btn btn-danger" name="deleteReportId">Delete Report</button>
                </div>
            </div>
            <div class="form-group">
                <label for="superTopoId">SuperTopo Report ID:</label>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="addSuperTopoCrawlerReports">Add All Scraped Reports</button>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" id="superTopoId" name="superTopoId" placeholder="t11567n" />
                    <button type="submit" class="btn btn-primary" name="addSuperTopoReport">Add Report</button>
                    <button type="submit" class="btn btn-info" name="addSuperTopoReportFromFile">Add Report From Scraper File</button>
                </div>
            </div>
            <div class="form-group">
                <label for="summitPostId">SummitPost Report ID:</label>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="addSummitPostCrawlerObjects">Add All Scraped Objects</button>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" id="summitPostId" name="summitPostId" placeholder="700719" />
                    <button type="submit" class="btn btn-primary" name="addSummitPostReport">Add Report/Article</button>
                    <button type="submit" class="btn btn-info" name="addSummitPostReportFromFile">Add Report From Scraper File</button>
                    <button type="submit" class="btn btn-info" name="addSummitPostArticleFromFile">Add Article From Scraper File</button>
                </div>
            </div>
            <div class="form-group">
                <label for="summitPostId">Other Operations:</label>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="cleanAlbumTitles">Clean Picasa Album Reference Titles</button>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="setUrlOther">Set All URL Other</button>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="cleanPhotoUrlStubs">Clean Photo URL Stubs</button>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="cleanAlbumUrlStubs">Clean Album URL Stubs</button>
                </div>

                <hr>

                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="associateReportToExternalSite">Associate Trip Reports to External Sites By Title</button>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="associateReportToExternalSiteByReference">Associate Trip Reports to External Sites By Reference</button>
                </div>


                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="associatePicasaToPiwigoPhotos">Associate Piwigo Photos with Picasa Photos by Title</button>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="associatePicasaToPiwigoAlbums">Associate Piwigo Albums with Picasa Albums by Title</button>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="associatePiwigoAlbumsToReportsByPhotos">Associate Piwigo Albums with Trip Reports by Photos</button>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="associatePhotosToAlbums">Associate Photos to Albums</button>
                </div>

                <hr>

                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="usePicasaPhotos">Use Picasa Photos</button>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="usePiwigoPhotos">Use Piwigo Photos</button>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="useOtherPhotos">Use Other Photos</button>
                </div>

                <hr>

                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="usePicasaAlbums">Use Picasa Albums</button>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" name="usePiwigoAlbums">Use Piwigo Albums</button>
                </div>
            </div>
        </form>
    </div>
</div>
