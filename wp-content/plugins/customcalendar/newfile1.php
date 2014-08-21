<?php
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
		
	$terms = get_the_terms( $post->ID, array('concerttypes','composers') );
$i=1;		$j=1;
foreach ($terms as $hhh)
{
	if ($hhh->taxonomy == 'concerttypes'){
		$cat1[$i]['id'] = $hhh->term_id;
		$cat1[$i]['name'] = $hhh->name;
		$i++;
	}
	if ($hhh->taxonomy == 'composers'){
		$cat2[$j][id] = $hhh->term_id;
		$cat2[$j][name] = $hhh->name;
		$j++;
	}
}
	
foreach ($cat1 as $cat1a)
{
	if(in_array($cat1a['name'],$arr))
	{
		$optname = 'concerttypes_'.$cat1a['id'].'_colorpick';
			
		$cal = '<div class="showevent">
								<span class="rounded_bull" style="background-color:'.get_option($optname).';">
										<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
		$concert = 'yes';
	}
}
foreach ($cat2 as $cat2a)
{
	if(in_array($cat2a['name'],$arr) && $concert!='yes')
	{
		$cal = '<div class="showevent">
								<span class="rounded_bull" style="background-color:#4C4C4C;">
										<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
		$concert = 'No';
	}
}
if ($tax1=='')
	$calendar.=$cal;
	
	
//print_r($terms);
if ( $terms && ! is_wp_error( $terms ) ) {
	foreach ($terms as $allterms):
	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ));
	$string1 = $post->post_content;
	$string2 = substr($string1, 0, 50);
	$buy_button = get_post_meta($post->ID,'buy_button');
	$buy_link='';
	if($buy_button[0]=='Enable')
		$buy_link = '<div class="bn"><a class="buy-ticket" href="http://www.choiceticketing.com/">Buy Tickets</a></div>';
		
	if ($allterms->name == $tax1 && in_array($allterms->name, $arr)) {
		$optname = $allterms->taxonomy.'_'.$allterms->term_id.'_colorpick';
		$calendar.= '<div class="showevent">
								<span class="rounded_bull" style="background-color:'.get_option($optname).';">
										<p class="event-highlight">'.$post->post_title.'</p>
								</span>';
		$calendar.= '<div class="highlight highlight-grid">
									<div class="high_panel" style="border-bottom:5px solid '.get_option($optname).';">
										<h2>'.$post->post_title.'</h2>
										<p>'.$allterms->name.'</p>
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
		$calendar.= '<div class="highlight">
									<div class="high_panel" style="border-bottom:10px solid '.get_option($optname).';">
									<h2>'.$post->post_title.'</h2>';

		$calendar.='<p>'.substr($eventsarrs->eventcategory,4,strlen($eventsarrs->eventcategory)).'</p>';

		$calendar.= '<img src="'.$image[0].'" alt="" width="335" />
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
	endforeach;
}
}

}
}
endforeach;