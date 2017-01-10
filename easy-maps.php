<?php if ( ! defined('ABSPATH') ) die('-1');
/*
Plugin Name: Easy Maps
Description: Make inserting Google maps easy
Version: 0.2
*/

$egm_insert_single_map = new EGM_Insert_Single_Map();

class EGM_Insert_Single_Map {

	var $pluginname      = 'egm_single_map';
	var $internalVersion = 600;

	/**
	 * the constructor
	 *
	 */
	function __construct()  {

		// Modify the version when tinyMCE plugins are changed.
		add_filter( 'tiny_mce_version',               array( $this, 'tiny_mce_version') );

		// init process for button control
		add_action( 'init',                           array( $this, 'init') );

		// init process for ajax popup
		add_action( 'wp_ajax_egm_single_map_tinymce', array( $this, 'window_cb') );

		// prepare to register a setting
		add_filter( 'admin_init',                     array( $this, 'admin_init' ) );
		// make an convenient link to the settings
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_link' ), 10, 4 );


		add_shortcode( 'map',                         array( $this, 'shortcode_map') );

	}

	/**
	 * ::init()
	 *
	 */
	function init() {

		// Don't bother doing this stuff if the current user lacks permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Add only in Rich Editor mode
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
			add_filter( 'mce_buttons',          array( $this, 'mce_buttons' ), 0 );
		}
	}

	/**
	 * ::mce_buttons()
	 *
	 * @param array $buttons Buttons
	 * @return array Buttons
	 */
	function mce_buttons( $buttons ) {

		array_push( $buttons, $this->pluginname );

		// I probably shouldn't put this here, but it works and is convenient
		?><style>
		i.mce-i-egm_single_map:before {
			font-family: dashicons;
			content: "\f231";
		}
		</style><?php

		return $buttons;
	}

	/**
	 * ::mce_external_plugins()
	 * Load the TinyMCE plugin : editor_plugin.js
	 *
	 * @param array $plugin_array
	 * @return array
	 */
	function mce_external_plugins( $plugin_array ) {

		$plugin_array[ $this->pluginname ] =  plugins_url( 'mce/editor_plugin.js', __FILE__ );

		return $plugin_array;
	}

	/**
	 * ::tiny_mce_version()
	 * A different version will rebuild the cache
	 *
	 * @param integer $version
	 * @return integer
	 */
	function tiny_mce_version( $version ) {
		$version = $version + $this->internalVersion;
		return $version;
	}

	/**
	 * ::window_cb()
	 * create the popup window
	 *
	 */
	function window_cb() {

		// check for rights
		if ( ! current_user_can( 'edit_pages' ) && ! current_user_can( 'edit_posts' ) ) {
			die( __( 'You are not allowed to be here', 'easy-maps' ) );
		}

		$api_key      = get_option( 'easy_maps_google_maps_api_key', false );
		$maps_api_url = 'https://maps.googleapis.com/maps/api/js';
		if ( $api_key ) {
			$maps_api_url = add_query_arg( 'key', $api_key, $maps_api_url );
		}

		$window = plugin_dir_path( __FILE__ ) .'mce/window.php';
		$window_strings = array(
			'dragme'       => __( 'Drag me to pinpoint your location!', 'easy-maps' ),
			'markertitle'  => __( 'Drag Me!', 'easy-maps' ),
			'getstarted'   => __( 'Enter an address to get started', 'easy-maps' ),
			'maps_api_url' => $maps_api_url,
		);
		include_once( $window );

		die();
	}



	/**
	 * Prep form fields
	 *
	 */
	function admin_init() {
		register_setting( 'general', 'easy_maps_google_maps_api_key', array( $this, 'sanitize_key' ) );
		add_settings_field(
			'easy_maps_google_maps_api_key',
			'<label for="easy_maps_google_maps_api_key">' . __( 'Google Maps API Key', 'easy-maps' ) . '</label>',
			array( $this, 'add_settings_field' ),
			'general'
		);
	}

	/**
	 * Sanitize key
	 * Do we know for certain the length and content of an API key? [A-Za-z0-9_-] maybe
	 *
	 * @param string $input Raw form input
	 * @return string Sanitized input
	 */
	function sanitize_key( $input ) {
		$input = wp_strip_all_tags( $input );
		$input = trim( $input );
		return $input;
	}

	/**
	 * Add an link to settings
	 *
	 * @param array $actions
	 * @param string $plugin_file
	 * @param array $plugin_data
	 * @param string $context
	 * @return array
	 */
	function plugin_link( $actions, $plugin_file, $plugin_data, $context ) {
		$actions['easy_maps_setting'] = sprintf( '<a href="%1$s">%2$s</a>',
			admin_url( 'options-general.php#easy_maps_google_maps_api_key'),
			__( 'Settings', 'easy-maps' )
		);
		return $actions;
	}

	/**
	 * Render settings field
	 *
	 */
	function add_settings_field() {
		$key = get_option( 'easy_maps_google_maps_api_key', '' );
		$key = esc_attr( $key );
		echo "<input type='text' class='regular-text' id='easy_maps_google_maps_api_key' name='easy_maps_google_maps_api_key' value='{$key}' />";
		echo '<p class="description">';
		_e( 'For use by the <em>Easy Maps</em> plugin. To generate a key, see <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#key">Google\'s API documentation</a>', 'easy-maps' );
		echo '</p>';

	}


	/**
	 * Render shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string HTML
	 */
	function shortcode_map( $atts, $content ) {
		global $easy_map_instance;
		if ( is_null( $easy_map_instance ) ) {
			$easy_map_instance = 0;
		}
		++$easy_map_instance;

		global $content_width;
		$atts = shortcode_atts( array(
			'lat'    => 0,
			'lng'    => 0,
			'zoom'   => 10,
			'type'   => 'roadmap',
			'width'  => "{$content_width}px",
			'height' => '400px'
		), $atts );

		$atts = extract( array_map( 'esc_attr', $atts ) );

		if ( ! $lat || ! $lng ) {
			return;
		}

		$api_key      = get_option( 'easy_maps_google_maps_api_key', false );
		$maps_api_url = 'https://maps.googleapis.com/maps/api/js';
		if ( $api_key ) {
			$maps_api_url = add_query_arg( 'key', $api_key, $maps_api_url );
		}

		wp_enqueue_script( 'googlemapsapi', $maps_api_url );
		wp_enqueue_script( 'easy-google-maps', plugins_url( 'easy-maps.js', __FILE__ ), array( 'jquery', 'googlemapsapi' ) );

		$html = "<div style='width:{$width};height:{$height};' class='easy-google-map' id='easy-google-map-{$easy_map_instance}'
		data-lat='$lat' data-lng='$lng' data-zoom='$zoom' data-type='$type' data-content='$content'></div>
		<style>.easy-google-map img { max-width: initial !important; }</style>";

		return $html;

	}
}

//eof