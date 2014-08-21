<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

class shortcode_calendarize {
	var $id = 0;
	var $added_footer = false;
	var $wp_footer = '';
	var $capabilities = array();
	function shortcode_calendarize($args=array()){
		$defaults = array(
			'capabilities'				=> array(
				'calendarize_author'	=> 'calendarize_author'
			)
		);
		foreach($defaults as $property => $default){
			$this->$property = isset($args[$property])?$args[$property]:$default;
		}	
		//---------
		
		
		add_shortcode(SHORTCODE_CALENDARIZE, array(&$this,'calendarize'));
		add_shortcode(SHORTCODE_CALENDARIZEIT, array(&$this,'calendarizeit'));
	}
	
	function get_bool($value){
		return (in_array(trim(strtolower($value)),array('1','yes','y','true','s')))?true:false;
	}
	
	function calendarizeit($atts,$content=null,$code=""){
		return do_shortcode(generate_calendarize_shortcode($atts));
	}
	
	function calendarize($atts,$content=null,$code=""){
		$atts = $this->replace_att_with_posted($atts);
		$month_names = __('January,February,March,April,May,June,July,August,September,October,November,December','rhc');
		$short_month_names = __('Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec','rhc');
		$day_names = __('Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','rhc');
		$short_day_names = __('Sun,Mon,Tue,Wed,Thu,Fri,Sat','rhc');
		//--
		$label_today 		= __('today','rhc');
		$label_month 		= __('month','rhc');
		$label_day 			= __('day','rhc');
		$label_week 		= __("week",'rhc');
		$label_Calendar 	= __('Calendar','rhc');
		$label_event 		= __('event','rhc');
		$label_detail 		= __('detail','rhc');
		//--
		extract(shortcode_atts(array(
			'id'		=> sprintf("calendarize-%s",$this->id++),
			'class'		=> '',
			'post_type' => RHC_EVENTS,
			'taxonomy' 	=> '',
			'terms' 	=> '',
			'calendar'	=> '',
			'venue'		=> '',
			'organizer'	=> '',
			'author'	=> '',
			'author_name'=> '',
			'editable'	=> '1',
			'notransition'=>'0',
			'transition_easing'=>'easeInOutExpo',
			'transition_duration'=>'600',
			'transition_direction'=>'horizontal',
			//'theme'		=> 'sunny'
			//'theme'		=> 'smoothness'
			'mode'		=> 'view',//or edit *edit not currenlty supported.
			'theme'		=> '',
			'defaultview' => 'month',//month, basicWeek, basicDay, agendaWeek, agendaDay
			'aspectratio' => 1.35,
	//		'header_left'	=> 'prevYear,prev,next,nextYear today ',
			'header_left'	=> 'rhc_search prevYear,prev,next,nextYear today',
			'header_center'	=> 'title',
			'header_right'	=> 'month,agendaWeek,agendaDay,rhc_event',
			'weekends'		=> '1',
			'alldaydefault'	=> '1',
			'timeformat'		=>  __('h(:mm)t','rhc'),
			'titleformat_month'	=> 	__('MMMM yyyy','rhc'),
			'titleformat_week'	=> 	__("MMM d[ yyyy]{ '&#8212;'[ MMM] d yyyy}",'rhc'),
			'titleformat_day'	=>  __('dddd, MMM d, yyyy','rhc'),
			'columnformat_month'=> 	__('ddd','rhc'),
			'columnformat_week'	=> 	__('ddd M/d','rhc'),
			'columnformat_day'	=> 	__('dddd M/d','rhc'),
			'timeformat_month'	=> __('h(:mm)t','rhc'),
			'timeformat_week'	=> __('h:mm{ - h:mm}','rhc'),
			'timeformat_day'	=> __('h:mm{ - h:mm}','rhc'),
			'timeformat_default'=> __('h(:mm)t','rhc'),
			'axisformat'		=> __('h(:mm)tt','rhc'),
			'eventlistdateformat'=> __('dddd MMMM d, yyyy','rhc'),
			'eventliststartdateformat'=> __('dddd MMMM d, yyyy. h:mmtt','rhc'),
			'eventlistshowheader'=> '1',
			'eventlistnoeventstext'=>__('No upcoming events in this date range','rhc'),
			'eventlistmonthsahead'=>'',
			'eventlistupcoming'=>'',
			'tooltip_startdate'	=> __('ddd MMMM d, yyyy h:mm TT','rhc'),
			'tooltip_startdate_allday'	=> __('ddd MMMM d, yyyy','rhc'),
			'tooltip_enddate'	=> __('ddd MMMM d, yyyy h:mm TT','rhc'),
			'tooltip_enddate_allday'	=> __('ddd MMMM d, yyyy','rhc'),
			'tooltip_disable_title_link'	=> '0',
			'isrtl'	=> '',
			'firstday' => '1',
			'monthnames'		=> $month_names,
			'monthnamesshort'	=> $short_month_names,
			'daynames'			=> $day_names,
			'daynamesshort'		=> $short_day_names,
			'button_text_today'	=> $label_today,
			'button_text_month'	=> $label_month,
			'button_text_day'	=> $label_day,
			'button_text_week'	=> $label_week,
			'button_text_prev'	=> '&nbsp;&#9668;&nbsp;',
			'button_text_next'	=> '&nbsp;&#9658;&nbsp;',
			'button_text_prevYear'	=> '&nbsp;&lt;&lt;&nbsp;',
			'button_text_nextYear'	=> '&nbsp;&gt;&gt;&nbsp;',
			'button_text_calendar' => $label_Calendar,
			'button_text_event'=> $label_event,
			'button_text_detail'=> $label_detail,
			'buttonicons_prev'	=> 'circle-triangle-w',
			'buttonicons_next'	=> 'circle-triangle-e',
			'for_widget'		=> 0,
			'widget_link'		=> '',
			'widget_link_view'	=> '',
			'gotodate'			=> '',
			'alldayslot'		=> '1',
			'alldaytext'		=> __('all-day','rhc'),
			'firsthour'			=> 6,
			'slotminutes'		=> 30,
			'mintime'			=> 0,
			'maxtime'			=> 24,
			'tooltip_target'	=> '_self',
			'icalendar'			=> 1,
			'icalendar_width'	=> 460,
			'icalendar_button'	=> __('iCal Feed','rhc'),
			'icalendar_title' 	=> __('iCal Feed','rhc'),
			'icalendar_description' => __('Get Feed for iCal (Google Calendar). This is for subscring to the events in the Calendar. Add this URL to either iCal (Mac) or Google Calendar, or any other calendar that supports iCal Feed.','rhc'),
			'icalendar_align'	=> 'right',
			'events_source'		=> site_url('/?rhc_action=get_calendar_events')
		), $atts));
						
		if(!$this->added_footer){
			$this->added_footer = true;
			add_action('wp_footer',array(&$this,'wp_footer'));	
		}
		
		//$events_source = site_url('/?rhc_action=get_calendar_events');
		$events_source_query = '';
		
		$single_source = site_url('/?rhc_action=get_rendered_item');
		
		foreach(array('post_type','calendar','venue','organizer','author','author_name') as $field){
			if(!empty($$field)){
				$events_source_query.=sprintf("&%s=%s",$field,$$field);	
			}
		}
				
		if(!empty($taxonomy)&&!empty($terms)){
			$events_source_query.=sprintf("&taxonomy=%s&terms=%s",$taxonomy,$terms);	
		}
//die($events_source);	
//$events_source = site_url('/?rhc_action=get_calendar_items');
//$events_source.=$events_source_query;
	
		$options = (object)array(
			'editable'		=> ($editable && current_user_can($this->capabilities['calendarize_author'])),
			'mode'			=> $mode,
			'modes'			=> array(
				'view' => array(
					'label'		=> 'View',
					'options'	=> (object)array(
						'header'	=> (object)array(
							'left' 		=> $header_left,
							'center'	=> $header_center,
							'right'		=> $header_right
						),
						'events_source'	=> $events_source,
						'events_source_query' => $events_source_query,
						'defaultView'	=> $defaultview,
						'aspectRatio'	=> $aspectratio,
						'weekends'		=> $this->get_bool($weekends),
						'allDayDefault'	=> $this->get_bool($alldaydefault),
						'titleFormat'	=> (object)array(
							'month'	=> $titleformat_month,
							'week'	=> $titleformat_week,
							'day'	=> $titleformat_day
						),
						'columnFormat'	=> (object)array(
							'month'	=> $columnformat_month,
							'week'	=> $columnformat_week,
							'day'	=> $columnformat_day
						),
						'timeFormat'	=> (object)array(
							'month'	=> $timeformat_month,
							'week'	=> $timeformat_week,
							'day'	=> $timeformat_day,
							''		=> $timeformat_default
						),
						'tooltip'	=> (object)array(
							'startDate' 		=> $tooltip_startdate,
							'startDateAllDay' 	=> $tooltip_startdate_allday,
							'endDate'			=> $tooltip_enddate,
							'endDateAllDay'		=> $tooltip_enddate_allday,
							'target'			=> $tooltip_target,
							'disableTitleLink' 	=> $tooltip_disable_title_link
						),
						'axisFormat' => $axisformat,
						'isRTL'				=> $this->get_bool($isrtl),
						'firstDay'			=> $firstday,
						'monthNames' 		=> explode(',',$monthnames),
						'monthNamesShort' 	=> explode(',',$monthnamesshort),
						'dayNames' 			=> explode(',',$daynames),
						'dayNamesShort'		=> explode(',',$daynamesshort),
						'buttonText'		=> (object)array(
							'today'	=> $button_text_today,
							'month'	=> $button_text_month,
							'week'	=> $button_text_week,
							'day'	=> $button_text_day,
							'prev'	=> $button_text_prev,
							'next'	=> $button_text_next,
							'prevYear'	=> $button_text_prevYear,
							'nextYear'	=> $button_text_nextYear,
							'rhc_search'=> $button_text_calendar,
							'rhc_event' => $button_text_event,
							'rhc_detail'=> $button_text_detail
						),
						'buttonIcons'	=> (object)array(
							'prev'	=> $buttonicons_prev,
							'next'	=> $buttonicons_next
						),
						'transition'	=> (object)array(
							'notransition'=> $notransition,
							'easing'	=> $transition_easing,
							'duration'	=> $transition_duration,
							'direction'	=> $transition_direction
						),
						'eventList'		=> (object)array(
							'DateFormat'  	=> $eventlistdateformat,
							'StartDateFormat' => $eventliststartdateformat,
							'ShowHeader'	=> $eventlistshowheader,
							'eventListNoEventsText'=>$eventlistnoeventstext,
							'monthsahead'	=>$eventlistmonthsahead,
							'upcoming' => $eventlistupcoming
						),
						'eventClick'	=> 'fc_click',
						'eventMouseover'=> 'fc_mouseover',
						'singleSource'	=> $single_source,
						'for_widget'	=> $for_widget,
						'widget_link'	=> $widget_link,
						'widget_link_view'	=> $widget_link_view,
						'gotodate'		=> $gotodate,
						'ev_calendar'	=> $calendar,
						'ev_venue'		=> $venue,
						'ev_organizer'	=> $organizer,
						'allDaySlot'	=> $this->get_bool($alldayslot),
						'allDayText'	=> $alldaytext,
						'firstHour'		=> $firsthour,
						'slotMinutes'	=> intval($slotminutes),
						'minTime'		=> $mintime,
						'maxTime'		=> $maxtime	
					)	
				),
				'edit' => array(
					'label'		=> 'Edit',
					'options'	=> (object)array(
						'header'	=> (object)array(
							'left' 		=> $header_left,
							'center'	=> $header_center,
							'right'		=> $header_right
						),
						'events_source'	=> $events_source,
						'events_source_query' => $events_source_query,
						'defaultView'	=> $defaultview,
						'aspectRatio'	=> $aspectratio,
						'weekends'		=> $this->get_bool($weekends),
						'allDayDefault'	=> $this->get_bool($alldaydefault),
						'titleFormat'	=> (object)array(
							'month'	=> $titleformat_month,
							'week'	=> $titleformat_week,
							'day'	=> $titleformat_day
						),
						'columnFormat'	=> (object)array(
							'month'	=> $columnformat_month,
							'week'	=> $columnformat_week,
							'day'	=> $columnformat_day
						),
						'timeFormat'	=> (object)array(
							'month'	=> $timeformat_month,
							'week'	=> $timeformat_week,
							'day'	=> $timeformat_day,
							''		=> $timeformat_default
						),						
						'isRTL'				=> $this->get_bool($isrtl),
						'firstDay'			=> $firstday,
						'monthNames' 		=> explode(',',$monthnames),
						'monthNamesShort' 	=> explode(',',str_replace(' ','',$monthnamesshort)),
						'dayNames' 			=> explode(',',$daynames),
						'dayNamesShort'		=> explode(',',$daynamesshort),
						'buttonText'		=> (object)array(
							'today'	=> $button_text_today,
							'month'	=> $button_text_month,
							'week'	=> $button_text_week,
							'day'	=> $button_text_day,
							'prev'	=> $button_text_prev,
							'next'	=> $button_text_next,
							'prevYear'	=> $button_text_prevYear,
							'nextYear'	=> $button_text_nextYear
						),
						'buttonIcons'	=> (object)array(
							'prev'	=> $buttonicons_prev,
							'next'	=> $buttonicons_next
						),
						'transition'	=> (object)array(
							'notransition'=> $notransition,
							'easing'	=> $transition_easing,
							'duration'	=> $transition_duration,
							'direction'	=> $transition_direction
						),
						'eventList'		=> (object)array(
							'DateFormat'  	=> $eventlistdateformat,
							'StartDateFormat' => $eventliststartdateformat,
							'ShowHeader'	=> $eventlistshowheader,
							'eventListNoEventsText'=>$eventlistnoeventstext,
							'monthsahead'	=>$eventlistmonthsahead,
							'upcoming' => $eventlistupcoming
						),
						'eventClick'	=> 'fc_click',
						'eventMouseover'=> 'fc_mouseover',
						'singleSource'	=> $single_source,
						'for_widget'	=> $for_widget,
						'widget_link'	=> $widget_link,
						'widget_link_view'	=> $widget_link_view,
						'gotodate'		=> $gotodate,
						'ev_calendar'	=> $calendar,
						'ev_venue'		=> $venue,
						'ev_organizer'	=> $organizer,
						'allDaySlot'	=> $this->get_bool($alldayslot),
						'allDayText'	=> $alldaytext,
						'firstHour'		=> $firsthour,
						'slotMinutes'	=> intval($slotminutes),
						'minTime'		=> $mintime,
						'maxTime'		=> $maxtime	,		
						//-- same as view mode
						'editable'	=> true,
						'selectable'=> true,
						'select'	=> 'fc_select'
					)	
				)				
			),
			'common' => array(
				'theme' => ($theme==''?false:true),
				'icalendar_align' => $icalendar_align
			)
		);

		//---initializing script
		$this->wp_footer .= sprintf('<script type="text/javascript">jQuery(document).ready(function($){%s$("#%s").Calendarize(%s);});</script>',
			trim($theme)==''?'':sprintf('$("#fullcalendar-theme-css").attr("href","%s");', $this->get_ui_theme_url($theme) ),
			$id,
			$this->get_calendarize_args($options)
		);
		//---
		return sprintf('<div id="%s" class="rhcalendar %s"><div class="fullCalendar"></div>%s%s<div style="clear:both"></div></div>',
			$id,
			$class,
			$this->calendars_form($post_type),
			$this->icalendar_dialog($icalendar,$icalendar_title,$icalendar_description,$icalendar_button,$icalendar_width,$icalendar_align)
		);
	}
	
