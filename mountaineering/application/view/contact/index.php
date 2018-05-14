<?php namespace markpthomas\mountaineering; ?>
<div class="col-md-9">
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
                                <input type="text" class="form-control" id="senderName" pattern="[a-zA-Z,. ]{2,64}" name="senderName" placeholder="Name" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="sr-only">Email</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject" class="sr-only">Subject</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-pencil color-blue"></i></span>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder="Subject" required >
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="body" id="body" cols="30" rows="10" placeholder="Message" required ></textarea>
                        </div>
                        <label for="captcha">Captcha:</label>
                        <div class="form-group">
                            <!-- show the captcha by calling the login/showCaptcha-method in the src attribute of the img tag -->
                            <img id="captcha" src="<?= Config::get('URL'); ?>register/showCaptcha" class="imageSrc" />
                            <!-- quick & dirty captcha reloader -->
                            <a href="#" style="display: block; font-size: 11px; margin: 5px 0 15px 0; text-align: center"
                               onclick="document.getElementById('captcha').src = '<?= Config::get('URL'); ?>register/showCaptcha?' + Math.random(); return false">Reload Captcha</a>
                            <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Enter captcha above" required />
                        </div>
                        <input type="submit" name="submit" id="btn-login" class="btn btn-custom btn-lg btn-block" value="Submit">
                    </form>

                </div>
            </div> <!-- /.col-xs-12 -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</div>