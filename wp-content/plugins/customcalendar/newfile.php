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
	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar list-cal">';

	/* table headings */
	$headings = array('S','M','T','W','TH','F','SA');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
	$calendar.= '<td class="calendar-day-np"> </td>';
	$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++){
	$calendar.= '<td class="calendar-day" id="'.$month.'-'.$list_day.'-'.$year.'">';

	/* add in the day number */
	$calendar.= '<div class="day-number">'.$list_day.'</div>';

	$args = array( 'numberposts' => -1, 'post_type' => 'events_booking');
	$myposts = get_posts( $args );
	//print_r($myposts);
	//echo '<br>';
	
	$calmonyear = $month.'/'.$year;

	foreach( $myposts as $post ) { setup_postdata($post);
	
		$eventsarr = $wpdb->get_results( "SELECT * FROM wp_eventdate WHERE eventid = '".$post->ID."' ");
		foreach ($eventsarr as $eventsarrs) 
		{
			$eventdateid = $eventsarrs->id;
			$event_start_date = $eventsarrs->eventstartdate;
			
			$statrt = explode("/", $event_start_date);
			$start_str=$statrt[0]."/".$statrt[2];

			$sdate = $event_start_date;
			
			$eventcategory = $eventsarrs->eventcategory;
			$evncat = explode(",", $eventcategory);
			
			
			for($i=0;$i<count($evncat);$i++)
			{
				if($i!=0)
					$arr[$i]=$evncat[$i];
			}
			
		
		$event_start_time = $eventsarrs->eventstarttime;
		$event_end_time = $eventsarrs->eventendtime;
	
		 if($calmonyear == $start_str) 
		{ 
			if($list_day == $statrt[1])
			{
				//$calendar.= $post->post_name;
				
				
				$terms = get_the_terms( $post->ID, 'concerttypes' );
				//print_r($terms);
				if ( $terms && ! is_wp_error( $terms ) ) 
				{
					foreach ($terms as $allterms)
					{
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ));
						$string1 = $post->post_content;
						$string2 = substr($string1, 0, 50);
						
						$buy_button = get_post_meta($post->ID,'buy_button');
						$link = get_post_meta($post->ID,'link');
						$buy_link='';
						if($buy_button[0]!='Disable')
							$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'">Buy Tickets</a></div>';
						if ($allterms->name == $tax1 && in_array($allterms->name, $arr)) 
						{
							$optname = $allterms->taxonomy.'_'.$allterms->term_id.'_colorpick';
							$calendar.= '<div class="showevent showevent-full"><span class="rounded_bull" style="background-color:'.get_option($optname).';"></span>';
							$calendar.= '<div class="highlight">
									<div class="high_panel" style="border-bottom:10px solid '.get_option($optname).';">
									<h2>'.$post->post_title.'</h2>
									<p style="margin-bottom: 3px;">'.$allterms->name.'</p>
									<img src="'.$image[0].'" alt="" width="335" />
									<p>'.$sdate.' '.$event_start_time.'</p>
									<p> '.$string2.'</p>
														
									<table border="0" width="100%">
									<tr>
									<td height="34" align="left"><div class="bn">'.$buy_link.'</div> </td>
									<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$allterms->name.'&taxid='.$allterms->term_id.'&taxname=concerttypes&resn=concert">Event Details</a></div></td>
									</tr>
					
									</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid '.get_option($optname).';"></div>
					
									<div class="clear"></div>
									</div><div class="clear"></div></div>';
						}
						if ($tax1=='' && in_array($allterms->name, $arr)) 
						{
							$optname = $allterms->taxonomy.'_'.$allterms->term_id.'_colorpick';
							
							$calendar.= '<div class="showevent showevent-full"><span class="rounded_bull" style="background-color:'.get_option($optname).';"></span></span>';
							$calendar.= '<div class="highlight">
									<div class="high_panel" style="border-bottom:10px solid '.get_option($optname).';">
									<h2>'.$post->post_title.'</h2>
									<p style="margin-bottom: 3px;">'.$allterms->name.'</p>
									<img src="'.$image[0].'" alt="" width="335" />
									<p>'.$sdate.' '.$event_start_time.'</p>
									<p> '.$string2.'</p>
			
									<table border="0" width="100%">
									<tr>
									<td height="34" align="left"> <div class="bn"> 
						  					'.$buy_link.'
										</div></td>
									<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$allterms->name.'&taxid='.$allterms->term_id.'&taxname=concerttypes&resn=concert">Event Details</a></div></td>
									</tr>
			
									</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid '.get_option($optname).';"></div>
			
									<div class="clear"></div>
									</div><div class="clear"></div></div>';
						}
					}
				}
				$termss = get_the_terms( $post->ID, 'composers' );
				//print_r($termss);
				if ( $termss && ! is_wp_error( $termss ) ) 
				{
					foreach ($termss as $alltermss)
					{
						if ($alltermss->name == $tax2 && in_array($alltermss->name, $arr)) 
						{
							$optnamee = $alltermss->taxonomy.'_'.$alltermss->term_id.'_colorpick';
							$calendar.= '<div class="showevent showevent-full"><span class="rounded_bull" style="background-color:#4C4C4C;"></span></span>';
							$calendar.= '<div class="highlight">
									<div class="high_panel" style="border-bottom:10px solid #4C4C4C;">
									<h2>'.$post->post_title.'</h2>
									<p style="margin-bottom: 3px;">'.$alltermss->name.'</p>
									<img src="'.$image[0].'" alt="" width="335" />
									<p>'.$sdate.' '.$event_start_time.'</p>
									<p> '.$string2.'</p>
			
									<table border="0" width="100%">
									<tr>
										<td height="34" align="left"> <div class="bn"> '.$buy_link.'</div></td>
										<td align="right"> 
											<div class="bn"> 
												<a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$alltermss->name.'&taxid='.$alltermss->term_id.'&taxname=composers&resn=concert">Event Details</a>
											</div>
										</td>
									</tr>
			
									</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid #4C4C4C;"></div>
			
									<div class="clear"></div>
								</div><div class="clear"></div></div>';
						}
						if ($tax2=='' && in_array($alltermss->name, $arr)) 
						{
							$optnamee = $alltermss->taxonomy.'_'.$alltermss->term_id.'_colorpick';
							$calendar.= '<div class="showevent showevent-full"><span class="rounded_bull" style="background-color:#4C4C4C;"></span></span>';
							$calendar.= '<div class="highlight">
									<div class="high_panel" style="border-bottom:5px solid #4C4C4C;">
									<h2>'.$post->post_title.'</h2>
									<p style="margin-bottom: 3px;">'.$alltermss->name.'</p>
									<img src="'.$image[0].'" alt="" width="335" />
									<p>'.$sdate.' '.$event_start_time.'</p>
									<p> '.$string2.'</p>
			
									<table border="0" width="100%">
									<tr>
									<td height="34" align="left"> <div class="bn"> '.$buy_link.'</div></td>
									<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$alltermss->name.'&taxid='.$alltermss->term_id.'&taxname=composers&resn=concert">Event Details</a></div></td>
									</tr>
			
									</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid #4C4C4C;"></div>
			
									<div class="clear"></div>
									</div><div class="clear"></div></div>';
						}
					}
				}
			}
		}
	}
}

	/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
	//$calendar.= str_repeat('<p> </p>',2);

	$calendar.= '</td>';
	if($running_day == 6):
	$calendar.= '</tr>';
	if(($day_counter+1) != $days_in_month):
	$calendar.= '<tr class="calendar-row">';
	endif;
	$running_day = -1;
	$days_in_this_week = 0;
	endif;
	$days_in_this_week++; $running_day++; $day_counter++;
	}

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
	for($x = 1; $x <= (8 - $days_in_this_week); $x++):
	$calendar.= '<td class="calendar-day-np"> </td>';
	endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';

	/* all done, return result */
	return $calendar;
}
/*============================================================================================================================
 * 												Normal Calendar
==============================================================================================================================*/

