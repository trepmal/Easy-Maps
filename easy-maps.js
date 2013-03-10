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
		// title: bubble
		// draggable: true
	}
	marker = new google.maps.Marker( markerOpts );

	infowindow = new google.maps.InfoWindow({
		// content: '<div style="height:30px;">' + bubble + '</div>'
		content: bubble
	});
	infowindow.open(map,marker);

	// var kmlLayer = new google.maps.KmlLayer('https://maps.google.com/maps/ms?ie=UTF8&t=m&authuser=0&msa=0&output=kml&msid=212043342089249462794.00048cfeb10fb9d85b995');
	// kmlLayer.setMap(gMap);
}
