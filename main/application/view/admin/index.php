<?php namespace markpthomas\main; ?>
<?php
        require_once(Config::get('PATH_VIEW') . '_controls/Form.php');
        require_once(Config::get('PATH_VIEW') . '_templates/delete_modal.php');
?>
<div  class="col-lg-12">
    <h1>Admin/index</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>What happens here ?</h3>

        <div>
            This controller/action/view shows a list of all users in the system, with the ability to soft delete a user
            or suspend a user.
        </div>
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
                                            $post = config::get("URL"). 'admin/actionAccountSuspend',
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
                                   data-post-Url='<?= config::get("URL"). 'admin/actionAccountSoftDelete' ?>'
                                   value='Delete'>
                        </td>
<!--                        <td><input type="checkbox" name="softDelete" --><?php //if ($user->is_deleted) { ?><!-- checked --><?php //} ?><!-- /></td>-->
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