function draw_calendar($month,$year,$tax1,$tax2){
global $wpdb;
	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
	$calendar.= '<td class="calendar-day-np"> </td>';
	$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
	$calendar.= '<td class="calendar-day" id="'.$month.'-'.$list_day.'-'.$year.'">';

	/* add in the day number */
	$calendar.= '<div class="day-number">'.$list_day.'</div>';
	
	$calmonyear = $month.'/'.$year;
	
	$args = array( 'numberposts' => -1, 'post_type' => 'events_booking');
	$myposts = get_posts( $args );
	//print_r($myposts);
	//echo '<br>';
	foreach( $myposts as $post ) : setup_postdata($post);
	
	$eventsarr = $wpdb->get_results( "SELECT * FROM wp_eventdate WHERE eventid = '".$post->ID."' ");
	foreach ($eventsarr as $eventsarrs)
	{
		$eventdateid = $eventsarrs->id;
		$event_start_date = $eventsarrs->eventstartdate;
			
		$statrt = explode("/", $event_start_date);
		$start_str=$statrt[0]."/".$statrt[2];
	
		$sdate = $event_start_date;
		
		$eventcategory = $eventsarrs->eventcategory;
		$evncat = explode(",", $eventcategory);
		for($i=0;$i<count($evncat);$i++)
		{
		if($i!=0)
			$arr[$i]=$evncat[$i];
		}
	
	$event_start_time = $eventsarrs->eventstarttime;
	$event_end_time = $eventsarrs->eventendtime;
	if($calmonyear == $start_str)
		{
			if($list_day == $statrt[1])
			{
			//$calendar.= $post->post_name;
			$terms = get_the_terms( $post->ID, 'concerttypes' );
			//print_r($terms);
			if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ($terms as $allterms):
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ));
			$string1 = $post->post_content;
			$string2 = substr($string1, 0, 50);
			$buy_button = get_post_meta($post->ID,'buy_button');
			$link = get_post_meta($post->ID,'link');
			$buy_link='';
			if($buy_button[0]!='Disable')
				$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'">Buy Tickets</a></div>';
			if ($allterms->name == $tax1 && in_array($allterms->name, $arr)) {
				$optname = $allterms->taxonomy.'_'.$allterms->term_id.'_colorpick';
				$calendar.= '<div class="showevent">
								<span class="rounded_bull" style="background-color:'.get_option($optname).';">
										<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
				$calendar.= '<div class="highlight highlight-grid">
									<div class="high_panel" style="border-bottom:5px solid '.get_option($optname).';">
										<h2>'.$post->post_title.'</h2>
										<p style="margin-bottom: 3px;">'.$allterms->name.'</p>
										<img src="'.$image[0].'" alt="" width="335" />
										<p>'.$sdate.' '.$event_start_time.'</p>
										<p> '.$string2.'</p>
														
										<table border="0" width="100%">
											<tr>
												<td height="34" align="left"><div class="bn">'.$buy_link.'</div> </td>
												<td align="right"> 
													<div class="bn"> 
														<a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$allterms->name.'&taxid='.$allterms->term_id.'&taxname=concerttypes&resn=concert">Event Details</a>
													</div>
												</td>
											</tr>
										</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid '.get_option($optname).';"></div>
									
									<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>';
			}
			if ($tax1=='' && in_array($allterms->name, $arr) ) {
				$optname = $allterms->taxonomy.'_'.$allterms->term_id.'_colorpick';
				$calendar.= '<div class="showevent">
								<span class="rounded_bull" style="background-color:'.get_option($optname).';">
										<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
				$calendar.= '<div class="highlight highlight-grid">
									<div class="high_panel" style="border-bottom:5px solid '.get_option($optname).';">
										<h2>'.$post->post_title.'</h2>
										<p style="margin-bottom: 3px;">'.$allterms->name.'</p>
										<img src="'.$image[0].'" alt="" width="335" />
										<p>'.$sdate.' '.$event_start_time.'</p>
										<p> '.$string2.'</p>
															
										<table border="0" width="100%">
											<tr>
											<td height="34" align="left"><div class="bn">'.$buy_link.'</div> </td>
											<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$allterms->name.'&taxid='.$allterms->term_id.'&taxname=concerttypes&resn=concert">Event Details</a></div></td>
											</tr>
										</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid '.get_option($optname).';"></div>
									
									<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>';
			}
			endforeach;
			}
			$termss = get_the_terms( $post->ID, 'composers' );
			if ( $termss && ! is_wp_error( $termss ) ) {
			foreach ($termss as $alltermss):

			if ($alltermss->name == $tax2 && in_array($alltermss->name, $arr)) {
				$optnamee = $alltermss->taxonomy.'_'.$alltermss->term_id.'_colorpick';
				$calendar.= '<div class="showevent">
								<span class="rounded_bull" style="background-color:#4C4C4C;">
									<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
				$calendar.= '<div class="highlight highlight-grid">
									<div class="high_panel" style="border-bottom:5px solid #4C4C4C;">
										<h2>'.$post->post_title.'</h2>
										<p style="margin-bottom: 3px;">'.$alltermss->name.'</p>
										<img src="'.$image[0].'" alt="" width="335" />
										<p>'.$sdate.' '.$event_start_time.'</p>
										<p> '.$string2.'</p>
															
										<table border="0" width="100%">
										<tr>
										<td height="34" align="left"><div class="bn">'.$buy_link.'</div> </td>
										<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$alltermss->name.'&taxid='.$alltermss->term_id.'&taxname=composers&resn=concert">Event Details</a></div></td>
										</tr>
						
										</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid #4C4C4C;"></div>
					
									<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>';
			}
			if ($tax2=='' && in_array($alltermss->name, $arr)) {
				$optnamee = $alltermss->taxonomy.'_'.$alltermss->term_id.'_colorpick';
				$calendar.= '<div class="showevent">
								<span class="rounded_bull" style="background-color:#4C4C4C;">
									<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
				$calendar.= '<div class="highlight highlight-grid">
									<div class="high_panel" style="border-bottom:5px solid #4C4C4C;">
									<h2>'.$post->post_title.'</h2>
									<p style="margin-bottom: 3px;">'.$alltermss->name.'</p>
									<img src="'.$image[0].'" alt="" width="335" />
									<p>'.$sdate.' '.$event_start_time.'</p>
									<p> '.$string2.'</p>
														
									<table border="0" width="100%">
									<tr>
									<td height="34" align="left"><div class="bn">'.$buy_link.'</div> </td>
									<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$alltermss->name.'&taxid='.$alltermss->term_id.'&taxname=composers&resn=concert">Event Details</a></div></td>
									</tr>
					
									</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid #4C4C4C;"></div>
					
									<div class="clear"></div>
							</div><div class="clear"></div></div>';
			}
			endforeach;
			}
		}

	}
	}
	endforeach;

	/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
	//$calendar.= str_repeat('<p> </p>',2);

	$calendar.= '</td>';
	if($running_day == 6):
	$calendar.= '</tr>';
	if(($day_counter+1) != $days_in_month):
	$calendar.= '<tr class="calendar-row">';
	endif;
	$running_day = -1;
	$days_in_this_week = 0;
	endif;
	$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
	for($x = 1; $x <= (8 - $days_in_this_week); $x++):
	$calendar.= '<td class="calendar-day-np"> </td>';
	endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';

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