	function replace_att_with_posted($atts){
		if(isset($atts['ignoreposted'])&&$atts['ignoreposted']==1)return $atts;
		foreach(array('defaultview','gotodate') as $field){
			if(isset($_REQUEST[$field])){
				$atts[$field]=$_REQUEST[$field];
			}
		}
		foreach(array('venue','calendar','organizer') as $_field){
			$field = 'f'.$_field;
			if(isset($_REQUEST[$field])){
				$atts[$_field]=$_REQUEST[$field];
			}
		}		
//echo "<PRE>";
//print_r($atts);
//print_r($_REQUEST);
//echo "</PRE>";		
		return $atts;
	}
	
	function get_ui_theme_url($theme){
		$url = sprintf('%sui-themes/%s/style.css',RHC_URL,$theme);
		return apply_filters('rhc_ui_theme_url',$url,$theme);
	}
	
	function get_calendarize_args($options){
		$out = json_encode($options); 
		foreach(array('fc_select','fc_click','fc_mouseover') as $method_name){
			$out = str_replace('"'.$method_name.'"',$method_name,$out);
		}
		return $out;
	}
	
	function wp_footer(){	
		$this->calendarize_form();
		//$this->calendars_form();
		$this->items_tooltip();
		echo $this->wp_footer;		
	}
	
