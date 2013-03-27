<?php if ( ! defined('ABSPATH') ) die('-1');
/*
Plugin Name: Easy Maps
Description: Make inserting Google maps easy
Version: 0.1
*/

$egm_insert_single_map = new EGM_Insert_Single_Map();

class EGM_Insert_Single_Map {

	var $pluginname = 'egm_single_map';
	var $internalVersion = 600;

	/**
	 * the constructor
	 *
	 * @return void
	 */
	function __construct()  {

		// Modify the version when tinyMCE plugins are changed.
		add_filter('tiny_mce_version', array( &$this, 'tiny_mce_version') );

		// init process for button control
		add_action('init', array( &$this, 'init') );

		// init process for ajax popup
		add_action('wp_ajax_egm_single_map_tinymce', array( &$this, 'window_cb') );

		add_shortcode('map', array( &$this, 'shortcode_map') );

	}

	/**
	 * ::init()
	 *
	 * @return void
	 */
	function init() {

		// Don't bother doing this stuff if the current user lacks permissions
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') )
			return;

		// Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {

			// add the button for wp2.5 in a new way
			add_filter( 'mce_external_plugins', array( &$this, 'mce_external_plugins' ));
			add_filter( 'mce_buttons', array( &$this, 'mce_buttons' ), 0);
		}
	}

	/**
	 * ::mce_buttons()
	 * used to insert button in wordpress 2.5x editor
	 *
	 * @return $buttons
	 */
	function mce_buttons( $buttons ) {

		array_push( $buttons, $this->pluginname );

		return $buttons;
	}

	/**
	 * ::mce_external_plugins()
	 * Load the TinyMCE plugin : editor_plugin.js
	 *
	 * @return $plugin_array
	 */
	function mce_external_plugins($plugin_array) {

		$plugin_array[ $this->pluginname ] =  plugins_url( 'mce/editor_plugin.js', __FILE__ );

		return $plugin_array;
	}

	/**
	 * ::tiny_mce_version()
	 * A different version will rebuild the cache
	 *
	 * @return $version
	 */
	function tiny_mce_version( $version ) {
		$version = $version + $this->internalVersion;
		return $version;
	}

	/**
	 * ::window_cb()
	 * create the popup windo
	 *
	 * @return void
	 */
	function window_cb() {

		// check for rights
		if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') )
			die(__("You are not allowed to be here"));

		$window = plugin_dir_path(__FILE__) .'mce/window.php';
		include_once( $window );

		die();
	}

	function shortcode_map( $atts, $content ) {
		global $easy_map_instance;
		if ( is_null( $easy_map_instance ) ) $easy_map_instance = 1;
		else                               ++$easy_map_instance;

		global $content_width;
		extract( shortcode_atts( array(
			'lat' => 0,
			'lng' => 0,
			'zoom' => 10,
			'type' => 'roadmap',
			'width' => "{$content_width}px",
			'height' => '400px'
		), $atts ) );
		if ( ! $lat || ! $lng ) return;

		wp_enqueue_script( 'googlemapsapi', 'http://maps.googleapis.com/maps/api/js?sensor=false' );
		wp_enqueue_script( 'easy-google-maps', plugins_url( 'easy-maps.js', __FILE__ ), array( 'jquery', 'googlemapsapi') );

		$html = "<div style='width:{$width};height:{$height};' class='easy-google-map' id='easy-google-map-{$easy_map_instance}'
		data-lat='$lat' data-lng='$lng' data-zoom='$zoom' data-type='$type' data-content='$content'></div>
		<style>.easy-google-map img { max-width: initial !important; }</style>";
		return $html;

	}
}

//eof