?><?php
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
	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar list-cal">';

	/* table headings */
	$headings = array('S','M','T','W','TH','F','SA');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
	$calendar.= '<td class="calendar-day-np"> </td>';
	$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++){
	$calendar.= '<td class="calendar-day" id="'.$month.'-'.$list_day.'-'.$year.'">';

	/* add in the day number */
	$calendar.= '<div class="day-number">'.$list_day.'</div>';

	$args = array( 'numberposts' => -1, 'post_type' => 'events_booking');
	$myposts = get_posts( $args );
	//print_r($myposts);
	//echo '<br>';
	
	$calmonyear = $month.'/'.$year;

	foreach( $myposts as $post ) { setup_postdata($post);
	
		$eventsarr = $wpdb->get_results( "SELECT * FROM wp_eventdate WHERE eventid = '".$post->ID."' ");
		foreach ($eventsarr as $eventsarrs) 
		{
			$eventdateid = $eventsarrs->id;
			$event_start_date = $eventsarrs->eventstartdate;
			
			$statrt = explode("/", $event_start_date);
			$start_str=$statrt[0]."/".$statrt[2];

			$sdate = $event_start_date;
			
			$eventcategory = $eventsarrs->eventcategory;
			$evncat = explode(",", $eventcategory);
			
			
			for($i=0;$i<count($evncat);$i++)
			{
				if($i!=0)
					$arr[$i]=$evncat[$i];
			}
			
		
		$event_start_time = $eventsarrs->eventstarttime;
		$event_end_time = $eventsarrs->eventendtime;
	
		 if($calmonyear == $start_str) 
		{ 
			if($list_day == $statrt[1])
			{
				//$calendar.= $post->post_name;
				
				
				$terms = get_the_terms( $post->ID, 'concerttypes' );
				//print_r($terms);
				if ( $terms && ! is_wp_error( $terms ) ) 
				{
					foreach ($terms as $allterms)
					{
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ));
						$string1 = $post->post_content;
						$string2 = substr($string1, 0, 50);
						$buy_button = get_post_meta($post->ID,'buy_button');
						$link = get_post_meta($post->ID,'link');
						$buy_link='';
						if($buy_button[0]!='Disable')
							$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'">Buy Tickets</a></div>';
						if ($allterms->name == $tax1 && in_array($allterms->name, $arr)) 
						{
							$optname = $allterms->taxonomy.'_'.$allterms->term_id.'_colorpick';
							$calendar.= '<div class="showevent showevent-full"><span class="rounded_bull" style="background-color:'.get_option($optname).';"></span>';
							$calendar.= '<div class="highlight">
									<div class="high_panel" style="border-bottom:10px solid '.get_option($optname).';">
									<h2>'.$post->post_title.'</h2>
									<p style="margin-bottom: 3px;">'.$allterms->name.'</p>
									<img src="'.$image[0].'" alt="" width="335" />
									<p>'.$sdate.' '.$event_start_time.'</p>
									<p> '.$string2.'</p>
														
									<table border="0" width="100%">
									<tr>
									<td height="34" align="left"><div class="bn">'.$buy_link.'</div> </td>
									<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$allterms->name.'&taxid='.$allterms->term_id.'&taxname=concerttypes&resn=concert">Event Details</a></div></td>
									</tr>
					
									</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid '.get_option($optname).';"></div>
					
									<div class="clear"></div>
									</div><div class="clear"></div></div>';
						}
						if ($tax1=='' && in_array($allterms->name, $arr)) 
						{
							$optname = $allterms->taxonomy.'_'.$allterms->term_id.'_colorpick';
							
							$calendar.= '<div class="showevent showevent-full"><span class="rounded_bull" style="background-color:'.get_option($optname).';"></span></span>';
							$calendar.= '<div class="highlight">
									<div class="high_panel" style="border-bottom:10px solid '.get_option($optname).';">
									<h2>'.$post->post_title.'</h2>
									<p style="margin-bottom: 3px;">'.$allterms->name.'</p>
									<img src="'.$image[0].'" alt="" width="335" />
									<p>'.$sdate.' '.$event_start_time.'</p>
									<p> '.$string2.'</p>
			
									<table border="0" width="100%">
									<tr>
									<td height="34" align="left"> <div class="bn"> 
						  					'.$buy_link.'
										</div></td>
									<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$allterms->name.'&taxid='.$allterms->term_id.'&taxname=concerttypes&resn=concert">Event Details</a></div></td>
									</tr>
			
									</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid '.get_option($optname).';"></div>
			
									<div class="clear"></div>
									</div><div class="clear"></div></div>';
						}
					}
				}
				$termss = get_the_terms( $post->ID, 'composers' );
				//print_r($termss);
				if ( $termss && ! is_wp_error( $termss ) ) 
				{
					foreach ($termss as $alltermss)
					{
						if ($alltermss->name == $tax2 && in_array($alltermss->name, $arr)) 
						{
							$optnamee = $alltermss->taxonomy.'_'.$alltermss->term_id.'_colorpick';
							$calendar.= '<div class="showevent showevent-full"><span class="rounded_bull" style="background-color:#4C4C4C;"></span></span>';
							$calendar.= '<div class="highlight">
									<div class="high_panel" style="border-bottom:10px solid #4C4C4C;">
									<h2>'.$post->post_title.'</h2>
									<p style="margin-bottom: 3px;">'.$alltermss->name.'</p>
									<img src="'.$image[0].'" alt="" width="335" />
									<p>'.$sdate.' '.$event_start_time.'</p>
									<p> '.$string2.'</p>
			
									<table border="0" width="100%">
									<tr>
										<td height="34" align="left"> <div class="bn"> '.$buy_link.'</div></td>
										<td align="right"> 
											<div class="bn"> 
												<a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$alltermss->name.'&taxid='.$alltermss->term_id.'&taxname=composers&resn=concert">Event Details</a>
											</div>
										</td>
									</tr>
			
									</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid #4C4C4C;"></div>
			
									<div class="clear"></div>
								</div><div class="clear"></div></div>';
						}
						if ($tax2=='' && in_array($alltermss->name, $arr)) 
						{
							$optnamee = $alltermss->taxonomy.'_'.$alltermss->term_id.'_colorpick';
							$calendar.= '<div class="showevent showevent-full"><span class="rounded_bull" style="background-color:#4C4C4C;"></span></span>';
							$calendar.= '<div class="highlight">
									<div class="high_panel" style="border-bottom:5px solid #4C4C4C;">
									<h2>'.$post->post_title.'</h2>
									<p style="margin-bottom: 3px;">'.$alltermss->name.'</p>
									<img src="'.$image[0].'" alt="" width="335" />
									<p>'.$sdate.' '.$event_start_time.'</p>
									<p> '.$string2.'</p>
			
									<table border="0" width="100%">
									<tr>
									<td height="34" align="left"> <div class="bn"> '.$buy_link.'</div></td>
									<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$alltermss->name.'&taxid='.$alltermss->term_id.'&taxname=composers&resn=concert">Event Details</a></div></td>
									</tr>
			
									</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid #4C4C4C;"></div>
			
									<div class="clear"></div>
									</div><div class="clear"></div></div>';
						}
					}
				}
			}
		}
	}
}

	/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
	//$calendar.= str_repeat('<p> </p>',2);

	$calendar.= '</td>';
	if($running_day == 6):
	$calendar.= '</tr>';
	if(($day_counter+1) != $days_in_month):
	$calendar.= '<tr class="calendar-row">';
	endif;
	$running_day = -1;
	$days_in_this_week = 0;
	endif;
	$days_in_this_week++; $running_day++; $day_counter++;
	}

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
	for($x = 1; $x <= (8 - $days_in_this_week); $x++):
	$calendar.= '<td class="calendar-day-np"> </td>';
	endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';

	/* all done, return result */
	return $calendar;
}
/*============================================================================================================================
 * 												Normal Calendar
==============================================================================================================================*/

