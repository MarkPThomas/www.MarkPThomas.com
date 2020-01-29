<?php namespace markpthomas\mountaineering; ?>
<div class="col-md-9 col-md-push-3">
    <h1 class="page-header">Photo Albums</h1>
    <!-- TODO: Make control to sort page alphabetically vs. by date -->
    <?php if (!empty($this->albums)) { ?>
    <div class="container-fluid">
        <?php foreach($this->albums as $album){ ?>
        <div class="col-md-3 previewAlbum">
            <h3><?= $album->title; ?></h3>
            Added: <?= $album->formatted_date; ?><br />
            <a href="album/<?= $album->id; ?>">
                <img src="<?= $album->thumb; ?>" alt="No Image Available" />
            </a>
            <br />
        </div>
        <?php } ?>
    <?php } ?>
    </div>
</div>