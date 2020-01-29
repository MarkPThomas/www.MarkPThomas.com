<?php namespace markpthomas\mountaineering; ?>
<div class="col-md-9 col-md-push-3">
    <h1 class="page-header">Alaska Reports</h1>
    <?php if (!empty($this->categories)) { ?>
        <ul>
            <?php foreach($this->categories as $cat_id => $category_title){ ?>
                <li>
                    <a href="<?php echo Config::get('URL') . "trip-reports/" . $cat_id ?>"><?php echo $category_title ?></a>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>