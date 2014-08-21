<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

class UpcomingEvents_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
	 		'upcoming_events_widget', 
			__('Calendarize (Upcoming Events)','rhc'), 
			array( 'description' => __( 'Upcoming events', 'rhc' ), ) 
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		global $post,$rhc_plugin;
		$tmp_post = $post;
		//----
		foreach(array('title','number') as $field ){
			$$field = $instance[$field];
		}
		if(intval($number)==0)return;		
		echo $before_widget;
		echo trim($title)==''?'':$before_title.$title.$after_title;

		$sel = 'rhc-upcoming-'.$rhc_plugin->uid++;
		echo sprintf("<div id=\"%s\"></div>",$sel);
		$upcoming = $this->get_upcoming($instance,$sel);

		echo $after_widget;				
		//-----
		$post = $tmp_post;
	}
	
	function get_upcoming($widget_args,$sel){
		global $rhc_plugin;

		foreach(array('calendar','venue','organizer','auto','horizon','number','showimage','words','fcdate_format','fctime_format','calendar_url') as $field ){
			$$field = isset($widget_args[$field])?$widget_args[$field]:'';
			//echo $field.": ".$$field."<BR />";
		}		
	
		$template = 'default';
		/* will handle horizon at render_events : the event retrieving functions compare agains date and not datetime. also we want to the horizon to be client side
		if($horizon=='hour'){
			$start = date('Y-m-d H:00:00');
		}else{
			$start = date('Y-m-d 00:00:00');
		}
		*/
		$start = date('Y-m-d 00:00:00');
		
		$end = date('Y-m-d 23:59:59',mktime(0,0,0,date('m')+12,date('d'),date('Y')));
	
		$number = intval($number);
		$number = $number==0?5:$number;

		if(is_tax()){
			$o = get_queried_object();
			$args = array(
				'post_type' 	=> RHC_EVENTS,
				'start'		=> $start,
				'end'		=> $end,
				'taxonomy'	=> $o->taxonomy,
				'terms'		=> $o->slug,
				'calendar'	=> false,
				'venue'		=> false,
				'organizer'	=> false,
				'author'	=> false,
				'author_name'=>false,
				'tax'		=> false,
				'numberposts' => $number
			);			
		}else{
			$args = array(
				'post_type' 	=> RHC_EVENTS,
				'start'		=> $start,
				'end'		=> $end,
				'taxonomy'	=> false,
				'terms'		=> false,
				'calendar'	=> $calendar==''?false:$calendar,
				'venue'		=> $venue==''?false:$venue,
				'organizer'	=> $organizer==''?false:$organizer,
				'author'	=> false,
				'author_name'=>false,
				'tax'		=> false,
				'tax_by_id' => true,
				'numberposts' => $number
			);
		}
	
		$events = $rhc_plugin->calendar_ajax->get_events_set($args);
		/*
		$events = array();
		$events = $rhc_plugin->calendar_ajax->events_in_start_range($events, $args);
		//this one duplicates events, needs to exlcude already read events
		//$events = $this->events_in_end_range($events, $args);
		$events = $rhc_plugin->calendar_ajax->recurring_events_with_end_interval($events, $args);
		$events = $rhc_plugin->calendar_ajax->recurring_events_without_end_interval($events, $args);
		*/
		//----
		//$events = $this->repeat_recurring_events($events,$number);
		//----
		//$r = usort($events,array(&$this,'compare_events'));	
		if(empty($events))return '';
		$using_calendar_url = false;
		if($calendar_url!=''){
			$using_calendar_url = true;
			foreach($events as $index => $e){
				$events[$index]['url']=$calendar_url;
			}
		}
		return $this->render_events($start,$end,$sel,$events,$number,$showimage,$words,$fcdate_format,$fctime_format,$horizon,$using_calendar_url);
	}


	
	function get_template_parts(){
		global $rhc_plugin;
		$template = file_get_contents($rhc_plugin->get_template_path('widget_upcoming_events.php'));
		$parts = (object)array(
			'holder'=>$template,
			'featured'=>''
		);
		if(preg_match('/<!--featured-->(.*)<!--featured-->/si',$template,$matches)){
			$parts->featured = $matches[1];
			$parts->holder = str_replace('<!--featured-->'.$parts->featured.'<!--featured-->','<!--featured-->',$parts->holder);
		}	
		return $parts;	
	}
	
	function render_events($start,$end,$sel,$events,$number,$showimage,$description_words=10,$fcdate_format='',$fctime_format='',$horizon='day',$using_calendar_url=false){
		global $rhc_plugin;
		$template = file_get_contents($rhc_plugin->get_template_path('widget_upcoming_events.php'));
		$description_words = is_numeric($description_words)?$description_words:10;
		$count = 0;
		
		foreach($events as $i => $e){			
			$description = '';
			$drr = explode(' ',$e['description']);
			for($a=0;$a<$description_words;$a++){
				if(isset($drr[$a]))
					$description.=" ".$drr[$a];
			}
			
			if(count($drr)>$description_words)
			$description.="<a href=\"".$e['url']."\">...</a>";
			
			$events[$i]['description']=$description;
		}
		
		if(empty($events))return '';
		
		$args = (object)array(
			'sel'=>$sel,
			'number'=>$number,
			'showimage'=>$showimage,
			'fcdate_format'=>$fcdate_format,
			'fctime_format'=>$fctime_format,
			'start'=>$start,
			'end'=>$end,
			'horizon'=>$horizon,
			'using_calendar_url'=>$using_calendar_url
		);
		
		//-- fill day and month names
		//-------- this portion is based on the code used on function.generate_calendarize_shortcode.php, TODO: simplify with a function
		global $rhc_plugin;
		
		$defaults = array(
			"monthnames"		=> __('January,February,March,April,May,June,July,August,September,October,November,December','rhc'),
			"monthnamesshort"	=> __('Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec','rhc'),
			"daynames"			=> __('Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday','rhc'),
			"daynamesshort"		=> __('Sun,Mon,Tue,Wed,Thu,Fri,Sat','rhc')
		);
		
		$options = (object)array();
		$field_option_map = array(
			"monthnames"=>"monthNames",
			"monthnamesshort"=>"monthNamesShort",
			"daynames"=>"dayNames",
			"daynamesshort"=>"dayNamesShort"
		);
		foreach($field_option_map as $field => $js_field){
			$option = 'cal_'.$field;
			if(isset($params[$field]))continue;
			$value = $rhc_plugin->get_option($option,$defaults[$field],true);
			if(trim($value)!=''){
				$params[$field]=$value;
			}
		}
		//--
		if(is_array($params) && count($params)>0){
			foreach($params as $field => $value){
				foreach(array('['=>'&#91;',']'=>'&#93;') as $replace => $with){
					$value = str_replace($replace,$with,$value);
				}
				$options->$field_option_map[$field]=explode(',',str_replace(' ','',$value));
			}	
		}			
		//--------		
		
		echo "<div class=\"rhc-widget-template\" style=\"display:none;\">".$template."</div>";
		echo sprintf("<script>try{render_upcoming_events(%s,%s,%s);}catch(error){}</script>",
			json_encode($args),
			json_encode($events),
			json_encode($options)
		);
//		echo "<pre>";
//		print_r($events);
//		echo "</prE>";

		//echo $sel;		
	}
	
	function _render_events($events,$number,$showimage,$description_words=10,$fcdate_format='',$fctime_format=''){
		$parts = $this->get_template_parts();
		$description_words = is_numeric($description_words)?$description_words:10;
		$count = 0;
		foreach($events as $e){
			$description = '';
			$drr = explode(' ',$e['description']);
			for($a=0;$a<$description_words;$a++){
				$description.=" ".$drr[$a];
			}
			
			if(count($drr)>$description_words)
			$description.="<a href=\"".$e['url']."\">...</a>";
	
			$replacements = array(
				'[URL]' 	=> $e['url'],
				'[SRC]'		=> isset($e['image'])&&isset($e['image'][0])?$e['image'][0]:'',
				'[TITLE]'	=> $e['title'],
				'[DESCRIPTION]'=> $description,
				'[DATE]'	=> $fcdate_format==''?'':date($fcdate_format,strtotime($e['start'])),
				'[TIME]'	=> $e['allDay']||$fctime_format==''?'':date($fctime_format,strtotime($e['start'])),
				'[NODATETIME]'=> (empty($fctime_format)&&empty($fcdate_format))||$e['allDay']&&empty($fcdate_format)?'rhc-hide':'',
				'[FEATURED]'=> $showimage?1:0
			);

			$holder = $parts->holder;
			$featured = $parts->featured;
			foreach($replacements as $replace => $with){
				$holder = str_replace($replace,$with,$holder);
				$featured = str_replace($replace,@$with,$featured);
			}
			$featured = $showimage&&trim( $replacements['[SRC]'] )!=''?$featured:'';
			$holder = str_replace("<!--featured-->",$featured,$holder);
			echo $holder;
			if($count++>$number)break;
		}
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = array();
		foreach(array('calendar','venue','organizer','auto','title','fcdate_format','fctime_format','horizon','number','showimage','words','calendar_url') as $field){
			$instance[$field] = $new_instance[$field];
		}
		return $instance;
	}

	function form( $instance ) {
		$taxmap = array('venue'=>RHC_VENUE,'organizer'=>RHC_ORGANIZER,'calendar'=>RHC_CALENDAR);
		foreach(array('calendar_url'=>'', 'auto'=>0,'title'=>'','horizon'=>'hour','number'=>5,'showimage'=>0,'words'=>10, 'fcdate_format'=>'MMM d, yyyy','fctime_format'=>'h:mmtt') as $field =>$default){
			$$field = isset( $instance[$field] )?$instance[$field]:$default;		
		}
?>
<div>
	<div class="" style="margin-top:10px;">
		<label><?php _e('Title','rhc')?></label>
		<input type="text" id="<?php echo $this->get_field_id('title')?>" class="widefat" name="<?php echo $this->get_field_name('title')?>" value="<?php echo $title?>" />
	</div>
	<div class="" style="margin-top:10px;">
		<?php _e('Date format','rhc')?>
		<input type="text" class="widefat" value="<?php echo $fcdate_format ?>" id="<?php echo $this->get_field_id('fcdate_format')?>" name="<?php echo $this->get_field_name('fcdate_format')?>" />
	</div>	
	<div class="" style="margin-top:10px;">
		<?php _e('Time format','rhc')?>
		<input type="text" class="widefat" value="<?php echo $fctime_format ?>" id="<?php echo $this->get_field_id('fctime_format')?>" name="<?php echo $this->get_field_name('fctime_format')?>" />
	</div>		
	<label><?php _e('Specific taxonomy:','wlbadds')?></label>
<?php foreach(array('calendar'=>__('Calendar','rhc'),'venue'=>__('Venue','rhc'),'organizer'=>__('Organizer','rhc')) as $field => $label):$$field = isset( $instance[$field] )?$instance[$field]:'';?>	
	<div class="" style="margin-top:10px;">
	<label for="<?php echo $field ?>"><?php echo $label?></label>
	<?php $this->taxonomy_dropdown($taxmap[$field],$this->get_field_id($field),$this->get_field_name($field),(isset( $instance[$field] )?$instance[$field]:''))?>
	</div>
<?php endforeach;?>
	<div class="" style="margin-top:10px;">
		<?php _e('Max number of posts','rhc')?>
		<input type="text" class="widefat" value="<?php echo $number ?>" id="<?php echo $this->get_field_id('number')?>" name="<?php echo $this->get_field_name('number')?>" />
	</div>
	
	<div class="" style="margin-top:10px;">
		<?php _e('Max description word count','rhc')?>
		<input type="text" class="widefat" value="<?php echo $words ?>" id="<?php echo $this->get_field_id('words')?>" name="<?php echo $this->get_field_name('words')?>" />
	</div>
	
	<div class="" style="margin-top:10px;">
		<?php _e('Remove event by','rhc')?>
		<select id="<?php echo $this->get_field_id('horizon')?>" name="<?php echo $this->get_field_name('horizon')?>" class="widefat">
			<option value="hour" <?php echo $horizon=='hour'?'selected="selected"':''?> ><?php _e('Hour','rhc')?></option>
			<option value="day" <?php echo $horizon=='day'?'selected="selected"':''?> ><?php _e('Day','rhc')?></option>
		</select>
	</div>
	
	<div class="" style="margin-top:10px;">
		<?php _e('Show featured image','rhc')?>
		<select id="<?php echo $this->get_field_id('showimage')?>" name="<?php echo $this->get_field_name('showimage')?>" class="widefat">
			<option value="0" <?php echo $showimage=='0'?'selected="selected"':''?> ><?php _e('No image','rhc')?></option>
			<option value="1" <?php echo $showimage=='1'?'selected="selected"':''?> ><?php _e('Show image','rhc')?></option>
		</select>
	</div>
	
	<div class="" style="margin-top:10px;">
		<input type="checkbox" id="<?php echo $this->get_field_id('auto')?>" name="<?php echo $this->get_field_name('auto')?>" <?php echo $auto==1?'checked="checked"':''?> value=1 />&nbsp;*<?php _e('Only related events.','rhc')?>
	</div>
	<p style="margin-top:3px;">*<?php _e('If the loaded page is a calendar, venue or organizer (taxonomy), only show events from the same taxonomy.')?></p>

	<div class="" style="margin-top:10px;">
		<?php _e('Calendar url(optional)','rhc')?>
		<input type="text" class="widefat" value="<?php echo $calendar_url ?>" id="<?php echo $this->get_field_id('calendar_url')?>" name="<?php echo $this->get_field_name('calendar_url')?>" />
	</div>
</div>
<?php
	}
	
	function taxonomy_dropdown($taxonomy,$id,$name,$posted_value){
		$terms = get_terms($taxonomy);
?>
<select id="<?php echo $id?>" name="<?php echo $name?>" class="widefat upcoming-<?php echo $taxonomy?>">
<?php if(is_array($terms)&&count($terms)>0):?>
<option value=""><?php _e('--any--','rhc')?></option>
<?php foreach($terms as $t):?>
<option value="<?php echo $t->term_id?>" <?php echo $posted_value==$t->term_id?'selected="selected"':''?> ><?php echo $t->name?></option>
<?php endforeach;?>
<?php else: ?>
<option value=""><?php _e('--no options--','rhc')?></option>
<?php endif;?>
</select>
<?php		
	}
}
?>