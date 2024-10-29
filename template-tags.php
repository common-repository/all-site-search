<?php

function all_site_search_form( $results_page, $what_option, $sort_option, $custom_css_url, $input_width, $sort_option_width, $what_option_width, $robots_excluded, $default_form_style ) {
	echo get_all_site_search_form( $results_page, $what_option, $sort_option, $custom_css_url, $input_width, $sort_option_width, $what_option_width, $robots_excluded, $default_form_style );
}

function get_all_site_search_form( $results_page, $what_option, $sort_option, $custom_css_url, $input_width, $sort_option_width, $what_option_width, $robots_excluded, $default_form_style ) { 

	$html = '<!-- Search Box here -->';
	$html .= '<form class="all-site-search-form" action="' . get_permalink( $results_page ) . '" method="GET" accept-charset="utf-8">';
	$html .= '<input type="hidden" name="co" value="q">';
	$html .= '<input type="hidden" name="vi" value="1">';
	
	if ( empty( $what_option ) ) {
		$html .= '<input type="hidden" name="ty" value="31">';
	} else {
	
		if ( empty( $what_option_width ) )
			$what_option_width = '140';
	
		$html .= '<select class="all-site-search-what all-site-search-element" name="ty" size="1" style="width:' . esc_attr( $what_option_width ) . 'px;">';
		$html .= '<option selected value="31">Everything</option>';
		$html .= '<option value="0">Pages and Documents</option>';
		$html .= '<option value="27">Images</option>';
		$html .= '<option value="26">Sounds</option>';
		$html .= '<option value="25">Videos</option>';
		$html .= '<option value="30">Software</option>';
		$html .= '<option value="29">Zip</option>';
		$html .= '<option value="24">Email</option>';
		$html .= '</select>';
	}
	
	$html .= '<input type="hidden" name="am" value="12">';
	$html .= '<input type="hidden" name="tf" value="s5eng2">';
	
	if ( empty( $sort_option ) ) {
		$html .= '<input type="hidden" name="rc" value="7">';
	} else {
	
		if ( empty( $sort_option_width ) )
			$sort_option_width = '140';
			
		$html .= '<select class="all-site-search-sort all-site-search-element" name="rc" size="1" style="width:' . esc_attr( $sort_option_width ) . 'px;">';
		$html .= '<option selected value="7"> By Relevancy </option>';
		$html .= '<option value="65"> Oldest first </option>';
		$html .= '<option value="33"> Newest first </option>';
		$html .= '</select>';
	}
	
	if ( empty( $custom_css_url ) )
		$custom_css_url = 'http://www.allsitesearch.com/als_wpstyle.css';

	$html .= '<input type="hidden" name="gi7" value="' . esc_attr( $custom_css_url ) . '">';
	
	$robot_value = 1;
	if ( ! empty( $robots_excluded ) )
		$robot_value = 0;
		
	$html .= '<input type="hidden" name="ro" value="' . $robot_value . '" />';
	
	$html .= '<input type="hidden" name="of" value="0">';
	$html .= '<input type="hidden" name="ln" value="255">';
	$html .= '<input type="hidden" name="ar" value="0">';
	$html .= '<input type="hidden" name="mnxs_dom" value="' . trailingslashit( site_url() ) . '|">';
	$html .= '<input type="hidden" name="al" value="0">';
	
	$name = get_bloginfo('name');
	$html .= '<input type="hidden" name="sn" value="' . all_site_search_remove_http( site_url() ) . '">';
	$html .= '<input type="hidden" name="dn" value="' . all_site_search_get_tld( site_url() ) . '">';
	$html .= '';
	$html .= '';
	
	if ( empty( $input_width ) )
		$input_width = '20';

	$html .= '<input type="text" name="st" value="" size="' . esc_attr( $input_width ) . '">&nbsp;&nbsp;';
	$html .= '<input class="all-site-search-submit all-site-search-button" type="submit" value=" Search ">';
	$html .= '</form>';
	
	if ( ! empty( $default_form_style ) )
		$html .= '<div class="clear"></div>';
	
	return $html;
}

/**
 * Retrieve TLD from URL
 *
 * @since 0.1.0
 */
function all_site_search_get_tld( $url ) {
		
	$tld = '';
		
	$url_parts = parse_url( (string) $url );
		
	if ( is_array( $url_parts ) && isset( $url_parts[ 'host'] ) ) {
		$host_parts = explode( '.', $url_parts[ 'host' ] );
		if ( is_array( $host_parts ) && count( $host_parts ) > 0 ) {
			$tld = array_pop( $host_parts );
		}
	}
		
	return $tld;
}

/**
 * Returns URL without the http:// at the beginning.
 */
function all_site_search_remove_http( $url ) {
	
	$disallowed = array(
		'http://',
		'https://',
	);
	
	foreach( $disallowed as $d ) {
		if ( strpos( $url, $d ) === 0 ) {
			return str_replace( $d, '', $url );
		}
	}
	
	return $url;
}

/**
 * Adds default form styles.
 */
add_action( 'wp_enqueue_scripts', 'all_site_search_add_default_form_style_action' );

function all_site_search_add_default_form_style_action() {
	
	$options = get_option( 'all_site_search' );
	$default_form_style = $options['default_form_style'];
	
	if ( empty( $default_form_style ) )
		return false;
	
	wp_register_style( 'all-site-search-forms', plugins_url( 'style/forms.css', __FILE__ ) );
	wp_enqueue_style( 'all-site-search-forms' );
}
