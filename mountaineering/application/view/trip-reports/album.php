<?php namespace markpthomas\mountaineering; ?>
<div class="col-md-9">
    <h1 class="page-header"><?= $this->album_title; ?></h1>
    <?php
    // TODO: Work out method of adding multiple albums. Perhaps post returns to 'viewAlbums'?
    // Data is aggregated & after first submit, a button to edit the report.

    if (!empty($this->photos)) { ?>
    <form name="selectPhotos" method="post" action="<?= Config::get('URL'); ?>trip-reports/editReport">
        <input type="hidden" name="album_id" value="<?= $this->album_id; ?>" />
            <?php
            $num_photos = count($this->photos);

            for($i = 0; $i < $num_photos; $i++) {
                $thumb_url 	= $this->photos[$i]["thumb_url"];
                $caption 	= $this->photos[$i]["caption"];
                $pic_id 	= $this->photos[$i]["pic_id"];
                ?>
                <div class="col-md-2 thumb">
                    <img src="<?= $thumb_url; ?>" alt="" title="<?= $caption; ?>" />
                    <input type="hidden" name="<?= $pic_id; ?>" value="0" />
                </div>
                <?php
            }
            ?>
        <hr style="clear: left;"/>
        <button type="submit" name="createReport" id="createReport" class="btn btn-primary" style="float:left; clear: left;">Create Report</button>
        <button type="submit" name="addAlbum" id="addAlbum" class="btn btn-info" style="float:left; clear: left;">Add Another Album</button>
    </form>
    <?php } ?>
</div>