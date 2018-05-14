<?php namespace markpthomas\mountaineering; ?>
<div class="col-xs-4">

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div class="login-page-box">
        <div class="table-wrapper">

            <!-- login box on left side -->
            <div class="login-box">
                <h1>Login</h1>
                <form action="<?php echo Config::get('URL'); ?>login/login" method="post">
                    <div class="form-group">
                        <label for="usernamePassword">Username:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user color-blue"></i></span>
                            <input type="text" class="form-control" id="usernamePassword"  name="user_name" placeholder="Username or email" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pwd">Password:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock color-blue"></i></span>
                            <input type="password" class="form-control" id="pwd"  name="user_password" placeholder="Password" required />
                        </div>
                        <label for="set_remember_me_cookie" class="remember-me-label">
                            <input type="checkbox" name="set_remember_me_cookie" class="remember-me-checkbox" />
                            Remember me for 2 weeks
                        </label>
                        <!-- when a user navigates to a page that's only accessible for a logged-in user, then
                             the user is sent to this page here, also having the page he/she came from in the URL parameter
                             (have a look). This "where did you come from" value is put into this form to send the user back
                             there after being logged in successfully.
                             Simple but powerful feature, big thanks to @tysonlist. -->
                        <?php if (!empty($this->redirect)) { ?>
                            <input type="hidden" name="redirect" value="<?php echo $this->encodeHTML($this->redirect); ?>" />
                        <?php } ?>
                        <!--
                          Set CSRF token in login form, although sending fake login requests mightn't be interesting gap here.
                          If you want to get deeper, check these answers:
                            1. natevw's http://stackoverflow.com/questions/6412813/do-login-forms-need-tokens-against-csrf-attacks?rq=1
                            2. http://stackoverflow.com/questions/15602473/is-csrf-protection-necessary-on-a-sign-up-form?lq=1
                            3. http://stackoverflow.com/questions/13667437/how-to-add-csrf-token-to-login-form?lq=1
                        -->
                        <input type="hidden" name="csrf_token" value="<?= Csrf::makeToken(); ?>" />
                    </div>
                    <button type="submit" class="btn btn-primary">Log In</button>
                </form>
                <div class="link-forgot-my-password">
                    <a href="<?php echo Config::get('URL'); ?>login/requestPasswordReset">I forgot my password</a>
                </div>
            </div>

            <!-- register box on right side -->
            <div class="register-box">
                <h2>No account yet ?</h2>
                <a href="<?php echo Config::get('URL'); ?>register/index">Register</a>
            </div>

        </div>
    </div>
</div>