	function calendarize_form_fields($t){
		$i = count($t);
		//--Custom Post Types -----------------------		
		$i++;
		$t[$i]->id 			= 'cbw-custom-types'; 
		$t[$i]->label 		= __('Custom Post Types','rhc');
		$t[$i]->right_label	= __('Enable calendar metabox for other post types.','rhc');
		$t[$i]->page_title	= __('Custom Post Types','rhc');
		$t[$i]->theme_option = true;
		$t[$i]->plugin_option = true;
		$t[$i]->options = array();
		
		//--------------
		$post_types=array();
		foreach(get_post_types(array(/*'public'=> true,'_builtin' => false*/),'objects','and') as $post_type => $pt){
			if(in_array($post_type,array('revision','nav_menu_item')))continue;
			$post_types[$post_type]=$pt;
		} 
		$post_types = apply_filters('calendar_metabox_post_type_options',$post_types);
		//--------------		
		if(count($post_types)==0){
			$t[$i]->options[]=(object)array(
				'id'=>'no_ctypes',
				'type'=>'description',
				'label'=>__("There are no additional Post Types to enable.",'rhc')
			);
		}else{
			$j=0;
			foreach($post_types as $post_type => $pt){
				$tmp=(object)array(
					'id'	=> 'post_types_'.$post_type,
					'name'	=> 'post_types[]',
					'type'	=> 'checkbox',
					'option_value'=>$post_type,
					'label'	=> (@$pt->labels->name?$pt->labels->name:$post_type),
					'el_properties' => array(),
					'save_option'=>true,
					'load_option'=>true
				);
				if($j==0){
					$tmp->description = __("Calendarizer metabox can be enabled for other post types.  Check the post types, where you want the calendar metabox to be displayed.",'rhc');
					$tmp->description_rowspan = count($post_types);
				}
				$t[$i]->options[]=$tmp;
				$j++;
			}
		}
		
		
		$t[$i]->options[]=(object)array(
				'type'=>'clear'
			);
		$t[$i]->options[]=(object)array(
				'type'	=> 'submit',
				'label'	=> __('Save','rhc'),
				'class' => 'button-primary'
			);
			
		return $t;	
	}
	
