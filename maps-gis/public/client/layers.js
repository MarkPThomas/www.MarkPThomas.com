//NEXT MAIN TO DOS:
//  1. Keep popup from appearing off the edge of the screen.
//  2. Spiderfy issue is back. See line 707
//  4. Make presentation page layout
//  6. re-cut photos
//    6a. Maybe make second background from illustrations
//  7. Put online!

//General TO DOs:
//  1. Convert various arrays into GeoJASON objects
//  2. Leaflet draw 
//    2a. Finish marker draw on double click? At least to store values w/o needing to enter edit mode. Sometimes other shapes don't finish drawing.
//    2b. Get working with deletions
//  3. Donkey Kong "barrelCount" is not iterating up from 0 so barrels are only going on one of two paths for each side.
//  4. Z-index is not constant for certain layers. Would like to get total control over this.
//  5. Spiderfy 
//    5a. Animate to make spread more smooth. Perhaps have different icon to highlight spiderfied icons better
//    5b. Trigger by mouseover instead of click


//Function TO DOs:
//  1.  Create some more functions for generalizing further the creation of various icons? 
//  2.  Split this file into several that more closely match specific functions?

//project takes lat/long coordinates and projects them back to pixel coordinates


// var project = function (latlng) {
  // if (latlng.lat) { // single latlng
    // return map.project(latlng, map.getMaxZoom());
  // } else { // latlng is Array
    // return _.collect(latlng, function (latlng) {
      // return map.project(latlng, map.getMaxZoom()); 
    // });
  // }
// };

// var unproject = function (points) {
  // if (points.x) { // single points
    // return map.unproject(points, map.getMaxZoom());
  // } else { // points is Array
    // return _.collect(points, function (points) {
      // return map.unproject(points, map.getMaxZoom()); 
    // });
  // }
// };

// var unproject = function (points) {
  // return map.unproject(points, map.getMaxZoom());
// };

var imagesDir = 'images/';

var project = function (latlng) {
  return map.project(latlng, map.getMaxZoom());
//  _.collect(projectList, function (latlng) {
//    return project(latlng);
//  })
};

var unproject = function (points) {
  return map.unproject(points, map.getMaxZoom());
//  _.collect(unprojectList, function (points) {
//    return unproject(points);
//  })
};

// var unprojectList = function (points) {
  // return unproject(points); 
// };

_marker = function (points, options) {
  options = _.extend({icon: myIcon}, options);
  return L.marker(unproject(points), options);
};

_circle = function (center, radius, options) {
  options = _.extend({}, options);
  return L.circle(unproject(center), radius, options);
};

_polygon = function (unprojectList, options) {
  options = _.extend({}, options);
  // return L.polygon(unproject(unprojectList(points)), options);
  // return L.polygon(unprojectList(points), options);
  return L.polygon(_.collect(unprojectList, function (points) {
    return unproject(points); 
  }), options);
};

_polyline = function (unprojectList, options) {
  options = _.extend({}, options);
  // return L.polyline(unproject(unprojectList(points)), options);
  // return L.polyline(unprojectList(points), options);
  return L.polyline(_.collect(unprojectList, function (points) {
    return unproject(points); 
  }), options);
};

//Takes coordinates and writes them in the text area
var coordsPix = [];
var myLayerType = [];
var listPointsType = function(coordsPix) {
  var pointsTextArea = document.getElementById('pointsTextArea');
  pointsTextArea.innerHTML = '';
  console.log(coordsPix);
  //TO DO: Consider array length for when one type ends and the check should be redone. This will retitle each section by the appropriate type
 if (coordsPix[0].type === 'marker') {
    pointsTextArea.innerHTML  += coordsPix[0].type + '\n'; 
                                  +   '\n';
      for (var i = 0; i <= coordsPix.length; i++){
        pointsTextArea.innerHTML  += coordsPix[i].x + ', ' + coordsPix[i].y + '\n'; 
                                  +   '\n';
      }
      // pointsTextArea.innerHTML  += '\n';
  } else if (coordsPix[0].type === 'circle'){
    // alert ('circle');
    pointsTextArea.innerHTML  += coordsPix[0].type + '\n'; 
                                  +   '\n';
      for (var i = 0; i <= coordsPix.length; i++){
        pointsTextArea.innerHTML  += '[' + coordsPix[i].x + ', ' + coordsPix[i].y + '], ' + coordsPix[i].r + '\n'; 
                                  +   '\n';
      }
  } else if (coordsPix[0][0].type !== 'circle' && coordsPix[0][0].type !== 'marker'){
    // alert('poly');
    pointsTextArea.innerHTML  += coordsPix[0][0].type + '\n'; 
                                  +   '\n';
      for (var i = 0; i <= coordsPix[0].length; i++){
        pointsTextArea.innerHTML  += coordsPix[0][i].x + ', ' + coordsPix[0][i].y + '\n'; 
                                  +   '\n';
      }      
  } else {  
    alert ('no type');
  }
}; 
$(function() {
  $('#getPoints').click(function() {
    listPointsType(coordsPix);
  });
});

//Shows/hides editor from button click
var drawControl;
var toggle;
$(function() { 
$('#editor').click(function() {
    if (toggle) {
      $('#editorOutput').fadeOut();
      map.removeControl(drawControl);
      toggle = false;
    } else {
      $('#editorOutput').fadeIn();
      $("#editorOutput").css({
          "display":"inline-block"
        });
      map.addControl(drawControl);
      toggle = true;
    }
  });
});
 
//Function scales icons and their anchor point. Applies to images & divs
var scaleIcon = function(scale, myiconSize, iconAnchorSize){
            j = Math.round(myiconSize[0]*scale);
            k = Math.round(myiconSize[1]*scale);
            l = Math.round(iconAnchorSize[0]*scale);
            m = Math.round(iconAnchorSize[1]*scale);
  return  [ scaledIconSize = [j, k],
            scaledAnchorSize = [l, m]
            ];
};

//Functions to open & close custom floating div
  var openDiv = function(){
    $("#imgLargeDiv").fadeIn({
    });
  };

  var closeDiv = function(){
    $("#imgLargeDiv").fadeOut({
    });
  };

//TO DO: 
//  1. Make map behind photo 'hidden' from div clicks.
//  2. Fix problem where if changing between normal & full screen, the img.src changes to appending the client path location before the URL!
var myCaptions = [];
var photoURL = [];
var photoNum;
var photoEnlarge = function(photoURL, photoNum, myCaptions){
    var img = new Image();
    var widthPercent, heightPercent, photoSvalue;
    openDiv();
    img.src = photoURL[photoNum][0]+photoURL[photoNum][1]+photoURL[photoNum][2]; 
    // alert(img.src);
    
    img.onload = function() {
    //Gather photo & div properties for scaling & determining aspect ratios
      var photoWidth = this.width;
      // alert("photoWidth is " + photoWidth);
      var photoHeight = this.height;
      // alert("photoHeight is " + photoHeight);
      var divWidth = $("#map").innerWidth();  //Gets inside pixel width of div
      // alert("divWidth is " + divWidth);
      var divHeight = $("#map").innerHeight();  //Gets inside pixel width of div
      // alert("divHeight is " + divHeight);
      var aspectRatioPhoto = photoWidth/photoHeight;
      // alert("aspectRatioPhoto is " + aspectRatioPhoto);
      var aspectRatioDiv = divWidth/divHeight;
      // alert("aspectRatioDiv is " + aspectRatioDiv);
      var photoDivWidth, photoDivHeight;
      var widthPercent = 0.97; //Resize bounding div to corresponding max % (%units)
      var heightPercent = 0.94; //Resize bounding div to corresponding max % (%units)
      var padding = 2*6;
   
      var dimensioning = function(orientation) {
        // alert("Orientation is " + orientation);
        if (orientation === "landscape") {                
        //Check aspect ratio compatibility   
          if (aspectRatioPhoto >= aspectRatioDiv){   
            photoSvalue = Math.round(widthPercent*divWidth - padding);   //Photo S scale value adjusted based on div size
          } else {  
            photoSvalue = Math.round((heightPercent*divHeight - padding)*aspectRatioPhoto);   //Photo S scale value adjusted based on div size
          }
          photoDivWidth = Math.round(photoSvalue + padding);        
          photoDivHeight = Math.round(photoDivWidth/aspectRatioPhoto);  
           
        } else {   //orientation is "portrait"                                       
        //Check aspect ratio compatibility         
          if (aspectRatioPhoto <= aspectRatioDiv){  //Check aspect ratio compatibility  
            photoSvalue = Math.round(heightPercent*divHeight - padding);  
          } else {  
            photoSvalue = Math.round(widthPercent*divWidth/aspectRatioPhoto - padding);
            alert("photoSvalue4 is " + photoSvalue);
          }
          photoDivWidth = Math.round(photoSvalue*aspectRatioPhoto + padding);
          photoDivHeight = Math.round(photoSvalue + padding); 
        }
      };    

     //Determine photo landscape vs. portrait & dimension photo & div accordingly
      if (photoWidth > photoHeight) { //Photo is landscape
        dimensioning("landscape");
      } else {
        dimensioning("portrait");
      };
      
    //Size div
      $("#imgLargeDiv").css({
         "width": photoDivWidth,
         "height": photoDivHeight
      });
    
    //Create new photo URL
      photoURL[photoNum] = photoURL[photoNum][0] + photoSvalue + photoURL[photoNum][2];
    //Inject new photo URL into div
      document.getElementById('imgLargeDiv').innerHTML = '<img id="imgLargeHTML" src="' + photoURL[photoNum] + '" /><a href="javascript:void(closeDiv())"><div id="closeDiv">&#215;</div></a><div id="imgLargeCaption">' + myCaptions[photoNum] + '</div>';          
    
    //Resize caption div
    divHeight = $("#imgLargeCaption").innerHeight();
    if (divHeight > 40) {
    $("#imgLargeCaption").css({
        "height": "40px"
      });
    };
    divHeight = $("#imgLargeCaption").innerHeight();
    // alert(divHeight);
    var lineHeight = 14;
    // alert("divHeight is " + divHeight);
    // alert("lineHeight is " + lineHeight);
    var divCaptionOffset = ((Math.round(divHeight)/lineHeight)-1)*lineHeight + 2
    // alert("divCaptionOffset is " + divCaptionOffset);
    var bottomCSS = divCaptionOffset + "px"
    // alert("bottomCSS is " + bottomCSS);
    $("#imgLargeCaption").css({
         "bottom": bottomCSS
      });
    };  
  };    
  
