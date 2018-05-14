<?php namespace markpthomas\mountaineering; ?>
    </div>
    <!-- Footer -->
    <footer>
        <div class="row">
            <div class="col-lg-12">
                <hr>
                <p>Copyright &copy; 2018 by Mark Thomas</p>
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