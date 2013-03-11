function make_easy_map( inst, lat, lng, zoom, type, bubble ) {
	latlng = new google.maps.LatLng( lat, lng );
	var myOptions = {
		center: latlng,
		zoom: parseInt( zoom ),
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
jQuery(document).ready( function($) {
	$('.easy-google-map').each( function() {
		$map = $(this);
		lat = $map.attr('data-lat');
		lng = $map.attr('data-lng');
		zoom = $map.attr('data-zoom');
		type = $map.attr('data-type');
		bubble = $map.attr('data-content');
		make_easy_map( $map.attr('id'), lat, lng, zoom, type, bubble );
	});
});

//eof