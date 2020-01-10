<?php namespace markpthomas\mountaineering; ?>
<div class="col-xs-4">

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <!-- login box on left side -->
    <div>
        <h1>Register</h1>

        <!-- register form -->
        <form method="post" action="<?= Config::get('URL'); ?>register/register_action">
            <!-- the user name input field uses an HTML5 pattern check -->
            <div class="form-group">
                <label for="username">Username:</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user color-blue"></i></span>
                    <input type="text" class="form-control" id="username" pattern="[a-zA-Z0-9]{2,64}" name="user_name" placeholder="Enter username (letters/numbers, 2-64 chars)" required />
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                    <input type="email" class="form-control" id="email" name="user_email" placeholder="Enter email" required />
                    <input type="email" class="form-control" name="user_email_repeat" placeholder="Repeat email" required />
                </div>
            </div>
            <div class="form-group">
                <label for="pwd">Password:</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock color-blue"></i></span>
                    <input type="password" class="form-control" id="pwd" name="user_password_new" pattern=".{6,}" placeholder="Password (6+ characters)" required autocomplete="off" />
                    <input type="password" class="form-control" name="user_password_repeat" pattern=".{6,}" required placeholder="Repeat password" autocomplete="off" />
                </div>
            </div>
            <!-- reCAPTCHA v2 -->
            <div class="g-recaptcha" data-sitekey="<?= Config::get('GOOGLE_RECAPTCHA'); ?>"></div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
    <div>
        <p class="note">
            <br />
            A confirmation email will be sent to you, containing a link that you must click to activate your account.
            Some email providers might treat the email as spam, so check your spam folder if you don't receive an e-mail.
        </p>
    </div>
</div>
