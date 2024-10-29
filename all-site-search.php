<?php
/*
Plugin Name: All Site Search
Description: Integrate All Site Search into your WordPress website.
Version: 1.0.6
Tested up to: 3.9.1
Stable tag: 1.0.6
Author: All Site Search
Author URI: http://allsitesearch.com
License: GPLv2 or later
*/


class AllSiteSearch_Load {
	
	/**
	 * Constructor method (PHP4).
	 *
	 * @since 0.1.0
	 */
	function AllSiteSearch_Load() {
		$this->__construct();
	}
	
	/**
	 * PHP5 constructor method.
	 *
	 * @since 0.1.0
	 */
	function __construct() {
		
		// Load up the plugin files we need.
		add_action( 'plugins_loaded', array( $this, 'includes' ) );	
		
		// Register the search widget
		add_action( 'widgets_init', create_function( '', 'register_widget( "allsitesearch_widget" );' ) );	
	}
	
	/**
	 * Require the files within our plugin that we need.
	 */
	function includes() {
		
		// Load the search widget class.
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'classes/search-widget.php' );
		
		// Load the plugin's options.
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'classes/plugin-options.php' );
		
		// Load the filter class.
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'classes/filter-content.php' );
		
		// Load up the template tags
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'template-tags.php' );
		
		// Load up the shortcodes
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'shortcodes.php' );
	}
	
}

$allsitesearch_load = new AllSiteSearch_Load();