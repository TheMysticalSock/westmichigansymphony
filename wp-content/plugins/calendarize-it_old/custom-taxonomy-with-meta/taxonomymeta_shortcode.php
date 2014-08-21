<?php


add_shortcode('taxonomymeta', 'shortcode_taxonomymeta');

function shortcode_taxonomymeta($atts,$content=null,$code=""){
	extract(shortcode_atts(array(
		'taxonomy' 	=> 'category',
		'multiple'	=> 1
	), $atts));
	//$terms = wp_get_post_terms( $post_id, $taxonomy, $args ) ;
	$output = '';
	$postid = get_the_ID();
	if($postid>0){
		$terms = wp_get_post_terms( $postid, $taxonomy ) ;	
		if(is_array($terms)&&count($terms)>0){
			if(!function_exists('get_term_meta'))require_once 'taxonomy-metadata.php';
			foreach($terms as $i => $term){
				$template = $content;
				//--replace standard taxonomy fields:
				foreach(array('term_id','name','slug','term_group','term_taxonomy_id','taxonomy','description','parent','count') as $field){
					if(!property_exists($term,$field))continue;
					$template = str_replace( sprintf('{%s}',$field) ,$term->$field,$template);
				}
				
				if(0<preg_match_all('/{([^}]+)}/i',$content,$matches)){
					foreach($matches[0] as $j => $replace){
						$field=$matches[1][$j];
						$value = get_term_meta($term->term_id,$field,true);
						$template = str_replace($replace,$value,$template);
					}
				}
				$output.=$template;
				if($multiple==0)break;			
			}
		}
	}
	
	return apply_filters('the_content',$output);
}
?>