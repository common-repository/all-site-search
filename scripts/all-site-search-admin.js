jQuery(document).ready(function($) {

	$( '#all-site-search-reset-account-info' ).click(function() {
		$( '.all-site-search-hidden-fields' ).show();
		$( '.all-site-search-hidden-fields input.text' ).val( '' );
		$( '#all-site-search-form #submit' ).click();
	});

});