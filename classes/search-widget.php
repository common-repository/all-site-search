<?php

/**
 * Adds AllSiteSearch_Widget widget.
 */
class AllSiteSearch_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'allsitesearch_widget', // Base ID
			'All Site Search Form', // Name
			array( 
				'description' => __( 'Display your All Site Search form.', 'allsitesearch' ), 
			)
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
			
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
		
		if ( empty( $results_page ) && current_user_can( 'manage_options' ) ) {
			echo '<p>You must finish configuring All Site Search <a href="' . site_url() . '/wp-admin/options-general.php?page=all_site_search">on the settings screen</a> before using this widget.</p>';
		} else if ( ! empty( $results_page ) ) { 
			all_site_search_form( $results_page, $what_option, $sort_option, $custom_css_url, $input_width, $sort_option_width, $what_option_width, $robots_excluded, $default_form_style );
		}
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Search', 'allsitesearch' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
	
} // class AllSiteSearch_Widget
