<?php
/*
Plugin Name: Widget Slider
Plugin URI: http://horttcore.de
Description: A WordPress Widget to create a slider
Version: 1.0.0
Author: Ralf Hortt
Author URI: http://horttcore.de
License: GPL2
*/

/**
 * Security, checks if WordPress is running
 **/
if ( !function_exists( 'add_action' ) ) :
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
endif;



/**
 * Kickstart
 */
require( 'classes/class.widget-slider.widget.php' );
require( 'classes/class.widget-slider.php' );

if ( is_admin() )
	require( 'classes/class.widget-slider.admin.php' );
