function make_easy_map( inst, lat, lng, zoom, type, bubble ) {
	latlng = new google.maps.LatLng( lat, lng );
	var myOptions = {
		center: latlng,
		zoom: zoom,
		mapTypeId: type
	};
	map = new google.maps.Map(document.getElementById( inst ), myOptions);

	markerOpts = {
		position: latlng,
		map: map
	}
	marker = new google.maps.Marker( markerOpts );

	infowindow = new google.maps.InfoWindow({
		content: bubble
	});
	infowindow.open(map,marker);

}

//eof