<?php namespace markpthomas\mountaineering; ?>
<div class="col-xs-4">
    <h1>LoginController/resetPassword</h1>

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div class="box">
        <h2>Set new password</h2>

        <!-- FYI: ... Identification process works via password-reset-token (hidden input field)-->

        <!-- new password form box -->
        <form method="post" action="<?= Config::get('URL'); ?>login/setNewPassword" name="new_password_form">
            <input type='hidden' name='user_name' value='<?= $this->username; ?>' />
            <input type='hidden' name='user_password_reset_hash' value='<?= $this->password_reset_hash; ?>' />

            <div class="form-group">
                <label for="reset_input_password_new">New password (min. 6 characters):</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock color-blue"></i></span>
                    <input id="reset_input_password_new" class="form-control" type="password"
                           name="user_password_new" pattern=".{6,}" placeholder="Password (6+ characters)"
                           required autocomplete="off" />
                </div>
            </div>
            <div class="form-group">
                <label for="reset_input_password_repeat">Repeat new password:</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock color-blue"></i></span>
                    <input id="reset_input_password_repeat" class="form-control" type="password"
                       name="user_password_repeat" pattern=".{6,}" placeholder="Repeat password"
                       required autocomplete="off" />
                </div>
            </div>
            <button type="submit" class="btn btn-primary" name="submit_new_password">Submit new password</button>
        </form>

        <a href="<?= Config::get('URL'); ?>login/index">Back to Login Page</a>
    </div>
</div>
