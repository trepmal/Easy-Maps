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
			position: position,
			map: map,
			title: "Drag Me!",
			draggable: true
		});
		marker.setDraggable (true);
		markersArray.push(marker);
		if ( typeof(alt_info) == 'undefined' ) {
			alt_info = 'Drag me to pinpoint your location!';
		}
		infowindow = new google.maps.InfoWindow({
			content: '<div style="height:30px;">' + alt_info + '</div>'
		});
		infowindow.open(map,marker);

		update_form( map, marker );

		google.maps.event.addListener( marker, 'dragstart', function() {
			infowindow.close(map,marker);
		});

		google.maps.event.addListener( marker, 'dragend', function() {
			update_form( map, marker );
		});
	}

	function do_map( latitude, longitude ) {

		var myLatlng = new google.maps.LatLng( latitude, longitude );
		var options = {
			zoom: parseInt( document.getElementById("map_zoom").value ),
			center: myLatlng,
			mapTypeId: document.getElementById("map_type").value
		}


		map = new google.maps.Map(document.getElementById("gmap"), options );
		text = 'Drag Me!';
		if (lat == '0' && lng == '0' ) {
			text = 'Enter an address to get started';
		}
		do_marker( map, myLatlng, text );

		google.maps.event.addListener( map, 'zoom_changed', function() {
			document.getElementById("map_zoom").value = map.zoom;
		});
		google.maps.event.addListener( map, 'maptypeid_changed', function() {
			document.getElementById("map_type").value = map.mapTypeId;
		});
	}

	function showAddress() {
		address = document.getElementById("address").value;

		geocoder.geocode( { 'address': address}, function(results, status) {

			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
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

document.getElementById("longitude").onblur=gmap_init;
document.getElementById("latitude").onblur=gmap_init;
document.getElementById("map_zoom").onblur=gmap_init;
document.getElementById("map_type").onblur=gmap_init;
