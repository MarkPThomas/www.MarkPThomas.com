<?php namespace markpthomas\gis; ?>
<div class="col-md-9">
    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <h1 class="page-header">Mark P. Thomas</h1>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <img src="public/img/contact-profile.png" width="300"/>
            </div>
            <div class="col-md-5">
                <p>Experienced Structural Engineer and Software Engineer with a demonstrated history of working in the structural engineering & computer software industry.
                    Skilled in SAP2000, ETABS, WPF, C#, LaTeX, and HTML. </p>
                <p>Strong engineering professional with a M.Eng. focused in Structural Engineering from University of California, Berkeley.</p>
                <p>Bachelor's of Arts in Architecture also from University of California, Berkeley.</p>
                <p>Also have a history of teaching various topics ranging from steel design to winter mountaineering and skiing.</p>
                <p>Currently working remotely while living in Draper, UT</p>

                <h3>Social Media Profiles:</h3>
                <a href="https://www.facebook.com/profile.php?id=1234350" target="_blank" title="Facebook" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/facebook/facebook_icon.svg" alt="facebook logo">
                </a>
                <a href="https://www.instagram.com/pellucidwombato/" target="_blank" title="Instagram" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/instagram/2993766 - instagram logo media social.png" alt="instagram logo">
                </a>
                <a href="https://twitter.com/PellucidWombat" target="_blank" title="Twitter" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/twitter/twitter_icon.svg" alt="twitter logo">
                </a>
                <a href="https://www.youtube.com/channel/UCH8u_4I-6iRoFEZvEb52nlg" target="_blank" title="YouTube" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/youTube/YouTube_social_red_squircle_(2017).svg" alt="youtube logo">
                </a>

                <h3>Professional Profiles:</h3>
                <a href="https://www.linkedin.com/in/mark-thomas-599b598" target="_blank" title="LinkedIn" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/linkedIn/linkedIn_icon.svg" alt="linkedIn logo">
                </a>
                <a href="https://github.com/MarkPThomas" target="_blank" title="GitHub" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/gitHub/gitHub_icon.svg" alt="gitHub logo">
                </a>
                <a href="https://stackoverflow.com/users/3341503/pellucidwombat" target="_blank" title="Stack Overflow" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/stackOverflow/stackOverflow_icon.png" alt="stackOverflow logo">
                </a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row contact-form">
            <div class="col-md-6 col-md-offset-1">
                <hr>
                <div class="form-wrap">
                    <h1>Contact</h1>
                    <form role="form" action="<?= Config::get('URL'); ?>contact/contact" method="post" id="login-form" autocomplete="off">
                        <div class="form-group">
                            <label for="name" class="sr-only">Name</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user color-blue"></i></span>
                                <?php
                                // Auto-fill field if it had values from a prior submission and clear the session placeholder. Otherwise, create empty field.
                                $fromName = Session::get('contact_name');
                                if (empty($fromName)){
                                    $valueAttribute = '';
                                } else {
                                    $valueAttribute = ' value="' . $fromName[0] . '" ';
                                    Session::set('contact_name', null);
                                }

                                echo '<input type="text" class="form-control" id="senderName" pattern="[a-zA-Z,. ]{2,64}" name="senderName" placeholder="Name" ' . $valueAttribute . 'required />';

                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="sr-only">Email</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                                <?php
                                // Auto-fill field if it had values from a prior submission and clear the session placeholder. Otherwise, create empty field.
                                $fromEmail = Session::get('contact_email');
                                if (empty($fromEmail)){
                                    $valueAttribute = '';
                                } else {
                                    $valueAttribute = ' value="' . $fromEmail[0] . '" ';
                                    Session::set('contact_email', null);
                                }

                                echo '<input type="email" name="email" id="email" class="form-control" placeholder="Email" ' . $valueAttribute . 'required />';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject" class="sr-only">Subject</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-pencil color-blue"></i></span>
                                <?php
                                // Auto-fill field if it had values from a prior submission and clear the session placeholder. Otherwise, create empty field.
                                $subject = Session::get('contact_subject');
                                if (empty($subject)){
                                    $valueAttribute = '';
                                } else {
                                    $valueAttribute = ' value="' . $subject[0] . '" ';
                                    Session::set('contact_subject', null);
                                }

                                echo '<input type="text" name="subject" id="subject" class="form-control" placeholder="Subject" ' . $valueAttribute . 'required />';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php
                            // Auto-fill field if it had values from a prior submission and clear the session placeholder. Otherwise, create empty field.
                            $body = Session::get('contact_body');
                            if (empty($body)){
                                $valueAttribute = '';
                            } else {
                                $valueAttribute = $body[0];
                                Session::set('contact_body', null);
                            }
                            echo '<textarea class="form-control" name="body" id="body" cols="30" rows="10" placeholder="Message" required >' . $valueAttribute . '</textarea>';
                            ?>
                        </div>

                        <!-- reCAPTCHA v2 -->
                        <div class="g-recaptcha" data-sitekey="<?= Config::get('GOOGLE_RECAPTCHA'); ?>"></div>
                        <br />
                        <input type="submit" name="submit" id="btn-login" class="btn btn-primary" value="Submit">
                    </form>

                </div>
            </div> <!-- /.col-xs-12 -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</div>