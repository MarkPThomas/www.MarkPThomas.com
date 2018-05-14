<?php namespace markpthomas\mountaineering; ?>
<div class="col-xs-4">
    <h1>Password Reset</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <!-- request password reset form box -->
        <form method="post" action="<?= Config::get('URL'); ?>login/requestPasswordReset_action">
            <div class="form-group">
                <label for="user_name_or_email">
                    Enter your username or email and you'll get an email with instructions:
                </label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user color-blue"></i></span>
                    <input type="text" class="form-control" id="user_name_or_email" name="user_name_or_email"
                       placeholder="Username or email" required />
                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                </div>
            </div>
            <label for="captcha">Captcha:</label>
            <div class="form-group">
                <!-- show the captcha by calling the login/showCaptcha-method in the src attribute of the img tag -->
                <img id="captcha" src="<?= Config::get('URL'); ?>register/showCaptcha" class="imageSrc" /><br/>
                <!-- quick & dirty captcha reloader -->
                <a href="#" style="display: block; font-size: 11px; margin: 5px 0 15px 0;"
                   onclick="document.getElementById('captcha').src = '<?= Config::get('URL'); ?>register/showCaptcha?' + Math.random(); return false">Reload Captcha</a>
                <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Enter captcha above" required />
            </div>
            <button type="submit" class="btn btn-primary">Send me a password-reset mail</button>
<!--            <input type="submit" value="Send me a password-reset mail" />-->
        </form>

    </div>
    <div>
        <p class="note">
            <br />
            A password reset email will be sent to you, containing a link that you must click to reset your password.
            Some email providers might treat the email as spam, so check your spam folder if you don't receive an e-mail.
        </p>
    </div>
</div>
