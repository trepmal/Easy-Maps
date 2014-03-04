<?php
if ( ! defined('ABSPATH') )
	die('-1');

@header( 'Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset') );
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e( 'Easy Map', 'easy-maps' ); ?></title>
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
.fields select,
.fields textarea {
	display: block;
	padding: 3px;
	font-size: 13px;
	width: 100%;
}
.fields select {
	margin: 4px 0;
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
						<button id="refreshmap" onclick="showAddress(); return false;"><?php _e( 'Find location', 'easy-maps' ); ?></button>
					</p>

					<div id="gmap" style="height: 380px; outline: 1px solid #333;">
						<p style="line-height:380px;text-align:center;"><?php _e( 'preview map here', 'easy-maps' ); ?></p>
					</div>
				</td>
			</tr>
			<tr class="fields">
				<?php
					$latitude = $longitude = 0.0;
					$map_zoom = 6;
					$map_type = 'roadmap';
					$width    = '100%';
					$height   = '400px';
				?>
				<td style="vertical-align:top;">
					<p>
						<label><?php _e( 'Latitude', 'easy-maps' ); ?>:<br />
							<input id="latitude" name="latitude" type="text" value="<?php echo $latitude; ?>" /><small><?php _e( 'decimal format', 'easy-maps' ); ?></small>
						</label>
					</p>
					<p>
						<label><?php _e( 'Longitude', 'easy-maps' ); ?>:<br />
							<input id="longitude" name="longitude" type="text" value="<?php echo $longitude; ?>" /><small><?php _e( 'decimal format', 'easy-maps' ); ?></small>
						</label>
					</p>
				</td>
				<td style="vertical-align:top;">
					<p>
						<label><?php _e( 'Map Zoom', 'easy-maps' ); ?>:<br />
							<input id="map_zoom" name="map_zoom" type="number" min='0' max='22' value="<?php echo $map_zoom; ?>" /><small>0 (<?php _e( 'farthest', 'easy-maps' ); ?>) - 22 (<?php _e( 'closest', 'easy-maps' ); ?>)</small>
						</label>
					</p>
					<p>
						<label><?php _e( 'Map Type', 'easy-maps' ); ?>:<br />
						<select id="map_type" name="map_type">
							<option <?php selected( $map_type, 'roadmap');   ?>><?php _e( 'roadmap', 'easy-maps' );   ?></option>
							<option <?php selected( $map_type, 'satellite'); ?>><?php _e( 'satellite', 'easy-maps' ); ?></option>
							<option <?php selected( $map_type, 'hybrid');    ?>><?php _e( 'hybrid', 'easy-maps' );    ?></option>
							<option <?php selected( $map_type, 'terrain');   ?>><?php _e( 'terrain', 'easy-maps' );   ?></option>
						</select>
						</label>
					</p>
				</td>
				<td style="vertical-align:top;">
					<p>
						<label><?php _e( 'Bubble', 'easy-maps' ); ?>:</label><br />
						<textarea id="bubble" name="bubble"></textarea>
					</p>
					<p>
						<label style="width:48%;float:left;"><?php _e( 'Width', 'easy-maps' ); ?>:<br /><input id="width" name="width" type="text" value="<?php echo $width; ?>" /><small><?php _e( 'include units', 'easy-maps' ); ?></small></label>
						<label style="width:48%;float:right;"><?php _e( 'Height', 'easy-maps' ); ?>:<br /><input id="height" name="height" type="text" value="<?php echo $height; ?>" /><small><?php _e( 'include units', 'easy-maps' ); ?></small></label>
					</p>
				</td>
			</tr>
		</table>

		<div class="mceActionPanel">
			<p style="text-align:right;">
				<input class="button-small" type="button" id="cancel" name="cancel" value="<?php _e( 'Cancel', 'easy-maps' ); ?>" onclick="tinyMCEPopup.close();" />
				<input type="submit" id="insert" name="insert" value="<?php _e( 'Insert', 'easy-maps' ); ?>" onclick="insertMapShortcode(event);" />
			</p>
		</div>
	</form>
	<script language="javascript" type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script>var easymaps = <?php echo json_encode( $window_strings ); ?>;</script>
	<script language="javascript" type="text/javascript" src="<?php echo plugins_url( 'maps.js', __FILE__ ); ?>"></script>
</body>
</html>