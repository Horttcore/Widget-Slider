<?php
if ( !function_exists( 'add_action' ) ) :
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
endif;



if ( !class_exists( 'WP_Widget_Slider' ) ) :
/**
 * WP_Widget_Slider
 *
 * @package Widget_Slider
 * @author Ralf Hortt <me@horttcore.de>
 **/
class WP_Widget_Slider extends WP_Widget
{



	/**
	 * Constructor
	 *
	 * @access public
	 * @since v1.0.0
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function __construct()
	{

		$widget_ops = array(
			'classname' => 'widget-slider',
			'description' => __( 'Select post objects for your slider', 'widget-slider' ),
		);

		$control_ops = array( 'id_base' => 'widget-slider' );

		$this->WP_Widget( 'widget-slider', __( 'Slider', 'widget-slider' ), $widget_ops, $control_ops );

	} // end __construct



	/**
	 * Widget settings
	 *
	 * @access public
	 * @param array $instance Widget instance
	 * @since v1.0.0
	 * @author Ralf Hortt <me@horttcore.de>
	 * @since v1.0.0
	 * @see class.widget-slider.admin.php for modal box content
	 */
	public function form( $instance )
	{

		$posts = $this->get_slides( $instance['post-ids'] );

		Widget_Slider_Admin::list_posts( $posts );

		?>

		<p>
			<a class="button thickbox add-slides" href="#TB_inline?width=640&amp;height=auto&amp;inlineId=widget-slider-modal" title="<?php _e( 'Select Slides', 'widget-slider' ); ?>"><?php _e( 'Add Slide', 'widget-slider' ); ?></a>
		</p>

		<input type="text" class="slider-post-ids" name="<?php echo $this->get_field_name( 'post-ids' ); ?>" id="<?php echo $this->get_field_name( 'post-ids' ); ?>" value="<?php echo $instance['post-ids'] ?>">

		<?php

	}



	/**
	 * Get slides
	 *
	 * @static
	 * @access public
	 * @param array $post_ids Post IDs
	 * @return obj Post slides
	 * @since v1.0.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	protected function get_slides( $post_ids )
	{

		if ( !$post_ids )
			return array();

		$posts = get_posts( array(
			'post_type' => 'any',
			'orderby' => 'post__in',
			'post__in' => explode(',', $post_ids),
			'showposts' => -1,
		) );

		if ( !$posts )
			return array();

		return $posts;

	} // end get_slides



	/**
	 * Save widget settings
	 *
	 * @access public
	 * @param array $new_instance New widget instance
	 * @param array $old_instance Old widget instance
	 * @since v1.0.0
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function update( $new_instance, $old_instance )
	{

		$instance = $old_instance;
		$instance['post-ids'] = sanitize_text_field( $new_instance['post-ids'] );

		return $instance;

	} // end update



	/**
	 * Output
	 *
	 * @access public
	 * @param array $args Arguments
	 * @param array $instance Widget instance
	 * @since v1.0.0
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function widget( $args, $instance )
	{

		$query = new WP_Query(array(
			'post_type' => 'any',
			'orderby' => 'post__in',
			'post__in' => explode(',', $instance['post-ids'] ),
			'showposts' => -1,
		));

		if ( $query->have_posts() ) :

			echo $before_widget;

			$output = '<div class="widget-slider-slides">';

			while ( $query->have_posts() ) : $query->the_post();

				$output .= apply_filters( 'widget-slider-slide-output', get_the_post_thumbnail( get_the_ID(), apply_filters( 'widget-slider-image-size', 'small' ) ) );

			endwhile;

			$output .= '</div><!-- .widget-slider-slides -->';

			echo apply_filters( 'widget-slider-output', $output, $args, $instance );

			echo $after_widget;

		endif;

		wp_reset_query();

	} // end widget



} // end Widget_Post_Thumbnail
endif;
