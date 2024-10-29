<?php

class AllSiteSearch_Filter {
	
	/**
	 * Constructor method (PHP4).
	 *
	 * @since 0.1.0
	 */
	function AllSiteSearch_Filter() {
		$this->__construct();
	}
	
	/**
	 * PHP5 constructor method.
	 *
	 * @since 0.1.0
	 */
	function __construct() {
		add_filter( 'the_content', array( $this, 'filter_content' ) );
	}
	
	function filter_content( $content ) {
		
		$options = get_option( 'all_site_search' );
		$results_page = $options['results_page'];
		
		if ( is_page( $results_page ) ) {
			$results = '<!-- results here -->
			<div id="als7322_results">&nbsp;</div>
			<script type="text/javascript">
			    var alswidth = 600;
			</script>
			<script type="text/javascript" language="JavaScript" 
			 src="http://do.allsitesearch.com/als7322_head.js"></script>';

			$content = $content . $results;
		}
		
		return $content;
	}

}

$allsitesearch_filter = new AllSiteSearch_Filter();