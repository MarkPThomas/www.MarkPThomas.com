// Guided by http://omarriott.com/aux/leaflet-js-non-geographical-imagery/
// and using https://github.com/rktjmp/tileup

var map, myIcon;

var initialize = function () {
  // maximum maxZoom is 18 because it seems that L.CRS.Simple only
  // maps pixels to latLngs as far as that zoom level. minZoom should be
  // >= 0 for the same reason.
  // http://leafletjs.com/reference.html#tilelayer
  
  map = L.map('map', {
  maxZoom: 18,
  minZoom: 14,
  crs: L.CRS.Simple
  }).setView([8192, 4096], 14);

  var southWest = map.unproject([0, 8192], map.getMaxZoom());
  var northEast = map.unproject([16384, 0], map.getMaxZoom());

  map.setMaxBounds(new L.LatLngBounds(southWest, northEast));
  var Attrib = 'Map data &copy; <a href="http://www.markpthomas.com" target="_blank">Mark Porter Thomas</a>';
  var imgURL = 'map_tiles/{z}/map_tile_{x}_{y}.jpg'
  // map_tiles directory not included with repo because map tiles
  // take up ~ 200MB.
  L.tileLayer(imgURL, {
  attribution: Attrib
  }).addTo(map);
  
  //--- Map Controls
    //---Mini Map Control
    //---Uses Plugin
    //TO DO:
    //  1. Adjust JS file to keep map from shifting background image
    //    1a. Plugin creater says this cannot currently be done. He has created an incident for an enhancement for this
    var miniMapParams = new L.TileLayer('map_tiles/9/map_tile_0_0.jpg', {minZoom:  9, maxZoom: 18, attribution: Attrib });
    var miniMap = new L.Control.MiniMap(miniMapParams, {zoomLevelFixed:12, toggleDisplay: true, width: 256, height: 130}).addTo(map);   //autoToggleDisplay: true
    
    //---Full Screen Control
    //---Uses Plugin
    L.control.fullscreen({ position: 'topleft', title: 'Show me the fullscreen !' }).addTo(map); 
    
    //---Scale Control
    //---Uses Plugin
      // Custom scale in meters
      map.addControl(new L.Control.ScaleCustom({
          metric: false,
          imperial: false,
          custom: function(maxMeters, leafletDefaultRoundingFunction) {
            var maxPixels = maxMeters / 5.047214805, 
              pixels;

            if(maxMeters >= 5.047214805) {
              pixels = leafletDefaultRoundingFunction(maxPixels);
            } else {
              pixels = maxPixels > 0.1 ? Math.round(maxPixels * 10) / 10 : Math.round(maxPixels * 100) / 100;
            }

            return {
              caption: pixels + ' m',
              ratio: pixels / maxPixels
            }
          }
        }));
      //Custom scale in feet
      map.addControl(new L.Control.ScaleCustom({
          metric: false,
          imperial: false,
          custom: function(maxMeters, leafletDefaultRoundingFunction) {
            var maxPixels = maxMeters / 1.538391072, 
              pixels;

            if(maxMeters >= 1.538391072) {
              pixels = leafletDefaultRoundingFunction(maxPixels);
            } else {
              pixels = maxPixels > 0.1 ? Math.round(maxPixels * 10) / 10 : Math.round(maxPixels * 100) / 100;
            }

            return {
              caption: pixels + ' ft',
              ratio: pixels / maxPixels
            }
          }
        }));
  };
  
window.onload = function () {
  initialize();
  prepareLayers();
}

