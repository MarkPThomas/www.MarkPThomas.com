<?php namespace markpthomas\main; ?>
<div class="col-md-9">
    <h1 class="page-header">Welcome to my website</h1>
    <?php $this->renderFeedbackMessages(); ?>
    <p>This is my website for coding projects, portfolio projects, outdoor activity blogs and apps, etc.</p>

    <h2>Original Site</h2>
    <p>Originally I had a website at www.markpthomas.com, but Google has since deleted the site on their cloud storage.
        Fortunately I was already in the process of creating a new site and I had scraped most of the information from there and parsed it into a database for re-use.
        So all of the original trip reports at that site can still be found here either under www.markpthomas.com or www.markpthomas.net.
        The look is different and a bit more static now, but I will flesh out this new design over time to make a much better, more dynamic, and more useful website.</p>

    <h2>Plans</h2>
    <p>The scope of my website has changed. Now it is serving to showcase coding portfolio projects, non-professional projects, and outdoor activities beyond mountaineering.
        Due to this, I will be having multiple  sub-domains within www.markpthomas.net, each corresponding to a major difference in topic. </p>

    <p>In fact, the website itself is to be a portfolio project example as I am building it mostly from scratch using the LAMP stack (Linux, Apache, MySQL, PHP).
        By 'mostly' I mean that I am not using major website Content Management System (CMS) frameworks such as WordPress or Drupal to facilitate creating the site.
        Instead, I am building my own CMS system(s) tailored to some unique project ideas. </p>

    <p>Also, for PHP, I am learning best practices by writing the site using the Model-View-Controller pattern, using the Object-Oriented Programming style.</p>

    <p>As the website gets more fully developed, I will be using more JavaScript (e.g. AJAX and Angular) to make the site more interactive.</p>

    <h2>Current State</h2>
    <p>Currently all that I have ready for public view are my original mountaineering trip reports and articles.
        These can be found at the following link or on the navigation ribbon above: <a href="<?= Config::get('URL'); ?>mountaineering">Mountaineering</a>.
    </p>
    <p>I have also begun adding GIS projects in the corresponding sub-domain.</p>
</div>