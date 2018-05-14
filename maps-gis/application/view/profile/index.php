<?php namespace markpthomas\gis; ?>
<div class="col-md-9">
    <h1>ProfileController/index</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>What happens here ?</h3>
        <div>
            This controller/action/view shows a list of all users in the system. You could use the underlying code to
            build things that use profile information of one or multiple/all users.
        </div>
        <div>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Avatar</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Profile</th>
                </tr>
                </thead>
                <?php foreach ($this->users as $user) { ?>
                    <tr class="<?= ($user->is_active == 0 ? 'inactive' : 'active'); ?>">
                        <td><?= $user->id; ?></td>
                        <td class="avatar">
                            <?php if (isset($user->user_avatar_link)) { ?>
                                <img src="<?= $user->user_avatar_link; ?>" />
                            <?php } ?>
                        </td>
                        <td><?= $user->username; ?></td>
                        <td><?= $user->email; ?></td>
                        <td><?= ($user->is_active == 0 ? 'Inactive' : 'Active'); ?></td>
                        <td><?= $user->user_account_type_id; ?></td>
                        <td>
                            <a href="<?= Config::get('URL') . 'profile/showProfile/' . $user->id; ?>">View</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
