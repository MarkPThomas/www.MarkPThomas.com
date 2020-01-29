<?php
namespace markpthomas\mountaineering;
require_once(Config::get('PATH_VIEW') . '_controls/Form.php');
?>
<div class="col-md-9 col-md-push-3">
    <h1>Edit your avatar</h1>

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div class="box">
        <h3>Upload an Avatar</h3>

        <div class="feedback info">
            If you still see the old picture after uploading a new one: Hard-Reload the page with F5! Your browser doesn't
            realize there's a new image as new and old one have the same filename.
        </div>

        <form action="<?php echo Config::get('URL'); ?>user/uploadAvatar_action" method="post" enctype="multipart/form-data">
            <label for="avatar_file">Select an avatar image from your hard-disk (will be scaled to 44x44 px, only .jpg currently):</label>
<!--            <input type="file" name="avatar_file" required />-->
<!--
<!--            <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />-->
            <?php
            // max size 5 MB (5000000) (as many people directly upload high res pictures from their digital cameras)
            echo Form::uploadFileControl('avatar_file', '', '', null, null, 5) . '<br />';
            ?>
            <input type="submit" value="Upload image" />
        </form>
    </div>

    <div class="box">
        <h3>Delete your avatar</h3>
        <p>Click this link to delete your (local) avatar: <a href="<?php echo Config::get('URL'); ?>user/deleteAvatar_action">Delete your avatar</a>
    </div>
</div>
