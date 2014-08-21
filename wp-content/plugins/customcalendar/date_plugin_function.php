<?php
/* echo plugin_dir_path(__FILE__); */
global $post;

/* =====================================================================================================
 						********* Get All Category form taxonomy Name *********
 						****** Function To get Term Form Custom Taxonomy ******
 =======================================================================================================*/ 
 
function get_cat_frm_taxonomy1($taxonomy_name)
{

	$args = array(
			'orderby'       => 'name',
			'order'         => 'ASC',
			'hide_empty'    => false
	);
	$terms = get_terms( $taxonomy_name, $args );
	echo "<select name='tax1' id='tax1' class='tax1_select'>";
	echo "<option name='' value=''>Concert Types</option>";
	foreach($terms as $term){
		if($_POST['tax1'] == $term->name)
			echo "<option name='$term->name' value='$term->name' selected>".$term->name."</option>";
		else
			echo "<option name='$term->name' value='$term->name' >".$term->name."</option>";
	}
	echo "</select>";
}

/* =====================================================================================================
 						********* Get All Category form taxonomy Name *********
 						****** Function To get Term Form Custom Taxonomy ******
 =======================================================================================================*/ 

function get_cat_frm_taxonomy2($taxonomy_name)
{

	$args = array(
			'orderby'       => 'name',
			'order'         => 'ASC',
			'hide_empty'    => false
	);
	$terms = get_terms( $taxonomy_name, $args );
	echo "<select name='tax2' id='tax2' class='tax2_select'>";
	echo "<option name='' value=''>Composers</option>";
	foreach($terms as $term){
		if($_POST['tax2'] == $term->name)
			echo "<option name='$term->name' value='$term->name' selected>".$term->name."</option>";
		else
			echo "<option name='$term->name' value='$term->name'>".$term->name."</option>";
	}
	echo "</select>";
}

 /*======================================================================================================
     	  								***** Draw Calendar funtion *****
   	  				*** Pass month format 07 and year format 2013 and taxonomy(optional) ***
    =====================================================================================================*/ 
function draw_calendar_full($month,$year,$tax1,$tax2){
global $wpdb;
//echo $tax2;
	/* draw table */
	$calendar = '<div class="calendar list-cal">';

	/* table headings */
	$headings = array('S','M','T','W','TH','F','SA');
	$calendar.= '<div class="calendar-row-headsml"><div class="calendar-day-head">'.implode('</div><div class="calendar-day-head">',$headings).'</div></div>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<div class="calendar-row-small">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
	$calendar.= '<div class="calendar-day calender-blank"> </div>';
	$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++){
	$calendar.= '<div class="calendar-day" id="'.$month.'-'.$list_day.'-'.$year.'">';
	$tmro = $list_day.'-'.$month.'-'.$year;
	$small_day = date('D', strtotime( $tmro));

	/* add in the day number */
	$calendar.= '<div class="day-number">
					<span class="fc-date">'.$list_day.'</span>
					<span class="fc-weekday">'.$small_day.'</span>
					<div class="clr"></div>
				 </div>';

	if($list_day<10)
			$list_day = '0'.$list_day;
	
	$calmonyear=$month.'/'.$list_day.'/'.$year;
	
	$calendar.= showevent($calmonyear,$tax1,$tax2,$calendartype='full');

	/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
	//$calendar.= str_repeat('<p> </p>',2);

	$calendar.= '</div>';
	if($running_day == 6):
	$calendar.= '</div>';
	if(($day_counter+1) != $days_in_month):
	$calendar.= '<div class="calendar-row-small">';
	endif;
	$running_day = -1;
	$days_in_this_week = 0;
	endif;
	$days_in_this_week++; $running_day++; $day_counter++;
	}

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
	for($x = 1; $x <= (8 - $days_in_this_week); $x++):
	$calendar.= '<div class="calendar-day calender-blank"> </div>';
	endfor;
	endif;

	/* final row */
	$calendar.= '</div>';

	/* end the table */
	$calendar.= '<div class="clr"></div></div>';

	/* all done, return result */
	return $calendar;
}
/*============================================================================================================================
 * 												Normal Calendar
==============================================================================================================================*/

