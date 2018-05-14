<?php namespace markpthomas\gis; ?>
<div class="col-md-9">
    <h1>UserController/showProfile</h1>

    <div class="box">
        <h2>Your profile</h2>

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <div class="col-sm-9">Your username: <?= $this->user_name; ?>
            <span class="iconBtn"><a href="<?= Config::get('URL')?>user/editUserName"><i class="glyphicon glyphicon-pencil color-blue"></i></a></span>
            | <span class="iconBtn"><a href="<?= Config::get('URL')?>user/changePassword">Change Password
                    <i class="glyphicon glyphicon-lock color-blue"></i></a></span>
        </div>
        <div class="col-sm-9">Your email: <?= $this->user_email; ?>
            <span class="iconBtn"><a href="<?= Config::get('URL')?>user/editUserEmail"><i class="glyphicon glyphicon-pencil color-blue"></i></a></span>
        </div>
        <div class="col-sm-9">Your avatar image:
            <span class="iconBtn"><a href="<?= Config::get('URL')?>user/editAvatar"><i class="glyphicon glyphicon-pencil color-blue"></i></a></span>
            <div class="col-sm-offset-1">
                <?php if (Config::get('USE_GRAVATAR')) { ?>
                    Your gravatar pic (on gravatar.com): <br /><img src='<?= $this->user_gravatar_image_url; ?>' />
                <?php } else { ?>
                    Your avatar pic (saved locally): <br /><img src='<?= $this->user_avatar_file; ?>' />
                <?php } ?>
            </div>
        </div>
        <div class="col-sm-9">Your account type is: <?= Session::userRole(); ?></div>
    </div>
</div>
