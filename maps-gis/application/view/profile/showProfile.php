<?php namespace markpthomas\gis; ?>
<div class="col-md-9">
    <h1>ProfileController/showProfile/:id</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>What happens here ?</h3>
        <div>This controller/action/view shows all public information about a certain user.</div>

        <?php if ($this->user) { ?>
            <div>
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Role</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr class="<?= ($this->user->is_active == 0 ? 'inactive' : 'active'); ?>">
                            <td class="avatar">
                                <?php if (isset($this->user->user_avatar_link)) { ?>
                                    <img src="<?= $this->user->user_avatar_link; ?>" />
                                <?php } ?>
                            </td>
                            <td><?= $this->user->username; ?></td>
                            <td><?= $this->user->email; ?></td>
                            <td><?= ($this->user->is_active == 0 ? 'Inactive' : 'Active'); ?></td>
                            <td><?= $this->user->user_account_type_id; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php } ?>

    </div>
</div>