function draw_calendar($month,$year,$tax1,$tax2){
global $wpdb;
	/* draw table */
	$calendar = '<div class="calendar">';

	/* table headings */
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$calendar.= '<div class="calendar-row-head">
					<div class="calendar-day-head">'.implode('</div><div class="calendar-day-head">',$headings).'
					</div>
				</div>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<div class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
	$calendar.= '<div class="calendar-day calender-blank"> </div>';
	$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
	$calendar.= '<div class="calendar-day" id="'.$month.'-'.$list_day.'-'.$year.'">';
    $tmro = $list_day.'-'.$month.'-'.$year;
	$small_day = date('D', strtotime( $tmro));
	/* add in the day number */
	$calendar.= '<div class="day-number">
					<span class="fc-date">'.$list_day.'</span>
					<span class="fc-weekday">'.$small_day.'</span>
					<div class="clr"></div>
				 </div>';
	
	if($list_day<10)
		$list_day = '0'.$list_day;
	
	$calmonyear=$month.'/'.$list_day.'/'.$year;
	
	$calendar.= showevent($calmonyear,$tax1,$tax2,$calendartype='');
	//var_dump($eventarray);
	
	

	/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
	//$calendar.= str_repeat('<p> </p>',2);

	$calendar.= '</div>';
	if($running_day == 6):
	$calendar.= '<div class="clr"></div> </div>';
	if(($day_counter+1) != $days_in_month):
	$calendar.= '<div class="calendar-row">';
	endif;
	$running_day = -1;
	$days_in_this_week = 0;
	endif;
	$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
	for($x = 1; $x <= (8 - $days_in_this_week); $x++):
	$calendar.= '<div class="calendar-day calender-blank"> </div>';
	endfor;
	endif;

	/* final row */
	$calendar.= '</div>';

	/* end the table */
	$calendar.= '<div class="clr"></div> </div>';

	/* all done, return result */
	return $calendar;
}