	function calendarize_form(){
		$this->fc_intervals = plugin_righthere_calendar::get_intervals();
		global $rhc_plugin; 
		include $rhc_plugin->get_template_path('calendarize_form.php');				
	}
	
	function calendars_form_tabs($post_type){
		$taxonomies = get_object_taxonomies(array('post_type'=>$post_type),'objects');
		if(!empty($taxonomies)){	
			$tabs = array();
			foreach($taxonomies as $taxonomy => $tax){
				$tabs[$taxonomy] = sprintf('<li class="fbd-tabs"><a rel=".tab-%s">%s</a></li>',$taxonomy,$tax->label);
			}
			//--
			
			$tabs_content = array();
			foreach($taxonomies as $taxonomy => $tax){
				$terms = get_terms($taxonomy);
				if(is_array($terms) && count($terms)>0){
					$tmp = sprintf("<div rel=\"%s\" class='fbd-filter-group fbd-tabs-panel tab-%s'>",$taxonomy,$taxonomy);
					
					$tmp.='<div class="fbd-checked"></div>';
					$tmp.='<div class="fbd-unchecked">';
					foreach($terms as $i => $term){
						$tmp.=sprintf('<div rel="%s" class="fbd-cell"><input class="fbd-checkbox fbd-filter" type="checkbox" name="%s" value="%s"/>&nbsp;<span class="fbd-term-label">%s</span></div>',
							$i,
							$taxonomy.'_'.$term->slug,
							$term->slug,
							$term->name
						);
					}
					$tmp.='</div>';
					$tmp.='<div class="fbd-clear"></div>';
					$tmp.= '</div>';
					
					$tabs_content[$taxonomy] = $tmp;
				}else{
					unset($tabs[$taxonomy]);	
				}
			}
			
			if(count($tabs)>0){
				$content = "<ul class='fbd-ul'>".implode('',$tabs)."</ul>";
				$content.= implode("",$tabs_content);
				return $content;			
			}
		}
		return sprintf('<div class="no-filters">%s</div>',__('No filters available.','rhc'). 'post_type:'.$post_type  );	
	}
	
	function calendars_form($post_type){
		global $rhc_plugin; 
		ob_start();
		include $rhc_plugin->get_template_path('calendars_form.php');			
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	function items_tooltip(){
		global $rhc_plugin; 
		include $rhc_plugin->get_template_path('calendar_item_tooltip.php');	
	}
	
	function icalendar_dialog($icalendar,$icalendar_title,$icalendar_description,$icalendar_button,$width=450){
		if($icalendar!='1')return;
		ob_start();
?>
<div id="rhc-icalendar-modal" title="<?php echo $icalendar_title?>" style='display:none;width:<?php echo $width?>px;' rel="<?php echo $icalendar_button ?>">
	<textarea class="rhc-icalendar-url"></textarea>
	<p class="rhc-icalendar-description"><?php echo $icalendar_description?></p>
</div>
<?php	
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

?>