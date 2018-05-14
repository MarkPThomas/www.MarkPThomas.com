# Half-dome Map

Use [Leaflet](http://leafletjs.com) to zoom in / pan around a [tiled](https://github.com/rktjmp/tileup) 16,614px by 8,406px composite photograph of the northwest face of [Half Dome](https://en.wikipedia.org/wiki/Half_Dome).

## Demo

Interact with overlays that detail climbing routes, share experiences, and offer tips.

See the current version here: [http://www.markpthomas.com/maps-gis/half-dome-map](http://www.markpthomas.com/maps-gis/half-dome-map "Half Dome Interactive Map")

## Development

The tileset is large (~ 200 MB) and so is not here under version control. To obtain a copy of the tileset, contact Mark Thomas or Donny Winston (`markums` or `dwinst` at gmail dot com, respectfully).

## Sources
`Leaflet` is use to display the app. The base tiles were generated and organized from the original photo by using `tileup`, which was dependent on `rmagick` and `ImageMagick`, all of which are Ruby gems.

## Plugins
The following plugins are also used on the client side to enhance the experience in `Leaflet`:


- `Leaflet.AnimatedMarker-master`
- `Leaflet.draw-master`
- `Leaflet.label-master`
- `Leaflet.markercluster-master`
- `leaflet-editable-polyline-master`
- `leaflet-plotter-master`
- `leaflet-plugins-master`
- `OverlappingMarkerSpiderfier-Leaflet-master`