var prepareLayers = function () {
 //-------Blank icon to hide icons-------
  var myIconBlank = L.icon({  
    iconUrl: imagesDir + 'blank' + '.png',
    iconSize: [1,1],
    iconAnchor: [1,1]
  });
  
  //--------Belay Icons----------
  //Belay coordinates in image
  var belayCoords = [];
      belayCoords[0] = [4162, 6600];
      belayCoords[1] = [4080, 6230];
      belayCoords[2] = [4050, 5940];
      belayCoords[3] = [3986, 5460];
      belayCoords[4] = [4026, 5232];
      belayCoords[5] = [3892, 4844];
      belayCoords[6] = [3890, 4380];
      belayCoords[7] = [4026, 4048];
      belayCoords[8] = [4182, 3872];
      belayCoords[9] = [4284, 3736];
      belayCoords[10] = [4426, 3580];
      belayCoords[11] = [4630, 3144];
      belayCoords[12] = [4652, 2838];
      belayCoords[13] = [4664, 2646];
      belayCoords[14] = [4746, 2370];
      belayCoords[15] = [4918, 1990];
      belayCoords[16] = [5212, 1828];  //Big Sandy
      belayCoords[17] = [5226, 1530];
      belayCoords[18] = [5270, 1250];
      belayCoords[19] = [5308, 1016];
      belayCoords[20] = [5104, 884];
      belayCoords[21] = [4966, 760];
      belayCoords[22] = [4840, 574];                   
    
    //Belay Icon formation types for different zoom levels
    var myiconSize = [100, 70];     //Original icon size
    var iconAnchorSize = [20, 50];    //Original icon anchor location within icon
    
    var myIconBelaySmall = [];  //For zoom = 14
      for (i=0; i < belayCoords.length; i++){
      myIconBelaySmall[i] = L.icon({
        iconUrl: imagesDir + 'marker-icon-belay0' + '.png',
        iconSize: [8,8],
        iconAnchor: [4,4],
        className: 'myIconBelay' + (i + 1)
      });
      }
    
    var myIconBelay = [];  //For 14 < zoom < 17
      var data = scaleIcon(0.25, myiconSize, iconAnchorSize);
      for (i=0; i < belayCoords.length; i++){
      myIconBelay[i] = L.icon({
        iconUrl: imagesDir + 'marker-icon-belay' + (i + 1) + '.png',
        iconSize: [scaledIconSize[0],scaledIconSize[1]],
        iconAnchor: [scaledAnchorSize[0],scaledAnchorSize[1]],
        className: 'myIconBelay' + (i + 1)
      });
      }

    var myIconBelayLarge = [];   //For zoom >= 17
      var data = scaleIcon(0.5, myiconSize, iconAnchorSize);
      for (i=0; i < belayCoords.length; i++){
      myIconBelayLarge[i] = L.icon({
        iconUrl: imagesDir + 'marker-icon-belay' + (i + 1) + '.png',
        iconSize: [scaledIconSize[0],scaledIconSize[1]],
        iconAnchor: [scaledAnchorSize[0],scaledAnchorSize[1]],
        className: 'myIconBelay' + (i + 1)
      });
      }
      
    //Constructing all belay markers w/ position
    var belays = [];
    for (i=0; i < belayCoords.length; i++){
      belays[i] = _marker(belayCoords[i], {icon:myIconBelaySmall[i], clickable:false, title:'Belay ' + (i + 1), zIndexOffset:1000});
    }
  
  //--------Bolts---------- 
  //------------ Perhaps use for roughly showing bolts for bolt ladders & TT anchors?      
    var boltCoords = [];
      boltCoords[0] = [4063, 5871.125];
      boltCoords[1] = [4065, 5850.125];
      boltCoords[2] = [4066, 5829.125];
      boltCoords[3] = [4067, 5806.125];
      boltCoords[4] = [4058, 5788.125];
      boltCoords[5] = [4058, 5762.125];
      boltCoords[6] = [4185, 3853.125];
      boltCoords[7] = [4191, 3839.125];
      boltCoords[8] = [4198, 3823.125];
      boltCoords[9] = [4204, 3806.125];
      boltCoords[10] = [4209, 3786.125];
      boltCoords[11] = [4214, 3763.125];
      boltCoords[12] = [4216, 3750.125];
      boltCoords[13] = [5081, 876.125];
      boltCoords[14] = [5076, 861.125];
      boltCoords[15] = [5071, 843.125];
      boltCoords[16] = [5067, 827.125];
      boltCoords[17] = [5063, 808.125];
      boltCoords[18] = [5055, 788.125];
      boltCoords[19] = [5007, 828.125];
      boltCoords[20] = [5009, 813.125];
      boltCoords[21] = [5007, 796.125];
      boltCoords[22] = [5007, 781.125];
 
    var bolts = [];
      for (i=0; i < boltCoords.length; i++){
        bolts[i] = _circle(boltCoords[i], 2, {
          color: 'red',
          weight: 4,
          fillColor: '#f03',
          fillOpacity: 0.5,
          clickable:false
        });
      }
  
  //--------Labels Icons---------- 
    //===Labels===
    //Defining label coordinates, anchor offset, and text. There are some redundant labels in different arrays_ 
    //  created for dealing with inconsistent style changes, for some labels to be visible on a different zoom level
    var myLabels = [];
        myLabels[0] = new Array([4297, 3807], [9, 25], "Robbins Traverse", "Tension traverse, face climbing traverse. Crux of route (mandatory free, or aid)");
        myLabels[1] = new Array([4526, 3280.125], [9, 25], "The Chimneys", "4 shorter pitches link into 2 great mega-pitches. Not too tough for Yosemite chimneys.");
        myLabels[2] = new Array([5035, 1916.125], [9, 25], "Double Crack", "Best crack pitch on the route! Very exposed.");
        myLabels[3] = new Array([5293, 1851.125], [9, 25], "Big Sandy Ledge", "Where's the sand? Comfortably sleeps a few.");
        myLabels[4] = new Array([5301, 1477.125], [9, 25], "Zig Zags", "Zig-zagging flake corner system.");
        myLabels[5] = new Array([5217, 940.125], [9, 25], "Thank God Ledge", "Named such by the FAs because it allowed a last-minute detour around the Visor overhang.");
        myLabels[6] = new Array([5127, 524.125], [9, 25], "The Visor", "Big overhang.");
        myLabels[7] = new Array([4900, 400], [9, 25], "Tourists", "Photo-op moment.");
        myLabels[8] = new Array([3500, 2150.25], [9, 25], "The Cables Route", "Scariest part of the climb");
        myLabels[9] = new Array([2068, 2344.5], [9, 25], "The Shoulder", "");
        myLabels[10] = new Array([892, 4416.5], [9, 25], "NW Face Approach from Trail", "Tough to follow in the dark. Starts at the end of the first switchback that climbs the shoulder");
        myLabels[11] = new Array([1504, 5224.5], [9, 25], "Water bottle, sunglasses & hats graveyard", "Items dropped from the Cables Route end up here");
        myLabels[12] = new Array([6228, 7988.5], [9, 25], "NW Face Approach from Death Slabs", "Not too bad");
        myLabels[13] = new Array([4284, 7380.5], [9, 25], "Good bivvies", "Stay in the trees. Even there, falling objects could reach you.");
        myLabels[14] = new Array([1952, 5220.5], [9, 25], "Big Lone Pine Tree", "If you pass by this, you are on route for the linkup to/from the trail.");
        myLabels[15] = new Array([4500, 430], [9, 25], "8,839 ft", "");
        myLabels[16] = new Array([4432, 7138], [9, 25], "~7,000 ft", "");
   
    var myLabelsFar = [];
        myLabelsFar[0] = new Array([4400, 4200], [9, 25], "Robbins Traverse", "Tension traverse, face climbing traverse. Crux of route (mandatory free, or aid)");
        myLabelsFar[1] = new Array([4000, 3000], [9, 25], "The Chimneys", "4 shorter pitches link into 2 great mega-pitches. Not too tough for Yosemite chimneys.");
        myLabelsFar[2] = new Array([5500, 2000], [9, 25], "Big Sandy Ledge", "Where's the sand? Comfortably sleeps a few.");
        myLabelsFar[3] = new Array([5600, 1477.125], [9, 25], "Zig Zags", "Zig-zagging flake corner system.");
        myLabelsFar[4] = new Array([3300, 1550.25], [9, 25], "The Cables Route", "Scariest part of the climb");
        myLabelsFar[5] = new Array([1700, 2344.5], [9, 25], "The Shoulder", "");
        myLabelsFar[6] = new Array([1000, 4416.5], [9, 25], "NW Face Approach from Trail", "Tough to follow in the dark. Starts at the end of the first switchback that climbs the shoulder");
        myLabelsFar[7] = new Array([6228, 7600], [9, 25], "NW Face Approach from Death Slabs", "Not too bad");
        myLabelsFar[8] = new Array([4650, 446], [9, 25], "8,839 ft", "");
        myLabelsFar[9] = new Array([4432, 7138], [9, 25], "~7,000 ft", "");
 
    //Label Icon formation types for different labels
    var myDivIcon = []; 
    for (i=0; i < myLabels.length; i++){
        myDivIcon[i] = L.divIcon({
          className: 'my-div-icon', 
          iconSize: null, //'null' allows div to be resized in CSS. Otherwise, CSS sizing is overwritten by JS.
          iconAnchor: myLabels[i][1], //offset from top left corner
          html: myLabels[i][2]});
    }
    
    var myDivIconFar = []; 
    for (i=0; i < myLabelsFar.length; i++){
        myDivIconFar[i] = L.divIcon({
          className: 'my-div-icon-far', 
          iconSize: null, //'null' allows div to be resized in CSS. Otherwise, CSS sizing is overwritten by JS.
          iconAnchor: myLabelsFar[i][1], //offset from top left corner
          html: myLabelsFar[i][2]});
    }
           
    //Constructing all label markers w/ position
    var labels = [];
    for (i=0; i < myLabels.length; i++){
      labels[i] = _marker(myLabels[i][0], {icon:myDivIcon[i], clickable:false, title:myLabels[i][3]});
    }
    var j = -1
    for (i=myLabels.length; i < myLabels.length + myLabelsFar.length; i++){
      j++
      labels[i] = _marker(myLabelsFar[j][0], {icon:myDivIconFar[j], clickable:false, title:myLabelsFar[j][3]});
    }
    
    //Tourists Icon. For zoom = 14
    var myiconSize = [750, 212];     //Original icon size
    var iconAnchorSize = [375, 212];    //Original icon anchor location within icon
    var data = scaleIcon(0.5, myiconSize, iconAnchorSize);
    var myIconTourists = L.icon({  
        iconUrl: imagesDir + '/StandardFamily' + '.png',
        iconSize: [scaledIconSize[0],scaledIconSize[1]],
        iconAnchor: [scaledAnchorSize[0],scaledAnchorSize[1]],
        className: 'myTourists'
      });   
    var tourists = _marker([4790, 460], {icon:myIconBlank, clickable:false, title:'Oh, you\'r so cool! Can I get a photo with you?', zIndexOffset:1000});  
      labels[labels.length] = tourists;
      
    //===Ratings===
    //Defining label coordinates, anchor offset, and text
    var myRatings = [];
        myRatings[0] = new Array([4094, 6963.125], [9, 25], "5.8 bulge","Awkward, fun");
        myRatings[1] = new Array([4120, 6740.125], [9, 25], "5.10c finger","or C1");
        myRatings[2] = new Array([4154, 6462.125], [9, 25], "5.9","Big, burly stem!");
        myRatings[3] = new Array([4091, 6114.125], [9, 25], "5.8","Meh");
        myRatings[4] = new Array([4088, 5831.125], [9, 25], "C1","French free on gear, then bolt ladder");
        myRatings[5] = new Array([4073, 5684.125], [9, 25], "5.9+","Or C1");
        myRatings[6] = new Array([3942, 5319.125], [9, 25], "5.9","Tough");
        myRatings[7] = new Array([3947, 5136.125], [9, 25], "5.9","Tough");
        myRatings[8] = new Array([3757, 4466.125], [9, 25], "5.8","Dirty");
        myRatings[9] = new Array([3952, 4354.125], [9, 25], "cl. 4","Easy");
        myRatings[10] = new Array([3987, 4083.125], [9, 25], "5.8","Short");
        myRatings[11] = new Array([4167, 3978.125], [9, 25], "cl. 4","Easy");
        myRatings[12] = new Array([4190, 3802.125], [9, 25], "C1","Bolt ladder");    
        myRatings[13] = new Array([4234, 3737.125], [9, 25], "Big TT", "Tension Traverse, followed by unprotected face climbing. Follower lower out");
        myRatings[14] = new Array([4338, 3649.125], [9, 25], "5.10b or C2F","Officially 5.8. tough! Fixed nut as of 2013");
        myRatings[15] = new Array([4549, 3344.125], [9, 25], "C1","Lots of fixed gear. Bring lots of Purple through Green C4s");
        myRatings[16] = new Array([4591, 3197.125], [9, 25], "TT", "Tension Traverse, then easy wide for a while with no pro.");
        myRatings[17] = new Array([4617, 3030.125], [9, 25], "5.7","Fun!");
        myRatings[18] = new Array([4580, 2749.125], [9, 25], "5.7 airy or 5.9 squeeze","No pro on either one. I liked the squeeze better, left the rope hanging outside for the follower");
        myRatings[19] = new Array([4658, 2487.125], [9, 25], "5.9","Leaving the chimney");
        myRatings[20] = new Array([4799, 2248.125], [9, 25], "cl. 4","Easy");
        myRatings[21] = new Array([4867, 2095.125], [9, 25], "5.9","A tad loose");
        myRatings[22] = new Array([5044, 1983.125], [9, 25], "5.9","Best crack pitch!");
        myRatings[23] = new Array([5208, 1719.125], [9, 25], "C1","Gear");
        myRatings[24] = new Array([5203, 1556.125], [9, 25], "TT", "Tension Traverse");
        myRatings[25] = new Array([5294, 1361.125], [9, 25], "5.10b or C1","Gear");
        myRatings[26] = new Array([5323, 1196.125], [9, 25], "C1","Gear");
        myRatings[27] = new Array([5300, 1154.125], [9, 25], "TT","Tension Traverse");
        myRatings[28] = new Array([5356, 1100.125], [9, 25], "C1","Gear");
        myRatings[29] = new Array([5138, 973.125], [9, 25], "5.9","Crux is entry to the 5.8 squeeze off of TGL");
        myRatings[30] = new Array([5082, 945.125], [9, 25], "C2 var.","To bypass the 5.8 squeeze");
        myRatings[31] = new Array([5088, 832.125], [9, 25], "C1","Bolt ladder");
        myRatings[32] = new Array([5039, 792.125], [9, 25], "TT", "Tension Traverse, follower lower out");
        myRatings[33] = new Array([5007, 856.125], [9, 25], "C1+","Use cam hook");
        myRatings[34] = new Array([4985, 819.125], [9, 25], "C1","Bolt ladder");
        myRatings[35] = new Array([5000, 792.125], [9, 25], "TT", "Tension Traverse, follower lower out");
        myRatings[36] = new Array([4907, 743.125], [9, 25], "5.7","Slabby. Crux is short, right above the belay. No biggie.");
        myRatings[37] = new Array([4800, 664.125], [9, 25], "5.5","Very easy. Watch for drag");

    //Label Icon formation types for different labels
    var myDivRatingsIcon = []; 
    for (i=0; i < myRatings.length; i++){
        myDivRatingsIcon[i] = L.divIcon({
          className: 'my-div-rating-icon', 
          iconSize: null, //'null' allows div to be resized in CSS. Otherwise, CSS sizing is overwritten by JS.
          iconAnchor: myRatings[i][1], //offset from top left corner
          html: myRatings[i][2]});
    }
   
    //Constructing all label markers w/ position
    var ratings = [];
    for (i=0; i < myRatings.length; i++){
      ratings[i] = _marker(myRatings[i][0], {icon:myDivRatingsIcon[i], clickable:false, title:myRatings[i][3]});
    }

  //--------Photo Icons----------
  // TO DO:
  //    3. setLatLng popup method to make sure images don't open up off of the page. Perhaps do after final photo size is determined
  //      3a. An ideal solution is to have a generic function/equation that calculates this based on marker position and image size (assuming height is the max dimension?)
  
  //---Initialize Spiderfier
  var Spiderfier = new OverlappingMarkerSpiderfier(map, {
      keepSpiderfied: true,
      circleFootSeparation: 40, 
      spiralFootSeparation: 40, 
      spiralLengthFactor: 20, 
      nearbyDistance: 10, 
      circleSpiralSwitchover: 5, 
      legWeight:2});
  
  //Prepare PopUps
      myCaptions[0] = ["Cool OW right start to the Direct North Face of Half Dome. "];
      myCaptions[1] = ["Cool OW left start to the Direct North Face of Half Dome. "];
      myCaptions[2] = ["Looking across the N Face at sunset. A cl. 4 route ascends that gully as a more interesting way to start Snake Dike. "];
      myCaptions[3] = ["Approaching Final Exam (5.10d) "];
      myCaptions[4] = ["The great chimney systems of the NWRR of Half Dome. "];
      myCaptions[5] = ["The Visor, with climbers on the aid pitches beneath. "];
      myCaptions[6] = ["The sweet crack and corner of Final Exam (5.10d) "];
      myCaptions[7] = ["Ongoing innovation in climbing hardware, over-communication of route beta and the ever-growing wave of amateurs wanting to call themselves \"big wall climbers\" brings with it a long list of repercussions. In many ways, Half Dome and El Cap have become the Mt Everests of the rock climbing world. At times, I felt sad returning to Half Dome and seeing the human footprint... like visiting a powerful and majestic animal confined by chains in a petting zoo. There is no glory here... I've found many of the five-star/classic routes so sought after to be completely lacking of passion. Still, there are times when the sun hits just right and your eyes connect and you feel the mystery and adventure that once was. (photo & quote by Nic Risser) "];
      myCaptions[8] = ["Looking up from our bivy spot at the looming north face of Half Dome under alpenglow. "];
      myCaptions[9] = ["Nic leading P1 under headlamp as it got dark. I followed in the dark on this fun 5.10c pitch, which we fixed for jugging the next morning. (5.10c or C1) "];
      myCaptions[10] = ["Nic and the nighttime horror of canned tuna without a fork or spoon! "];
      myCaptions[11] = ["Nic atop P1. Jenny Abegg, Steph Abegg's sister, is leading P2. "];
      myCaptions[12] = ["Nic leading the 5.9 crux on P2. The crux is pulling the roof to get into the stem box. "];
      myCaptions[13] = ["Nic leading the 5.9 crux on P2. The crux is pulling the roof to get into the stem box. "];
      myCaptions[14] = ["We ran into Steph Abegg's sister, Jenni, on the climb! They were doing the route in a day. (by Nic Risser) "];
      myCaptions[15] = ["Following Nic up P2-3, which we linked. (5.9 and 5.8) "];
      myCaptions[16] = ["The 5.11 roof on P4 (5.9+ C1). Nic wanted to lead this rather than aid it. Technically he got it clean, but he fell out/pumped out when trying to place gear, which was very difficult in that awkward corner. "];
      myCaptions[17] = ["Following P4-5 (5.9+ C1, and 5.9) "];
      myCaptions[18] = ["Following P4-5, at the bolt ladder. (5.9+ C1, and 5.9) "];
      myCaptions[19] = ["Looking at P6 (5.9). Kind of mungy and uninspiring. "];
      myCaptions[20] = ["Looking up P7 (5.8). Even more mungy and uninspiring. Rock gets rotten and grassy as well. "];
      myCaptions[21] = ["Following up P7 (5.8). "];
      myCaptions[22] = ["Following up P7 (5.8). "];
      myCaptions[23] = ["Looking over to the huge chimneys that we will climb later today, with the Visor above. "];
      myCaptions[24] = ["Wall Nut enjoying the views as we gain elevation. "];
      myCaptions[25] = ["Following the lower angle terrain of P8-9, which we easily linked (5.8, and  cl. 4). (by Nic Risser) "];
      myCaptions[26] = ["Following up P8-9, which we easily linked. It is mostly 4th class, with one short easy 5.8 bit. (5.8, and  cl. 4) "];
      myCaptions[27] = ["Looking up the P10 bolt ladder. The bolts were a bit far apart, but doable. (C1) "];
      myCaptions[28] = ["Nic enjoying the increased exposure at the P10 belay. It is finally starting to feel like we are getting somewhere! "];
      myCaptions[29] = ["Looking down P10 from near the top of the bolt ladder and beginning of the tension traverse. (C1) "];
      myCaptions[30] = ["The view one sees from the tension traverse. It was quite tough to get over there statically and you'd face a big swing while face climbing on sloping holds, so unfortunately I backed off and let Nic get through this section. "];
      myCaptions[31] = ["Following the bolt ladder. I couldn't get the tension traverse to work, so gave the finish up to Nic to work out. (by Nic Risser) "];
      myCaptions[32] = ["Mark gets ready to lower out as we near the Robbins Traverse. (by Nic Risser) "];
      myCaptions[33] = ["Nic leading out on P11 toward the Robbins Traverse (5.9-5.10). This is very exposed! The crux is higher up and is in no way 5.8. I'd agree with the 5.10b opinion and would call it the free crux of the route for me. "];
      myCaptions[34] = ["Reaching the tough crux of the Robbins Traverse (P11, 5.9-5.10). You go to the right and make a very thin and physical step left on very slick rock. This part felt like 5.10b face, and goes at C2. Fortunately there is a fixed nut on that little roof. "];
      myCaptions[35] = ["Following the Robbins Traverse (P11, 5.9-5.10). (by Nic Risser) "];
      myCaptions[36] = ["The final ledge traverse on P11. Good spot for some warm sun and a lunch break! The chimney pitches are beyond. "];
      myCaptions[37] = ["Leading into the P12 5.6 chimney. This part is VERY easy. "];
      myCaptions[38] = ["Nic enjoying the afternoon sun while I lead the P12 chimneys and aid corner. "];
      myCaptions[39] = ["The easy 5.6 chimney on P12. "];
      myCaptions[40] = ["The step over into the 5.11-C1 corner was not trivial! It was not too secure and a fall would land one hard on the chimney chockstones, so I explored the 5.9 squeeze to 5.10 crack tunnel through option first. "];
      myCaptions[41] = ["Looking deep into the 5.9 squeeze. "];
      myCaptions[42] = ["Looking up into the 5.9 squeeze. Unfortunately the tunnel-through would not work with the follower's pack, so I had to back off and figure out how to get safely into the aid corner. "];
      myCaptions[43] = ["After placing gear high in an expanding flake, and doing some big stemming, I found it all right to get here. Now to free climb, then french free, then aid! Unfortunately I used up the sizes I needed earlier as ST called for larger cams on this section. Doh! "];
      myCaptions[44] = ["Looking down P12 after the tension traverse and final bit of unprotected 5.7 wide. Looks like I took way too long. "];
      myCaptions[45] = ["P13, P14, and P15 (5.7, 5.7-5.9, 5.9) chimney pitches that I would link in one 230' pitch with our 70m rope. Good thing chimneys are easy to climb in the dark! "];
      myCaptions[46] = ["Rootbeer and smashed cupcakes! In this state we decided to forgo lighting candles . . . "];
      myCaptions[47] = ["30th Birthday party atop Big Sandy Ledge! (by Nic Risser) "];
      myCaptions[48] = ["Signs of life from the ledge below. "];
      myCaptions[49] = ["Climb on! "];
      myCaptions[50] = ["Nic leading P18 (C1). I should have led this pitch, but the exposure of the wall was getting to me. "];
      myCaptions[51] = ["Nic leading P18 (C1) nearly to the tension traverse tat. "];
      myCaptions[52] = ["Looking down to Mark on Big Sandy Ledge. (by Nic Risser) "];
      myCaptions[53] = ["Mark jugging P18, nostrils flared in determination to ignore the exposure. (by Nic Risser) "];
      myCaptions[54] = ["Jugging P19 (5.10b or C1) and P20 (C1), which Nic linked, freeing P19. Rope drag made P20 slow enough that this was probably slower than breaking up pitches. "];
      myCaptions[55] = ["Jugging up the final corner of P20 (C1). "];
      myCaptions[56] = ["Mark jugging P20, nostrils flared in determination to ignore the exposure. (by Nic Risser) "];
      myCaptions[57] = ["Thank God Ledge awaits (P21, 5.9). I should have also led this pitch, but exposure and serious food poisoning left me feeling less up to the task. Quite thrilling to follow! "];
      myCaptions[58] = ["Nic at the start of the bolt ladder on P23 (C1+, two tension traverses requiring lower-outs to follow). He found the cam hook to be invaluable on this pitch. "];
      myCaptions[59] = ["Following P23 (C1+), just before the second lower-out. "];
      myCaptions[60] = ["Nic leading P24 (5.7), on easy terrain. The 5.7 slab crux is right off the belay for a couple of moves and not too bad. "];
      myCaptions[61] = ["Wall Nut atop Half Dome after ascending the Northwest Regular Route! Ascenders and alpine aiders came in handy. "];
      myCaptions[62] = ["Me and Nic atop Half Dome after climbing the Northwest Regular Route. (by Nic Risser) "];
      myCaptions[63] = ["Nic descending the Cables at sunset. Similar to my experience of climbing Snake Dike, this was one of the more unsettling parts of the climb. "];
  
  var sizeLarge = 1600;
  var size = 400;
  var myOffset = (25, 17);
  
      photoURL[0] = new Array(["https://lh3.googleusercontent.com/-TogP3rHtDYs/UibIVUOXMkI/AAAAAAACoAA/HJG6P_UKXc0/s"], size, ["/2013-08-10%252520-%252520056%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2507.jpg"]);
      photoURL[1] = new Array(["https://lh5.googleusercontent.com/-SWbphwG2jus/UibIbgacdnI/AAAAAAACoAk/zEgZreGIMQ0/s"], size, ["/2013-08-10%252520-%252520057%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2508.jpg"]);
      photoURL[2] = new Array(["https://lh3.googleusercontent.com/-CMtw6p2In3E/UibI72UlPnI/AAAAAAACoEM/DLHSua-AKSo/s"], size, ["/2013-08-10%252520-%252520066%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2518.jpg"]);
      photoURL[3] = new Array(["https://lh6.googleusercontent.com/-FA6L1Z3o-_U/UibJGtqtVZI/AAAAAAACoFc/S0QRKh9OfVw/s"], size, ["/2013-08-10%252520-%252520068%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2520.jpg"]);
      photoURL[4] = new Array(["https://lh6.googleusercontent.com/-ImJEpaPhrlE/UibJKFlfN7I/AAAAAAACoF0/FfgfR7Um8Io/s"], size, ["/2013-08-10%252520-%252520069%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2521.jpg"]);
      photoURL[5] = new Array(["https://lh5.googleusercontent.com/-cE0AYTiudqk/UibJUw6Pq_I/AAAAAAACoG4/AwnbQyOD6mU/s"], size, ["/2013-08-10%252520-%252520069c%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2521.jpg"]);
      photoURL[6] = new Array(["https://lh5.googleusercontent.com/-ywW2gh8wn1E/UibJidVFUjI/AAAAAAACoIU/JInSGxnm-Ec/s"], size, ["/2013-08-10%252520-%252520071%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2530.jpg"]);
      photoURL[7] = new Array(["https://lh3.googleusercontent.com/-pGNptZSR9a8/UibJtMXUH0I/AAAAAAACoJU/rYY3kdB-RM0/s"], size, ["/2013-08-10%252520-%252520082%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000217.jpg"]);
      photoURL[8] = new Array(["https://lh5.googleusercontent.com/-lGmlkoUKcHU/UibJv5ufFDI/AAAAAAACoJs/ObN9p-BLiBk/s"], size, ["/2013-08-10%252520-%252520086%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2532.jpg"]);
      photoURL[9] = new Array(["https://lh5.googleusercontent.com/-tp5fuY8JWnk/UibJ5xhTysI/AAAAAAACoKs/H98olX4C0x8/s"], size, ["/2013-08-10%252520-%252520090%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2534.jpg"]);
      photoURL[10] = new Array(["https://lh5.googleusercontent.com/-DsLAvH6B-dg/UibKBWrhLII/AAAAAAACoLU/TPuc7YL5mRU/s"], size, ["/2013-08-10%252520-%252520092%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2539.jpg"]);
      photoURL[11] = new Array(["https://lh6.googleusercontent.com/-LBPNpTMa59A/UibKHd4uw2I/AAAAAAACoME/oCasmrKG1dc/s"], size, ["/2013-08-11%252520-%252520094%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2542.jpg"]);
      photoURL[12] = new Array(["https://lh4.googleusercontent.com/-TxXgfand_fw/UibKTc-ILgI/AAAAAAACoNE/8FH5HhILwpI/s"], size, ["/2013-08-11%252520-%252520096%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2544.jpg"]);
      photoURL[13] = new Array(["https://lh3.googleusercontent.com/-cUowpdym4EU/UibKYjOa3DI/AAAAAAACoNY/PET6uy-yKao/s"], size, ["/2013-08-11%252520-%252520097%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2545.jpg"]);
      photoURL[14] = new Array(["https://lh3.googleusercontent.com/-Osdu9i3bKPs/UibKcpsq4vI/AAAAAAACoN0/hOfQI7IFJJU/s"], size, ["/2013-08-11%252520-%252520098%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000223.jpg"]);
      photoURL[15] = new Array(["https://lh6.googleusercontent.com/-hj13bErz6uo/UibKkn6pnvI/AAAAAAACoOQ/3cBBRRUkzlk/s"], size, ["/2013-08-11%252520-%252520100%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2547.jpg"]);
      photoURL[16] = new Array(["https://lh6.googleusercontent.com/-Hd3cBNAogps/UibKsTZCnjI/AAAAAAACoO4/nyOGPbSaN-Q/s"], size, ["/2013-08-11%252520-%252520101%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2549.jpg"]);
      photoURL[17] = new Array(["https://lh3.googleusercontent.com/-dP33tN2jipI/UibK034lVNI/AAAAAAACoPU/BzSTVC3bCLY/s"], size, ["/2013-08-11%252520-%252520107%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2550.jpg"]);
      photoURL[18] = new Array(["https://lh5.googleusercontent.com/-7vR4cWTOeVQ/UibK86BvjfI/AAAAAAACoP0/q4Eg5HRjKFY/s"], size, ["/2013-08-11%252520-%252520108%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2551.jpg"]);
      photoURL[19] = new Array(["https://lh5.googleusercontent.com/-qn3RbKdsY9s/UibLEeeBSGI/AAAAAAACoQM/0Dan9d1En2s/s"], size, ["/2013-08-11%252520-%252520109%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2552.jpg"]);
      photoURL[20] = new Array(["https://lh5.googleusercontent.com/-pI3aY4z9t_A/UibLRxgX7kI/AAAAAAACoRE/wu-PViL-NdE/s"], size, ["/2013-08-11%252520-%252520111%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2554.jpg"]);
      photoURL[21] = new Array(["https://lh5.googleusercontent.com/-2oONoZ5W3A4/UibLdZexapI/AAAAAAACoRs/dy09XZ1ZiAs/s"], size, ["/2013-08-11%252520-%252520113%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2556.jpg"]);
      photoURL[22] = new Array(["https://lh4.googleusercontent.com/-oPQ9EFxVqyE/UibLk1FC_0I/AAAAAAACoSE/oai5G8hON94/s"], size, ["/2013-08-11%252520-%252520114%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2557.jpg"]);
      photoURL[23] = new Array(["https://lh6.googleusercontent.com/-htgVBbXj0z4/UibLopN5A0I/AAAAAAACoSg/ZT2fJxGNe2k/s"], size, ["/2013-08-11%252520-%252520115%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2559.jpg"]);
      photoURL[24] = new Array(["https://lh4.googleusercontent.com/-w2pOgz6e6ck/UibL85RSiFI/AAAAAAACoUQ/gRPi-UttWLI/s"], size, ["/2013-08-11%252520-%252520119%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2563.jpg"]);
      photoURL[25] = new Array(["https://lh6.googleusercontent.com/-ZtFdgS0xsyk/UibMHc52R2I/AAAAAAACoVM/xpXFa4HkgvM/s"], size, ["/2013-08-11%252520-%252520122%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000230.jpg"]);
      photoURL[26] = new Array(["https://lh5.googleusercontent.com/-pFF2TherGUc/UibMNRYJIXI/AAAAAAACoVw/ZyJEJpGzhf0/s"], size, ["/2013-08-11%252520-%252520123%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2565.jpg"]);
      photoURL[27] = new Array(["https://lh6.googleusercontent.com/-Txj3fFqCXEc/UibMZFrHevI/AAAAAAACoWo/XZvaW_hJKNQ/s"], size, ["/2013-08-11%252520-%252520126%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2566.jpg"]);
      photoURL[28] = new Array(["https://lh5.googleusercontent.com/-r9PSGpaSVs8/UibMfChGjvI/AAAAAAACoXM/-j9SlJtuepc/s"], size, ["/2013-08-11%252520-%252520127%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2567.jpg"]);
      photoURL[29] = new Array(["https://lh3.googleusercontent.com/-OpKe1fEjSq8/UibMj4k5A_I/AAAAAAACoX0/J9MTMOsXMng/s"], size, ["/2013-08-11%252520-%252520128%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2568.jpg"]);
      photoURL[30] = new Array(["https://lh4.googleusercontent.com/-SdDjWV4GIzE/UibMoYxoVJI/AAAAAAACoYU/xS7_qlr-LPM/s"], size, ["/2013-08-11%252520-%252520129%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2569.jpg"]);
      photoURL[31] = new Array(["https://lh6.googleusercontent.com/-jJ6qqc2EozM/UibMu1C0Y0I/AAAAAAACoYs/9Rb8h2oZkZI/s"], size, ["/2013-08-11%252520-%252520130%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000233.jpg"]);
      photoURL[32] = new Array(["https://lh5.googleusercontent.com/-w80pu9F-smk/UibM1fQl9zI/AAAAAAACoZI/JROhHXt6H18/s"], size, ["/2013-08-11%252520-%252520135%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000238.jpg"]);
      photoURL[33] = new Array(["https://lh3.googleusercontent.com/-ztwaULiB8Vk/UibNAwX5hkI/AAAAAAACoaM/rHW5tIldj5U/s"], size, ["/2013-08-11%252520-%252520139%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2570.jpg"]);
      photoURL[34] = new Array(["https://lh5.googleusercontent.com/-h_MSXUOf8KY/UibNReU148I/AAAAAAACobc/2zp6FYCdWvE/s"], size, ["/2013-08-11%252520-%252520142%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2571.jpg"]);
      photoURL[35] = new Array(["https://lh5.googleusercontent.com/-0JkBwVcV4gE/UibNZBh6bII/AAAAAAACocA/Acy19Nuv_FY/s"], size, ["/2013-08-11%252520-%252520143%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000244.jpg"]);
      photoURL[36] = new Array(["https://lh5.googleusercontent.com/-cx7qrFIUmdc/UibNgknAX-I/AAAAAAACocY/ll-wF-P0AvQ/s"], size, ["/2013-08-11%252520-%252520145%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2572.jpg"]);
      photoURL[37] = new Array(["https://lh4.googleusercontent.com/-ikZ6T-WKBO8/UibNkKsJqdI/AAAAAAACocw/mikXF9jxQG0/s"], size, ["/2013-08-11%252520-%252520146%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2573.jpg"]);
      photoURL[38] = new Array(["https://lh5.googleusercontent.com/-YjXwLvBTWeU/UibNujyIB3I/AAAAAAACods/vWU5iklBBoc/s"], size, ["/2013-08-11%252520-%252520149%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2578.jpg"]);
      photoURL[39] = new Array(["https://lh4.googleusercontent.com/-IBYX8Cj9pLY/UibNznUPaFI/AAAAAAACoeM/FeL5y3bEUeM/s"], size, ["/2013-08-11%252520-%252520151%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2577.jpg"]);
      photoURL[40] = new Array(["https://lh3.googleusercontent.com/-iOx605fZmKw/UibN24HJJNI/AAAAAAACoew/8xEmMO8jH_M/s"], size, ["/2013-08-11%252520-%252520152%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2580.jpg"]);
      photoURL[41] = new Array(["https://lh3.googleusercontent.com/-4P0YinyfD9o/UibN8VI72PI/AAAAAAACofU/SiEEwitRe5U/s"], size, ["/2013-08-11%252520-%252520153%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2581.jpg"]);
      photoURL[42] = new Array(["https://lh4.googleusercontent.com/-WUkZfyjvhdE/UibOBnK_tZI/AAAAAAACofk/GrlQXRBsDH8/s"], size, ["/2013-08-11%252520-%252520154%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2582.jpg"]);
      photoURL[43] = new Array(["https://lh4.googleusercontent.com/-LUroh32_oRo/UibOEk0vGVI/AAAAAAACof8/ZUi8WeOIJvo/s"], size, ["/2013-08-11%252520-%252520156%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2584.jpg"]);
      photoURL[44] = new Array(["https://lh6.googleusercontent.com/-U-9TncLV0wY/UibOMEZnSeI/AAAAAAACogw/TNBwe3MQA1E/s"], size, ["/2013-08-11%252520-%252520157%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2585.jpg"]);
      photoURL[45] = new Array(["https://lh3.googleusercontent.com/-OPNp3bc7_G0/UibOVAjw6aI/AAAAAAACohs/bTELWpH-wLs/s"], size, ["/2013-08-11%252520-%252520158a%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2586.jpg"]);
      photoURL[46] = new Array(["https://lh4.googleusercontent.com/-ICheV8dTfgc/UibOdDlp0bI/AAAAAAACojI/MERHMaYr-_8/s"], size, ["/2013-08-12%252520-%252520161%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2592.jpg"]);
      photoURL[47] = new Array(["https://lh5.googleusercontent.com/-yTKpq_Nb370/UibOfsVoMOI/AAAAAAACojg/sViC2RZuJmc/s"], size, ["/2013-08-12%252520-%252520162%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2593.jpg"]);
      photoURL[48] = new Array(["https://lh4.googleusercontent.com/-1agkspaq16o/UibOyEkV37I/AAAAAAAColM/AKgdoHMtwNs/s"], size, ["/2013-08-12%252520-%252520166%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2598.jpg"]);
      photoURL[49] = new Array(["https://lh5.googleusercontent.com/--SOkYhauGSs/UibO_sH0QZI/AAAAAAAComg/8iEJ_ihQ9FQ/s"], size, ["/2013-08-12%252520-%252520169%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000248.jpg"]);
      photoURL[50] = new Array(["https://lh4.googleusercontent.com/-g3kSWfaR1Xw/UibPHNFxINI/AAAAAAAConA/7blvg5u-h_k/s"], size, ["/2013-08-12%252520-%252520171%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2601.jpg"]);
      photoURL[51] = new Array(["https://lh5.googleusercontent.com/-rcea82NylvI/UibPSTVZzbI/AAAAAAACooA/EojfhApYhes/s"], size, ["/2013-08-12%252520-%252520173%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2604.jpg"]);
      photoURL[52] = new Array(["https://lh4.googleusercontent.com/-VdiJvfnEvqk/UibPcDJhm-I/AAAAAAACoo4/ldij4s9kagA/s"], size, ["/2013-08-12%252520-%252520176%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000250.jpg"]);
      photoURL[53] = new Array(["https://lh4.googleusercontent.com/-Qj0Ipuywxzg/UibPhx9G6XI/AAAAAAACopY/JNjY3Pz89Wg/s"], size, ["/2013-08-12%252520-%252520178%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000252.jpg"]);
      photoURL[54] = new Array(["https://lh5.googleusercontent.com/-ReLvM1j9ARs/UibPnJtK3II/AAAAAAACopw/IkKfu_f08Tc/s"], size, ["/2013-08-12%252520-%252520180%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2606.jpg"]);
      photoURL[55] = new Array(["https://lh6.googleusercontent.com/-H0-c6zITAQ4/UibPqQ4G_rI/AAAAAAACoqY/VmYSEIJP6A0/s"], size, ["/2013-08-12%252520-%252520181%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2607.jpg"]);
      photoURL[56] = new Array(["https://lh5.googleusercontent.com/-ZP0dtb5_YT0/UibPsocwY9I/AAAAAAACorQ/0RfWtRBzCc8/s"], size, ["/2013-08-12%252520-%252520182%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000254.jpg"]);
      photoURL[57] = new Array(["https://lh5.googleusercontent.com/-R_TEAhD_B4c/UibP1ip41DI/AAAAAAACosI/6ZYgbIESxFM/s"], size, ["/2013-08-12%252520-%252520188%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2609.jpg"]);
      photoURL[58] = new Array(["https://lh4.googleusercontent.com/-8Wx678uFNCo/UibP4-UG7SI/AAAAAAACoso/xOVvRnCKYzQ/s"], size, ["/2013-08-12%252520-%252520190%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2611.jpg"]);
      photoURL[59] = new Array(["https://lh3.googleusercontent.com/-IGp7ousDnlU/UibP792F_oI/AAAAAAACotA/OnWLx2967GA/s"], size, ["/2013-08-12%252520-%252520191%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2613.jpg"]);
      photoURL[60] = new Array(["https://lh6.googleusercontent.com/-MtS7C5GRVlg/UibQA0mAwdI/AAAAAAACotg/yOWKqfW48-U/s"], size, ["/2013-08-12%252520-%252520192%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2614.jpg"]);
      photoURL[61] = new Array(["https://lh3.googleusercontent.com/-jzNEfzUo7Zg/UibQJqs2hYI/AAAAAAACouI/cxLKJhTRF3w/s"], size, ["/2013-08-12%252520-%252520194%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2616.jpg"]);
      photoURL[62] = new Array(["https://lh3.googleusercontent.com/-lIukoA17hrk/UibQP4WtZZI/AAAAAAACovQ/8EPMAkCaxS8/s"], size, ["/2013-08-12%252520-%252520199%252520-%252520Half%252520Dome%252520NWRR%252520-%252520P1000263.jpg"]);
      photoURL[63] = new Array(["https://lh3.googleusercontent.com/-qLfx-e3oj_Y/UibQW4cZvOI/AAAAAAACowQ/zDP5rnU3FPw/s"], size, ["/2013-08-12%252520-%252520209%252520-%252520Half%252520Dome%252520NWRR%252520-%252520IMG_2620.jpg"]);

      //TO DO: 
      //  1. Adjust popup to form to the side of the icon? Or see API reference list below as these might address the issues.
  var pop = [];    
  for (i=0; i < myCaptions.length; i++){    
    photoNum = i;
    pop[i] = "<div><center><a href=\"javascript:void(photoEnlarge(photoURL, " + photoNum + ", myCaptions))\"><img src=\"" + photoURL[i][0] + photoURL[i][1] + photoURL[i][2] + "\" /></a><br><b><i>" + myCaptions[i] + "</i></b></center></div>";
  };
 
      //Created to make sure popup sizes to interior image
      var divNode = [];
      for (i=0; i < myCaptions.length; i++){
      divNode[i] = document.createElement('DIV');
      divNode[i].innerHTML = pop[i];
      }

  //Photo coordinates in image
  var photoCoords = []; 
      photoCoords[0] = [5560, 7704];
      photoCoords[1] = [5120, 7560];
      photoCoords[2] = [2994, 6450];
      photoCoords[3] = [2908, 6378];
      photoCoords[4] = [2846, 6340];
      photoCoords[5] = [2802, 6306];
      photoCoords[6] = [2716, 6216];
      photoCoords[7] = [3918, 7072];
      photoCoords[8] = [4226, 7270];
      photoCoords[9] = [4172, 7178];
      photoCoords[10] = [4220, 7164];
      photoCoords[11] = [4210, 7174];
      photoCoords[12] = [4154, 6636];
      photoCoords[13] = [4172, 6622];
      photoCoords[14] = [4064, 5962];
      photoCoords[15] = [4106, 6248];
      photoCoords[16] = [4033, 5922];
      photoCoords[17] = [4068, 5902];
      photoCoords[18] = [4066, 5782];
      photoCoords[19] = [3972, 5440];
      photoCoords[20] = [3882, 4806];
      photoCoords[21] = [3820, 4652];
      photoCoords[22] = [3780, 4492];
      photoCoords[23] = [3864, 4384];
      photoCoords[24] = [3880, 4408];
      photoCoords[25] = [4160, 3892];
      photoCoords[26] = [4012, 4248];
      photoCoords[27] = [4180, 3900];
      photoCoords[28] = [4206, 3898];
      photoCoords[29] = [4214, 3738];
      photoCoords[30] = [4226, 3746];
      photoCoords[31] = [4276, 3750];
      photoCoords[32] = [4291, 3753];
      photoCoords[33] = [4308, 3750];
      photoCoords[34] = [4390, 3702];
      photoCoords[35] = [4422, 3598];
      photoCoords[36] = [4378, 3616];
      photoCoords[37] = [4442, 3592];
      photoCoords[38] = [4494, 3568];
      photoCoords[39] = [4510, 3560];
      photoCoords[40] = [4522, 3544];
      photoCoords[41] = [4538, 3526];
      photoCoords[42] = [4532, 3560];
      photoCoords[43] = [4522, 3472];
      photoCoords[44] = [4624, 3170];
      photoCoords[45] = [4631, 3158];
      photoCoords[46] = [5210, 1850];
      photoCoords[47] = [5232, 1846];
      photoCoords[48] = [5226, 1837];
      photoCoords[49] = [5226, 1862];
      photoCoords[50] = [5252, 1844];
      photoCoords[51] = [5246, 1860];
      photoCoords[52] = [5238, 1546];
      photoCoords[53] = [5250, 1548];
      photoCoords[54] = [5220, 1496];
      photoCoords[55] = [5250, 1408];
      photoCoords[56] = [5318, 1042];
      photoCoords[57] = [5294, 994];
      photoCoords[58] = [5087, 891];
      photoCoords[59] = [5004, 804];
      photoCoords[60] = [4950, 780];
      photoCoords[61] = [5018, 444];
      photoCoords[62] = [5044, 448];
      photoCoords[63] = [5512, 524];
                 
    //Defining coordinates, anchor offset, and text for photos
    var iconAnchorSize = [25, 25];    //Original icon anchor location within icon
    var myPhotos = [];
        for (var i = 0; i < photoCoords.length; i++){
        myPhotos[i] = new Array(photoCoords[i], iconAnchorSize, pop[i], 'clicked-false');  
        };

    //====Circle img variation====
    //Icon formation types for photos
      var myPhotoIcon = L.icon({ 
          iconAnchor: [6, 6],
          iconSize: [12, 12],
          iconUrl: imagesDir + 'marker-icon-photo.png'
          });
 
      var myPhotoIconHover = L.icon({
          iconAnchor: [6, 6],
          iconSize: [12, 12],
          iconUrl: imagesDir + 'marker-icon-photo-hover.png'
          });
    
      var myPhotoIconClick = L.icon({
          iconAnchor: [6, 6],
          iconSize: [12, 12],
          iconUrl: imagesDir + 'marker-icon-photo-clicked.png'
          });
    //================================
   
       // This is for possibly having div icons with numbers for the belays
    // var myDivIconBelay = L.divIcon({
        // className: 'my-div-icon-belay', 
        //iconSize: new L.Point(700, 400),
        //iconSize: [20, 20],
        // iconSize: null, //'null' allows div to be resized in CSS. Otherwise, CSS sizing is overwritten by JS.
        // iconAnchor: [9, 25], //offset from top left corner
        // html:'1'});
    // var myDivIconBelayTest = _marker([4162+100, 6600+100], {icon:myDivIconBelay}).addTo(map);  
   
    //Constructing all markers w/ position
    var photosMarker = [];
    var photos = [];
    for (i=0; i < myPhotos.length; i++){       
     photosMarker[i] = _marker(myPhotos[i][0], {icon:myIconBlank, title:myCaptions[i], zIndexOffset:1500});  
     photos[i] = photosMarker[i];
     photos[i] = photosMarker[i].bindPopup(divNode[i], {minWidth: 3/4*size, maxWidth: size, autoPan: false});  
      Spiderfier.addMarker(photos[i]);
    }
    
    //Spiderfy effects
    var spiderfyStatus;   // Sets whether to suppress 'clicked' state of icon. 
    var spiderfyCounter;      // Determines which icon was clicked, for suppressing 'clicked' state.
    Spiderfier.addListener('spiderfy', function(markers) {
      map.closePopup();   // This prevents the popup from initiating upon the first click to spiderfy
 //     alert(spiderfyStatus)
 //     alert(spiderfyCounter)
      // debugger;
      if (spiderfyStatus === true){
        myPhotos[spiderfyCounter][3] = 'clicked-false';
        photos[spiderfyCounter].setIcon(myPhotoIcon);
      }
      // spiderfyStatus = false;
    });
 
 //===See below. Spiderfy 'clicked' status suppression only works on first click. After this, status remains false.   
    //--- Change icon on hover & on click
    map.on('zoomend', function(e){
      for (i=0; i < myPhotos.length; i++){
        (function(i) {
          photos[i].on('mouseover', function(e){
            var zoomLevel = map.getZoom();
            if (zoomLevel > 14){ 
              photos[i].setIcon(myPhotoIconHover);
            } else {
              photos[i].setIcon(myIconBlank);
            }
          });
          photos[i].on('popupopen', function(e){
            if (myPhotos[i][3] === 'clicked-false'){
              spiderfyCounter = i;
              spiderfyStatus = true;
              myPhotos[i][3] = 'clicked-true';
              photos[i].setIcon(myPhotoIconClick);
            } else {
              spiderfyStatus = false;
            }
          });
          photos[i].on('mouseout', function(e){
              var zoomLevel = map.getZoom();
              if (zoomLevel > 14){ 
                if (myPhotos[i][3] == 'clicked-false'){
                  photos[i].setIcon(myPhotoIcon);
                } else {
                  photos[i].setIcon(myPhotoIconClick);
                }
              } else {
                photos[i].setIcon(myIconBlank);
              }
          });
        })(i);
      };
    });
  

  
  //--------Polygons----------  
  //Foreground
    var Background = _polygon([
      [0, 0],
      [0, 8192],
      [16384, 8192],
      [16384, 0],
      [0, 0]
    ], {
      color: 'black',
      weight: 1,
      opacity: 1,
      fillColor: '#ccccff', //#ffff66
      fillOpacity: 1,
      clickable:false
    });
    
    var Foreground = _polygon([
      [16, 4402],
      [164, 4472],
      [388, 4538],
      [582, 4444],
      [804, 4192],
      [874, 3520],
      [1152, 3202],
      [1462, 2888],
      [1824, 2594],
      [2076, 2416],
      [2564, 2216],
      [2850, 2122],
      [3072, 2098],
      [3440, 2194],
      [3664, 2146],
      [3736, 2000],
      [3784, 1832],
      [3948, 1334],
      [4024, 1090],
      [4128, 954],
      [4224, 690],
      [4394, 552],
      [4592, 450],
      [4960, 450],
      [5266, 496],
      [5824, 594],
      [6088, 612],
      [6354, 582],
      [6842, 514],
      [7082, 468],
      [7336, 442],
      [7682, 426],
      [8028, 448],
      [8346, 476],
      [8656, 532],
      [9280, 706],
      [9634, 818],
      [9936, 962],
      [10624, 1266],
      [11632, 1616],
      [11620, 1664],
      [12232, 1902],
      [12958, 2140],
      [13742, 2504],
      [14230, 2746],
      [14592, 2994],
      [15466, 3632],
      [15690, 3846],
      [15872, 4066],
      [16100, 4374],
      [16382, 4674],
      [16370, 8148],
      [15962, 8180],
      [6, 8190],
      [16, 4402]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffffcc',  
      fillOpacity: 1,
      clickable:false
    });
  
  // HalfDome
    var HalfDome = _polygon([
      [2420, 6024],
      [1740, 5500],
      [1444, 5620],
      [1432, 5396],
      [1552, 5112],
      [1444, 4672],
      [1284, 4336],
      [1068, 4008],
      [874, 3520],
      [1152, 3202],
      [1462, 2888],
      [1824, 2594],
      [2076, 2416],
      [2564, 2216],
      [2850, 2122],
      [3072, 2098],
      [3440, 2194],
      [3664, 2146],
      [3736, 2000],
      [3784, 1832],
      [3948, 1334],
      [4024, 1090],
      [4128, 954],
      [4224, 690],
      [4394, 552],
      [4592, 450],
      [4960, 450],
      [5266, 496],
      [5824, 594],
      [6088, 612],
      [6354, 582],
      [6842, 514],
      [7082, 468],
      [7336, 442],
      [7682, 426],
      [8028, 448],
      [8346, 476],
      [8656, 532],
      [9280, 706],
      [9634, 818],
      [9936, 962],
      [10624, 1266],
      [11632, 1616],
      [11620, 1664],
      [12232, 1902],
      [12958, 2140],
      [13742, 2504],
      [14230, 2746],
      [14592, 2994],
      [15466, 3632],
      [15690, 3846],
      [15872, 4066],
      [16100, 4374],
      [16382, 4674],
      [16372, 5156],
      [15324, 4908],
      [13704, 4880],
      [13496, 5048],
      [13176, 5744],
      [12928, 6288],
      [12472, 6600],
      [11440, 6416],
      [10160, 6512],
      [9488, 6832],
      [9140, 7044],
      [8852, 7420],
      [8592, 7876],
      [8532, 8172],
      [6504, 8192],
      [6012, 7828],
      [5060, 7528],
      [4648, 7340],
      [4212, 7124],
      [4048, 7096],
      [3884, 7036],
      [3540, 6816],
      [2420, 6024]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
  
  // NWFace
    var nwFace = _polygon([
      [2420, 6024],
      [1740, 5500],
      [1708, 5360],
      [1936, 4776],
      [2192, 4136],
      [2492, 3568],
      [2740, 3152],
      [2776, 3308],
      [2904, 3052],
      [2964, 2860],
      [3200, 2768],
      [3328, 2840],
      [3456, 2740],
      [3572, 2476],
      [3616, 2240],
      [3664, 2146],
      [3736, 2000],
      [3784, 1832],
      [3948, 1334],
      [4024, 1090],
      [4128, 954],
      [4224, 690],
      [4394, 552],
      [4592, 450],
      [4960, 450],
      [5246, 512],
      [5784, 606],
      [5934, 656],
      [6082, 666],
      [6316, 724],
      [6630, 726],
      [6714, 694],
      [6844, 706],
      [7128, 760],
      [7954, 888],
      [8792, 1156],
      [9522, 1286],
      [9960, 1452],
      [10340, 1684],
      [10768, 1982],
      [11014, 2092],
      [11162, 2232],
      [11416, 2442],
      [11716, 2626],
      [11860, 2624],
      [12136, 2860],
      [12380, 3196],
      [12704, 3504],
      [12980, 3948],
      [13268, 4316],
      [13184, 4516],
      [13028, 4908],
      [12788, 5040],
      [12380, 4940],
      [12128, 5104],
      [11388, 5456],
      [11192, 6220],
      [10160, 6512],
      [9488, 6832],
      [9140, 7044],
      [8852, 7420],
      [8592, 7876],
      [8532, 8172],
      [6504, 8192],
      [6012, 7828],
      [5060, 7528],
      [4648, 7340],
      [4212, 7124],
      [4048, 7096],
      [3884, 7036],
      [3540, 6816],
      [2420, 6024]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
  
  // === NRidge
  // NRidge
    var nRidge = _polygon([
      [2420, 6024],
      [1740, 5500],
      [1708, 5360],
      [1936, 4776],
      [2192, 4136],
      [2492, 3568],
      [2740, 3152],
      [2776, 3308],
      [2904, 3052],
      [2964, 2860],
      [3200, 2768],
      [3328, 2840],
      [3456, 2740],
      [3572, 2476],
      [3616, 2240],
      [3706, 2252],
      [3762, 2510],
      [3904, 3104],
      [3936, 3760],
      [3982, 4208],
      [4048, 4500],
      [4144, 4832],
      [4264, 5792],
      [4436, 7224],
      [4212, 7124],
      [4048, 7096],
      [3884, 7036],
      [3540, 6816],
      [2420, 6024]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
  
  // NRidgeFront
    var nRidgeFront = _polygon([
      [2420, 6024],
      [1740, 5500],
      [1708, 5360],
      [1936, 4776],
      [2192, 4136],
      [2492, 3568],
      [2740, 3152],
      [2776, 3308],
      [2812, 4136],
      [2864, 4476],
      [2900, 4588],
      [3000, 4912],
      [2936, 5232],
      [2896, 5704],
      [2884, 5840],
      [2924, 5884],
      [2872, 6056],
      [2920, 6108],
      [2884, 6376],
      [2420, 6024]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
  
  // === pedestalFinalExam
    var pedestalFinalExam = _polygon([
      [2474, 6062],
      [2464, 5980],
      [2404, 5914],
      [2384, 5748],
      [2412, 5707],
      [2460, 5620],
      [2500, 5560],
      [2554, 5538],
      [2590, 5478],
      [2606, 5422],
      [2618, 5358],
      [2636, 5317],
      [2652, 5318],
      [2666, 5284],
      [2679, 5324],
      [2712, 5322],
      [2727, 5363],
      [2718, 5522],
      [2718, 5620],
      [2690, 5700],
      [2724, 5742],
      [2730, 6026],
      [2716, 6216],
      [2690, 6244],
      [2474, 6062]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.5,
      clickable:false
    });
  // ===NWRR Features
  // Dike Corner
    var dikeCorner = _polygon([
      [3968, 6068.5],
      [3936, 6252.5],
      [3952, 6344.5],
      [3932, 6476.5],
      [3956, 6660.5],
      [4040, 7092.5],
      [4212, 7116.5],
      [4440, 7228.5],
      [4344, 6528.5],
      [4340, 6444.5],
      [4332, 6368.5],
      [4292, 6146],
      [4240, 5958],
      [4222, 5818],
      [4228, 5590],
      [4172, 5066],
      [4122, 4824],
      [4074, 4756],
      [4076, 4604],
      [4050, 4576],
      [4066, 4506],
      [4038, 4484],
      [4046, 4386],
      [3984, 4208],
      [3974, 4008],
      [3972, 3856],
      [4002, 3566],
      [4004, 3482],
      [4062, 3334],
      [4020, 3180],
      [3904, 3074],
      [3870, 2964],
      [3806, 2854],
      [3796, 2702],
      [3784, 2582],
      [3704, 2252],
      [3616, 2238],
      [3576, 2472],
      [3456, 2746],
      [3326, 2840],
      [3568, 3026],
      [3634, 3204],
      [3584, 3372],
      [3540, 3600],
      [3556, 3680],
      [3596, 3730],
      [3582, 3812],
      [3628, 3862],
      [3648, 3922],
      [3632, 4096],
      [3666, 4170],
      [3722, 4236],
      [3732, 4332],
      [3750, 4442],
      [3778, 4578],
      [3836, 4704],
      [3840, 4758],
      [3864, 4794],
      [3884, 4874],
      [3932, 5128],
      [3934, 5290],
      [3934, 5470],
      [3902, 5636],
      [3926, 5664],
      [3930, 5718],
      [3958, 5752],
      [3968, 6068.5]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.25,
      clickable:false
    });
  
  // Lower Pillar
    var lowerPillar1 = _polygon([
      [5040, 4279],
      [4997, 4223],
      [4955, 4192],
      [4894, 4194],
      [4836, 4212],
      [4815, 4243],
      [4827, 4274],
      [4804, 4313],
      [4781, 4361],
      [4778, 4413],
      [4791, 4451],
      [4760, 4485],
      [4716, 4622],
      [4702, 4697],
      [4615, 4861],
      [4594, 4947],
      [4566, 4961],
      [4514, 4892],
      [4532, 4604],
      [4528, 4537],
      [4528, 4371],
      [4481, 4336],
      [4474, 4189],
      [4442, 4161],
      [4425, 4116],
      [4397, 4101],
      [4366, 4145],
      [4312, 4109],
      [4290, 3996],
      [4265, 3958],
      [4229, 3931],
      [4211, 3893],
      [4179, 3873],
      [4117, 3925],
      [4068, 3977],
      [4025, 4034],
      [3976, 4022],
      [3984, 4206],
      [4048, 4383],
      [4036, 4481],
      [4066, 4504],
      [4050, 4578],
      [4077, 4602],
      [4073, 4755],
      [4120, 4821],
      [4176, 5092],
      [4227, 5591],
      [4220, 5844],
      [4241, 5960],
      [4290, 6150],
      [4335, 6372],
      [4343, 6534],
      [4437, 7226],
      [4515, 7262],
      [4545, 7237],
      [4585, 7316],
      [4625, 7315],
      [4635, 7345],
      [4950, 7508],
      [5049, 7538],
      [5116, 7522],
      [5124, 7380],
      [5147, 7238],
      [5161, 6831],
      [5110, 6750],
      [5113, 6706],
      [5075, 6638],
      [5057, 6561],
      [5042, 6388],
      [5092, 6219],
      [5212, 5960],
      [5215, 5755],
      [5224, 5668],
      [5191, 5577],
      [5197, 5366],
      [5208, 5275],
      [5191, 5147],
      [5180, 4899],
      [5155, 4818],
      [5143, 4708],
      [5149, 4633],
      [5140, 4580],
      [5160, 4479],
      [5172, 4406],
      [5139, 4328],
      [5040, 4279]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
    
    var lowerPillar2 = _polygon([
      [4292, 6142],
      [4324, 6328],
      [4346, 6194],
      [4320, 5980],
      [4305, 5920],
      [4317, 5702],
      [4331, 5600],
      [4366, 5502],
      [4366, 5299],
      [4334, 5195],
      [4352, 5005],
      [4367, 4843],
      [4373, 4735],
      [4372, 4638],
      [4396, 4503],
      [4403, 4365],
      [4376, 4270],
      [4362, 4216],
      [4317, 4179],
      [4312, 4109],
      [4290, 3996],
      [4265, 3958],
      [4229, 3931],
      [4211, 3893],
      [4179, 3873],
      [4117, 3925],
      [4068, 3977],
      [4025, 4034],
      [3976, 4022],
      [3984, 4206],
      [4048, 4383],
      [4036, 4481],
      [4066, 4504],
      [4050, 4578],
      [4077, 4602],
      [4073, 4755],
      [4120, 4821],
      [4176, 5092],
      [4227, 5591],
      [4220, 5844],
      [4292, 6142]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
    
    var lowerPillar3 = _polygon([
      [4244, 5966],
      [4252, 5846],
      [4274, 5760],
      [4292, 5572],
      [4265, 5347],
      [4288, 5292],
      [4285, 5173],
      [4272, 5061],
      [4256, 4891],
      [4276, 4860],
      [4294, 4779],
      [4306, 4629],
      [4332, 4607],
      [4329, 4446],
      [4337, 4319],
      [4299, 4260],
      [4283, 4172],
      [4255, 4157],
      [4224, 4207],
      [4189, 4252],
      [4145, 4288],
      [4074, 4264],
      [3984, 4206],
      [4048, 4383],
      [4036, 4481],
      [4066, 4504],
      [4050, 4578],
      [4077, 4602],
      [4073, 4755],
      [4120, 4821],
      [4176, 5092],
      [4227, 5591],
      [4220, 5844],
      [4244, 5966]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
   
  // === Pedestal by NWRR
    var pedestal = _polygon([
      [4768, 6904],
      [4737, 7008],
      [4712, 7042],
      [4627, 7319],
      [4637, 7347],
      [4952, 7508],
      [5056, 7537],
      [5071, 7428],
      [5101, 7306],
      [5044, 7221],
      [5026, 7097],
      [5026, 7054],
      [4914, 6886],
      [4853, 6869],
      [4768, 6904]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.5,
      clickable:false
    });
    
  // === Upper Pillar 
    var upperPillar1 = _polygon([
      [5303, 1243],
      [5320, 1194],
      [5310, 1145],
      [5335, 1090],
      [5327, 1046],
      [5308, 1030],
      [5315, 993],
      [5218, 1000],
      [5149, 998],
      [5107, 982],
      [5132, 929],
      [5111, 910],
      [5099, 885],
      [5072, 905],
      [5050, 889],
      [5009, 748],
      [4942, 780],
      [4888, 807],
      [4851, 843],
      [4775, 858],
      [4741, 885],
      [4687, 900],
      [4634, 936],
      [4578, 912],
      [4533, 919],
      [4488, 1012],
      [4519, 1041],
      [4488, 1125],
      [4449, 1244],
      [4405, 1419],
      [4321, 1483],
      [4302, 1581],
      [4238, 1667],
      [4225, 1737],
      [4239, 1766],
      [4256, 1861],
      [4324, 1935],
      [4329, 2000],
      [4414, 2025],
      [4448, 2126],
      [4500, 2156],
      [4515, 2210],
      [4496, 2384],
      [4511, 2572],
      [4557, 2642],
      [4626, 2868],
      [4594, 3092],
      [4581, 3340],
      [4555, 3408],
      [4540, 3482],
      [4523, 3490],
      [4515, 3507],
      [4477, 3533],
      [4479, 3560],
      [4392, 3603],
      [4341, 3604],
      [4320, 3522],
      [4293, 3442],
      [4255, 3372],
      [4271, 3268],
      [4263, 3185],
      [4243, 3161],
      [4255, 3078],
      [4245, 3034],
      [4179, 3180],
      [4179, 3261],
      [4195, 3303],
      [4230, 3345],
      [4222, 3399],
      [4249, 3467],
      [4259, 3522],
      [4276, 3627],
      [4389, 3677],
      [4412, 3774],
      [4507, 3802],
      [4608, 3825],
      [4629, 3882],
      [4653, 3889],
      [4667, 3926],
      [4721, 3894],
      [4750, 3871],
      [4813, 3866],
      [4864, 3823],
      [4917, 3758],
      [4962, 3647],
      [4966, 3599],
      [4930, 3573],
      [4957, 3508],
      [4983, 3408],
      [5018, 3370],
      [5092, 3472],
      [5093, 3608],
      [5096, 3723],
      [5076, 3832],
      [5056, 3900],
      [5064, 3924],
      [5093, 3925],
      [5132, 3892],
      [5226, 3902],
      [5276, 3914],
      [5388, 3768],
      [5345, 3754],
      [5288, 3662],
      [5280, 3611],
      [5232, 3548],
      [5230, 3355],
      [5256, 3202],
      [5267, 3035],
      [5286, 2872],
      [5280, 2592],
      [5313, 2320],
      [5291, 2222],
      [5284, 2134],
      [5302, 2064],
      [5291, 1984],
      [5303, 1880],
      [5362, 1775],
      [5360, 1752],
      [5329, 1701],
      [5322, 1621],
      [5292, 1561],
      [5288, 1454],
      [5272, 1441],
      [5313, 1323],
      [5303, 1243]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
    
    var upperPillar2 = _polygon([
      [5272, 1247],
      [5298, 1151],
      [5280, 1132],
      [5275, 1105],
      [5289, 1068],
      [5308, 1030],
      [5315, 993],
      [5218, 1000],
      [5149, 998],
      [5107, 982],
      [5132, 929],
      [5111, 910],
      [5099, 885],
      [5072, 905],
      [5050, 889],
      [5009, 748],
      [4942, 780],
      [4888, 807],
      [4851, 843],
      [4775, 858],
      [4741, 885],
      [4687, 900],
      [4634, 936],
      [4650, 967],
      [4673, 991],
      [4657, 1148],
      [4677, 1180],
      [4665, 1206],
      [4663, 1305],
      [4649, 1332],
      [4657, 1433],
      [4690, 1505],
      [4687, 1552],
      [4700, 1582],
      [4684, 1679],
      [4694, 1725],
      [4681, 1807],
      [4703, 1812],
      [4690, 1939],
      [4701, 2174],
      [4707, 2253],
      [4677, 2317],
      [4676, 2367],
      [4663, 2442],
      [4670, 2476],
      [4654, 2506],
      [4648, 2713],
      [4656, 2763],
      [4665, 2799],
      [4644, 2865],
      [4648, 2970],
      [4649, 3097],
      [4622, 3167],
      [4591, 3213],
      [4581, 3340],
      [4555, 3408],
      [4540, 3482],
      [4523, 3490],
      [4515, 3507],
      [4477, 3533],
      [4479, 3560],
      [4379, 3612],
      [4389, 3677],
      [4436, 3696],
      [4412, 3774],
      [4507, 3802],
      [4608, 3825],
      [4629, 3882],
      [4653, 3889],
      [4688, 3876],
      [4701, 3820],
      [4684, 3782],
      [4698, 3760],
      [4732, 3719],
      [4768, 3678],
      [4777, 3594],
      [4809, 3501],
      [4872, 3484],
      [4957, 3508],
      [4983, 3408],
      [5018, 3370],
      [5046, 3331],
      [5023, 3250],
      [5045, 3103],
      [5079, 2977],
      [5102, 2946],
      [5201, 2957],
      [5258, 2646],
      [5264, 2494],
      [5252, 2357],
      [5262, 2224],
      [5274, 2127],
      [5302, 2064],
      [5291, 1984],
      [5285, 1877],
      [5249, 1831],
      [5222, 1817],
      [5177, 1678],
      [5199, 1554],
      [5220, 1451],
      [5236, 1449],
      [5243, 1388],
      [5278, 1311],
      [5272, 1247]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
    
    var upperPillar3 = _polygon([
      [5190, 1231],
      [5216, 1199],
      [5215, 1167],
      [5239, 1087],
      [5180, 1070],
      [5129, 1050],
      [5097, 1027],
      [5083, 997],
      [5051, 975],
      [5067, 937],
      [5050, 889],
      [5009, 748],
      [4942, 780],
      [4888, 807],
      [4851, 843],
      [4775, 858],
      [4741, 885],
      [4687, 900],
      [4634, 936],
      [4650, 967],
      [4673, 991],
      [4657, 1148],
      [4677, 1180],
      [4665, 1206],
      [4663, 1305],
      [4649, 1332],
      [4657, 1433],
      [4690, 1505],
      [4687, 1552],
      [4700, 1582],
      [4684, 1679],
      [4694, 1725],
      [4681, 1807],
      [4703, 1812],
      [4690, 1939],
      [4701, 2174],
      [4707, 2253],
      [4677, 2317],
      [4676, 2367],
      [4663, 2442],
      [4670, 2476],
      [4654, 2506],
      [4648, 2713],
      [4656, 2763],
      [4665, 2799],
      [4664, 2855],
      [4677, 2897],
      [4655, 2979],
      [4649, 3097],
      [4622, 3167],
      [4591, 3213],
      [4581, 3340],
      [4555, 3408],
      [4540, 3482],
      [4523, 3490],
      [4515, 3507],
      [4477, 3533],
      [4479, 3560],
      [4379, 3612],
      [4389, 3677],
      [4436, 3696],
      [4412, 3774],
      [4507, 3802],
      [4608, 3825],
      [4629, 3882],
      [4653, 3889],
      [4688, 3876],
      [4701, 3820],
      [4684, 3782],
      [4698, 3760],
      [4732, 3719],
      [4768, 3678],
      [4777, 3594],
      [4809, 3501],
      [4872, 3484],
      [4957, 3508],
      [4983, 3408],
      [5018, 3370],
      [5046, 3331],
      [5023, 3250],
      [5045, 3103],
      [5079, 2977],
      [5102, 2946],
      [5201, 2957],
      [5258, 2646],
      [5264, 2494],
      [5252, 2357],
      [5262, 2224],
      [5274, 2127],
      [5302, 2064],
      [5291, 1984],
      [5285, 1877],
      [5234, 1867],
      [5198, 1842],
      [5178, 1748],
      [5164, 1666],
      [5199, 1554],
      [5192, 1470],
      [5177, 1426],
      [5183, 1325],
      [5171, 1299],
      [5190, 1231]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
    
    var upperPillar4 = _polygon([
      [5135, 1282],
      [5139, 1206],
      [5186, 1095],
      [5124, 1071],
      [5096, 1044],
      [5083, 997],
      [5051, 975],
      [5067, 937],
      [5050, 889],
      [5009, 748],
      [4942, 780],
      [4888, 807],
      [4851, 843],
      [4775, 858],
      [4741, 885],
      [4687, 900],
      [4634, 936],
      [4650, 967],
      [4673, 991],
      [4657, 1148],
      [4677, 1180],
      [4665, 1206],
      [4663, 1305],
      [4649, 1332],
      [4657, 1433],
      [4690, 1505],
      [4687, 1552],
      [4700, 1582],
      [4684, 1679],
      [4694, 1725],
      [4681, 1807],
      [4703, 1812],
      [4690, 1939],
      [4701, 2174],
      [4707, 2253],
      [4677, 2317],
      [4676, 2367],
      [4663, 2442],
      [4670, 2476],
      [4654, 2506],
      [4648, 2713],
      [4656, 2763],
      [4665, 2799],
      [4664, 2855],
      [4677, 2897],
      [4655, 2979],
      [4649, 3097],
      [4622, 3167],
      [4591, 3213],
      [4581, 3340],
      [4555, 3408],
      [4540, 3482],
      [4523, 3490],
      [4515, 3507],
      [4477, 3533],
      [4479, 3560],
      [4379, 3612],
      [4389, 3677],
      [4436, 3696],
      [4412, 3774],
      [4507, 3802],
      [4608, 3825],
      [4629, 3882],
      [4653, 3889],
      [4688, 3876],
      [4701, 3820],
      [4684, 3782],
      [4698, 3760],
      [4732, 3719],
      [4768, 3678],
      [4777, 3594],
      [4809, 3501],
      [4872, 3484],
      [4957, 3508],
      [4983, 3408],
      [5018, 3370],
      [5046, 3331],
      [5023, 3250],
      [5045, 3103],
      [5079, 2977],
      [5102, 2946],
      [5201, 2957],
      [5258, 2646],
      [5264, 2494],
      [5252, 2357],
      [5262, 2224],
      [5274, 2127],
      [5302, 2064],
      [5291, 1984],
      [5285, 1877],
      [5234, 1867],
      [5198, 1842],
      [5161, 1790],
      [5132, 1793],
      [5115, 1811],
      [5122, 1714],
      [5086, 1662],
      [5061, 1669],
      [5067, 1604],
      [5072, 1550],
      [5101, 1500],
      [5079, 1467],
      [5103, 1367]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.35,
      fillColor: '#ffff66',
      fillOpacity: 0.05,
      clickable:false
    });
    
    var upperPillar5A = _polygon([
      [5093, 1874],
      [5077, 1933],
      [5067, 2001],
      [5033, 2010],
      [4997, 1991],
      [4992, 1963],
      [4915, 1951],
      [4886, 1974],
      [4848, 1990],
      [4875, 2054],
      [4889, 2121],
      [4856, 2209],
      [4790, 2325],
      [4719, 2408],
      [4715, 2435],
      [4684, 2502],
      [4678, 2569],
      [4691, 2608],
      [4648, 2713],
      [4656, 2763],
      [4665, 2799],
      [4664, 2855],
      [4677, 2897],
      [4655, 2979],
      [4649, 3097],
      [4622, 3167],
      [4591, 3213],
      [4581, 3340],
      [4555, 3408],
      [4540, 3482],
      [4523, 3490],
      [4515, 3507],
      [4477, 3533],
      [4479, 3560],
      [4379, 3612],
      [4389, 3677],
      [4436, 3696],
      [4412, 3774],
      [4507, 3802],
      [4608, 3825],
      [4629, 3882],
      [4653, 3889],
      [4688, 3876],
      [4701, 3820],
      [4684, 3782],
      [4698, 3760],
      [4732, 3719],
      [4768, 3678],
      [4777, 3594],
      [4809, 3501],
      [4872, 3484],
      [4957, 3508],
      [4983, 3408],
      [5018, 3370],
      [5046, 3331],
      [5023, 3250],
      [5045, 3103],
      [5079, 2977],
      [5102, 2946],
      [5201, 2957],
      [5258, 2646],
      [5264, 2494],
      [5252, 2357],
      [5262, 2224],
      [5274, 2127],
      [5302, 2064],
      [5291, 1984],
      [5285, 1877],
      [5234, 1867],
      [5198, 1842],
      [5165, 1793],
      [5132, 1794],
      [5102, 1829],
      [5093, 1874]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.35,
      fillColor: '#ffff66',
      fillOpacity: 0.05,
      clickable:false
    });
 
    var upperPillar5B = _polygon([
      [4685, 898],
      [4741, 886],
      [4775, 861],
      [4851, 843],
      [4939, 935],
      [4922, 1008],
      [4923, 1066],
      [4894, 1140],
      [4916, 1217],
      [4907, 1346],
      [4867, 1422],
      [4810, 1431],
      [4818, 1500],
      [4791, 1554],
      [4702, 1582],
      [4684, 1547],
      [4690, 1504],
      [4656, 1433],
      [4651, 1331],
      [4662, 1309],
      [4663, 1205],
      [4678, 1180],
      [4658, 1150],
      [4672, 990],
      [4656, 972],
      [4635, 933],
      [4685, 898]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
    
    var upperPillar6 = _polygon([
      [5093, 1874],
      [5110, 1936],
      [5121, 1974],
      [5120, 2033],
      [5104, 2088],
      [5065, 2112],
      [5063, 2086],
      [5011, 2082],
      [4982, 2046],
      [4932, 2020],
      [4929, 1992],
      [4910, 1989],
      [4889, 2121],
      [4856, 2209],
      [4790, 2325],
      [4719, 2408],
      [4715, 2435],
      [4684, 2502],
      [4678, 2569],
      [4691, 2608],
      [4648, 2713],
      [4656, 2763],
      [4665, 2799],
      [4664, 2855],
      [4677, 2897],
      [4655, 2979],
      [4649, 3097],
      [4622, 3167],
      [4591, 3213],
      [4581, 3340],
      [4555, 3408],
      [4540, 3482],
      [4523, 3490],
      [4515, 3507],
      [4477, 3533],
      [4479, 3560],
      [4379, 3612],
      [4389, 3677],
      [4436, 3696],
      [4412, 3774],
      [4507, 3802],
      [4608, 3825],
      [4629, 3882],
      [4653, 3889],
      [4688, 3876],
      [4701, 3820],
      [4684, 3782],
      [4698, 3760],
      [4732, 3719],
      [4768, 3678],
      [4777, 3594],
      [4809, 3501],
      [4872, 3484],
      [4957, 3508],
      [4983, 3408],
      [5018, 3370],
      [5046, 3331],
      [5023, 3250],
      [5045, 3103],
      [5079, 2977],
      [5102, 2946],
      [5201, 2957],
      [5258, 2646],
      [5264, 2494],
      [5252, 2357],
      [5262, 2224],
      [5274, 2127],
      [5302, 2064],
      [5291, 1984],
      [5285, 1877],
      [5234, 1867],
      [5198, 1842],
      [5165, 1793],
      [5132, 1794],
      [5102, 1829],
      [5093, 1874]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.1,
      clickable:false
    });
    
  // === The Visor
    var Visor1 = _polygon([
      [4998, 468],
      [4977, 558],
      [4986, 656],
      [5047.6366258111, 651.889777029871],
      [5070, 714],
      [5162, 712],
      [5206, 753],
      [5215, 796],
      [5308, 780],
      [5315, 796],
      [5452, 791],
      [5525, 806],
      [5700.10670511896, 730.031973075306],
      [5803, 749],
      [5854, 767],
      [5871, 827],
      [5988, 852],
      [6072, 864],
      [6156.43547224225, 874.294488851494],
      [6277, 884],
      [6404.61427541456, 892.327303323518],
      [6560.72674837779, 858.265320431919],
      [6708.83345349676, 816.188753330529],
      [6772.8795962509, 782.126770438929],
      [6635, 762],
      [6436, 741],
      [6311, 726],
      [6079, 694],
      [5771, 639],
      [5779, 624],
      [5621, 590],
      [5533.9870223504, 565.732996774646],
      [5415.90194664744, 533.674659935494],
      [5241.77649603461, 517.645491515918],
      [5139.70295602019, 475.568924414529],
      [5081, 463],
      [4998, 468]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.25,
      clickable:false
    });

    var Visor2 = _polygon([
      [4998, 468],
      [4977, 558],
      [4986, 656],
      [5047.6366258111, 651.889777029871],
      [5070, 714],
      [5162, 712],
      [5206, 753],
      [5215, 796],
      [5308, 780],
      [5315, 796],
      [5452, 791],
      [5525, 806],
      [5700.10670511896, 730.031973075306],
      [5803, 749],
      [5854, 767],
      [5771, 639],
      [5779, 624],
      [5621, 590],
      [5533.9870223504, 565.732996774646],
      [5415.90194664744, 533.674659935494],
      [5241.77649603461, 517.645491515918],
      [5139.70295602019, 475.568924414529],
      [5081, 463],
      [4998, 468]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#ffff66',
      fillOpacity: 0.25,
      clickable:false
    }); 
    
// === DNWF Start
    var DNWF1 = _polyline([
      [5192, 7558.25],
      [5222, 7520.25],
      [5230, 7434.25],
      [5214, 7402.25],
      [5222, 7264.25],
      [5234, 7100.25],
      [5268, 6890.25]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.75,
      clickable:false
    });
    
    var DNWF2 = _polyline([
      [5186, 7139],
      [5198, 7029],
      [5260, 6873],
      [5320, 6712],
      [5505, 6366],
      [5561, 6301]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.75,
      clickable:false
    });
    
    var DNWF3 = _polygon([
      [5356, 7622],
      [5180, 7546],
      [5194, 7272],
      [5182, 7054],
      [5344, 6658],
      [5476, 6420],
      [5564, 6304],
      [5610, 6376],
      [5606, 6454],
      [5634, 6528],
      [5660, 6576],
      [5622, 6738],
      [5618, 6850],
      [5596, 7024],
      [5588, 7244],
      [5554, 7450],
      [5524, 7658],
      [5356, 7622]
    ], {
      color: 'black',
      weight: 1,
      opacity: 0.25,
      fillColor: '#ffff66',
      fillOpacity: 0.2,
      clickable:false
    }); 
    
  // === Trees    
    var trees1 = _polygon([
      [1012, 3352],
      [736, 3532],
      [308, 3464],
      [0, 3684],
      [24, 4356],
      [336, 4580],
      [628, 4576],
      [756, 4272],
      [920, 3932],
      [1012, 3352]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#00cc33',
      fillOpacity: 1,
      clickable:false
    });
  
    var trees2 = _polygon([
      [1116, 4008],
      [1040, 4476],
      [848, 4872],
      [808, 5256],
      [944, 5628],
      [1288, 5476],
      [1332, 4988],
      [1320, 4664],
      [1116, 4008]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#00cc33',
      fillOpacity: 1,
      clickable:false
    });
    
    var trees3 = _polygon([
      [6040, 7880],
      [5112, 7584],
      [4400, 7448],
      [4056, 7248],
      [3232, 6632],
      [2352, 5984],
      [2400, 6544],
      [2144, 5840],
      [1744, 5464],
      [1416, 5616],
      [1240, 5856],
      [928, 6344],
      [1200, 6616],
      [1504, 6944],
      [2064, 7392],
      [2896, 7648],
      [3104, 7904],
      [3424, 7696],
      [3104, 7072],
      [3040, 6672],
      [3464, 7000],
      [3768, 7176],
      [3912, 7512],
      [4096, 7912],
      [4288, 8160],
      [4536, 7808],
      [4872, 8128],
      [6544, 8160],
      [6040, 7880]
    ], {
      color: 'black',
      weight: 2,
      opacity: 0.5,
      fillColor: '#00cc33',
      fillOpacity: 1,
      clickable:false
    });
    
  //--------Prepare Polylines--------  
  //Note: One method for generating large arrays for complex polylines is follows:
      // 1. Draw a vector path in Adobe Photoshop over the original, full size image.
      // 2. Select 'Image>Image Rotation>Flip Canvas Vertical' to invert the line. This is important as the y-coordinate system is inverse in Photoshop to Leaflet.
      // 3. Export the path to an Adobe Illustrator *.ai file
      // 4. Open the *.ai file in any text editor 
      // 5. Do a global find and replace of '.0000 1' to '.0000],' and '.0000' to '.0000,'
      // 6. Manually change '.0000 M' to '.0000],'
      // 7. Import text file into Excel, as the coordinates are in points and need to be converted to pixels
      // 8. Convert the points to pixels in Excel, and append "[", ",", & "]," characters with the two columns with converted values into a new column.
      // 9. Copy the column and paste it as an array within a polyline variable defined below.
  
    var routeNWRR = _polyline([
      [4164, 7144],
      [4159.99711607787, 7094.91067171505],
      [4153.99279019466, 7056.84139671855],
      [4132.97764960346, 7025.78488290562],
      [4131.97692862292, 6989.71925396158],
      [4122.97043979813, 6958.66274014865],
      [4127.97404470079, 6930.61169541439],
      [4136.9805335256, 6900.55700462768],
      [4140.98341744773, 6860.48408357874],
      [4137.98125450613, 6815.40204739868],
      [4137.98125450613, 6752.2871967466],
      [4132.97764960346, 6670.13770859627],
      [4148.989185292, 6644.09030991446],
      [4165.00072098053, 6608.02468097041],
      [4134.97909156453, 6602.01374281307],
      [4134.97909156453, 6554.92806058056],
      [4136.9805335256, 6503.83508624316],
      [4137.98125450613, 6433.70747440752],
      [4135.97981254506, 6371.59444678166],
      [4132.97764960346, 6323.50694152293],
      [4124.97188175919, 6293.45225073622],
      [4107.95962509012, 6275.4194362642],
      [4090.94736842105, 6248.37021455616],
      [4106.95890410959, 6230.33740008414],
      [4096.95169430425, 6195.27359416632],
      [4078.93871665465, 6141.17515075024],
      [4069.93222782985, 6090.08217641285],
      [4069.93222782985, 6048.00560931146],
      [4061.92645998558, 6010.93815734119],
      [4050.91852919971, 5969.86341326602],
      [4041.91204037491, 5931.79413826953],
      [4047.91636625811, 5905.74673958771],
      [4056.92285508291, 5888.71574814192],
      [4061.92645998558, 5876.69387182723],
      [4067.93078586878, 5851.64829617164],
      [4066.93006488825, 5829.60818959473],
      [4068.93150684932, 5800.55532183424],
      [4064.92862292718, 5773.50610012621],
      [4059.92501802451, 5749.46234749684],
      [4059.92501802451, 5720.40947973636],
      [4056.92285508291, 5683.34202776609],
      [4053.92069214131, 5634.25269948114],
      [4040.91131939438, 5606.20165474688],
      [4033.90627253064, 5580.15425606507],
      [4019.89617880317, 5549.09774225214],
      [4010.88968997837, 5505.0175290983],
      [3997.88031723144, 5482.97742252139],
      [3981.8687815429, 5466.94825410181],
      [3975.8644556597, 5445.90997055111],
      [3956.85075702956, 5420.86439489553],
      [3956.85075702956, 5391.81152713504],
      [3963.8558038933, 5350.73678305988],
      [3979.86733958183, 5315.67297714206],
      [3992.87671232877, 5269.58911793577],
      [3998.88103821197, 5256.56541859487],
      [4016, 5244],
      [3990, 5220],
      [3958, 5181],
      [3941, 5144],
      [3941, 5106],
      [3931, 5075],
      [3928, 5046],
      [3924, 5004],
      [3922, 4959],
      [3912, 4894],
      [3887.80100937275, 4851.82891600056],
      [3875.79235760635, 4792.72135745337],
      [3844.77000720981, 4753.65025943065],
      [3844.77000720981, 4709.57004627682],
      [3824.75558759913, 4656.47342588697],
      [3787.72891131939, 4580.33487589398],
      [3779.72314347513, 4548.27653905483],
      [3767.71449170872, 4497.18356471743],
      [3802.7397260274, 4460.11611274716],
      [3817.7505407354, 4427.05595288178],
      [3834.76279740447, 4406.01766933109],
      [3857.77937995674, 4399.00490814752],
      [3895.80677721702, 4393.99579301641],
      [3946.84354722423, 4387.98485485907],
      [3984.8709444845, 4368.95021736082],
      [4018.89545782264, 4340.89917262656],
      [4014.8925739005, 4294.81531342028],
      [4011.8904109589, 4250.73510026644],
      [4027.90194664744, 4222.68405553218],
      [4034.90699351118, 4199.64212592904],
      [4019.89617880317, 4166.58196606367],
      [4012.89113193944, 4124.50539896228],
      [4016.89401586157, 4077.41971672977],
      [4044.91420331651, 4036.34497265461],
      [4089.94664744052, 4040.3522647595],
      [4129.97548666186, 4036.34497265461],
      [4165.00072098053, 4005.28845884168],
      [4213.03532804614, 3995.27022857944],
      [4239.05407354001, 3980.24288318608],
      [4250.06200432588, 3949.18636937316],
      [4227.04542177361, 3933.15720095358],
      [4216.03749098774, 3908.11162529799],
      [4204.02883922134, 3889.07698779975],
      [4184.01441961067, 3873.04781938017],
      [4194, 3845],
      [4202, 3820],
      [4211, 3791],
      [4216, 3761],
      [4224, 3717],
      [4253.06416726748, 3776.87280886271],
      [4272.07786589762, 3752.82905623335],
      [4281.08435472242, 3735.79806478755],
      [4304.10093727469, 3751.82723320712],
      [4319.1117519827, 3747.81994110223],
      [4338.12545061283, 3746.81811807601],
      [4352.1355443403, 3745.81629504978],
      [4372.14996395097, 3717.76525031552],
      [4378.15428983417, 3701.73608189595],
      [4389.16222062004, 3685.70691347637],
      [4397.16798846431, 3667.67409900435],
      [4399.16943042538, 3648.6394615061],
      [4386.16005767844, 3632.61029308652],
      [4379.15501081471, 3614.5774786145],
      [4413.17952415285, 3598.54831019492],
      [4441.19971160779, 3585.52461085402],
      [4466.21773612113, 3576.50820361801],
      [4483.22999279019, 3560.47903519843],
      [4516.2537851478, 3540.44257467396],
      [4544.27397260274, 3488.34777731033],
      [4548.27685652487, 3440.26027205161],
      [4556.28262436914, 3405.19646613378],
      [4584, 3310],
      [4580, 3191],
      [4599, 3212],
      [4623.33093006489, 3180.78810825971],
      [4643.34534967556, 3124.68601879119],
      [4650, 3064],
      [4643, 2994],
      [4641, 2928],
      [4641, 2877],
      [4648, 2835],
      [4663.35976928623, 2802.09900434722],
      [4653.35255948089, 2762.02608329827],
      [4648.34895457823, 2714.94040106577],
      [4666.36193222783, 2663.84742672837],
      [4690, 2606],
      [4678.37058399423, 2572.68153134203],
      [4687.37707281903, 2545.63230963399],
      [4686.3763518385, 2503.5557425326],
      [4702.38788752704, 2464.48464450989],
      [4721.40158615717, 2434.42995372318],
      [4721.40158615717, 2420.40443135605],
      [4733.41023792358, 2405.3770859627],
      [4761.43042537852, 2370.31328004487],
      [4787.44917087239, 2342.26223531062],
      [4811.46647440519, 2307.19842939279],
      [4850.494592646, 2228.05441032113],
      [4870.50901225667, 2194.99425045576],
      [4880.516222062, 2162.9359136166],
      [4897.52847873107, 2118.85570046277],
      [4900.53064167268, 2081.7882484925],
      [4912.53929343908, 1987.61688402749],
      [4927.55010814708, 1989.62053007993],
      [4931.55299206922, 2017.67157481419],
      [4953.56885364095, 2025.68615902398],
      [4973.58327325162, 2029.69345112887],
      [4997.60057678443, 2042.71715046978],
      [5010.60994953136, 2066.76090309914],
      [5028.62292718097, 2080.78642546627],
      [5049.63806777217, 2081.7882484925],
      [5050.6387887527, 2016.66975178797],
      [5071.65392934391, 2004.64787547329],
      [5079.65969718818, 1972.58953863413],
      [5083.66258111031, 1936.52390969009],
      [5098.67339581831, 1914.48380311317],
      [5114.68493150685, 1898.45463469359],
      [5131.69718817592, 1878.41817416912],
      [5153.71304974766, 1858.38171364465],
      [5186.73684210526, 1851.36895246109],
      [5212.75558759914, 1841.35072219885],
      [5219.76063446287, 1820.31243864816],
      [5211.7548666186, 1797.27050904501],
      [5201.74765681327, 1774.22857944187],
      [5188.73828406633, 1725.13925115692],
      [5180.73251622206, 1693.08091431777],
      [5178.731074261, 1667.03351563596],
      [5185.73612112473, 1634.9751787968],
      [5189.73900504686, 1612.93507221988],
      [5193.741888969, 1591.89678866919],
      [5194.74260994953, 1573.86397419717],
      [5196.7440519106, 1553.8275136727],
      [5215.75775054074, 1545.81292946291],
      [5229.76784426821, 1536.7965222269],
      [5224.76423936554, 1518.76370775487],
      [5219.76063446287, 1501.73271630907],
      [5222.76279740447, 1482.69807881083],
      [5231.76928622927, 1453.64521105034],
      [5241.77649603461, 1448.63609591923],
      [5242.77721701514, 1421.58687421119],
      [5246.78010093728, 1397.54312158183],
      [5259.78947368421, 1364.48296171645],
      [5275.80100937275, 1334.42827092974],
      [5285.80821917808, 1310.38451830038],
      [5279.80389329488, 1277.324358435],
      [5276.80173035328, 1249.27331370074],
      [5283.80677721702, 1224.22773804515],
      [5290.81182408075, 1191.16757817978],
      [5296.81614996395, 1164.11835647174],
      [5299.81831290555, 1147.08736502594],
      [5308.82480173035, 1139.07278081615],
      [5315.82984859409, 1117.03267423924],
      [5324.83633741889, 1102.00532884588],
      [5332.84210526316, 1083.97251437386],
      [5327.83850036049, 1065.93969990184],
      [5321.83417447729, 1044.90141635114],
      [5311.82696467195, 1033.88136306268],
      [5310.82624369142, 1013.84490253821],
      [5314.82912761356, 998.81755714486],
      [5303.82119682769, 990.802972935072],
      [5273.79956741168, 991.804795961296],
      [5245.77937995674, 992.806618987519],
      [5221.76207642394, 999.819380171084],
      [5189.73900504686, 997.815734118637],
      [5165.72170151406, 995.81208806619],
      [5133.69863013699, 992.806618987519],
      [5119.68853640952, 987.797503856402],
      [5108.68060562365, 983.790211751508],
      [5110.68204758472, 969.764689384378],
      [5123.69142033165, 945.720936755013],
      [5131.69718817592, 929.691768335437],
      [5119.68853640952, 917.669892020754],
      [5113.68421052632, 896.63160847006],
      [5100.67483777938, 882.606086102931],
      [5086.66474405191, 892.624316365166],
      [5079.65969718818, 873.589678866919],
      [5072.65465032444, 843.534988080213],
      [5056, 788],
      [5009.60922855083, 848.544103211331],
      [5003.60490266763, 769.400084139672],
      [4961.57462148522, 791.440190716589],
      [4953.56885364095, 764.390969008554],
      [4946.56380677722, 730.328986116954],
      [4931.55299206922, 717.305286776048],
      [4896.52775775054, 712.29617164493],
      [4867.50684931507, 722.314401907166],
      [4836.48449891853, 731.330809143178],
      [4812.46719538573, 734.336278221848],
      [4794.45421773612, 717.305286776048],
      [4817.47080028839, 687.250595989342],
      [4829.47945205479, 664.208666386201],
      [4837.48521989906, 636.157621651942],
      [4838.4859408796, 619.126630206142],
      [4834.48305695746, 618.124807179919],
      [4830.48017303533, 617.122984153695],
      [4820.47296322999, 612.113869022577],
      [4821.47368421053, 590.07376244566],
      [4839.48666186013, 581.057355209648],
      [4865.505407354, 571.039124947413],
      [4881.51694304254, 560.019071658954],
      [4895.52703677001, 536.977142055813],
      [4918.54361932228, 529.964380872248],
      [4924.54794520548, 516.940681531342],
      [4950.56669069935, 514.937035478895],
      [4962.57534246575, 498.907867059318],
      [4975.58471521269, 491.895105875754],
      [4981.58904109589, 475.865937456177],
      [4997.60057678443, 464.845884167718],
      [5008.6085075703, 453.82583087926]
    ], {
     // clickable:false,
    }).bindLabel('Northwest Regular Route (VI, 5.9 C1+)', { direction: 'auto' });
    
    var routeDeathSlabs = _polyline([
      [4344, 7241],
      [4752, 7432],
      [5576, 7792],
      [6184, 8153]
    ], {
      color: 'yellow',
      weight: 2,
      //clickable:false,
    }).bindLabel('Death Slabs Approach/Descent', { direction: 'auto' });
    
    var routeTrail = _polyline([
      [394, 3836],
      [564, 4064],
      [844, 4200],
      [772, 4288],
      [892, 4392],
      [744, 4472],
      [1292, 4632],
      [1468, 4876],
      [1332, 5000],
      [1490, 5104],
      [1384, 5132],
      [1528, 5198],
      [1588, 5293],
      [1602, 5371],
      [1679, 5484],
      [1903, 5623],
      [2158, 5871],
      [2232, 5865],
      [2340, 5921],
      [2426, 6004],
      [2472, 6082],
      [2603, 6211],
      [2705, 6258],
      [2790, 6302],
      [3016, 6458],
      [3250, 6666],
      [3547, 6864],
      [3733, 6972],
      [3964, 7088],
      [4110, 7174]
    ], {
      color: 'red',
      weight: 2
      //clickable:false,
    }).bindLabel('Cables Trail Approach/Descent', { direction: 'auto' });
  
  //-------Animated Marker-------
  var myiconSize = [50, 75];     //Original icon size
  var iconAnchorSize = [25, 75];    //Original icon anchor location within icon
  var data = scaleIcon(0.5, myiconSize, iconAnchorSize);
  
  var myIconHonnold= L.icon({  
    iconUrl: imagesDir + 'Alex-Honnold-small.png',
    iconSize: [scaledIconSize[0],scaledIconSize[1]],
    iconAnchor: [scaledAnchorSize[0],scaledAnchorSize[1]],
  });
  
  var animatedHonnold = L.animatedMarker(routeNWRR.getLatLngs(), {
    icon: myIconHonnold, 
    zIndexOffset:3000,
    distance: 300,  // meters
    interval: 2000, // milliseconds
    autoStart: false,
    onEnd: function() {
      $(this._shadow).fadeOut();
      $(this._icon).fadeOut(500, function(){
        map.removeLayer(this);
      });
      }
  });
  
  //Donkey Kong climbing
  var myiconSize = [40, 34];     //Original icon size
  var iconAnchorSize = [20, 34];    //Original icon anchor location within icon
  var data = scaleIcon(1.0, myiconSize, iconAnchorSize);
  var myIconKong= L.icon({  
    iconUrl: imagesDir + 'Donkey-Kong-climb.gif',
    iconSize: [scaledIconSize[0],scaledIconSize[1]],
    iconAnchor: [scaledAnchorSize[0],scaledAnchorSize[1]],
  });
  
  //Donkey Kong throwing a barrel
  var myiconSize = [40, 34];     //Original icon size
  var iconAnchorSize = [20, 34];    //Original icon anchor location within icon
  var data = scaleIcon(1.0, myiconSize, iconAnchorSize);
  var myIconKongBarrel = L.icon({  
    iconUrl: imagesDir + 'Donkey-Kong-Barrel.gif',
    iconSize: [scaledIconSize[0],scaledIconSize[1]],
    iconAnchor: [scaledAnchorSize[0],scaledAnchorSize[1]],
  });
  
  var data = scaleIcon(2.0, myiconSize, iconAnchorSize);
  var myIconKongBarrelSmall = L.icon({  
    iconUrl: imagesDir + 'Donkey-Kong-Barrel.gif',
    iconSize: [scaledIconSize[0],scaledIconSize[1]],
    iconAnchor: [scaledAnchorSize[0],scaledAnchorSize[1]]
  });
  
  var animatedKongBarrel = _marker([5008.6085075703, 453.82583087926], {icon:myIconKongBarrel, clickable:false, zIndexOffset:3000});
  
  //Barrels
  var barrelLineL1 = _polyline([
    [4966, 436.25],
    [4950, 440],
    [4384, 660],
    [4156, 992],
    [3648, 2848],
    [3280, 4784]
  ], {
    color: 'red',
    weight: 2,
    opacity:0,
    clickable:false
  }).addTo(map);
  
  var barrelLineL2 = _polyline([
    [4966, 436.25],
    [4900, 460],
    [4384+200, 660],
    [4156+300, 992],
    [3648+400, 3848],
    [3280+800, 5784]
  ], {
    color: 'red',
    weight: 2,
    opacity:0,
    clickable:false
  }).addTo(map);
  
  var barrelLineR1 = _polyline([
    [5054, 438.25],
    [5065, 440],
    [5696, 832],
    [6432, 1664],
    [7184, 3552],
    [7680, 6752]
  ], {
    color: 'red',
    weight: 2,
    opacity:0,
    clickable:false
  }).addTo(map);
 
 var barrelLineR2 = _polyline([
    [5054, 438.25],
    [5200, 540],
    [5696-200, 1032],
    [6432-400, 1864],
    [7184-400, 3852],
    [7680-800, 7752]
  ], {
    color: 'red',
    weight: 2,
    opacity:0,
    clickable:false
  }).addTo(map);
 
  var myiconSize = [40, 34];     //Original icon size
  var iconAnchorSize = [20, 34];    //Original icon anchor location within icon
  var data = scaleIcon(0.25, myiconSize, iconAnchorSize);
  var myIconBarrel = L.icon({  
    iconUrl: imagesDir + 'Barrel.png',
    iconSize: [scaledIconSize[0],scaledIconSize[1]],
    iconAnchor: [scaledAnchorSize[0],scaledAnchorSize[1]]
  });
 
  //Donkey Kong Animation
  var barrelCount = 0;
  var animatedBarrel = [];
  
    //Creates new barrel icons and added to an array as the function is iterated. Different paths are associated with different barrels
  function animatedBarrelAdd(barrelCount, path){
        animatedBarrel[barrelCount] = L.animatedMarker(path, {
          icon: myIconBarrel, 
          zIndexOffset:3000,
          distance: 600,  // meters
          interval: 1000, // milliseconds
          autoStart: false,
          onEnd: function() {
            $(this._shadow).fadeOut();
            $(this._icon).fadeOut(5, function(){
              map.removeLayer(this);
            });
            }
        }); 
  };

    //Iterates the the generation of thrown barrels, and sets them on different paths
    //TO DO: barrelCount is not iterating up from 0 so barrels are only going on one of two paths for each side.
  function barrelThrows(){
        var path;
        var barrelLine;
        if(barrelCount===1 || barrelCount===5){
          path = barrelLineL1.getLatLngs() 
          // barrelLine = "barrelLineL1"
        } else {
          path = barrelLineL2.getLatLngs()
          // barrelLine = "barrelLineL2"
          }
        // console.log(barrelCount);
        // console.log(barrelLine);
        animatedBarrelAdd(barrelCount, path)
        map.addLayer(animatedBarrel[barrelCount]);      //Left barrel
        animatedBarrel[barrelCount].start();
        barrelCount = barrelCount++;
        // console.log(barrelCount);
        
        var delay = 1950/2; 
        setTimeout(function(){
          if(barrelCount===2 || barrelCount===6){
          path = barrelLineR1.getLatLngs() 
          // barrelLine = "barrelLineR1"
        } else {
          path = barrelLineR2.getLatLngs()
          // barrelLine = "barrelLineR2"
          }
          // console.log(barrelCount);
          // console.log(barrelLine);
          animatedBarrelAdd(barrelCount, path)
          map.addLayer(animatedBarrel[barrelCount]);      //Right barrel
          animatedBarrel[barrelCount].start();
          barrelCount = barrelCount++ ;   
        }, delay);        
  };
  
  var animatedKong = L.animatedMarker(routeNWRR.getLatLngs(), {
    icon: myIconKong, 
    zIndexOffset:3000,
    distance: 300,  // meters
    interval: 1500, // milliseconds
    autoStart: false,
    onEnd: function() {
        map.removeLayer(this);
      map.addLayer(animatedKongBarrel);   //Stationary Donkey Kong GIF
      setTimeout(function(){barrelThrows()},600);
      setTimeout(function(){
      var barrelThrow = setInterval(function(){barrelThrows()}, 1930);  //Barrel throw animation
      setTimeout(function(){
        clearInterval(barrelThrow); //End barrel throw animation
      }, 10000-1950/4);
      },1950/3);
      var delay = 10000;  //Delay until barrel throw ends & Donkey Kong disappears
      setTimeout(function(){
        $(animatedKongBarrel._icon).fadeOut(500, function(){
        map.removeLayer(animatedKongBarrel);
      });
        // clearInterval(barrelThrow); //End barrel throw animation
      }, delay);
    }
  }); 
 
  //Starts animation upon button click & hides button
//Donkey Kong Animation Click Function
 $(function() {
  $('#start').click(function() {
    var divHeight = $("#map").innerHeight();
    if (divHeight === 512) {
    map.setView([8192, 4096], 14);  //resets view to outermost zoom, as this is the best spot to view the animation
    } else {
    map.setView([8192, 4096], 15); //resets view to second outermost zoom for full-screen mode, as this is the best spot to view the animation
    }
    
    console.log('start');
    map.addLayer(animatedHonnold);      
    animatedHonnold.start();
    
    var delay = 10000 //10 seconds
    setTimeout(function(){
      map.addLayer(animatedKong);      
      animatedKong.start();
    }, delay);
    
    $(this).hide();
  });
 });
  
    // === Assemble Layer Groups  
    //Note: Z-Index of polygons & polylines determined only by order in which they are added to the map.
    //Listing in the layers control is determined by the order by which layers are added to the map.
    //Layers must be added to the map here if they are to appear on load, and for the checkboxes to be checked. 
    //If layers are not added, the checkbox will start out empty and the layer hidden to start.
    
    //Adds all polylines to one layer
    var routes = L.layerGroup([
      routeNWRR,
      routeTrail,
      routeDeathSlabs
    ]).addTo(map);
        
    //Adds all belay markers to one layer
    var belaysLayer = L.layerGroup(belays).addTo(map);
    
    //Adds all bolt circles to one layer
    var boltsLayer = L.layerGroup(bolts).addTo(map);
    
    //Adds all photo markers to one layer
    var photosLayer = L.layerGroup(photos).addTo(map);
    
    //Adds all label icons to one layer 
    var labelsLayer = L.layerGroup(labels).addTo(map);
    
    //Adds all rating label icons to one layer 
    var ratingsLayer = L.layerGroup(ratings).addTo(map);  
    
    //Adds all polygons to one layer
    //Select features around the NWRR
      var features = L.layerGroup([
        pedestalFinalExam,
        dikeCorner,
        lowerPillar1,
        lowerPillar2,
        lowerPillar3,
        pedestal,
        upperPillar1,
        upperPillar2,
        upperPillar3,
        upperPillar4,
        upperPillar5A,
        upperPillar5B,
        upperPillar6,
        Visor1,
        Visor2,
        DNWF3,
        DNWF2,
        DNWF1
    ]).addTo(map);
    
    //Illustrated overlay of the entire Half Dome scene
    var illustration = L.layerGroup([
        Background,
        Foreground,
        HalfDome,
        nwFace,
        nRidge,
        nRidgeFront,
        trees1,
        trees2,
        trees3
      ]);
  
  
  //=== Zoom
    //Scales all markers depending on level of zoom
    map.on('zoomend', function(e){
      var zoomLevel = map.getZoom();
      if (zoomLevel <= 14){ 
        for (i=0; i < belayCoords.length; i++){
          belays[i].setIcon(myIconBelaySmall[i]);
        }
        for (i=0; i < photos.length; i++){
          photos[i].setIcon(myIconBlank);
        }
      }
      if (zoomLevel > 14){
        for (i=0; i < photos.length; i++){
          if (myPhotos[i][3] == 'clicked-false'){
            photos[i].setIcon(myPhotoIcon);
          } else {
          photos[i].setIcon(myPhotoIconClick);
          }
        }
      }
      if (zoomLevel > 14 && zoomLevel < 17){ 
        for (i=0; i < belayCoords.length; i++){
          belays[i].setIcon(myIconBelay[i]);
        }
      }
      if (zoomLevel >= 17){
        for (i=0; i < belayCoords.length; i++){
          belays[i].setIcon(myIconBelayLarge[i]);
        }
      }
      if (zoomLevel === 18){
        labels[labels.length-1].setIcon(myIconTourists);
        animatedKongBarrel.setIcon(myIconKongBarrelSmall);
      }
      
      if (zoomLevel < 18){
        labels[labels.length-1].setIcon(myIconBlank);
        animatedKongBarrel.setIcon(myIconKongBarrel);
      }
    });  
    
    //Scales labels with zoom, hides labels beyond a certain zoom level
    map.on('zoomend', function(e){
      var zoomLevel = map.getZoom();
      switch(zoomLevel){
      case 18:
        $(".my-div-icon").css({
          "font-size":"20px",
          "line-height":"20px"
        });
        $(".my-div-icon-far").css({
          "display":"none"
        });
        $(".my-div-rating-icon").css({
          "display":"inline"
        });
        break;
      case 17:      
        $(".my-div-icon").css({
          "font-size":"10px",
          "line-height":"10px"
        });
        $(".my-div-icon-far").css({
          "display":"none"
        });
         $(".my-div-rating-icon").css({
          "display":"none"
        });
        break;
      case 16:
        $(".my-div-icon").css({
          "display":"inline",
          "font-size":"10px",
          "line-height":"10px"
        });
        $(".my-div-icon-far").css({
          "display":"none"
        });
        break;
      default:
        $(".my-div-icon").css({
          "display":"none"
        });
        $(".my-div-rating-icon").css({
          "display":"none"
        });
        $(".my-div-icon-far").css({
          "display":"inline",
          "font-size":"10px",
          "line-height":"10px"
        });
      break;
      }
      });
      
    //This is needed to make the labels reappear upon layer unhide
    map.on('overlayadd', function(e){
      var zoomLevel = map.getZoom();
      switch(zoomLevel){
      case 18:
        $(".my-div-icon").css({
          "display":"inline",
          "font-size":"20px",
          "line-height":"20px"
        });
        $(".my-div-rating-icon").css({
        "display":"inline"
        });
        $(".my-div-rating-icon").css({
          "display":"inline"
        });
        break;
      case 17:      
        $(".my-div-icon").css({
          "display":"inline",
          "font-size":"10px",
          "line-height":"10px"
        });
        $(".my-div-rating-icon").css({
        "display":"inline"
        });
        $(".my-div-rating-icon").css({
          "display":"none"
        });
        break;
      case 16:
        $(".my-div-icon").css({
          "display":"inline",
          "font-size":"10px",
          "line-height":"10px"
        });
        $(".my-div-rating-icon").css({
        "display":"inline"
        });
        break;
      default:
      $(".my-div-icon").css({
        "display":"none"
      });
      $(".my-div-rating-icon").css({
        "display":"none"
      });
      $(".my-div-icon-far").css({
        "display":"inline",
        "font-size":"10px",
        "line-height":"10px"
      });
      break;
      }
    });
   
  //--------Prepare Layer Controls--------
  //TO DO: Currently layers Z-index can be messed up by ordering of checking/unchecking boxes. 
  //       Also illustration underlay layer comes in on top of everything at first.
  //        1. Create even handler that automatically adds layers to map in the desired order to maintain correct z-index
  var overlayMaps = {
  "Belays": belaysLayer, 
  "Routes": routes,
  "Photos": photosLayer,
  "Labels": labelsLayer,
  "Bolts": boltsLayer,
  "Ratings": ratingsLayer,
  "Features": features
  // "Illustration Underlay": illustration
  };

  //Layers Control
  L.control.layers(null, overlayMaps).addTo(map);      //Use 'null' to make an input optional.
  
  // If using multiple base maps
  // var baseMaps = {
  // "Half Dome NW Face": map
  // "Illustration Underlay": illustration // Cannot work as a base map. Must generate tiled image?
  // };
  // L.control.layers(baseMaps,overlayMaps).addTo(map);
  
  //---Leaflet Draw
  //Makes existing group of markers editable. See Edit options below.
    //Note: for the variable to be valid, it must be an array containing the objects (markers, polylines, etc), rather than the layer of the object itself.
    // belays
    // photosMarker
    // routeNWRR
    var polyEdit = []; //[];       //Place desired objects to be edited within this variable. Single objects, such as polylines, need to be in arrays.
                                      //Arrays of objects, such as photo & belay markers, should be added as-is
    if (polyEdit.length>0){
      var myDrawnItems = L.featureGroup(polyEdit);   //Makes existing group of markers editable. See Edit options below.
      var drawnItems = myDrawnItems;    //For loading layers. Use the existing drawn items layer when loading layers. Otherwise, the 'edit' function won't work with fetching coords.
    } else {
      var drawnItems = new L.FeatureGroup(); // Default blank variable. Use if drawing & editing new shapes.
      map.addLayer(drawnItems);
    }
  //==================
    
    drawControl = new L.Control.Draw({
		// var drawControl = new L.Control.Draw({
			draw: {
				position: 'topleft',
				polygon: {
					title: 'Draw a sexy polygon!',
					allowIntersection: false,
					drawError: {
						color: '#b00b00',
						timeout: 1000
					},
					shapeOptions: {
						color: '#bada55'
					},
					showArea: true
				},
				polyline: {
					metric: false
				},
				circle: {
					shapeOptions: {
						color: '#662d91'
					}
				},
        marker: {
          repeatMode: true
        }
			},
			edit: {
				featureGroup: drawnItems
			}
		});
		// map.addControl(drawControl);
   
     //=== Captures coordinates of a shape
     //TO DO:
     // 2. Fix the create event. Coords for markers are no longer coming in now. Must be edited. Probably due to continuous marker option.
     // 4. Expand functionality:
     //   4b. To load data into shapes to delete.
     //   4c. To save & retrieve data to/from an external source
    
    var i = -1; //Keep outside of draw:created in order to have multiple items within one array/layer
    map.on('draw:created', function(e) {
      //Important! Keep this section for drawn layers to close & stay on screen
      var type = e.layerType;
        layer = e.layer
        layer.type = type;
        drawnItems.addLayer(layer);
      //=======
      
        if (type === 'marker') {
            i++; 
            coordsPix[i] = project(e.layer.getLatLng());
            coordsPix[i].type = type;
          }
        if (type === 'circle') {
          i++; 
          coordsPix[i] = project(e.layer.getLatLng());
          var circleRadius = e.layer.getRadius();
          coordsPix[i].r = circleRadius;
          coordsPix[i].type = type;
        }
        if (type !== 'circle' && type !== 'marker') {
          i++;
          coordsPix[i] = _.collect(e.layer.getLatLngs(), function (latlng) {
            return project(latlng); 
          })
          coordsPix[0][0].type = type;
        }
    });
    
    map.on('draw:edited', function (e) {
        var i = -1;    //keep i in this function, else a null value is added to each array entry
        drawnItems.eachLayer(function (layer) {
          var type = layer.type
          if (type === 'marker') {
            i++; 
            coordsPix[i] = project(layer.getLatLng());
            coordsPix[i].type = type;
          }
          if (type === 'circle') {
            i++;
            coordsPix[i] = project(layer.getLatLng()); 
            var circleRadius = layer.getRadius();
            coordsPix[i].r = circleRadius;
            coordsPix[i].type = type;
          }
          if (type !== 'circle' && type !== 'marker') {
            i++;
            coordsPix[i] = _.collect(layer.getLatLngs(), function (latlng) {
              return project(latlng); 
            })
            coordsPix[0][0].type = type;
          }  
        });
    });
          
    map.on('draw:deleted', function (e) { //DOES NOT WORK. Doesn't break it either. Keep for now. Work out later. Deletions of vertices in poly edits work.
      var coordsPix = [];
      var i = -1
      drawnItems.eachLayer(function (layer) {
          var type = layer.type
          if (type === 'marker') {
            i++; 
            coordsPix[i] = project(layer.getLatLng());
            coordsPix[i].type = type;
          }
          if (type === 'circle') {
            i++;
            coordsPix[i] = project(layer.getLatLng()); 
            var circleRadius = layer.getRadius();
            coordsPix[i].r = circleRadius;
            coordsPix[i].type = type;
          }
          if (type !== 'circle' && type !== 'marker') {
            i++;
            coordsPix[i] = _.collect(layer.getLatLngs(), function (latlng) {
              return project(latlng); 
            })
            coordsPix[0][0].type = type;
          }  
        });
    });   
};