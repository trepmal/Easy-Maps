<?php
if ( ! defined('ABSPATH') )
	die('-1');

@header( 'Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset') );
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Easy Map</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo includes_url('js/tinymce/tiny_mce_popup.js'); ?>"></script>
	<base target="_self" />
<style>
#search {
	background: white;
	padding: 2px 130px 2px 5px;
	position: relative;
}
#address {
	font-size: 15px;
	padding: 3px 0;
	width: 100%;
	border: 0;
	display: block;
}
#address:focus {
	background: whitesmoke;
	outline: 0;
}
#refreshmap {
	position: absolute;
	top: 2px;
	right: 3px;
	font-size: 15px;
	padding: 2px 4px;
	width: 120px;
	border: 2px solid lightgrey;
	background: whitesmoke;
}
td {
	width:33%;
}
.fields label {
	font-size: 13px;
}
.fields input,
.fields textarea {
	display: block;
	padding: 3px;
	font-size: 13px;
}
</style>
</head>
<body id="link" style="display: none">
	<form action="#">
		<table border="0" cellpadding="4" cellspacing="0" width="100%">
			<tr>
				<td colspan='3'>
					<p id="search">
						<input type="text" id="address" placeholder="search" />
						<button id="refreshmap" onclick="showAddress(); return false;">Find location</button>
					</p>

					<div id="gmap" style="height: 380px; outline: 1px solid #333;">
						<p style="line-height:380px;text-align:center;">preview map here</p>
					</div>
				</td>
			</tr>
			<tr class="fields">
				<?php
					$latitude = $longitude = 0.0;
					$map_zoom = 6;
					$map_type = 'roadmap';
				?>
				<td>
					<p><label>Latitude:<br /><input id="latitude" name="latitude" type="text" value="<?php echo $latitude; ?>" /></label><br /><small>decimal format</small><br />
					<label>Longitude:<br /><input id="longitude" name="longitude" type="text" value="<?php echo $longitude; ?>" /></label><br /><small>decimal format</small></p>
				</td>
				<td>
					<p><label>Map Zoom:<br /><input id="map_zoom" name="map_zoom" type="text" value="<?php echo $map_zoom; ?>" /></label><br /><small>0 (farthest) - 22 (closest)</small><br />
					<label>Map Type:<br /><input id="map_type" name="map_type" type="text" value="<?php echo $map_type; ?>" /></label><br /><small>roadmap, satellite, hybrid, terrain</small></p>
				</td>
				<td>
					<p><label>Bubble:<br /><textarea id="bubble" name="bubble"></textarea></label></p>
				</td>
			</tr>
		</table>

		<div class="mceActionPanel">
			<div style="float: left">
				<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel"); ?>" onclick="tinyMCEPopup.close();" />
			</div>

			<div style="float: right">
				<input type="submit" id="insert" name="insert" value="<?php _e("Insert"); ?>" onclick="insertLink(event);" />
			</div>
		</div>
	</form>
	<script language="javascript" type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script language="javascript" type="text/javascript" src="<?php echo plugins_url( 'maps.js', __FILE__ ); ?>"></script>
<script type="text/javascript">
function insertLink(evt) {

	var tagtext;

	//get the form values
	var lat = document.getElementById('latitude').value;
	var lng = document.getElementById('longitude').value;
	var zoom = document.getElementById('map_zoom').value;
	var type = document.getElementById('map_type').value;
	var bubble = document.getElementById('bubble').value;

	if ( lat != '' && lng != '' ) {
		tagtext = '[map lat='+ lat +' lng='+ lng +' zoom='+ zoom +' type='+ type +']';
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
</script>
</body>
</html>