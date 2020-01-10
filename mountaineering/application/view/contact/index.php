<?php namespace markpthomas\mountaineering; ?>
<div class="col-md-9">
    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <h1 class="page-header">Mark P. Thomas</h1>
    <div class="container">
        <div class="row">
            <div class="col-xs-4">
                <img src="public/img/contact-profile.jpg" width="300"/>
            </div>
            <div class="col-xs-8">
                <p>Currently living in Draper, UT.</p>

                <h2>Climbing Sites</h2>
                <ul>
                    <li><a href="http://www.supertopo.com/inc/view_profile.php?dcid=Pj44PTU-OSI,">SuperTopo</a></li>
                    <li><a href="https://www.mountainproject.com/u/106560803">MountainProject</a></li>
                    <li><a href="https://www.summitpost.org/users/pellucidwombat/12893">SummitPost</a></li>
                    <li><a href="http://cascadeclimbers.com/forum/profile/30164-pellucidwombat/">CascadeClimbers</a></li>
                </ul>

                <h2>Contact Info</h2>
                <ul>
                    <li><a href="https://github.com/MarkPThomas">GitHub</a></li>
                    <li><a href="https://www.linkedin.com/in/mark-thomas-599b598">LinkedIn</a></li>
                    <li><a href="https://www.facebook.com/profile.php?id=1234350">Facebook</a></li>
                    <li><a href="https://twitter.com/PellucidWombat">Twitter</a></li>
                    <li><a href="https://www.instagram.com/pellucidwombato/">Instagram</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-1">
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