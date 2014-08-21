<?php
/* 
 * Template Name: Event Show
 */
?>
<?php get_header();?>

<?php 
global $wpdb;
$evenid = $_GET['evnid'];
//$eventdateid = $_GET['eventdateid'];
if($evenid!='')
	{
		$eventsarrs = $wpdb->get_results( "SELECT t.* FROM wp_eventdate t,wp_posts y WHERE t.id ='".$evenid."' and y.ID=t.eventid and y.post_status='publish'");
		
		$events=array();
		foreach ($eventsarrs as $eventsarr)
		{
			/*echo "<pre>";
			print_r($eventsarr);
			echo "</pre>";*/
			$eventcategory = $eventsarr->eventcategory;
			
			$evncat = explode(",", $eventcategory);
			for($i=0;$i<count($evncat);$i++)
			{
			if($i!=0)
				$arr_category[$i]=$evncat[$i];
			}
			
			$row=$wpdb->get_row("select tm.taxonomy, t.term_id from wp_terms t, wp_term_taxonomy tm where t.term_id=tm.term_id and t.name='".$arr_category[1]."'");
			
			if ($row->taxonomy=='concerttypes') {
				$optname = $row->taxonomy.'_'.$row->term_id.'_colorpick';
				$optname = get_option($optname);
			}
			else{
				$optname = '#5F5F5F';
			}
			$buy_button = get_post_meta($eventsarr->eventid,'buy_button');
			$link = get_post_meta($eventsarr->eventid,'link');
			$buy_link='';
			if($buy_button[0]!='Disable'){
				
				$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'" target="_blank">Buy Tickets</a></div>';
				$buy_link_2 = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'-2" target="_blank">Buy Tickets</a></div>';
				
			}
//				$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'-2">Buy Tickets</a></div>';
?>
		<div class="container-box">
		<div class="heading-box">
        <?php
		$eventStripped = substr($eventsarr->eventcategory, 4);
		$eventStripped = preg_replace('/^([^,]*).*$/', '$1', $eventStripped);
        //$theID = get_the_ID();
		//echo $theID;
		if ($eventStripped == 'The Block') {
			$scheduleLinkBack = 'http://westmichigansymphony.org/the-block/2013-2014-programs/';
		} else {
			$scheduleLinkBack = 'http://westmichigansymphony.org/concerts-tickets/2013-2014-programs/';
		}
		?>
	
        <!--<script>
    document.write('<a href="' + document.referrer + '">< TO THE SCHEDULE 13/14</a>');
</script>-->
		<a href="<?php echo $scheduleLinkBack; ?>">< TO THE SCHEDULE 13/14</a>
		</div>
		<div class="left-area">
			<div class="divder">
				<div class="blue-heading" style="background:<?php echo $optname; ?>"> 
				</div>  <!-- Get Color of Category-->
				<h3>
					<strong><?php echo get_the_title($eventsarr->eventid);?></strong>
				</h3> <!-- Post Heading Title-->
				<p>
					<span>
						<?php 
						$eventStripped = substr($eventsarr->eventcategory, 4);
						$eventStripped=preg_replace('/^([^,]*).*$/', '$1', $eventStripped);
						echo $eventStripped; ?>
					</span>
				</p> <!-- Category Name -->
				<br />
				<p> 
					<?php 
					   $group = get_post_meta($eventsarr->eventid,'group_name'); 
					   echo str_replace("{b}", "<br />", $group[0]);
					?>
				</p> <!-- Get Group Name of Concert-->
				<div class="clr"></div>
			</div>
			<div class="divder">
					<div class="blue-heading blk-hd" style="background:#484848;"> Dates and Tickets</div>
					<div class="info-box">
						<?php 
						$evdatarrs = $wpdb->get_results( "SELECT * FROM wp_eventdate WHERE eventid = '".$eventsarr->eventid."' ");
						//print_r($evdatarrs);
						$this_event = 1;
						foreach ($evdatarrs as $evdatarr)
						{
							//echo "New Event ";
							//print_r($evdatarr);
							//echo "<br /><br />";
							$event_start_date = $evdatarr->eventstartdate;
							$event_start_time = $evdatarr->eventstarttime;
							$event_end_time = $evdatarr->eventendtime;
						
							$evn_strt = $event_start_date;
							list($month, $date, $year) = explode("/", $evn_strt);
							$tmro = $date.'-'.$month.'-'.$year;
								?>
								<div class="small-box">
									<p><b>
										<?php 
										echo date('l', strtotime( $evdatarr->eventstartdate)).', '.date('F', strtotime( $evdatarr->eventstartdate)).' '.date('d', strtotime( $evdatarr->eventstartdate)).' at '.$evdatarr->eventstarttime;
										?>
									</b></p> <!-- Get Time and Date From Concert -->
									<p> <?php 
									   $theater = get_post_meta($evdatarr->eventid,'theater_name'); 
									   echo $theater[0];
									?></p> <!-- Theater Name -->
									<br />
									<p style="margin-bottom: 20px;"> <?php 
									   $tickets = get_post_meta($evdatarr->eventid,'tickets_information');
									   //echo $tickets[0]; 
									   echo str_replace("{b}", "<br />", $tickets[0]);
									?></p> <!-- Tickets Textarea -->
									
									<?php if($this_event == 1){
										// check to see if this event is in the past
		if(strtotime($event_start_date) < mktime(0,0,0,date('m'),date('d'),date('Y'))){
			$buy_link = '';
		}
												echo $buy_link;
												$this_event = 2;
									} else {
										// check to see if this event is in the past
		if(strtotime($event_start_date) < mktime()){
			$buy_link_2 = '';
		}
										echo $buy_link_2;	
									}
									?> <!-- Buy Tickets Button -->
								</div>
								<?php
								//$evn_strt = $tmro;
							}
							?>
						<?php ?>
					</div>
					<div class="clr"></div>
				</div>
				<div class="clr"></div>
				<div class="divder">
					<div class="blue-heading blk-hd" style="background:#484848;">Program</div>

					<p><?php 
					   $program = get_post_meta($eventsarr->eventid,'program'); 
					   echo str_replace("{b}", "<br />", $program[0]);
					   //echo $program[0];
					?></p> <!-- Program Textarea -->
					<br />
					<div class="clr"></div>
				</div>
			</div>
			<div class="right-area">
				<?php 
				   		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $eventsarr->eventid )); 
				   		echo "<img src='".$image[0]."' />";
				   	?> <!-- Image Of the Post -->
				<p>
					<span> </span> <!-- Image Title name -->
				</p>
				<div class="add-box" >
					
					<?php 
					$fivesdrafts = $wpdb->get_results("	SELECT * FROM wp_sponsor WHERE eventid = '".$eventsarr->eventid."' AND type = 'concert'");
				//	print_r($fivesdrafts);
					//echo "	SELECT * FROM wp_sponsor WHERE eventid = '".$eventsarr->eventid."' AND type = 'concert'";
					//die();
 	 				$uploads = wp_upload_dir();
					//print_r($uploads);
					$imgpath = $uploads[url];
					if(count($fivesdrafts)>0) 
					{
						 foreach ( $fivesdrafts as $fivesdraft ) 
							{	
							?>
								<p style="margin-bottom: 10px;">
									<span>Concert Sponsor</span>
								</p>
                               <?php //echo $fivesdraft->title;
                                
								echo '<a href="'.$fivesdraft->link.'" target="_blank"><img src="'.$imgpath.'/'.$fivesdraft->image.'" /></a>';
							}
					}
						
					?>
				</div>
				<div class="add-box" >
					
					<?php 
					
					$fivesdraftss = $wpdb->get_results("SELECT * FROM wp_sponsor WHERE eventid = '".$eventsarr->eventid."' AND type = 'guestartist'");
					$uploads = wp_upload_dir();
					$imgpath = $uploads[url];
					if(count($fivesdraftss)>0)
					{
						foreach ($fivesdraftss as $eventsarrs)
						{
							?>
                            <p style="margin-bottom: 10px;">
                                <span>Guest Artist Sponsor</span>
                            </p>
                            <?php //echo $eventsarrs->title;
							echo '<a href="'.$eventsarrs->link.'" target="_blank"><img src="'.$imgpath.'/'.$eventsarrs->image.'" /></a>';
						}
					}
					?>
				</div>
                <div class="add-box" >
					
					<?php 
					
					$fivesdraftss = $wpdb->get_results("SELECT * FROM wp_sponsor WHERE eventid = '".$eventsarr->eventid."' AND type = 'media'");
					$uploads = wp_upload_dir();
					$imgpath = $uploads[url];
					if(count($fivesdraftss)>0)
					{
						foreach ($fivesdraftss as $eventsarrs)
						{
							?>
                            <p style="margin-bottom: 10px;">
                                <span>Media Sponsor</span>
                            </p>
                            <?php //echo $eventsarrs->title;
							echo '<a href="'.$eventsarrs->link.'" target="_blank"><img src="'.$imgpath.'/'.$eventsarrs->image.'" /></a>';
						}
					}
					?>
				</div>
			</div>
		</div>
<?php
		}
	}
get_footer(); 
?>