function draw_calendar($month,$year,$tax1,$tax2){
global $wpdb;
	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
	$calendar.= '<td class="calendar-day-np"> </td>';
	$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
	$calendar.= '<td class="calendar-day" id="'.$month.'-'.$list_day.'-'.$year.'">';

	/* add in the day number */
	$calendar.= '<div class="day-number">'.$list_day.'</div>';
	
	$calmonyear = $month.'/'.$year;
	
	$args = array( 'numberposts' => -1, 'post_type' => 'events_booking');
	$myposts = get_posts( $args );
	//print_r($myposts);
	//echo '<br>';
	foreach( $myposts as $post ) : setup_postdata($post);
	
	$eventsarr = $wpdb->get_results( "SELECT * FROM wp_eventdate WHERE eventid = '".$post->ID."' ");
	foreach ($eventsarr as $eventsarrs)
	{
		$eventdateid = $eventsarrs->id;
		$event_start_date = $eventsarrs->eventstartdate;
			
		$statrt = explode("/", $event_start_date);
		$start_str=$statrt[0]."/".$statrt[2];
	
		$sdate = $event_start_date;
		
		$eventcategory = $eventsarrs->eventcategory;
		$evncat = explode(",", $eventcategory);
		for($i=0;$i<count($evncat);$i++)
		{
		if($i!=0)
			$arr[$i]=$evncat[$i];
		}
	
	$event_start_time = $eventsarrs->eventstarttime;
	$event_end_time = $eventsarrs->eventendtime;
	if($calmonyear == $start_str)
		{
			if($list_day == $statrt[1])
			{
			//$calendar.= $post->post_name;
			$terms = get_the_terms( $post->ID, 'concerttypes' );
			//print_r($terms);
			if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ($terms as $allterms):
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ));
			$string1 = $post->post_content;
			$string2 = substr($string1, 0, 50);
			//print_r($post);
			$buy_button = get_post_meta($post->ID,'buy_button');
			$link = get_post_meta($post->ID,'link');
			$buy_link='';
			if($buy_button[0]!='Disable')
				$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'">Buy Tickets</a></div>';
			if ($allterms->name == $tax1 && in_array($allterms->name, $arr)) {
				$optname = $allterms->taxonomy.'_'.$allterms->term_id.'_colorpick';
				$calendar.= '<div class="showevent">
								<span class="rounded_bull" style="background-color:'.get_option($optname).';">
										<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
				$calendar.= '<div class="highlight highlight-grid">
									<div class="high_panel" style="border-bottom:5px solid '.get_option($optname).';">
										<h2>'.$post->post_title.'</h2>
										<p style="margin-bottom: 3px;">'.$allterms->name.'</p>
										<img src="'.$image[0].'" alt="" width="335" />
										<p>'.$sdate.' '.$event_start_time.'</p>
										<p> '.$string2.'</p>
														
										<table border="0" width="100%">
											<tr>
												<td height="34" align="left"><div class="bn">'.$buy_link.'</div> </td>
												<td align="right"> 
													<div class="bn"> 
														<a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$allterms->name.'&taxid='.$allterms->term_id.'&taxname=concerttypes&resn=concert">Event Details</a>
													</div>
												</td>
											</tr>
										</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid '.get_option($optname).';"></div>
									
									<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>';
			}
			if ($tax1=='' && in_array($allterms->name, $arr) ) {
				$optname = $allterms->taxonomy.'_'.$allterms->term_id.'_colorpick';
				$calendar.= '<div class="showevent">
								<span class="rounded_bull" style="background-color:'.get_option($optname).';">
										<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
				$calendar.= '<div class="highlight highlight-grid">
									<div class="high_panel" style="border-bottom:5px solid '.get_option($optname).';">
										<h2>'.$post->post_title.'</h2>
										<p style="margin-bottom: 3px;">'.$allterms->name.'</p>
										<img src="'.$image[0].'" alt="" width="335" />
										<p>'.$sdate.' '.$event_start_time.'</p>
										<p> '.$string2.'</p>
															
										<table border="0" width="100%">
											<tr>
											<td height="34" align="left"><div class="bn">'.$buy_link.'</div> </td>
											<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$allterms->name.'&taxid='.$allterms->term_id.'&taxname=concerttypes&resn=concert">Event Details</a></div></td>
											</tr>
										</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid '.get_option($optname).';"></div>
									
									<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>';
			}
			endforeach;
			}
			$termss = get_the_terms( $post->ID, 'composers' );
			if ( $termss && ! is_wp_error( $termss ) ) {
			foreach ($termss as $alltermss):

			if ($alltermss->name == $tax2 && in_array($alltermss->name, $arr)) {
				$optnamee = $alltermss->taxonomy.'_'.$alltermss->term_id.'_colorpick';
				$calendar.= '<div class="showevent">
								<span class="rounded_bull" style="background-color:#4C4C4C;">
									<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
				$calendar.= '<div class="highlight highlight-grid">
									<div class="high_panel" style="border-bottom:5px solid #4C4C4C;">
										<h2>'.$post->post_title.'</h2>
										<p style="margin-bottom: 3px;">'.$alltermss->name.'</p>
										<img src="'.$image[0].'" alt="" width="335" />
										<p>'.$sdate.' '.$event_start_time.'</p>
										<p> '.$string2.'</p>
															
										<table border="0" width="100%">
										<tr>
										<td height="34" align="left"><div class="bn">'.$buy_link.'</div> </td>
										<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$alltermss->name.'&taxid='.$alltermss->term_id.'&taxname=composers&resn=concert">Event Details</a></div></td>
										</tr>
						
										</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid #4C4C4C;"></div>
					
									<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>';
			}
			if ($tax2=='' && in_array($alltermss->name, $arr)) {
				$optnamee = $alltermss->taxonomy.'_'.$alltermss->term_id.'_colorpick';
				$calendar.= '<div class="showevent">
								<span class="rounded_bull" style="background-color:#4C4C4C;">
									<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
				$calendar.= '<div class="highlight highlight-grid">
									<div class="high_panel" style="border-bottom:5px solid #4C4C4C;">
									<h2>'.$post->post_title.'</h2>
									<p style="margin-bottom: 3px;">'.$alltermss->name.'</p>
									<img src="'.$image[0].'" alt="" width="335" />
									<p>'.$sdate.' '.$event_start_time.'</p>
									<p> '.$string2.'</p>
														
									<table border="0" width="100%">
									<tr>
									<td height="34" align="left"><div class="bn">'.$buy_link.'</div> </td>
									<td align="right"> <div class="bn"> <a href="'.site_url().'/eventdetails?evnid='.$post->ID.'&eventdateid='.$eventdateid.'&catname='.$alltermss->name.'&taxid='.$alltermss->term_id.'&taxname=composers&resn=concert">Event Details</a></div></td>
									</tr>
					
									</table>
									</div>
									<div class="arrow-down" style="border-top: 20px solid #4C4C4C;"></div>
					
									<div class="clear"></div>
							</div><div class="clear"></div></div>';
			}
			endforeach;
			}
		}

	}
	}
	endforeach;

	/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
	//$calendar.= str_repeat('<p> </p>',2);

	$calendar.= '</td>';
	if($running_day == 6):
	$calendar.= '</tr>';
	if(($day_counter+1) != $days_in_month):
	$calendar.= '<tr class="calendar-row">';
	endif;
	$running_day = -1;
	$days_in_this_week = 0;
	endif;
	$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
	for($x = 1; $x <= (8 - $days_in_this_week); $x++):
	$calendar.= '<td class="calendar-day-np"> </td>';
	endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';

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

?>