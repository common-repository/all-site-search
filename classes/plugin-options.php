<?php

class AllSiteSearch_Options {
	
	/**
	 * Constructor method (PHP4).
	 *
	 * @since 0.1.0
	 */
	function AllSiteSearch_Options() {
		$this->__construct();
	}
	
	/**
	 * PHP5 constructor method.
	 *
	 * @since 0.1.0
	 */
	function __construct() {
		
		// Add our plugin settings screen.
		add_action( 'admin_menu', array( $this, 'add_settings_screen' ) );
		
		// Add our assorted plugin settings.
		add_action( 'admin_init', array( $this, 'add_individual_settings' ) );
		
		// Load up our admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		
	}
	
	/**
	 * Javascript for settings screen.
	 *
	 * @since 0.1.0
	 */
	public function scripts() {
		wp_enqueue_script( 'all-site-search-admin', plugins_url( 'all-site-search/scripts/all-site-search-admin.js' ), array( 'jquery' ), '0.2' );
	}
	
	/**
	 * Run the HTTP request and return the status results.
	 *
	 * @since 0.1.0
	 */
	public function http_request( $email_address, $password, $check ) {
		
		$http_query = http_build_query( array(
			'co'	=> 'f',
			'fd'	=> 'allss_wpreg',
			'tf'	=> 'als_wpreg',
			'gi2'	=> get_site_url(),
			'gi5'	=> $password,
			'gi1'	=> $email_address,
			//'test'	=> 1,
		));
			
		$url_base = 'http://www.allsitesearch.com/msearch?';
		$url_complete = $url_base . $http_query;
		
        // Check if this is a check and we have data
        if ( $check == '1' ) {

            if ( $email_address != '' ) {
			    $return['status'] = 1;
			    $return['message'] = 'OK';
            }
            else {
			    $return['status'] = 0;
			    $return['message'] = 'Failure. Not registered';
            }

        }
        // wp_remote_get with response
        else {
		    $response = wp_remote_get( $url_complete );
		    $response_content = $response['body'];
       
		    // Let's format our returned response.
		    if ( $response_content == 'OK') {
			    $return['status'] = 1;
			    $return['message'] = $response_content;
		    } else if ( $response_content == 'Failure. Site already exists as registered' ) {
			    $return['status'] = 1;
			    $return['message'] = 'OK';
		    } else {
			    $return['status'] = 0;
			    $return['message'] = $response_content;
		    }
        }
		
		return $return;
	}
	
	/**
	 * Request to AllSiteSearch.com conditional.
	 *
	 * @since 0.1.0
	 */
	public function should_display_registration( $email_address, $password ) {
		$http_return = $this->http_request( $email_address, $password, '1' );
		
		// If the return is successful, then we don't need to display registration fields.
		if ( $http_return['status'] == 1 ) {
			$return = false;
		} else {
			$return = true;
		}
		
		return $return;
	}
	
	/**
	 * Add our settings screen.
	 *
	 * @since 0.1.0
	 */
	public function add_settings_screen() {
		add_options_page( 
			'All Site Search Options', 
			'All Site Search', 
			'manage_options', 
			'all_site_search', 
			array( $this, 'settings_screen_display' ) );
	}
	
