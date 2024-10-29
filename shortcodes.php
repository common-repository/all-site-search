<?php

/**
 * All Site Search form shortcode.
 */
function all_site_search_form_shortcode() {
	
	// Get defaults
	$options = get_option( 'all_site_search' );
	$results_page = $options['results_page'];
	$what_option = $options['what_option'];
	$sort_option = $options['sort_option'];
	$custom_css_url = $options['custom_css_url'];
	$input_width = $options['input_width'];
	$sort_option_width = $options['sort_option_width'];
	$what_option_width = $options['what_option_width'];
	$robots_excluded = $options['robots_excluded'];
	$default_form_style = $options['default_form_style'];
	
	return get_all_site_search_form( $results_page, $what_option, $sort_option, $custom_css_url, $input_width, $sort_option_width, $what_option_width, $robots_excluded, $default_form_style );
}

add_shortcode( 'all-site-search-form', 'all_site_search_form_shortcode' );