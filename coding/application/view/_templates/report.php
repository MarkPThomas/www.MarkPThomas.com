<?php namespace markpthomas\mountaineering; ?>
<?php
if (!empty($this->bodies)){
    for($i = 0; $i < count($this->bodies); $i++){ ?>
    <div>
    <!-- Add Header -->
    <?php if (!empty($this->bodies[$i]['header_type']) && !empty($this->bodies[$i]['header_value']))
    {
        $headerType = strtolower($this->bodies[$i]['header_type']);
        $headerClass = ($headerType === 'h1')? 'page-header' : '';

        echo '<' . $headerType . ' class="' . $headerClass . '">' . $this->bodies[$i]['header_value'] . '</' . $headerType . '>';
    }
    ?>

    <!-- Add Image -->
    <?php if (!empty($this->bodies[$i]['report_photo']['photo']['url'])) { ?>
        <div class="imageGroup">
            <img src="<?= Url::getAbsolute($this->bodies[$i]['report_photo']['photo']['url']); ?>" width="500"  class="imageSrc" />

        <!-- Add Image Caption -->
        <?php if (!empty($this->bodies[$i]['report_photo']['photo']['caption'])) { ?>
            <div class="caption"><?= $this->bodies[$i]['report_photo']['photo']['caption']; ?></div>
        <?php } ?>
        </div>
    <?php } ?>

    <!-- Add Video -->
    <?php if (!empty($this->bodies[$i]['report_video']['video']['url'])) { ?>
        <div class="videoGroup">
        <iframe src="<?= Url::getAbsolute($this->bodies[$i]['report_video']['video']['url']); ?>" class="videoSrc myIframe"></iframe>

        <!-- Add Video Caption -->
        <?php if (!empty($this->bodies[$i]['report_video']['video']['caption'])) {?>
            <div class="caption"><?= $this->bodies[$i]['report_video']['video']['caption']; ?></div>
        <?php } ?>
        </div>
    <?php } ?>

    <!-- Add Text -->
    <?php if (!empty($this->bodies[$i]['text_body']) ) { ?>
        <div class="text"><p><?= $this->bodies[$i]['text_body']; ?></p></div>
    <?php }?>
    </div>
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