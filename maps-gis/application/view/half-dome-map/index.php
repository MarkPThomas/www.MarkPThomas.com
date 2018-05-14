<?php namespace markpthomas\gis; ?>


<div id="master">
  <div id="banner">
    <h1>&nbsp;Half Dome Interactive Map</h1>
  </div>
  <div id="pageBound">
    <div class="global">
      <p><b class="firstLetter">W</b>elcome to the Half Dome interactive map project! The map is a high resolution photograph covering the Northwest Face of Half Dome, as seen from the summit of North Dome. You can pan around, and zoom in closely enough to see climbers. I've found two parties. Can you find them?</p>
      <p>For the best experience, I <b class="specialBold">highly</b> recommend that you select the <b class="specialBold">'full screen'</b> mode from the navigation menu before exploring.</p>
    </div>
    
    <hr />
    <div id="map">
      <button id="start">Kong it Up!</button>
      <div id="imgLargeDiv">Squirrel!</div> <!-- Image full screen popup div -->
    </div>
    
    <button id="editor">Map Editor <i>(On|Off)</i></button>
    <br/> <br/>
    <div id="editorOutput">
      <button id="getPoints">Get Points</button>
        <div id="textArea">
            <textarea id="pointsTextArea" rows="20" cols="70">
                Layer type and point coordinates will be listed here. (Editor is in EARLY development. Nothing to see here!)
            </textarea>
        </div>
    </div>
    <hr />
    
    <div class="global">
      <h2>Interactive Experience Includes</h2>
      <div class="hr"></div>
      <ul>
        <li>An ability to navigate by <b class="specialBold">mousewheel zoom</b>, and <b class="specialBold">click-and-drag panning</b> (or you can use the navigation menu & keyboard arrows).</li>
        <li>A number of <b class="specialBold">layers</b> to add to the experience, all of which can be turned on or off from the icon on the upper right.</li>
        <li><b class="specialBold">Multiple levels of information</b> that change and increase in detail as you zoom in closer.</li>
        <li><b class="specialBold">Tooltips</b> on many of the labels and icons.</li>
        <li>A <b class="specialBold">mini navigation map</b> in the lower right corner, which can be toggled on & off.</li>
        <li>A <b class="specialBold">scale</b> in the lower left corner that is fairly accurate (give or take a few feet).</li>
        <li><b class="specialBold">Clickable photo icons</b> that have popup thumbnail images of photos taken from the icon location.
          <ul>
            <li>Clustered icons will spread apart on the first click for easier selection (this works in general, but I plan to refine it later).</li>
            <li>Click the photo in the popup to see a full screen version.</li>
          </ul>
        </li>
      </ul>

      <p>You can get beta and a first-person experience of climbing the route by clicking the photos. Between the images and captions, they constitute a trip report of the climb, or for a more standard version of our outing, see the following link to <a href="http://www.markpthomas.com/mountaineering/trip-reports/california/half-dome-regular-nw-face" target="_blank">a version on my website</a>.</p>
      <p>The project is still a beta version, and I have a list of bugs and enhancements to work through, but I'm happy to get feedback to consider as I develop it further. Or if anyone knowledgeable about coding is interested, I am new to Javascript and always happy to get some assistance in understanding things and working through issues. For those interested, the project is on <a href="https://github.com/MarkPThomas/half-dome-map.git" target="_blank">GitHub</a>.</p>

      <h2>Future Possibilities</h2>
      <div class="hr"></div>
      <ul>
          <li>Include <b class="specialBold">additional rock faces</b> in scenes like this.
              <ul>
                  <li>Death Slabs approach aerial view <i>(photos on hand)</i></li>
                  <li>NE Buttress of Higher Cathedral Rock <i>(photos on hand)</i></li>
                  <li>El Capitan!</li>
              </ul>
          </li>
          <li>Enable <b class="specialBold">editing capabilities</b> for users to add their own content.
              <ul>
                  <li>People could draw their own route lines and add their own markers, labels, and photos, basically creating a 'Big Wall Wiki' . . . <b class="specialBold">Crowdsourced topos!</b></li>
              </ul>
          </li>
      </ul>
      
      <h2>Credits</h2>
      <div class="hr"></div>
      <p>First, I want to say that this project would not have been possible without my friend Donny Winston collaborating with me on this. He helped me find a workable solution to the project when I was still working out a method for achieving my idea. He taught me a lot and gave me just the right amount of help to nudge me in the right direction, but without doing the work for me. That level of help let me do the real work to truly learn how to work with Javascript & CSS, and feel that this project came from me and my efforts, without becoming too lost or discouraged.</p>
      <p>Thanks to John Thomas, Scott Berry and Nicole Bouchard for giving me some user GUI feedback with an aesthetic eye. Also, thanks for the coding advice and tips for debugging!</p>
      <p>Of course, I didn't develop this from scratch, but started with other Javascript plugins developed, documented, and shared freely by others. Some of the creators were even nice enough to respond to e-mail inquiries.</p>
      <p><b><i>Sources used:</i></b> <a href="http://leafletjs.com/index.html" target="_blank">Leaflet</a>, <a href="https://github.com/rktjmp/tileup" target="_blank">Tile Up</a>, <a href="https://github.com/Leaflet/Leaflet.draw" target="_blank">Leaflet.draw</a>, <a href="https://github.com/Leaflet/Leaflet.label" target="_blank">Leaflet.label</a>, <a href="https://github.com/brunob/leaflet.fullscreen" target="_blank">Leaflet.Control.FullScreen</a>, <a href="https://github.com/openplans/Leaflet.AnimatedMarker" target="_blank">Leaflet.AnimatedMarker</a>, <a href="https://github.com/jawj/OverlappingMarkerSpiderfier-Leaflet" target="_blank">Leaflet.Spiderfy</a>, <a href="https://github.com/Norkart/Leaflet-MiniMap" target="_blank">Leaflet.MiniMap</a>.
      </p>
    <hr />    
    </div>
    <div id="disqus_thread"></div>
      <script type="text/javascript">
          /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
          var disqus_shortname = 'wwwmarkpthomascom'; // required: replace example with your forum shortname

          /* * * DON'T EDIT BELOW THIS LINE * * */
          (function() {
              var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
              dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
              (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
          })();
      </script>
      <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
      <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
  </div>
</div>

<!-- Misc Scripts -->
<script src="<?= Config::get('URL'); ?>client/lib/underscore-min.js"></script>
<script src="<?= Config::get('URL'); ?>client/lib/jquery-1.11.0.min.js"></script>

<!-- Leaflet -->
<script src="<?= Config::get('URL'); ?>client/lib/leaflet.js"></script>
<!-- Leaflet - Full Screen -->
<script src="<?= Config::get('URL'); ?>client/lib/Control.FullScreen.js?<?= time(); ?>" type="text/javascript"></script>
<!-- Leaflet - Mini Map -->
<script src="<?= Config::get('URL'); ?>client/lib/Control.MiniMap.js?<?= time(); ?>" type="text/javascript"></script>
<!-- Leaflet - Custom Scale -->
<script src="<?= Config::get('URL'); ?>client/lib/leaflet.customscale.js?<?= time(); ?>" type="text/javascript"></script>
<!-- Leaflet - Spiderfy Marker Cluster -->
<script src="<?= Config::get('URL'); ?>client/lib/oms.min.js?<?= time(); ?>" type="text/javascript"></script>
<!-- Leaflet - Leaflet Draw -->
<script src="<?= Config::get('URL'); ?>client/lib/leaflet.draw-src.js?<?= time(); ?>" type="text/javascript"></script>
<!-- Leaflet - Labels -->
<script src="<?= Config::get('URL'); ?>client/lib/leaflet.label.js?<?= time(); ?>" type="text/javascript"></script>
<!-- Leaflet - Animated Marker -->
<script src="<?= Config::get('URL'); ?>client/lib/AnimatedMarker.js?<?= time(); ?>" type="text/javascript"></script>

<!-- Custom Scripts -->
<script src="<?= Config::get('URL'); ?>client/map.js?<?= time(); ?>" type="text/javascript"></script>
<script src="<?= Config::get('URL'); ?>client/layers.js?<?= time(); ?>" type="text/javascript"></script>