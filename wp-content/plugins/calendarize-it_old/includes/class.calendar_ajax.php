<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class calendar_ajax {
	var $numberposts = -1;
	var $skip_duplicates = true;
	var $done_ids = array();
	function calendar_ajax(){
		add_action('wp_loaded', array(&$this,'init'));
		$this->fc_intervals = array(
					''			=> __('Never(Not a recurring event)','rhc'),
					'1 DAY'		=> __('Every day','rhc'),
					'1 WEEK'	=> __('Every week','rhc'),
					'2 WEEK'	=> __('Every 2 weeks','rhc'),
					'1 MONTH'	=> __('Every month','rhc'),
					'1 YEAR'	=> __('Every year')
				);		
	}
	
	function init(){
		if(!isset($_REQUEST['rhc_action']))return;
		$action = $_REQUEST['rhc_action'];
		if(method_exists($this,$action))$this->$action();
	}
	
	function get_rendered_item(){
		$item_id = explode('-',$_REQUEST['id']);
		if(count($item_id)==2){
			global $wp_query;
			query_posts( 'page_id='.$item_id[0] );	
			query_posts( array(
				'p'=>$item_id[0],
				'post_type'=>$item_id[1]
			) );	

			ob_start();
			wp_head();
			$header = ob_get_contents();
			ob_end_clean();
			
			ob_start();
			include RHC_PATH.'templates/calendar-single-post.php';
			$content = ob_get_contents();
			ob_end_clean();
			
			ob_start();
			wp_footer();
			$footer = ob_get_contents();
			ob_end_clean();
			
			$response = (object)array(
				'R'=>'OK',
				'MSG'=>'',
				'DATA'=>array(
					'body'		=> $content,
					'footer'	=> $footer
				)
			);
			die(json_encode($response));	
		}else{
			die(json_encode(array('R'=>'ERR','MSG'=> __('Invalid item id','rhc') )));
		}
	}
	
	function get_calendar_items(){
		die(json_encode($this->_get_calendar_items()));
	}
	
	function get_calendar_events(){
		$r = array(
			'R'			=> 'OK',
			'MSG'		=> '',
			'EVENTS' 	=> $this->_get_calendar_items()
		);		
		return die(json_encode($r));	
	}
	
	function get_icalendar_events(){
		require 'class.rhc_icalendar.php';
		$_REQUEST['start']=0;
		$_REQUEST['end']=mktime(0,0,0,0,0,date('Y')+20);
		$ical = new events_to_vcalendar( $this->_get_calendar_items() );
		die( $ical->get_vcalendar() );
	}
	
	function _get_calendar_items($post_ID=false){
		global $rhc_plugin;

		$post_types = $rhc_plugin->get_option('post_types',array());
		$post_types[] = RHC_EVENTS;
		
		$post_fields = array(
			'post_type' 	=> RHC_EVENTS,
			'start'		=> date('Y-m-d 00:00:00'),
			'end'		=> date('Y-m-d 23:59:59'),
			'taxonomy'	=> false,
			'terms'		=> false,
			'calendar'	=> false,
			'venue'		=> false,
			'organizer'	=> false,
			'author'	=> false,
			'author_name'=>false,
			'tax'		=> false
		);
		
		//limit query to a specific id.
		if(false!==$post_ID){
			$post_fields['ID']=$post_ID;
		}
		
		foreach($post_fields as $field => $default){
			if($field=='start'){
				$value = isset($_REQUEST[$field])?date('Y-m-d 00:00:00',$_REQUEST['start']):$default;
			}else if($field=='end'){
				$value = isset($_REQUEST[$field])?date('Y-m-d 23:59:59',$_REQUEST['end']):$default;			
			}else{
				$value = isset($_REQUEST[$field])?$_REQUEST[$field]:$default;
			}
			$$field = $value;
		}
		
		if(!in_array($post_type,$post_types)){
			return array();
			//die(json_encode(array()));
		}
				
		$field_names = array_keys($post_fields);
		
		$args = compact($field_names);		
		if('1'==$rhc_plugin->get_option('show_all_post_types','',true)){
			$args['post_type']=$post_types;
		}

		return $this->get_events_set($args);
	}
	
	function get_events_set($args){
		$this->done_ids = array();
		$events = array();
		$events = $this->events_in_start_range($events, $args);
		//this one duplicates events, needs to exlcude already read events
		//$events = $this->events_in_end_range($events, $args);
		$events = $this->recurring_events_with_end_interval($events, $args);
		$events = $this->recurring_events_without_end_interval($events, $args);
		return $events;
	}
	
	function repeat_recurring_events($events){
		return $events;
	}
	
	function get_events($r,$args){
		$args['numberposts'] = $this->numberposts;
		$posts = get_posts($args);
		global $wpdb;	
		if(!empty($posts)){
			if(!function_exists('get_term_meta'))require_once RHC_PATH.'custom-taxonomy-with-meta/taxonomy-metadata.php';
			foreach($posts as $post){
				setup_postdata($post);	
				$tmp = array(
					'id' 			=> sprintf("%s-%s",$post->ID,$post->post_type),
					'title' 		=> get_the_title($post->ID),
					'start' 		=> $this->get_start_from_post_id($post->ID),
					'end' 			=> $this->get_end_from_post_id($post->ID),
					'url' 			=> get_permalink($post->ID),
					'description'	=> do_shortcode($post->post_excerpt),
					'image'			=> (has_post_thumbnail( $post->ID )?wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ):''),
					'terms'			=> array(),
					'fc_click_link' => 'view'
				);
				//----handle duplicates
				if($this->skip_duplicates){
					if(in_array($tmp['id'],$this->done_ids)){
						continue;
					}else{
						$this->done_ids[]=$tmp['id'];
					}
				}
				//----
				foreach(array( 'fc_rdate'=>'fc_rdate', 'fc_exdate'=>'fc_exdate', 'fc_allday'=>'allDay', 'fc_start'=>'fc_start','fc_start_time'=>'fc_start_time','fc_end'=>'fc_end','fc_end_time'=>'fc_end_time', 'fc_interval'=>'fc_interval','fc_rrule'=>'fc_rrule','fc_end_interval'=>'fc_end_interval','fc_color'=>'color','fc_text_color'=>'textColor','fc_click_link'=>'fc_click_link','fc_click_target'=>'fc_click_target') as $meta_field => $event_field){
					$meta_value = get_post_meta($post->ID,$meta_field,true);
					if(''!=trim($meta_value)){
						$tmp[$event_field]=$meta_value;
					}
				}
				$tmp['allDay']=isset($tmp['allDay'])&&$tmp['allDay']?true:false;
				//----
				$taxonomies = get_object_taxonomies(array('post_type'=>$post->post_type),'objects');
				if(!empty($taxonomies)){
					foreach($taxonomies as $taxonomy => $tax){
						$terms = wp_get_post_terms( $post->ID, $taxonomy );
						if(is_array($terms) && count($terms)>0){
							foreach($terms as $term){
//								$url = get_term_meta($term->term_id,'website',true);
//								$url = trim($url)==''?get_term_meta($term->term_id,'url',true):$url;
//								$url = trim($url)==''?get_term_link( $term, $taxonomy ):$url;
								$url = get_term_link( $term, $taxonomy );
								$gaddress = get_term_meta($term->term_id,'gaddress',true);
								
								$tmp['terms'][]=(object)array(
									'taxonomy'=>$taxonomy,
									'taxonomy_label'=>$tax->labels->singular_name,
									'slug'=>$term->slug,
									'name'=>$term->name,
									'url'=>$url,
									'gaddress'=>$gaddress
								);
							}
						}
					}
				}
				//----
				$r[]=$tmp;
			}
		}
		return $r;
	}
	
	function events_in_start_range($r,$parameters){
		extract($parameters);
		$args = array(
			'post_type'		=> $post_type,
			'post_status' 	=> 'publish',
			'meta_query'	=> array(
				'relation'		=> 'OR',
				array(
					'key'		=> 'fc_start',
					'value'		=> array($start,$end),
					'compare'	=> 'BETWEEN',
					'type'		=> 'DATE'
				),
				array(
					'key'		=> 'fc_rdatetime',
					'value'		=> array($start,$end),
					'compare'	=> 'BETWEEN',
					'type'		=> 'DATE'
				)		
			)
		);
		$args = $this->apply_parameters($args,$parameters);
		return $this->get_events($r,$args);
	}
	
	function events_in_end_range($r,$parameters){
		extract($parameters);
		$args = array(
			'post_type'		=> $post_type,
			'post_status' 	=> 'publish',
			'meta_query'	=> array(
				array(
					'key'		=> 'fc_end',
					'value'		=> array($start,$end),
					'compare'	=> 'BETWEEN',
					'type'		=> 'DATE'
				)			
			)
		);
		$args = $this->apply_parameters($args,$parameters);
		return $this->get_events($r,$args);
	}
	
	function recurring_events_with_end_interval($r,$parameters){
		extract($parameters);
		$args = array(
			'post_type'		=> $post_type,
			'post_status' 	=> 'publish',
			'meta_query'	=> array(
				'relation' => 'AND',
				array(
					'key'		=> 'fc_start',
					'value'		=> $start,
					'compare'	=> '<',
					'type'		=> 'DATE'
				),
				array(
					'key'		=> 'fc_interval',
					'value'		=> '',
					'compare'	=> '!=',
					'type'		=> 'CHAR'
				),
				array(
					'key'		=> 'fc_end_interval',
					'value'		=> '',
					'compare'	=> '!=',
					'type'		=> 'CHAR'
				),
				array(
					'key'		=> 'fc_end_interval',
					'value'		=> $start,
					'compare'	=> '>',
					'type'		=> 'DATE'
				)
			)
		);	
		$args = $this->apply_parameters($args,$parameters);
		return $this->get_events($r,$args);
	}
	
	function recurring_events_without_end_interval($r,$parameters){
		extract($parameters);
		$args = array(
			'post_type'		=> $post_type,
			'post_status' 	=> 'publish',
			'meta_query'	=> array(
				'relation' => 'AND',
				array(
					'key'		=> 'fc_start',
					'value'		=> $start,
					'compare'	=> '<',
					'type'		=> 'DATE'
				),
				array(
					'key'		=> 'fc_interval',
					'value'		=> '',
					'compare'	=> '!=',
					'type'		=> 'CHAR'
				),
				array(
					'key'		=> 'fc_end_interval',
					'value'		=> '',
					'compare'	=> '=',
					'type'		=> 'CHAR'
				)
			)
		);	
		$args = $this->apply_parameters($args,$parameters);
		return $this->get_events($r,$args);
	}
	
	function get_start_from_post_id($post_ID){
		return $this->event_date(get_post_meta($post_ID,'fc_start',true),get_post_meta($post_ID,'fc_start_time',true));
	}
	
	function get_end_from_post_id($post_ID){
		$date = get_post_meta($post_ID,'fc_end',true);
		$time = get_post_meta($post_ID,'fc_end_time',true);
		return $this->event_date($date,$time);
	}
	
	function event_date($date,$time,$default=null){
		$time = ''==trim($time)?'00:00:00':$time;
		if(''==trim($date))return $default;
		return date('Y-m-d H:i:s',strtotime(sprintf("%s %s", trim($date), trim($time) )));
	}
	
	function apply_parameters($args,$parameters){			
		foreach(array('taxonomy','tax','calendar','venue','organizer','author','author_name','tax_by_id') as $field){
			if(empty($parameters[$field])){
				$parameters[$field]=false;
			}
		}
	
		extract($parameters);
		//--
		if(isset($parameters['ID'])){
			$args['p']=$parameters['ID'];
		}
		
		//--- build taxonomies query
		// tax have priority over taxonomy, tax is passed when checking terms on the search dialog
		$taxonomies = array();
//error_log(print_r($_REQUEST,true),3,ABSPATH.'rhc.log');		
		if(false!==$tax && is_array($tax) && count($tax)>0){
			foreach($tax as $slug => $terms){
				$taxonomies[$slug]=explode(',',str_replace(' ','',$terms));
			}
		}else{
			if(false!==$taxonomy && false!==$terms){
				$taxonomies[$taxonomy]=explode(',',str_replace(' ','',$terms));
			}
			
			if(false!==$calendar){
				$taxonomies[RHC_CALENDAR]=$calendar;
			}
			
			if(false!==$venue){
				$taxonomies[RHC_VENUE]=$venue;
			}
			
			if(false!==$organizer){
				$taxonomies[RHC_ORGANIZER]=$organizer;
			}	
			/* bugged: only one is added to args where all shoul be possible.:			
			if(false!==$taxonomy && false!==$terms){
				$taxonomies[$taxonomy]=explode(',',str_replace(' ','',$terms));
			}else if(false!==$calendar){
				$taxonomies[RHC_CALENDAR]=$calendar;
			}else if(false!==$venue){
				$taxonomies[RHC_VENUE]=$venue;
			}else if(false!==$organizer){
				$taxonomies[RHC_ORGANIZER]=$organizer;
			}		
			*/ 
		}
			
		if(!empty($taxonomies)){
			$args['tax_query']=array(
				/*'relation'=>'OR'*/////--- multiple taxonomies with relation OR does not work as expected when combined with meta_query
			);
			foreach($taxonomies as $taxonomy => $terms){
				$args['tax_query'][]=array(
					'taxonomy'	=> $taxonomy,
					'field'		=> $tax_by_id?'id':'slug',
					'terms'		=> $terms,
					'operator'	=> 'IN'
				);
			}
		}
		//---done with taxonomies
		//---built author query
		if(false!==$author){
			//$args['author']=explode(',',str_replace(' ','',$author));
			$args['author']=$author;
		}
		if(false!==$author_name){
			$args['author_name']=$author_name;
		}		
		//---end author query
		return $args;
	}
}

?>