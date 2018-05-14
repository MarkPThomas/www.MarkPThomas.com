<?php namespace markpthomas\mountaineering; ?>
<div class="col-md-9">

    <h1>Verification</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <a href="<?= Config::get('URL'); ?>">Go back to home page</a>
    </div>

</div>
