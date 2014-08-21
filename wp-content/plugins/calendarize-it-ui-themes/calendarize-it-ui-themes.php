<?php

/**
Plugin Name: Calendarize It! jQuery UI Themes Add-on
Plugin URI: http://plugins.righthere.com/calendarize-it/
Description: Adds additional jQuery UI themes to use with Calendarize It!
Version: 1.0.1 rev29432
Author: Alberto Lau (RightHere LLC)
Author URI: http://plugins.righthere.com
 **/

add_action('after_setup_theme','rhc_ui_themes_plugins_loaded',20);//hook after core.
function rhc_ui_themes_plugins_loaded(){
	if(class_exists('ui_themes_for_calendarize_it')){
		new ui_themes_for_calendarize_it(plugin_dir_path(__FILE__),plugin_dir_url(__FILE__));
	}
}
?>