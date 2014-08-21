<?php

/**
Plugin Name: Calendarize It! Add-on - Custom Taxonomies
Plugin URI: http://plugins.righthere.com/calendarize-it/
Description: This add-on lets you add your own filters to Calendarize It! with Custom Taxononies.
Version: 1.0.2 rev29059
Author: Alberto Lau (RightHere LLC)
Author URI: http://plugins.righthere.com
 **/
//echo RHC_PATH."in";
class plugin_rhc_taxonomies {
	function plugin_rhc_taxonomies(){
		add_action('plugins_loaded',array(&$this,'plugins_loaded'),9);
	}
	
	function plugins_loaded(){
		include_once RHC_PATH.'includes/class.CalendarizeItTaxonomies.php';
		//include_once 'C:/Inetpub/vhosts/wsso.org/httpdocs/staging/wp-content/plugins/calendarize-it/includes/class.CalendarizeItTaxonomies.php';
		if(class_exists('CalendarizeItTaxonomies')){
			include 'config.php';
			if(is_array($my_custom_taxonomies)&& count($my_custom_taxonomies)>0){
				foreach($my_custom_taxonomies as $settings){
					$labels = isset($settings['labels'])?$settings['labels']:array();
					new CalendarizeItTaxonomies($settings,$labels);
				}			
			}
		} 
	}
} 
new plugin_rhc_taxonomies(); 

?>