	/**
	 * The settings screen markup and sections.
	 *
	 * @since 0.1.0
	 */
	public function settings_screen_display() { ?>
		<div class="wrap" id="all-site-search-wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>All Site Search</h2>
			
			<div style="min-width:auto;max-width:800px;">
			
				<p>All Site Search is a free off-site All-Content Search Technology. Installing it will provide your site visitors with the most extensive and very best search experience. Satisfied visitors are returning visitors.</p>
				
				<p><b>Note:</b> Before continuing here,<br>
				<br>
				1. Goto Settings / Permalinks and click in "Post name" and then on "Save Changes".<br>
				2. Under Pages, do Add New and create a page named: Search Site, with settings: Parent = no parent, Template = default. <br>
				3. When you (below) will do "Search Results Page", then choose "Search Site". <br>
				<br>			
				<p>To install All Site Search on your site, fill out the fields below and click on the Register button, then fill out the fields for the plugin itself and click on the Save Changes button.</p>
			
			</div>
			
			<form action="options.php" id="all-site-search-form" method="post">
				<?php settings_fields( 'all_site_search_settings' ); ?>
				
				<?php 
				$options = get_option( 'all_site_search' );
				$response = $this->http_request( $options['email_field'], $options['password_field'], '0' ); ?>
				
				<?php if ( ! $this->should_display_registration( $options['email_field'], $options['password_field'] ) ) { ?>
				<div style="display:none;" class="all-site-search-hidden-fields">
				<?php } else { ?>
				<div>
				<?php } ?>
					<?php do_settings_sections( 'all_site_search_registration' ); ?>
					<?php if ( $this->should_display_registration( $options['email_field'], $options['password_field'] ) ) { ?>
					<p><strong>Registration Status:</strong> <span style="color:red;"><?php if ( empty( $response['message'] ) ) { ?>Not yet registered.<?php } else { echo $response['message']; } ?></span></p>
					<p>Save the account data you enter somewhere. You will need if you want to login to <a href="http://www.allsitesearch.com/" target="_blank">All Site Search</a> for managing your Search.</p>
					<?php } ?>
					<?php submit_button( 'Register with All Site Search' ); ?>
				</div>
				
				<div style="min-width:auto;max-width:800px;">
					<?php if ( ! $this->should_display_registration( $options['email_field'], $options['password_field'] ) ) { ?>
					<p><strong>Registration Status:</strong> <span style="color:green;"><?php if ( empty( $response['message'] ) ) { ?>Not yet registered.<?php } else { echo $response['message']; } ?></span> <br />(<a href="#" id="all-site-search-reset-account-info">Clear account info</a>)</p>
					<?php } ?>
					<p><b>Note:</b> After the above registration, you will receive an email where you must confirm the registration within 24 hours.<br><br>The first time indexing of your site will not be done immediately. Your site will be put in a batch job and indexed as soon as possible (within 24 hours after you have clicked confirm in the received email). When the first-time indexing is completed, you will receive another email. You can also check the indexing status by visiting All Site Search and logging in, using the address of your site and the password you entered here.<br><br><b>Do not forget</b> to add the All Site Search widget to your sidebar under Appearance &gt; Widgets.<br></p>
				</div>
				
				<?php do_settings_sections( 'all_site_search' ); ?>
				<?php submit_button(); ?>
			</form>
			
			<p>Please visit the following pages for more information:</p>
			
			<ul>
				<li><a href="http://www.allsitesearch.com" target="_blank" title="All Site Search">All Site Search</a></li>
				<li><a href="http://www.allsitesearch.com/als_compare.htm" target="_blank" title="Product Comparison">Product Comparison</a></li>
				<li><a href="http://www.allsitesearch.com/als_advanced.htm" target="_blank" title="Advanced Search Options">Advanced Search Options</a></li>
				<li><a href="http://www.allsitesearch.com/als_stylesexplained.htm" target="_blank" title="Styling your Search">Styling your Search</a></li>
				<li><a href="http://www.allsitesearch.com/als_usageterms.htm" target="_blank" title="Usage Terms">Usage Terms</a></li>
			</ul>
		</div><!-- .wrap -->
	<?php }
	
	/**
	 * Fire up each of our individual settings.
	 *
	 * @since 0.1.0
	 */
	public function add_individual_settings() {
		
		// Register settings
		register_setting( 'all_site_search_settings', 'all_site_search', '' );
		register_setting( 'all_site_search_settings', 'all_site_search_registration', '' );
		
		// Set up registration setting section.
		add_settings_section( 'all_site_search_reg', 'Register Your Site for Indexing', '', 'all_site_search_registration' );
		
		// Email field
		add_settings_field( 'all_site_search_email_field', 'Account Email', array( $this, 'email_field' ), 'all_site_search_registration', 'all_site_search_reg' );
		
		// Password field
		add_settings_field( 'all_site_search_password_field', 'Account Password', array( $this, 'password_field' ), 'all_site_search_registration', 'all_site_search_reg' );
		
		// Set up default setting section
		add_settings_section( 'all_site_search_main', 'Main Settings', '', 'all_site_search' );
		
		// Search Results option
		add_settings_field( 'all_site_search_results_page', 'Search Results Page', array( $this, 'results_page' ), 'all_site_search', 'all_site_search_main' );
		
		// Search Input Width field
		add_settings_field( 'all_site_search_input_width', 'Search Input Width', array( $this, 'input_width' ), 'all_site_search', 'all_site_search_main' );
		
		// Include default form style CSS
		add_settings_field( 'all_site_search_default_form_style', '', array( $this, 'default_form_style' ), 'all_site_search', 'all_site_search_main' );
		
		// Set up advanced settings section
		add_settings_section( 'all_site_search_advanced', 'Advanced Settings', '', 'all_site_search' );
		
		add_settings_field( 'all_site_search_what_option', '', array( $this, 'what_option' ), 'all_site_search', 'all_site_search_advanced' );
		add_settings_field( 'all_site_search_what_option_width', '', array( $this, 'what_option_width' ), 'all_site_search', 'all_site_search_advanced' );
		
		add_settings_field( 'all_site_search_sort_option', '', array( $this, 'sort_option' ), 'all_site_search', 'all_site_search_advanced' );
		add_settings_field( 'all_site_search_sort_option_width', '', array( $this, 'sort_option_width' ), 'all_site_search', 'all_site_search_advanced' );
		
		add_settings_field( 'all_site_search_robots_excluded', '', array( $this, 'robots_excluded' ), 'all_site_search', 'all_site_search_advanced' );
		
		add_settings_field( 'all_site_search_custom_css_url', 'Your custom CSS URL', array( $this, 'custom_css_url' ), 'all_site_search', 'all_site_search_advanced' );
	}
	
