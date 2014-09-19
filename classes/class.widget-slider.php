<?php
if ( !function_exists( 'add_action' ) ) :
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
endif;



/**
 * Widget_Slider
 *
 * @package Widget_Slider
 * @author Ralf Hortt <me@horttcore.de>
 **/
final class Widget_Slider
{



	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 * @since v1.0.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function __construct()
	{

		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

	} // end __construct



	/**
	 * Register the widget
	 *
	 * @access public
	 * @return void
	 * @since v1.0.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function widgets_init()
	{

		register_widget( 'WP_Widget_Slider' );

	} // end widget_init



} // END final class Widget_Slider

new Widget_Slider;
