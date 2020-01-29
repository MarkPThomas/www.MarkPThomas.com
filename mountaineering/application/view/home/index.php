<?php namespace markpthomas\mountaineering; ?>
<div class="col-md-9 col-md-push-3">
    <h1 class="page-header">Mountaineering</h1>
    <?php $this->renderFeedbackMessages(); ?>
    <p>This is my website for all mountaineering-related things.</p>

    <h2>Original Site</h2>
    <p>Originally I had a website at www.markpthomas.com, but Google has since deleted the site on their cloud storage.
        Fortunately I was already in the process of creating a new site and I had scraped most of the information from there and parsed it into a database for re-use.
        So all of the original trip reports at that site can still be found here either under www.markpthomas.com or www.markpthomas.net.
        The look is different and a bit more static now, but I will flesh out this new design over time to make a much better, more dynamic, and more useful website.</p>

    <h2>Plans</h2>
    <p>The scope of my website has changed. Now it is serving to showcase coding portfolio projects, non-professional projects, and outdoor activities beyond mountaineering.
        Due to this, I will be having multiple  sub-domains within www.markpthomas.net, each corresponding to a major difference in topic. </p>

    <p>This new mountaineering website will have a smoother workflow for creating trip reports. It will have better integration with my photos online.
    It will also be able to sync with other online mountaineering sites. I will be making more useful behavior on this site to facilitate planning climbs,
    coordinating profile data on multiple climbing websites, tracking list completions, etc. As part of this, I will be creating a means for users to log in with profiles so that they can participate in the nice things that I am making!
    So stay tuned...</p>

    Websites that I will be syncing with my site are:
    <div class="container site-logos-list">
            <div>
                <a href="https://www.mountainproject.com" target="_blank" title="MountainProject">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/mountainProject/mountainProject_Logo.svg" alt="mountainProject logo">
                </a>
            </div>
            <div>
                <a href="https://www.summitpost.org" target="_blank" title="SummitPost">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/summitPost/summitPost_Logo.png" alt="summitPost logo">
                </a>
            </div>
            <div>
                <a href="http://www.supertopo.com" target="_blank" title="SuperTopo">
                    <img src="<?= Config::get('URL'); ?>public/img/icons/superTopo/superTopo_Logo.gif" alt="superTopo logo">
                </a>
            </div>
    </div>

    <h2>Current State</h2>
    <p>Currently all that I have ready for public view are my original mountaineering trip reports and articles, and a basic root page for all site sub-domains.
        The main site home page can be found here: <a href="/">www.MarkPThomas.net</a>.
    </p>
</div>