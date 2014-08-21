<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

if( 'plugin_rhc_taxonomies'==get_class($this) ):

	$my_custom_taxonomies=array(
		array(
			'taxonomy' 			=> 'concert_type',
			'slug'				=> 'concert-type',
			'singular_label' 	=> 'Concert Type',
			'plural_label'		=> 'Concert Types'
		),
		array(
			'taxonomy' 			=> 'composer',
			'slug'				=> 'composer',
			'singular_label' 	=> 'Composer',
			'plural_label'		=> 'Composers'
			)
		
		//other possible settings
		/*
		,
		array(
			'post_type'			=> 'events',				//the post type that the taxonomy will be assinged 
			'capability'		=> 'calendarize_author',	//capability that can manage the taxonomy
 			'taxonomy' 			=> 'mycustomtax',
			'slug'				=> 'my-custom-tax',
			'singular_label' 	=> 'Custom tax',
			'plural_label'		=> 'Custom taxes',
			'hierarchical'		=> true,					//set to true for categories, false for tags
			'singular_label'	=> 'My tax',				//a label for the taxonomy in singular
			'plural_label'		=> 'My taxes',				//a label for the taxonomy in pluarl
			'labels'			=> array(
				'name' 				=> 'My taxes',
				'singular_name' 	=> 'My tax',
				'search_items' 		=> 'Seach my taxes',
				'popular_items' 	=> 'Popular My taxs',
				'all_items' 		=> 'All my taxes',
				'edit_item' 		=> 'Edit tax', 
				'update_item' 		=> 'Update taxes',
				'add_new_item' 		=> 'Add tax',
				'new_item_name' 	=> 'New my tax'
			)
		)
		*/
	);


endif;

?>