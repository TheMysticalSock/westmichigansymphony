<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class rhc_archive_layout_frontend {
	function rhc_archive_layout_frontend(){
		add_filter('the_content',array(&$this,'the_content'),10,1);
	}

	function the_content($content){
		global $wpdb,$post;
		if(is_archive() && is_object($post) && property_exists($post,'post_type') && $post->post_type==RHC_EVENTS){
			$template = $this->get_template();
			$str = $wpdb->get_var("SELECT post_excerpt FROM $wpdb->posts WHERE ID={$post->ID}",0,0);
			//get the excerpt crashes, probably it loops back to the_content
			//get_the_excerpt();
			if(trim($str)!=''){
				$content = do_shortcode(str_replace('{excerpt}',$str,$template));
			}			
		}	
		return $content;

	}

	function get_template(){
		return '{excerpt}[post_info]';
	}
}
?>