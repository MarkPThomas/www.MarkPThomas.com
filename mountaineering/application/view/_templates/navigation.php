<?php namespace markpthomas\mountaineering; ?>
<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
<!--    <div class="container">-->
<!--    <div class="container-fluid">-->
        <!-- logo -->
<!--        <div class="logo"></div>-->

        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand navbar-brand-logo" href="<?= Config::get('URL') . '../'; ?>">
                <div class="logo">
                    <img src="<?= Config::get('URL'); ?>..\main\favicon-32x32.png">
                </div>
            </a>
            <a class="navbar-brand" href="<?= Config::get('URL'); ?>">Mountaineering Home</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-left">
                <?php if (!empty($this->navItemsStream)) echo $this->navItemsStream; ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if (!empty($this->navLoginStream)) echo $this->navLoginStream; ?>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;</li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
<!--    </div>-->
    <!-- /.container -->
</nav>