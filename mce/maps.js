var map = null;
var geocoder = new google.maps.Geocoder();
var marker = null;
var markersArray = [];

function clear_markers() {
	if (markersArray) {
		for (i in markersArray) {
			markersArray[i].setMap(null);
		}
	}
}

function update_form( map, marker ) {

	lat = marker.getPosition().lat();
	lng = marker.getPosition().lng();

	document.getElementById("latitude").value = lat;
	document.getElementById("longitude").value = lng;
}

function do_marker( map, position, alt_info ) {

	clear_markers();

	marker = new google.maps.Marker({
		position:  position,
		map:       map,
		title:     easymaps.markertitle,
		draggable: true
	});
	marker.setDraggable (true);
	markersArray.push(marker);

	if ( typeof(alt_info) == 'undefined' ) {
		alt_info = easymaps.dragme;
	}

	infowindow = new google.maps.InfoWindow({
		content: '<div style="height:30px;">' + alt_info + '</div>'
	});
	infowindow.open( map, marker );

	// update lat/lng fields
	update_form( map, marker );

	// close window when dragging
	google.maps.event.addListener( marker, 'dragstart', function() {
		infowindow.close( map, marker );
	});

	// update when marker dropped
	google.maps.event.addListener( marker, 'dragend', function() {
		update_form( map, marker );
	});
}

function do_map( latitude, longitude ) {

	var myLatlng = new google.maps.LatLng( latitude, longitude );
	var options = {
		zoom:      parseInt( document.getElementById("map_zoom").value ),
		center:    myLatlng,
		mapTypeId: document.getElementById("map_type").value
	}

	map = new google.maps.Map(document.getElementById("gmap"), options );

	text = easymaps.markertitle;
	if ( lat == '0' && lng == '0' ) {
		text = easymaps.getstarted;
	}
	do_marker( map, myLatlng, text );

	// when map zoom changed, update form field
	google.maps.event.addListener( map, 'zoom_changed', function() {
		document.getElementById("map_zoom").value = map.zoom;
	});
	// when map type changed, update form field
	google.maps.event.addListener( map, 'maptypeid_changed', function() {
		document.getElementById("map_type").value = map.mapTypeId;
	});
}

// when updating map on given address
function showAddress() {
	address = document.getElementById("address").value;

	geocoder.geocode( { 'address': address}, function(results, status) {

		if ( status == google.maps.GeocoderStatus.OK ) {
			map.setCenter( results[0].geometry.location );
			do_marker( map, results[0].geometry.location );
		} else {
			alert("Geocode was not successful for the following reason: " + status + "\nDid you provide a city and state?");
		}

	});

	return false;
}

function gmap_init( ) {

	lat = document.getElementById("latitude").value;
	lng = document.getElementById("longitude").value;

	if ( lat == '' && lng == '' ) {
		lat = 0;
		lng = 0;
	}
	do_map(lat, lng);
}

window.onload=gmap_init;

// update map on form field change
document.getElementById("longitude").onblur=gmap_init;
document.getElementById("latitude").onblur=gmap_init;
document.getElementById("map_zoom").onchange=gmap_init;
document.getElementById("map_type").onchange=gmap_init;

// do insert on button click
function insertMapShortcode(evt) {

	var tagtext;

	//get the form values
	var lat    = document.getElementById('latitude').value;
	var lng    = document.getElementById('longitude').value;
	var zoom   = document.getElementById('map_zoom').value;
	var type   = document.getElementById('map_type').value;
	var bubble = document.getElementById('bubble').value;
	var height = document.getElementById('height').value;
	var width  = document.getElementById('width').value;

	if ( lat != '' && lng != '' ) {
		tagtext = '[map lat='+ lat +' lng='+ lng +' zoom='+ zoom +' type='+ type +' height='+ height +' width='+ width +']';
		if ( bubble != '' )
			tagtext += bubble + '[/map]';
	}
	else
		tinyMCEPopup.close();

	if(window.tinyMCE) {
		//send the shortcode to the editor
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML.
		tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches.
		tinyMCEPopup.editor.execCommand('mceRepaint');
		//close the popup window
		tinyMCEPopup.close();
	}
	return;
}
