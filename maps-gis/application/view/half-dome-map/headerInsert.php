<?php namespace markpthomas\gis; ?>

<!-- Leaflet -->
<link rel="stylesheet" href="<?= Config::get('URL'); ?>client/lib/leaflet.css?<?= time(); ?>"/>
<!-- Leaflet - Full Screen -->
<link rel="stylesheet" href="<?= Config::get('URL'); ?>client/lib/Control.FullScreen.css?<?= time(); ?>" type="text/css"/>
<!-- Leaflet - Mini Map -->
<link rel="stylesheet" href="<?= Config::get('URL'); ?>client/lib/Control.MiniMap.css?<?= time(); ?>" type="text/css"/>
<!-- Leaflet - Leaflet Draw -->
<link rel="stylesheet" href="<?= Config::get('URL'); ?>client/lib/leaflet.draw.css?<?= time(); ?>" type="text/css"/>
<!-- Leaflet - Labels -->
<link rel="stylesheet" href="<?= Config::get('URL'); ?>client/lib/leaflet.label.css?<?= time(); ?>" type="text/css"/>


<!-- Custom CSS -->
<link rel="stylesheet" href="<?= Config::get('URL'); ?>client/map.css?<?= time(); ?>" type="text/css"/>