function date_funct_button_sixmon()
{
	/* date settings */
	$month = (int) ($_GET['monthadd'] ? $_GET['monthadd'] : date('m'));
	$year = (int)  ($_GET['yearadd'] ? $_GET['yearadd'] : date('Y'));

	/* "next month" control */
	$next_month_link = '<a href="'.site_url().'/concerts-tickets/concert-calendar/?typ=full&monthadd='.($month != 7 ? $month + 6 : 1).'&yearadd='.($month != 7 ? $year : $year + 1).'" class="next1">Next</a>';

	/* "previous month" control */
	$previous_month_link = '<a href="'.site_url().'/concerts-tickets/concert-calendar/?typ=full&monthadd='.($month != 1 ? $month - 6 : 7).'&yearadd='.($month != 1 ? $year : $year - 1).'" class="prev">Prev</a>';

	/* bringing the controls together */
	$controls.= '<div class="Controlbutton" >';
	$controls.= $previous_month_link;
	
	
	$controls.= $next_month_link;
	$controls.='</div>';

	echo $controls;
}
function date_funct_button_sixmon_block()
{
	/* date settings */
	$month = (int) ($_GET['monthadd'] ? $_GET['monthadd'] : date('m'));
	$year = (int)  ($_GET['yearadd'] ? $_GET['yearadd'] : date('Y'));

	/* "next month" control */
	$next_month_link = '<a href="'.site_url().'/the-block/concert-calendar/?typ=full&monthadd='.($month != 7 ? $month + 6 : 1).'&yearadd='.($month != 7 ? $year : $year + 1).'" class="next">Next</a>';

	/* "previous month" control */
	$previous_month_link = '<a href="'.site_url().'/the-block/concert-calendar/?typ=full&monthadd='.($month != 1 ? $month - 6 : 7).'&yearadd='.($month != 1 ? $year : $year - 1).'" class="prev">Prev</a>';

	/* bringing the controls together */
	$controls.= '<div class="Controlbutton" >';
	$controls.= $previous_month_link;
	
	
	$controls.= $next_month_link;
	$controls.='</div>';

	echo $controls;
}
function showevent($eventdate,$tax11,$tax22,$calendartype)
{
	global $wpdb;
	$flag=false;
	$eventTimeStamp = strtotime($eventdate);
	//echo date('Y-m-d', $eventTimeStamp);
	$my_query = "SELECT t.* FROM wp_eventdate t,wp_posts y WHERE t.eventstartdate ='".date('Y-m-d', $eventTimeStamp)."' and y.ID=t.eventid and y.post_status='publish'";
	
	//echo $my_query."<br /><br />";
	$eventsarr = $wpdb->get_results($my_query);
	
	if(!empty($eventsarr)){
		$new_query = "select min(t.eventstartdate) as last_date from wp_eventdate t where t.eventid = ".$eventsarr[0]->eventid;
		$last_event = $wpdb->get_results($new_query);
	}
	$events=array();
	$event_text="";
	foreach ($eventsarr as &$eventsarrs)
	{
		$eventcategory = $eventsarrs->eventcategory;
		
		$evncat = explode(",", $eventcategory);
		for($i=0;$i<count($evncat);$i++)
		{
		if($i!=0)
			$arr_category[$i]=$evncat[$i];
		}
		
		$event_start_time = $eventsarrs->eventstarttime;
		
		$event_start_date = $eventsarrs->eventstartdate;
		//search condition
		if($tax11!="" || $tax22!=""){
			
			if(in_array($tax11,$arr_category)){
				$event_title=get_the_title($eventsarrs->eventid);
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $eventsarrs->eventid ));
				$eventid = $eventsarrs->eventid;
				$eventdateid = $eventsarrs->id;
				$content_post = get_post($eventsarrs->eventid);
				$content = $content_post->post_content;
				$content = apply_filters('the_content', $content);
				$string2 = substr($content, 0, 50);
					
				$buy_button = get_post_meta($eventsarrs->eventid,'buy_button');
				$link = get_post_meta($eventsarrs->eventid,'link');
				
				$buy_link='';
				if($buy_button[0]!='Disable')
					if($eventsarrs->eventstartdate == $last_event[0]->last_date){
						$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'" target="_blank">Buy Tickets</a></div>';
						
					} else {
						$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'-2" target="_blank">Buy Tickets</a></div>';

					}
					//$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'">Buy Tickets</a></div>';
				
				$events['id'][$j] = $eventsarrs->id;
				$flag=true;
			}
			
			if(in_array($tax22,$arr_category)){
				$event_title=get_the_title($eventsarrs->eventid);
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $eventsarrs->eventid ));
				$eventid = $eventsarrs->eventid;
				$eventdateid = $eventsarrs->id;
				$content_post = get_post($eventsarrs->eventid);
				$content = $content_post->post_content;
				$content = apply_filters('the_content', $content);
				$string2 = substr($content, 0, 50);
					
				$buy_button = get_post_meta($eventsarrs->eventid,'buy_button');
				$link = get_post_meta($eventsarrs->eventid,'link');
				
				$buy_link='';
				if($buy_button[0]!='Disable')
				
					if($eventsarrs->eventstartdate == $last_event[0]->last_date){
						$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'" target="_blank">Buy Tickets</a></div>';
						
					} else {
						$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'-2" target="_blank">Buy Tickets</a></div>';

					}
					//$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'">Buy Tickets</a></div>';
				
				$events['id'][$j] = $eventsarrs->id;
				$flag=true;
			}
			
		}
		else{	
			$event_title=get_the_title($eventsarrs->eventid);
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $eventsarrs->eventid ));
			$eventid = $eventsarrs->eventid;
			$eventdateid = $eventsarrs->id;
			$content_post = get_post($eventsarrs->eventid);
			$content = $content_post->post_content;
			$content = apply_filters('the_content', $content);
			$string2 = substr($content, 0, 50);
			
			$buy_button = get_post_meta($eventsarrs->eventid,'buy_button');
			$link = get_post_meta($eventsarrs->eventid,'link');
			
			$buy_link='';
			if($buy_button[0]!='Disable')
				if($eventsarrs->eventstartdate == $last_event[0]->last_date){
						$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'" target="_blank">Buy Tickets</a></div>';
						
					} else {
						$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'-2" target="_blank">Buy Tickets</a></div>';

					}
					//$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'">Buy Tickets</a></div>';
			
			$events['id'][$j] = $eventsarrs->id;
			$flag=true;
		}
					
		$events['category'][$j]=$arr_category;
		
		
		
		if($flag){
			
		$row=$wpdb->get_row("select tm.taxonomy, t.term_id from wp_terms t, wp_term_taxonomy tm where t.term_id=tm.term_id and t.name='".$arr_category[1]."'");
		
		if ($row->taxonomy=='concerttypes') {
			    $optname = $row->taxonomy.'_'.$row->term_id.'_colorpick';
				$optname = get_option($optname);
		}
		else{ 
			    $optname = '#5F5F5F';
		}
		
		$event_text.= '<div class="showevent">
								<span class="rounded_bull" style="background-color:'.$optname.';">';
		if($calendartype!='full')
		if(isMobile()) {
   			$event_text.=	'<a class="highlightLink" href="'.site_url().'/eventdetails?evnid='.$eventsarrs->id.'">'.$event_title.'</a>';
		} else {
			$event_text.=	'<p class="event-highlight">'.$event_title.'</p>';
		}
			
		// check to see if this event is in the past
		if(strtotime($event_start_date) < mktime()){
			$buy_link = '';
		}
		
								
		$event_text.= '</span><div class="highlight highlight-grid">
									<div class="high_panel" style="border-bottom:5px solid '.$optname.';">
										<h2>'.$event_title.'</h2>
										<p>'.$arr_category[1].'</p>
										<img src="'.$image[0].'" alt="" width="335" />
										<p>'.date('m/d/y', strtotime($event_start_date)).' '.$event_start_time.'</p>
										<p> '.$string2.'.....'.'</p>
										<p class="pspacer">&nbsp;</p>
										<div class="highlight-btn-wrap">
  <div class="bn flt-left">'.$buy_link.'</div>
  <div class="bn flt-right"> <a href="'.site_url().'/eventdetails?evnid='.$eventsarrs->id.'">Event Details</a> </div>
</div>
									</div>
									<div class="arrow-down" style="border-top: 20px solid '.$optname.';"></div>
					
									<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>';
		}
		
		
		
		$j++;
		

	}
	
	 
	
	return $event_text;
}




?>