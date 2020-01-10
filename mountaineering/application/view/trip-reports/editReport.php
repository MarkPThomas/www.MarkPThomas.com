<?php namespace markpthomas\mountaineering; ?>
<div class="col-md-9">
    <h1 class="page-header">Create/Edit Trip Report</h1>
    <?php
    // TODO: Add albums section
    // TODO: Add links section
    // TODO: Add header level selection
    // TODO: For controls, handle custom vs. auto captions.
    // TODO: Add controls with add/remove/hide options.
    // TODO: Load data for editing from database
    // TODO: Update data in database
    // TODO: Add new report in database.

    if (!empty($this->photosDisplaySize)) { ?>
    <form name="selectPhotos" method="post" action="<?= Config::get('URL'); ?>trip-reports/displayAlbum">
        <!-- Title -->
        <div class="form-group">
            <label for="title" class="sr-only">Title</label>
            <input class="form-control" type="text" name="title" id="title" placeholder="Title" required >
        </div>

        <!-- Album description or introduction -->
        <div class="form-group" id="contentGroup0">
            <label for="header0" class="sr-only">Header</label>
            <input class="form-control" type="text" name="header0" id="header0" placeholder="Header (optional)" >

            <label for="body0" class="sr-only">Body</label>
            <?php if ($this->subtitle) { ?>
            <textarea class="form-control" name="body0" id="body0" cols="30" rows="10" placeholder="Content (optional)" ><?= $this->subtitle; ?></textarea>
            <?php } else { ?>
            <textarea class="form-control" name="body0" id="body0" cols="30" rows="10" placeholder="Content (optional)" ></textarea>
            <?php } ?>
        </div>
        <hr />

        <?php
        // TODO: Much of this processing should be done in the Piwigo model into a form identical to when loading data from the database to edit.
        // See report.php for a starting point on how to refine this file.

        $num_photos = count($this->photosDisplaySize);

        for($i = 0; $i < $num_photos; $i++) {
            $j = $i + 1;

            $img_url 	= $this->photosDisplaySize[$i]["img_url"];
            $img_urlFullSize = $this->photosFullSize[$i]["img_url"];
            $caption 	= trim($this->photosDisplaySize[$i]["caption"]);

            $video_url = '';
            $video_caption = '';

            if($this->picIdsSelection[$i] == '1' && !empty($img_url)) {
                ?>
                <div class="form-group" id="contentGroup<?= $j; ?>">
                    <!-- Header -->
                    <input class="form-control" type="text" name="header<?= $j; ?>" id="header<?= $j; ?>" placeholder="Header (optional)" >

                    <!-- Image -->
                    <div class="imageGroup">
                        <a href="<?= Url::getAbsolute($img_urlFullSize); ?>">
                            <img src="<?= Url::getAbsolute($img_url); ?>"  width="500"  class="imageSrc" />
                        </a>
                        <!-- Image Caption -->
                        <?php if (!empty($caption)) { ?>
                        <textarea class="form-control" name="caption<?= $j; ?>" id="caption<?= $j; ?>" cols="30"><?= $caption; ?></textarea>
                        <?php } else { ?>
                        <textarea class="form-control" name="caption<?= $j; ?>" id="caption<?= $j; ?>" cols="30" placeholder="Image Caption (optional)"></textarea>
                    <?php } ?>
                    </div>

                    <!-- Video -->
                    <div class="videoGroup">
                        <iframe src="<?= Url::getAbsolute($video_url); ?>" class="videoSrc myIframe"></iframe>
                        <!-- Video Caption -->
                        <?php if (!empty($video_caption)) { ?>
                            <textarea class="form-control" name="caption<?= $j; ?>" id="caption<?= $j; ?>" cols="30"><?= $video_caption; ?></textarea>
                        <?php } else { ?>
                            <textarea class="form-control" name="caption<?= $j; ?>" id="caption<?= $j; ?>" cols="30" placeholder="Video Caption (optional)"></textarea>
                        <?php } ?>
                    </div>

                    <!-- Body Text -->
                    <textarea class="form-control" name="body<?= $j; ?>" id="body<?= $j; ?>" cols="30" rows="10" placeholder="Content (optional)" ></textarea>
                </div>
                <hr />
                <?php
            }
        }
        ?>

        <!-- Album references -->
        <?php if (!empty($this->albums) && count($this->albums) > 0){ ?>
            <div class="album">
                <h2>Albums:</h2>
                <ul>
                    <?php foreach ($this->albums as $album){ ?>
                        <li><a href="<?= Url::getAbsolute($album['url']); ?>"><?= $album['title']; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <!--Link references-->
        <?php if (!empty($this->externalLinks) && count($this->externalLinks) > 0){ ?>
            <div class="linkExternal">
                <h2>External Links:</h2>
                <ul>
                    <?php foreach ($this->externalLinks as $externalLink){ ?>
                        <li><a href="<?= $externalLink['website_URL']; ?>"><?= $externalLink['name']; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <?php if (!empty($this->internalLinks) && count($this->internalLinks) > 0){ ?>
            <div class="linkInternal">
                <h2>Internal Links:</h2>
                <ul>
                    <?php foreach ($this->internalLinks as $internalLink){ ?>
                        <li>
                            <a href="<?= Url::getAbsolute($internalLink['website_URL']); ?>">
                                <?= $internalLink['name']; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php }?>
        <button type="submit" name="submit" id="submit" class="btn btn-primary">Submit</button>
    </form>
    <?php } ?>
</div>