	/**
	 * Output the email field.
	 */
	public function email_field() {
		$options = get_option( 'all_site_search' );
		$value = esc_attr( $options[ 'email_field' ] );
		
		echo "<input id='all_site_search[email_field]' name='all_site_search[email_field]' class='text' size='40' type='text' value='{$value}' />
		<br /><label for='all_site_search[email_field]'>Enter your email address.</label>";
	}
	
	/**
	 * Output the password field.
	 */
	public function password_field() {
		$options = get_option( 'all_site_search' );
		$value = esc_attr( $options[ 'password_field' ] );
		
		echo "<input id='all_site_search[password_field]' name='all_site_search[password_field]' class='text' size='40' type='text' value='{$value}' />
		<br /><label for='all_site_search[password_field]'>Choose a password. Use characters a-z and 0-9.</label>";
	}
	
	/**
	 * Output the search results option.
	 */
	public function results_page() {
		$options = get_option( 'all_site_search' );
		$value = $options['results_page'];
		
		wp_dropdown_pages(
			array(
				'name'				=> 'all_site_search[results_page]',
				'echo'				=> 1,
				'show_option_none'	=> __( 'Select Page' ),
				'option_none_value'	=> '0',
				'selected'			=> absint( $value ),
			)
		);		
	}
	
	/**
	 * Output the search input width field.
	 */
	public function input_width() {
		$options = get_option( 'all_site_search' );
		$value = esc_attr( $options[ 'input_width' ] );
		
		echo "<input id='all_site_search[input_width]' name='all_site_search[input_width]' size='5' type='text' value='{$value}' />
		<br /><label for='all_site_search[input_width]'><em>Default is 20.</em></label>";
	}
	
	/**
	 * Output the default form CSS checkbox.
	 */
	public function default_form_style() {
		$options = get_option( 'all_site_search' );
		
		$html = '<label for="all_site_search[default_form_style]"><input type="checkbox" id="all_site_search[default_form_style]" name="all_site_search[default_form_style]" value="1"' . checked( 1, $options['default_form_style'], false ) . '/>';  
		$html .= '&nbsp;Add default form styles.</label>';
		echo $html;
	}
	
	/**
	 * Output the search within option checkbox.
	 */
	public function what_option() {
		$options = get_option( 'all_site_search' );
		
		$html = '<label for="all_site_search[what_option]"><input type="checkbox" id="all_site_search[what_option]" name="all_site_search[what_option]" value="1"' . checked( 1, $options['what_option'], false ) . '/>';  
		$html .= '&nbsp;Allow users to choose what items to search within.</label>';
		echo $html;
	}
	
	/**
	 * Output the what option width field.
	 */
	public function what_option_width() {
		$options = get_option( 'all_site_search' );
		$value = esc_attr( $options[ 'what_option_width' ] );
		
		echo "<input id='all_site_search[what_option_width]' name='all_site_search[what_option_width]' size='5' type='text' value='{$value}' />
		<br /><label for='all_site_search[what_option_width]'><em>Search within dropdown width (default is 140).</em></label>";
	}
	
	/**
	 * Output the sorting order checkbox.
	 */
	public function sort_option() {
		$options = get_option( 'all_site_search' );

		$html = '<label for="all_site_search[sort_option]"><input type="checkbox" id="all_site_search[sort_option]" name="all_site_search[sort_option]" value="1"' . checked( 1, $options['sort_option'], false ) . '/>';  
		$html .= '&nbsp;Allow users to control the sort order of search results.</label>';
		echo $html;
	}
	
	/**
	 * Output the sort order width field.
	 */
	public function sort_option_width() {
		$options = get_option( 'all_site_search' );
		$value = esc_attr( $options[ 'sort_option_width' ] );
		
		echo "<input id='all_site_search[sort_option_width]' name='all_site_search[sort_option_width]' size='5' type='text' value='{$value}' />
		<br /><label for='all_site_search[sort_option_width]'><em>Sort dropdown width (default is 140).</em></label>";
	}
	
	/**
	 * Output the robots excluded checkbox.
	 */
	public function robots_excluded() {
		$options = get_option( 'all_site_search' );
		
		$html = '<label for="all_site_search[robots_excluded]"><input type="checkbox" id="all_site_search[robots_excluded]" name="all_site_search[robots_excluded]" value="1"' . checked( 1, $options['robots_excluded'], false ) . '/>';  
		$html .= '&nbsp;Display robot excluded content.</label>';
		echo $html;
	}
	
	/**
	 * Custom CSS URL.
	 */
	public function custom_css_url() {
		$options = get_option( 'all_site_search' );
		$value = esc_attr( $options[ 'custom_css_url' ] );
		
		echo "<input id='all_site_search[custom_css_url]' name='all_site_search[custom_css_url]' size='40' type='text' value='{$value}' />
		<br /><label for='all_site_search[custom_css_url]'>Please read the <a href='http://www.allsitesearch.com/als_stylesexplained.htm' target='_blank' title='Styling your Search'>Style Guide</a> for the CSS file format, or else leave blank for default.</label>";
	}

}

$allsitesearch_options = new AllSiteSearch_Options();