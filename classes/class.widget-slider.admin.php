<?php
if ( !function_exists( 'add_action' ) ) :
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
endif;



/**
 * Plugin class
 *
 * @package Widget Post Thumbnail
 * @since v1.0.0
 * @author Ralf Hortt
 **/
final class Widget_Slider_Admin
{


	/**
	 * Plugin constructor
	 *
	 * @access public
	 * @since v1.0.0
	 * @author Ralf Hortt
	 **/
	public function __construct()
	{

		add_action( 'admin_print_scripts-widgets.php', array( $this, 'wp_enqueue_script' ) );
		add_action( 'admin_print_styles-widgets.php', array( $this, 'wp_enqueue_style' ) );
		add_action( 'admin_footer-widgets.php', array( $this, 'modal' ) );
		add_action( 'wp_ajax_widget-slider-search-posts', array( $this, 'search_posts' ) );

	} // end construct


	/**
	 * List posts
	 *
	 * @static
	 * @access public
	 * @param array $post_ids Post IDs
	 * @return void
	 * @since v1.0.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	static public function list_posts( $posts )
	{

		?>

		<ul class="slider-list">

			<?php

			if ( !empty( $posts ) ) :

				foreach ( $posts as $post ) :

					?>

					<li data-id="<?php echo $post->ID ?>"><a class="dashicons dashicons-menu sort-slide" href="#"></a> <?php echo apply_filters( 'the_title', $post->post_title ) ?> <a href="#" class="dashicons dashicons-dismiss remove-slide"></a></li>

					<?php

				endforeach;

			endif;

			?>

		</ul>

		<?php

	} // end list_posts



	/**
	 * List posts
	 *
	 * @access public
	 * @since v1.0.0
 	 * @author Ralf Hortt
	 **/
	public function list_search( $posts )
	{

		$post_types = array();

		?>

		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Title' ) ?></th>
					<th><?php _e( 'Type' ) ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 1;
				foreach ( $posts as $post ) :

					$class = ( 1 == $i % 2 ) ? 'alternate' : 'default';
					$title = ( '' == $post->post_title ) ? __( '( No title )', 'redirector' ) : $post->post_title;

					if ( !isset( $post_types[$post->post_type] ) ) :

						$post_type = get_post_type_object( $post->post_type );
						$post_types[$post->post_type] = $post_type->labels->singular_name;

					endif;

					?>

					<tr class="<?php echo $class ?>">
						<th class="item-title">
							<a href="<?php echo get_permalink( $post->ID ) ?>" target="_blank"><?php echo $title ?></a>
							<?php if ( 'post' == $post->post_type ) :
								echo '<br><i>' . date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) . '</i>';
							endif;
							?>
						</th>
						<td class="item-info"><?php echo $post_types[$post->post_type] ?></td>
						<td><a class="button add-slide" href="#" data-id="<?php echo $post->ID ?>" data-title="<?php echo $post->post_title ?>"><?php _e( 'Select', 'redirector' ); ?></a></td>
					</tr>

					<?php

					$i++;

				endforeach;

				?>

			</tbody>
			<tfoot>
				<tr>
					<th><?php _e( 'Title' ) ?></th>
					<th><?php _e( 'Type' ) ?></th>
					<th>&nbsp;</th>
				</tr>
			</tfoot>
		</table>

		<?php

	} // end list_posts



	/**
	 * Modal box content
	 *
	 * @static
	 * @param array $post_ids Post IDs
	 * @return void
	 * @since v1.0.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function modal()
	{

		?>

		<div id="widget-slider-modal" style="display: none;">

			<?php do_action( 'widget-slider-modal-search-begin' ) ?>

			<p>
				<input type="search" value="" id="widget-slider-search" placeholder="<?php _e( 'Search' ); ?>"> <a href="#" class="button" id="widget-slider-search-button"><?php _e( 'Search' ); ?></a>
			</p>

			<div id="widget-slider-search-result"></div>

			<div id="widget-slider-recent-posts">

				<?php $this->recent_posts(); ?>

			</div>

			<?php do_action( 'widget-slider-modal-search-end' ) ?>

		</div>

		<?php

	} // end modal



	/**
	 * List recent posts
	 *
	 * @access protected
	 * @return void
	 * @since v1.0.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	protected function recent_posts()
	{

		$this->list_search( get_posts( array(
			'post_type' => 'any',
			'showposts' => 10,
		) ) );

	}



	/**
	 * Search posts
	 *
	 * @access public
	 * @since v.3.0.0
	 * @author Ralf Hortt
	 **/
	public function search_posts()
	{

		if ( !wp_verify_nonce( $_POST['nonce'], 'widget-slider-search-nonce' ) )
			return;

		$query = apply_filters( 'widget-slider-search-query', array(
			'post_type' => any,
			'suppress_filters' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			's' => sanitize_text_field( $_REQUEST['search'] ),
		) );

		$query = new WP_Query( $query );

		$response = array();

		ob_start();

		printf( '<h2>' . __( 'Search Result for „%s“', 'widget-slider' ) . '</h2>', sanitize_text_field( $_REQUEST['search'] ) );

		$this->list_search( $query->posts );

		$response['output'] = ob_get_contents();
		ob_end_clean();

		wp_reset_query();

		die( json_encode( $response ) );

	} // end search_posts



	/**
	 * Register script
	 *
	 * @access public
	 * @since v1.0.0
	 * @author Ralf Hortt
	 **/
	public function wp_enqueue_script()
	{

		wp_enqueue_script( 'widget-slider', plugins_url( '../scripts/widget-slider.js', __FILE__ ), array( 'jquery', 'thickbox', 'jquery-ui-sortable' ), FALSE, TRUE );
		wp_localize_script( 'widget-slider', 'widgetSlider', array(
			'searchNonce' => wp_create_nonce( 'widget-slider-search-nonce' ),
		) );

	} // end wp_enqueue_script



	/**
	 * Register script
	 *
	 * @access public
	 * @since v1.0.0
	 * @author Ralf Hortt
	 **/
	public function wp_enqueue_style()
	{

		wp_enqueue_style( 'widget-slider', plugins_url( '../styles/widget-slider.css', __FILE__ ), array( 'thickbox' ) );

	} // end wp_enqueue_styles



} // end final class Widget_Slider_Admin

new Widget_Slider_Admin;
