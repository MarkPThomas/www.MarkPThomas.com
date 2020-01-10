<?php namespace markpthomas\main; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-114940440-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-114940440-1');
    </script>

    <!-- reCAPTCHA widget -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="My personal webpage for my professional and mountaineering-related activities, such as architecture, structural engineering, programming, GIS, hiking, rock climbing, ice climbing, skiing, and snowshoeing.">
    <meta name="author" content="Mark Thomas">

    <title>MarkPThomas</title>

    <!--  Browser Icon  -->
    <link rel="shortcut icon" href="<?= Config::get('URL'); ?>favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?= Config::get('URL'); ?>favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= Config::get('URL'); ?>apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= Config::get('URL'); ?>favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= Config::get('URL'); ?>favicon-16x16.png">
    <link rel="manifest" href="<?= Config::get('URL'); ?>site.webmanifest">
    <link rel="mask-icon" href="<?= Config::get('URL'); ?>safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <!-- send empty favicon fallback to prevent user's browser hitting the server for lots of favicon requests resulting in 404s -->
    <link rel="icon" href="data:;base64,=">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!--    <link rel="stylesheet" href="--><?php //echo Config::get('URL'); ?><!--/../../lib/public/css/bootstrap-3.3.7.min.css?--><?php //echo time(); ?><!--">-->

    <!-- Custom Icons -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>
<!--    <link rel="stylesheet" href="--><?//= Config::get('URL') ?><!--/../../lib/public/fonts/fontawesome-free-5.0.8/svg-with-js/js/fontawesome-all.min.js" >-->

    <!-- Custom Toggle Switch -->
    <link rel="stylesheet" href="<?= Config::get('URL'); ?>lib/public/css/bootstrap-toggle.min.css?<?= time(); ?>">

    <!-- Fuel UX -->
    <link rel="stylesheet" href="<?= Config::get('URL'); ?>lib/public/css/fuelux.min.css?<?= time(); ?>">

    <!-- Custom CSS -->
    <!-- Below will override anything listed above -->
    <!--    See: https://stackoverflow.com/questions/12717993/stylesheet-not-updating -->
    <link rel="stylesheet" href="<?= Config::get('URL'); ?>lib/personal/css/navigator.css?<?= time(); ?>">
    <link rel="stylesheet" href="<?= Config::get('URL'); ?>lib/personal/css/styles.css?<?= time(); ?>">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="fuelux">
<!-- Page Content -->
<div class="container">

    <div class="row">