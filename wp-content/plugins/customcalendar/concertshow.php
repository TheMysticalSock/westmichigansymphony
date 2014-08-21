<?php
/* 
 * Template Name: Concert Show
 */
?>
<?php get_header(); ?>
<?php
	$args = array( 'numberposts' => -1, 'post_type' => 'events_booking');
	$myposts = get_posts( $args );
	//print_r($myposts);
	echo '<div class="event_panel">
				<ul class="event">';
	//$post->post_title;
	foreach( $myposts as $post ) : setup_postdata($post);

		$event_start_date = get_post_meta($post->ID,'event_start_date');
		$event_start_time = get_post_meta($post->ID,'event_start_time');
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ));
		$buy_button = get_post_meta($post->ID,'buy_button');
		$link = get_post_meta($post->ID,'link');
		$buy_link='';
		if($buy_button[0]!='Disable') {
			if($eventsarr->eventid != $previous_id){
				$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'" target="_blank">Buy Tickets</a></div>';
				$previous_id = $eventsarr->eventid;
			} else {
				$buy_link = '<div class="bn"><a class="buy-ticket" href="'.$link[0].'-2" target="_blank">Buy Tickets</a></div>';
			}
		}
		
		$string1 = $post->post_content;
		$string2 = substr($string1, 0, 200);
		
		$terms = get_the_terms( $post->ID, 'concerttypes' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ($terms as $allterms):
				$optname = $allterms->taxonomy.'_'.$allterms->term_id.'_colorpick';
				echo '<li><div class="concertdate"><p>';
					echo date('l', strtotime( $event_start_date[0]));
				echo '</p><p>';
					echo date('F', strtotime( $event_start_date[0]));
				echo '</p><p class="number">';
					echo date('d', strtotime( $event_start_date[0]));
				echo '</p><p class="time">';
					echo $event_start_time[0];
				echo '</p></div>';
				echo '<div class="color" style="background:'.get_option($optname).'"> </div>
				<div class="pic_panel"> 
					<img src="'.$image[0].'" alt="" />
				</div>
				<div class="panel_content">
					<h2> '.$post->post_title.' 
						<span class="buy"> 
							'.$buy_link.'
						</span> 
					</h2>

					<div class="cont_mid">
						<p>'; 
					echo $allterms->name;
				echo '</p>
					</div>
					<p>';	
				echo $string2;	
				echo '<span> 
					  		<a href="'.site_url().'/eventdetails?evnid='.$post->ID.'"> MORE </a>
					  </span> 
					</p>
				</div>
				<div class="clear"></div> ';
			endforeach;
		}
		$termss = get_the_terms( $post->ID, 'composers' );
		if ( $termss && ! is_wp_error( $termss ) ) {
			foreach ($termss as $alltermss):
				//$optname = $alltermss->taxonomy.'_'.$alltermss->term_id.'_colorpick';
				echo '<li><div class="date"><p>';
					echo date('l', strtotime( $event_start_date[0]));
				echo '</p><p>';
					echo date('F', strtotime( $event_start_date[0]));
				echo '</p><p class="number">';
					echo date('d', strtotime( $event_start_date[0]));
				echo '</p><p class="time">';
					echo $event_start_time[0];
				echo '</p></div>';
				echo '<div class="color" style="background:#000000;"> </div>
				<div class="pic_panel"> 
					<img src="'.$image[0].'" alt="" />
				</div>
				<div class="panel_content">
					<h2> '.$post->post_title.' 
						<span class="buy"> 
							'.$buy_link.'
						</span> 
					</h2>

					<div class="cont_mid">
						<p>'; 
					echo $alltermss->name;
				echo '</p>
					</div>
					<p>';	
				echo $string2;	
				echo '<span> 
					  		<a href="'.site_url().'/eventdetails?evnid='.$post->ID.'"> MORE </a>
					  </span> 
					</p>
				</div>
				<div class="clear"></div> ';
			endforeach;
		}
	endforeach;
	
	echo "		</ul>
		</div>";
?>
<?php get_footer(); ?>