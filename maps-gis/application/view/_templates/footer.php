<?php namespace markpthomas\gis; ?>
    </div>
    <!-- Footer -->
    <footer class="footer">
        <hr>
        <div class="row footer-logo-cr">
            <div class="col-lg-12">
                <div class="footer-content logo">
                    <div class="footer-logo">
                        <p>Copyright &copy; <?= date("Y"); ?> by Mark Thomas</p>
                    </div>
                </div>
            </div>
            <div class="sociallinks-footer">
                <a href="https://www.facebook.com/profile.php?id=1234350" target="_blank" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/facebook/facebook_icon.svg" alt="facebook logo">
                </a>
                <a href="https://www.instagram.com/pellucidwombato/" target="_blank" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/instagram/2993766 - instagram logo media social.png" alt="instagram logo">
                </a>
                <a href="https://twitter.com/PellucidWombat" target="_blank" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/twitter/twitter_icon.svg" alt="twitter logo">
                </a>
                <a href="https://www.youtube.com/channel/UCH8u_4I-6iRoFEZvEb52nlg" target="_blank" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/youTube/YouTube_social_red_squircle_(2017).svg" alt="youtube logo">
                </a>
                <div class="social-icon-wrapper w-inline-block"></div>
                <a href="https://www.linkedin.com/in/mark-thomas-599b598" target="_blank" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/linkedIn/linkedIn_icon.svg" alt="linkedIn logo">
                </a>
                <a href="https://github.com/MarkPThomas" target="_blank" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/gitHub/gitHub_icon.svg" alt="gitHub logo">
                </a>
                <a href="https://stackoverflow.com/users/3341503/pellucidwombat" target="_blank" class="social-icon-wrapper w-inline-block">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/stackOverflow/stackOverflow_icon.png" alt="stackOverflow logo">
                </a>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
    </footer>
</div>
<!-- /.container -->

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    if (!window.jQuery) document.write('<script src="<?= Config::get('URL'); ?>lib/public/js/jquery-3.3.1.min.js"><\/script>');
</script>

<!-- Popper - Used for Bootstrap v4-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>-->

<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    if (typeof $().emulateTransitionEnd != 'function') document.write('<script src="<?= Config::get('URL'); ?>lib/public/js/bootstrap-3.3.7.min.js"><\/script>');
</script>

<!-- Custom Toggle Switch -->
<script src="<?= Config::get('URL'); ?>lib/public/js/bootstrap-toggle.min.js?<?= time(); ?>"></script>

<!-- Fuel UX -->
<script src="<?= Config::get('URL'); ?>lib/public/js/fuelux.min.js?<?= time(); ?>"></script>

<!-- Custom JavaScript -->
<script src="<?= Config::get('URL'); ?>lib/personal/js/scripts.js?<?= time(); ?>"></script>
</body>

</html>