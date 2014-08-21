<?php

// This version of "Calendarize It" is heavily modded by Next I.T. - block plugin update notifications - 
add_filter('site_transient_update_plugins', 'dd_remove_update_nag');
function dd_remove_update_nag($value) {
 unset($value->response[ plugin_basename(__FILE__) ]);
 return $value;
}
/**
Plugin Name: Calendarize It! for WordPress
Plugin URI: http://plugins.righthere.com/calendarize-it/
Description: Calendarize It! for WordPress is a powerful calendar and event plugin. 
Version: 1.2.3 rev30654
Author: Alberto Lau (RightHere LLC)
Author URI: http://plugins.righthere.com
 **/

define('RHC_VERSION','1.2.3'); 
define('RHC_PATH', plugin_dir_path(__FILE__) ); 
define("RHC_URL", plugin_dir_url(__FILE__) ); 
define("RHC_ADMIN_ROLE", 'administrator');

//this can only be modified when installing for the first time,//created taxonomies will be lost if changed after.
define("RHC_CALENDAR",	'calendar');
define("RHC_VENUE",		'venue');
define("RHC_ORGANIZER",	'organizer');
define("RHC_VISUAL_CALENDAR", 'calendar');
define("RHC_CONCERT_TYPE",	'concert-type');
define("RHC_COMPOSER",	'composer');
//custom post type, this afects slugs
define("RHC_EVENTS", 'events');
define("RHC_CAPABILITY_TYPE", 'event');

define('RHC_DEFAULT_DATE_FORMAT','D. F j, g:ia');

define('RHC_DISPLAY','rhcdisplay');

define('SHORTCODE_CALENDARIZE','calendarize');
define('SHORTCODE_CALENDARIZEIT','calendarizeit');

load_plugin_textdomain('rhc', null, dirname( plugin_basename( __FILE__ ) ).'/languages' );

if(!function_exists('property_exists')):
function property_exists($o,$p){
	return is_object($o) && 'NULL'!==gettype($o->$p);
}
endif;

if(!class_exists('plugin_righthere_calendar')){
	require_once RHC_PATH.'includes/class.plugin_righthere_calendar.php';
}

$settings = array();
//$settings['debug_menu']=true;//provides a debug menu with debugging information

global $rhc_plugin; 
$rhc_plugin = new plugin_righthere_calendar($settings);




//-------------------------------------------------------- 
register_activation_hook(__FILE__,'rhc_install');
function rhc_install() {
	include RHC_PATH.'includes/install.php';
	if(function_exists('handle_rhc_install'))handle_rhc_install();	
}
//---
register_deactivation_hook( __FILE__, 'rhc_uninstall' );
function rhc_uninstall(){
	include RHC_PATH.'includes/install.php';
	if(function_exists('handle_rhc_uninstall'))handle_rhc_uninstall();
}
//--------------------------------------------------------







/*
function feed_dir_rewrite( $wp_rewrite ) {
    $feed_rules = array(
        'index.rdf' => 'index.php?feed=rdf',
        'index.xml' => 'index.php?feed=rss2',
        '(.+).xml' => 'index.php?feed=' . $wp_rewrite->preg_index(1)
    );

    $wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
	
	error_log(print_r($wp_rewrite->rules,true)."\n\r",3,ABSPATH.'rewrite.log');
	
}

// Hook in.
add_filter( 'generate_rewrite_rules', 'feed_dir_rewrite' );
*/

//return apply_filters( "{$type}_template", locate_template( $templates ) );

?>