<?php namespace markpthomas\mountaineering; ?>
<div class="col-md-9 col-md-push-3">
    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <h1 class="page-header">Mark P. Thomas</h1>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <img src="public/img/contact-profile.jpg" width="300"/>
            </div>
            <div class="col-md-5">
                <p>Trad is rad, ice is nice, and alpine is divine.</p>
                <p>Currently living in Draper, UT.</p>
                <h4>Favorite Climbs</h4>
                <p>
                    Beckey-Chouinard <br/>
                    Serenity-Sons & CPoF & Steck-Salathe <br/>
                    SW Face - Mt Conness <br/>
                    N Chimney - 3 Penguins <br/>
                    Honeymoon Chimney - The Priest <br/>
                    Palisade Crest Traverse <br/>
                    Mithral Dihedral <br/>
                    NWRR of Half Dome <br/>
                    Ptarmigan & Liberty Ridges <br/>
                    N Couloir - N Pk (AI2) <br/>
                </p>
                <h4>Other Interests</h4>
                <p>Alpinism, photography, cycling, endurance rides/hikes/scrambles, whitewater, sea kayaking.</p>

                <h3>Climbing Site Profiles:</h3>
                <a href="https://www.mountainproject.com/user/106560803/mark-p-thomas" target="_blank" title="MountainProject" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/mountainProject/mountainProject_icon.png" alt="mountainProject logo">
                </a>
                <a href="https://www.summitpost.org/users/pellucidwombat/12893" target="_blank" title="SummitPost" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/summitPost/summitPost_icon.png" alt="summitPost logo">
                </a>
                <a href="http://www.supertopo.com/inc/view_profile.php?dcid=Pj44PTU-OSI," target="_blank" title="SuperTopo" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/superTopo/superTopo_icon.png" alt="superTopo logo">
                </a>
                <a href="https://cascadeclimbers.com/forum/profile/30164-pellucidwombat/" target="_blank" title="CascadeClimbers" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/cascadeClimbers/cascadeClimbers_icon.png" alt="cascadeClimbers logo">
                </a>

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