<?php namespace markpthomas\mountaineering; ?>
    </div>
    <!-- Footer -->
    <footer class="footer">
        <hr>
            <div class="row footer-logo-cr">
                <div class="col-md-6 col-md-push-7">
                    <div class="row">
                        <div class="col-md-6 sociallinks-footer">
                            <!-- Generic Social Media -->
                            <a href="https://www.facebook.com/profile.php?id=1234350" target="_blank" class="social-icon-wrapper w-inline-block" title="Facebook">
                                <img src="<?= Config::get('URL'); ?>public/img/icons/facebook/facebook_icon.svg" alt="facebook logo">
                            </a>
                            <a href="https://www.instagram.com/pellucidwombato/" target="_blank" class="social-icon-wrapper w-inline-block" title="Instagram">
                                <img src="<?= Config::get('URL'); ?>public/img/icons/instagram/2993766 - instagram logo media social.png" alt="instagram logo">
                            </a>
                            <a href="https://twitter.com/PellucidWombat" target="_blank" class="social-icon-wrapper w-inline-block" title="Twitter">
                                <img src="<?= Config::get('URL'); ?>public/img/icons/twitter/twitter_icon.svg" alt="twitter logo">
                            </a>
                            <a href="https://www.youtube.com/channel/UCH8u_4I-6iRoFEZvEb52nlg" target="_blank" class="social-icon-wrapper w-inline-block" title="YouTube">
                                <img src="<?= Config::get('URL'); ?>public/img/icons/youTube/YouTube_social_red_squircle_(2017).svg" alt="youtube logo">
                            </a>
                            <div class="social-icon-wrapper w-inline-block"></div>
                        </div>
                        <div class="col-md-6 sociallinks-footer">
                            <!-- Specific to sub-site-->
                            <a href="https://www.mountainproject.com/user/106560803/mark-p-thomas" target="_blank" class="social-icon-wrapper w-inline-block" title="MountainProject">
                                <img src="<?= Config::get('URL'); ?>public/img/icons/mountainProject/mountainProject_icon.png" alt="mountainProject logo">
                            </a>
                            <a href="https://www.summitpost.org/users/pellucidwombat/12893" target="_blank" class="social-icon-wrapper w-inline-block" title="SummitPost">
                                <img src="<?= Config::get('URL'); ?>public/img/icons/summitPost/summitPost_icon.png" alt="summitPost logo">
                            </a>
                            <a href="http://www.supertopo.com/inc/view_profile.php?dcid=Pj44PTU-OSI," target="_blank" class="social-icon-wrapper w-inline-block" title="SuperTopo">
                                <img src="<?= Config::get('URL'); ?>public/img/icons/superTopo/superTopo_icon.png" alt="superTopo logo">
                            </a>
                            <a href="https://cascadeclimbers.com/forum/profile/30164-pellucidwombat/" target="_blank" class="social-icon-wrapper w-inline-block" title="CascadeClimbers">
                                <img src="<?= Config::get('URL'); ?>public/img/icons/cascadeClimbers/cascadeClimbers_icon.png" alt="cascadeClimbers logo">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-md-pull-6 footer-content logo">
                    <div class="footer-logo">
                        <p>Copyright &copy; 2018-<?= date("Y"); ?> by Mark P. Thomas</p>
                    </div>
                </div>
            </div>
            <!-- /.col-lg-12 -->
        <!-- /.row -->
    </footer>
</div>
<!-- /.container -->

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    if (!window.jQuery) document.write('<script src="<?= Config::get('URL'); ?>/../../lib/public/js/jquery-3.3.1.min.js"><\/script>');
</script>

<!-- Popper - Used for Bootstrap v4-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>-->

<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    if (typeof $().emulateTransitionEnd != 'function') document.write('<script src="<?= Config::get('URL'); ?>/../../lib/public/js/bootstrap-3.3.7.min.js"><\/script>');
</script>

<!-- Custom Toggle Switch -->
<script src="<?= Config::get('URL'); ?>lib/public/js/bootstrap-toggle.min.js?<?= time(); ?>"></script>

<!-- Fuel UX -->
<script src="<?= Config::get('URL'); ?>lib/public/js/fuelux.min.js?<?= time(); ?>"></script>

<!-- Custom JavaScript -->
<script src="<?= Config::get('URL'); ?>lib/personal/js/scripts.js?<?= time(); ?>"></script>
</body>

</html>