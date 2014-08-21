<?php


/* ========================= Calendar Genearator Function=======================================*/
function adddatefuntion()
{
	include 'date_plugin_function.php';
}
add_action( 'init', 'adddatefuntion' );

/*========================= External CSS Add In Plugin =========================================*/
function my_plugin_admin_init() {
	// Respects SSL, Style.css is relative to the current file
	wp_register_style( 'mystyle', plugins_url('custstyleplugin.css', __FILE__) );
	wp_enqueue_style( 'mystyle' );
	
	wp_register_style( 'datestyle', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' );
	wp_enqueue_style( 'datestyle' );
	
}
add_action( 'init', 'my_plugin_admin_init' );

/* =============== Create Custom Post Type & Taxonomy Register In that Custom Post Type ===============*/
function create_Events_Booking() {

	$args_cg = array(
			'labels' => array
			(
					'name' => 'Events Booking',
					'singular_name' => 'Events Booking',
					'add_new' => 'Add Events',
					'add_new_item' => 'Add New Events',
					'edit_item' => 'Edit Events',
					'new_item' => 'New Events',
					'all_items' => 'All Events',
					'view_item' => 'View Events',
					'search_items' => 'Search News',
					'not_found' => 'No News found',
					'not_found_in_trash' => 'No News found in Trash',
					'parent_item_colon' => 'Parent Page',
					'menu_name' => 'Events Booking',
					'show_in_nav_menus' => true
			),
			'taxonomies' => array('concerttypes','composers'),
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title', 'excerpt','editor', 'thumbnail')


	);

	register_post_type( 'events_booking', $args_cg);
}
add_action( 'init', 'create_Events_Booking' );

/* =============================================================================================================================
 *			 									Create Custom Taxonomy 
 ** =============================================================================================================================*/
function ConcertTypes_init() {
	// create a new taxonomy
	register_taxonomy(
	'concerttypes',
	'events_booking',
	array(
	'hierarchical'          => true,
	'show_admin_column'     => true,
	'show_ui'               => true,
	'label' => __( 'Concert Types' ),
	'rewrite' => array( 'slug' => 'ConcertTypes' ),
	'public' => true
	)
	);
}
add_action( 'init', 'ConcertTypes_init' );

/* =============================================================================================================================
 *			 									Create Custom Taxonomy 
 ** =============================================================================================================================*/
function Composers_init() {
	// create a new taxonomy
	register_taxonomy(
	'composers',
	'events_booking',
	array(
	'hierarchical'          => true,
	'show_admin_column'     => true,
	'show_ui'               => true,
	'label' => __( 'Composers' ),
	'rewrite' => array( 'slug' => 'Composers' ),
	'public' => true
	)
	);
}
add_action( 'init', 'Composers_init' );
/* =================================================================================================
                        Add Sub Menu in Admin Panel for custom post type
====================================================================================================*/
function menuevents()
{
	require('event.php');
}
add_action("admin_menu","add_menu_event");
function add_menu_event()
{
	add_submenu_page( "edit.php?post_type=events_booking", "Add Event Date", "Add Event Date", "manage_options", "addeventdate", "menuevents" );
	//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}
/* =================================================================================================
 					Add Sub Menu in Admin Panel for custom post type
====================================================================================================*/
function alleventdate()
{
	require('allevent.php');
}
add_action("admin_menu","all_menu_event");
function all_menu_event()
{
	add_submenu_page( "edit.php?post_type=events_booking", "All Events Date", "All Events Date", "manage_options", "alleventdate", "alleventdate" );
	//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}
/*======================== Create Custom Page for Event Details ======================================*/
function eventdetailspage()
{
	$post = array(
			'comment_status' => 'open', // 'closed' means no comments.
			'ping_status'    => 'open', // 'closed' means pingbacks or trackbacks turned off
			'post_author'    =>  1 , //The user ID number of the author.
			'post_name'      => 'eventdetails', // The name (slug) for your post
			'post_status'    => 'publish', //Set the status of the new post.
			'post_title'     => 'Event Details Page Show', //The title of your post.
			'post_type'      => 'page' //You may want to insert a regular post, page, link, a menu item or some custom post type
	);
	wp_insert_post( $post, $wp_error );
}
register_activation_hook( __FILE__, 'eventdetailspage' );

/* ============================ Define Template to the Custom Page ================================ */
add_filter( 'page_template', 'event_page_template' );
function event_page_template( $page_template )
{
	//echo "help";
	if ( is_page( 'eventdetails' ) ) 
	{
		$nstr=str_replace("\\","/",dirname(__FILE__)).'/';
		$page_template = $nstr.'/eventshow.php';
	}
	return $page_template;
}
/*===============================================================================================================
										Shortcode For Event Section Calendar 
=================================================================================================================*/
function custevnt_func(){
	$month = date('m');
	echo '<div class="calendar-showup">';
	echo '<ul class="list-grid-style">
		<li><span>View</span></li>
		<li><a class="list-view" href="'.site_url().'/concerts-tickets/concert-calendar/?monthadd='.$month.'&yearadd='.date('Y').'"><img src="'.plugins_url( 'images/list-view.png', __FILE__ ).'" /></a></li>
		<li><a class="grid-view" href="'.site_url().'/concerts-tickets/concert-calendar/?typ=full&monthadd='. $month .'&yearadd='.date('Y').'"><img src="'.plugins_url( 'images/grid-view.png', __FILE__ ).'" /></a></li>
	</ul>';
	echo "<form name='taax1' id='taax1' action='' method='post' class='taxonomy_form'>";
	echo get_cat_frm_taxonomy1('concerttypes');
	echo get_cat_frm_taxonomy2('composers');
	echo "<input type='submit' name='submit' value='View Results' class='taxonomy_submit'/>";
	echo "</form>";
	
	$taxonomysearch1 = ($_POST['tax1']!='' ? $_POST['tax1'] : '');
	$taxonomysearch2 = ($_POST['tax2']!='' ? $_POST['tax2'] : '');
	
	if($_GET['typ']=='' && $_GET['monthadd']==''){
		$mmon = date('m');
		$years = date('Y');
		echo '<ul class="month-list-view">
					<li><a href="'.site_url().'/concerts-tickets/concert-calendar/?monthadd='.$mmon.'&yearadd='.$years.'">'.date('F',mktime(0,0,0,$mmon,1,$year)).' '.$years.'</a>
						<ul>';
		for($i=0;$i<12;$i++){
			if($i==0)
				$mmon =$mmon-1;
			if($mmon<10)
				$mmon ='0'.$mmon;
			echo '<li><a href="'.site_url().'/concerts-tickets/concert-calendar/?monthadd='.$mmon.'&yearadd='.$years.'">'.date('F',mktime(0,0,0,$mmon,1,$yrs)).' '.$years.'</a></li>';
			$years = ($mmon != 12 ? $years : $years + 1);
			$mmon = ($mmon != 12 ? $mmon + 1 : 1);
		}
		echo'</ul>
					</li>
				  </ul>';
		echo draw_calendar(date('m'),date('Y'),$taxonomysearch1,$taxonomysearch2);
	}
	
	
	if($_GET['typ']=='full' && $_GET['monthadd']!=''){
			date_funct_button_sixmon();
			$mmon = $_GET['monthadd'];
			$yar = $_GET['yearadd'];
			echo '<div class="calendar-area-full">';
			for($i=0;$i<6;$i++) {
				if($mmon<10)
					$mmon ='0'.$mmon;
				echo '<div class="calendar-panel">';
				echo '<h1>'.date('F',mktime(0,0,0,$mmon,1,$yar)).'</h1>';
				echo draw_calendar_full($mmon,$yar,$taxonomysearch1,$taxonomysearch2);
				echo '</div>';
				$mmon = $mmon != 12 ? $mmon + 1 : 1;
			}
			echo '<div class="clear"></div></div>';
	}
	
	
	if ($_GET['monthadd']!='' && $_GET['typ']=='')
	{
		//date_funct_button();
		$mmon = $_GET['monthadd'];
		$years = $_GET['yearadd'];
	
		echo '<ul class="month-list-view">
					<li><a href="'.site_url().'/concerts-tickets/concert-calendar/?monthadd='.$mmon.'&yearadd='.$years.'">'.date('F',mktime(0,0,0,$mmon,1,$years)).' '.$years.'</a>
						<ul>';
		for($i=0;$i<12;$i++)
		{
			if($i==0){
				//$mmon =($mmon != 1 ? $mmon - 1 : 12);
				$mmon = date('m') - 1;
				//echo $years;
				$years = ($mmon != 12 ? $years : $years - 1);
				if($mmon<10)
					$mmon ='0'.$mmon;
				echo '<li><a href="'.site_url().'/concerts-tickets/concert-calendar/?monthadd='.$mmon.'&yearadd='.$years.'">'.date('F',mktime(0,0,0,$mmon,1,$years)).' '.$years.'</a></li>';
				$years = ($mmon != 12 ? $years : $years + 1);
				$mmon = ($mmon != 12 ? $mmon + 1 : 1);
			}
			if($i>=1){
				if($mmon<10)
					$mmon ='0'.$mmon;
				echo '<li><a href="'.site_url().'/concerts-tickets/concert-calendar/?monthadd='.$mmon.'&yearadd='.$years.'">'.date('F',mktime(0,0,0,$mmon,1,$years)).' '.$years.'</a></li>';
			
				$years = ($mmon != 12 ? $years : $years + 1);
				$mmon = ($mmon != 12 ? $mmon + 1 : 1);
			}
		}
		echo'</ul>
					</li>
				  </ul>';
		echo draw_calendar($_GET['monthadd'],$_GET['yearadd'],$taxonomysearch1,$taxonomysearch2);
	}
	echo '</div>';
	}
	
	add_shortcode( 'eventcalendar', 'custevnt_func' );
/*===============================================================================================================
		Shortcode For Block Section Calendar  [calendarizeit taxonomy='concert_type' terms='olthoff-hall']
=================================================================================================================*/
function custblock_func(){
	$month = date('m');
	echo '<div class="calendar-showup">';
	echo '<ul class="list-grid-style">
		<li><span>View</span></li>
		<li><a class="list-view" href="'.site_url().'/the-block/concert-calendar/?monthadd='.date('m').'&yearadd='.date('Y').'"><img src="'.plugins_url( 'images/list-view.png', __FILE__ ).'" /></a></li>
		<li><a class="grid-view" href="'.site_url().'/the-block/concert-calendar/?typ=full&monthadd='.$month.'&yearadd='.date('Y').'"><img src="'.plugins_url( 'images/grid-view.png', __FILE__ ).'" /></a></li>
	</ul>';
	echo "<form name='taax1' id='taax1' action='' method='post' class='taxonomy_form'>";
	echo get_cat_frm_taxonomy1('concerttypes');
	echo get_cat_frm_taxonomy2('composers');
	echo "<input type='submit' name='submit' value='View Results' class='taxonomy_submit'/>";
	echo "</form>";

	
	$taxonomysearch1 = ($_POST['tax1']!='' ? $_POST['tax1'] : '');
	$taxonomysearch2 = ($_POST['tax2']!='' ? $_POST['tax2'] : '');
	
	if($_GET['typ']=='' && $_GET['monthadd']==''){
		$mmon = date('m');
		$years = date('Y');
		echo '<ul class="month-list-view">
					<li><a href="'.site_url().'/the-block/concert-calendar/?monthadd='.$mmon.'&yearadd='.$years.'">'.date('F',mktime(0,0,0,$mmon,1,$year)).' '.$years.'</a>
						<ul>';
		for($i=0;$i<12;$i++){
			if($i==0)
				$mmon =$mmon-1;
			if($mmon<10)
				$mmon ='0'.$mmon;
			echo '<li><a href="'.site_url().'/the-block/concert-calendar/?monthadd='.$mmon.'&yearadd='.$years.'">'.date('F',mktime(0,0,0,$mmon,1,$yrs)).' '.$years.'</a></li>';
			$years = ($mmon != 12 ? $years : $years + 1);
			$mmon = ($mmon != 12 ? $mmon + 1 : 1);
		}
		echo'</ul>
					</li>
				  </ul>';
		echo draw_calendar(date('m'),date('Y'),$taxonomysearch1,$taxonomysearch2);
	}
	
	
	if($_GET['typ']=='full' && $_GET['monthadd']!=''){
			date_funct_button_sixmon_block();
			$mmon = $_GET['monthadd'];
			$yar = $_GET['yearadd'];
			echo '<div class="calendar-area-full">';
			for($i=0;$i<6;$i++) {
				if($mmon<10)
					$mmon ='0'.$mmon;
				echo '<div class="calendar-panel">';
				echo '<h1>'.date('F',mktime(0,0,0,$mmon,1,$yar)).'</h1>';
				echo draw_calendar_full($mmon,$yar,$taxonomysearch1,$taxonomysearch2);
				echo '</div>';
				$mmon = $mmon != 12 ? $mmon + 1 : 1;
			}
			echo '<div class="clear"></div></div>';
	}
	
	
	if ($_GET['monthadd']!='' && $_GET['typ']=='')
	{
		//date_funct_button();
		$mmon = $_GET['monthadd'];
		$years = $_GET['yearadd'];
	
		echo '<ul class="month-list-view">
					<li><a href="'.site_url().'/the-block/concert-calendar/?monthadd='.$mmon.'&yearadd='.$years.'">'.date('F',mktime(0,0,0,$mmon,1,$years)).' '.$years.'</a>
						<ul>';
		for($i=0;$i<12;$i++)
		{
			if($i==0){
				$mmon =($mmon != 1 ? $mmon - 1 : 12);
				//echo $years;
				$years = ($mmon != 12 ? $years : $years - 1);
				if($mmon<10)
					$mmon ='0'.$mmon;
				echo '<li><a href="'.site_url().'/the-block/concert-calendar/?monthadd='.$mmon.'&yearadd='.$years.'">'.date('F',mktime(0,0,0,$mmon,1,$years)).' '.$years.'</a></li>';
				$years = ($mmon != 12 ? $years : $years + 1);
				$mmon = ($mmon != 12 ? $mmon + 1 : 1);
			}
			if($i>=1){
				if($mmon<10)
					$mmon ='0'.$mmon;
				echo '<li><a href="'.site_url().'/the-block/concert-calendar/?monthadd='.$mmon.'&yearadd='.$years.'">'.date('F',mktime(0,0,0,$mmon,1,$years)).' '.$years.'</a></li>';
			
				$years = ($mmon != 12 ? $years : $years + 1);
				$mmon = ($mmon != 12 ? $mmon + 1 : 1);
			}
		}
		echo'</ul>
					</li>
				  </ul>';
		echo draw_calendar($_GET['monthadd'],$_GET['yearadd'],$taxonomysearch1,$taxonomysearch2);
	}
	echo '</div>';
	}
	
	add_shortcode( 'blockcalendar', 'custblock_func' );

/* =====================================================================================================
	   						Short-Code for the Concert Event in Symphony
======================================================================================================== */
function ShowConcertevent_func()
{
	global $wpdb;
	echo '<div class="event_panel">
				<ul class="event">';
	$eventsarrs = $wpdb->get_results( "SELECT t.* FROM wp_eventdate t,wp_posts y WHERE y.ID=t.eventid and y.post_status='publish' and t.eventcategory NOT LIKE '%Block' ORDER by t.eventstartdate ASC");
	foreach ($eventsarrs as $eventsarr)
	{
		$eventdateid = $eventsarr->id.'<br>';
		
		/*list($month, $date, $year) = explode("/", $eventsarr->eventstartdate);
		$tmro = $date.'-'.$month.'-'.$year;*/
		
		$eventcategory = $eventsarr->eventcategory;
		$evncat = explode(",", $eventcategory);
		for($i=0;$i<count($evncat);$i++)
		{
		if($i!=0)
			$arr_category[$i]=$evncat[$i];
		}
		
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $eventsarr->eventid ));
		
		$buy_button = get_post_meta($eventsarr->eventid,'buy_button');
		$buy_link='';
		if($buy_button[0]=='Enable')
			 $buy_link = '<div class="bn"><a class="buy-ticket" href="http://www.choiceticketing.com/">Buy Tickets</a></div>';
		
		$content_post = get_post($eventsarr->eventid);
		$content = $content_post->post_content;
		$content = apply_filters('the_content', $content);
		$string2 = substr($content, 0, 200);
		
		$row=$wpdb->get_row("select tm.taxonomy, t.term_id from wp_terms t, wp_term_taxonomy tm where t.term_id=tm.term_id and t.name='".$arr_category[1]."'");
		if ($row->taxonomy=='concerttypes') {
			$optname = $row->taxonomy.'_'.$row->term_id.'_colorpick';
			$optname = get_option($optname);
		}
		else{
			$optname = '#5F5F5F';
		}
		
		// $event_start_time
		echo '<li>
				<div class="concertdate">
					<p>'.date('l', strtotime( $eventsarr->eventstartdate)).'</p>
					<p>'.date('F', strtotime( $eventsarr->eventstartdate)).'</p>
					<p class="number">'.date('d', strtotime( $eventsarr->eventstartdate)).'</p>
					<p class="time">'.$eventsarr->eventstarttime.'</p>
				</div>
				<div class="color" style="background:'.$optname.'"> </div>
				<div class="pic_panel">
					<img src="'.$image[0].'" alt="" />
				</div>
				<div class="panel_content">
					<h2> '.get_the_title($eventsarr->eventid).'</h2>
					<div class="buy">'.$buy_link.'</div>
					<div class="cont_mid">
						<p>'.$arr_category[1].'</p>
					</div>
					<p>
						'.$string2.'
						<span>
						  <a href="'.site_url().'/eventdetails?evnid='.$eventsarr->id.'" style="color:#00A6BD!important;"> MORE&nbsp;></a>
						</span>
					</p>
				</div>
						  		
				<div class="clear"></div> </li>';
	}
	echo "</ul></div>";
}
add_shortcode( 'showconcertevent', 'ShowConcertevent_func' );
/* =====================================================================================================
	   						Short-Code for the Concert Event in Block
======================================================================================================== */
function ShowConcerteventblock_func(){
	global $wpdb;
	echo '<div class="event_panel">
				<ul class="event">';
	$eventsarrs = $wpdb->get_results( "SELECT t.* FROM wp_eventdate t,wp_posts y WHERE y.ID=t.eventid and y.post_status='publish' and t.eventcategory LIKE '%Block' ORDER by t.eventstartdate ASC");
	if(count($eventsarrs)<=0)
		echo 'Not Found';
	foreach ($eventsarrs as $eventsarr)
	{
		$eventdateid = $eventsarr->id.'<br>';
		
		list($month, $date, $year) = explode("/", $eventsarr->eventstartdate);
		$tmro = $date.'-'.$month.'-'.$year;
		
		$eventcategory = $eventsarr->eventcategory;
		$evncat = explode(",", $eventcategory);
		for($i=0;$i<count($evncat);$i++)
		{
		if($i!=0)
			$arr_category[$i]=$evncat[$i];
		}
		
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $eventsarr->eventid ));
		
		$buy_button = get_post_meta($eventsarr->eventid,'buy_button');
		$buy_link='';
		if($buy_button[0]=='Enable')
			 $buy_link = '<div class="bn"><a class="buy-ticket" href="https://www.choicesecure03.net/mainapp/eventschedule.aspx?Clientid=WestMichiganSymphony">Buy Tickets</a></div>';
		
		$content_post = get_post($eventsarr->eventid);
		$content = $content_post->post_content;
		$content = apply_filters('the_content', $content);
		$string2 = substr($content, 0, 200);
		
		$row=$wpdb->get_row("select tm.taxonomy, t.term_id from wp_terms t, wp_term_taxonomy tm where t.term_id=tm.term_id and t.name='".$arr_category[1]."'");
		if ($row->taxonomy=='concerttypes') {
			$optname = $row->taxonomy.'_'.$row->term_id.'_colorpick';
			$optname = get_option($optname);
		}
		else{
			$optname = '#5F5F5F';
		}
		
		echo '<li>
				<div class="concertdate">
										<p>'.date('l', strtotime( $eventsarr->eventstartdate)).'</p>
					<p>'.date('F', strtotime( $eventsarr->eventstartdate)).'</p>
					<p class="number">'.date('d', strtotime( $eventsarr->eventstartdate)).'</p>
					<p class="time">'.$eventsarr->eventstarttime.'</p>
				</div>
				<div class="color" style="background:'.$optname.'"> </div>
				<div class="pic_panel">
					<img src="'.$image[0].'" alt="" />
				</div>
				<div class="panel_content">
					<h2> '.get_the_title($eventsarr->eventid).'</h2>
					<span class="buy">'.$buy_link.'</span>
					<div class="cont_mid">
						<p>'.$arr_category[1].'</p>
					</div>
					<p>'.$string2.'
						<span>
						  <a href="'.site_url().'/eventdetails?evnid='.$eventsarr->id.'" style="color:#00A6BD!important;"> MORE&nbsp;></a>
						</span>
					</p>
				</div>
				<div class="clear"></div>
			</li>';
	}
	echo "</ul></div>";
}
add_shortcode( 'showconcerteventblock', 'ShowConcerteventblock_func' );
/*=========================================================================================
							Home page show top 3 content Event
===========================================================================================*/
function top3event_func(){
	global $wpdb;
	
	echo '<div class="top-current-div ">
					<div class="top-current-head">
	 					<h3>Concerts &amp; Tickets</h3>
						<a href="'.site_url().'/concerts-tickets/concert-calendar/">See the whole season calendar &gt;</a>
					</div>
					<div class="top-current-content">
				      <ul class="concerts-n-tickets">';
	//$eventsarrs = $wpdb->get_results( "SELECT t.* FROM wp_eventdate t,wp_posts y WHERE y.ID=t.eventid and y.post_status='publish' and t.eventstartdate > now() ORDER by t.eventstartdate ASC LIMIT 0,3");
	$eventsarrs = $wpdb->get_results( "SELECT t.id, t.eventid, min(t.eventstartdate) as eventstartdate, max(t.eventstartdate) as eventenddate, t.eventstarttime, t.eventendtime, t.eventcategory FROM wp_eventdate t,wp_posts y WHERE y.ID=t.eventid and y.post_status='publish' and t.eventstartdate > now() group by t.eventid ORDER by t.eventstartdate ASC LIMIT 0,3");
	foreach ($eventsarrs as $eventsarr)
	{
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $eventsarr->eventid ));
		
		$buy_button = get_post_meta($eventsarr->eventid,'buy_button');
		$buy_link='';
		if($buy_button[0]=='Enable')
			$buy_link = '<div class="bn"><a class="buy-ticket" href="http://www.choiceticketing.com/">Buy Tickets</a></div>';
		
		$content_post = get_post($eventsarr->eventid);
		$content = $content_post->post_content;
		$content = apply_filters('the_content', $content);
		$string2 = substr($content, 0, 100);
		
		//$startdate = substr($eventsarr->eventstartdate, 
			
	echo '<li>
			<a href="'.site_url().'/eventdetails?evnid='.$eventsarr->id.'" style="color:#00A6BD!important;"> <img src="'.$image[0].'" alt="" /> </a>
			<h2>'.date('m/d/y', strtotime($eventsarr->eventstartdate));
			
			if($eventsarr->eventstartdate != $eventsarr->eventenddate){
				echo '-'.date('m/d/y', strtotime($eventsarr->eventenddate));
			}
	echo '&nbsp;&nbsp;&nbsp;'. $eventsarr->eventstarttime.'<br></h2>
			<p>'.$string2.'...'.'</p>
			<a href="'.site_url().'/eventdetails?evnid='.$eventsarr->id.'" style="color:#00A6BD!important;"> MORE&nbsp;></a>
			<div class="bn">
				'.$buy_link.'
			</div>
		</li>';
	}
	echo '</ul>
			</div>
			</div>';
}
add_shortcode('showtop3event','top3event_func');

/*========================================================================================================================
 	*							Show Event Category value pass using ajax from event.php
 =======================================================================================================================*/

function show_post_category($postid)
{
	global $wpdb;
	
	$postid = $_POST['aad_nonce'];
	$spontype = $_POST['spon'];
	
	if($spontype!='sponsor')
	{
		
		$terms = get_the_terms( $postid, 'concerttypes' );
		
		if ( $terms && ! is_wp_error( $terms ) )
		{
		echo '<div class="evenmaindiv">
				<h3>Concert Types</h3>
				';
			//echo "<input type='text' value='concerttypes' name='taxonomyname'/>";
		foreach ($terms as $allterms):
		echo '<p><input type="checkbox" value="'.$allterms->name.'" name="postcat[]" />'.$allterms->name.'</p>';
		endforeach;
		echo '</div>';
		}
		
		
		$termss = get_the_terms( $postid, 'composers' );
		
		if ( $termss && ! is_wp_error( $termss ) )
		{
			echo '<div class="evenmaindiv">
				<h3>Composers</h3>
				';
			//	echo "<input type='text' value='composers' name='taxonomyname'/>";
		foreach ($termss as $alltermss):
		echo '<p><input type="checkbox" value="'.$alltermss->name.'" name="postcat[]" />'.$alltermss->name.'</p>';
		endforeach;
		echo '</div>';
		}
		
	}
	if($spontype=='sponsor')
	{
			$eventsarr = $wpdb->get_results( "SELECT * FROM wp_eventdate WHERE eventid = '".$postid."' ");
			$eventcount = $wpdb->get_var( "SELECT count(*) FROM wp_eventdate WHERE eventid = '".$postid."' ");
			if($eventcount>0)
			{
				$evnshow = '<div class="sponmaindiv">
						<table class="sponsorcatshow" cellspacing="0" cellpadding="0"> 
							<tr>
								<th>Select</th>
								<th>Event Date </th>
						        <th>Event Start Time</th>
								<th>Event End Time </th>
								<th>Event Category </th>
							</tr>';
				foreach ($eventsarr as $eventsarrs)
				{
					
					$evnshow.='<tr>
							<td><input type="checkbox" name="posttid[]" value="'.$eventsarrs->id.'" /></td>
							<td>'.$eventsarrs->eventstartdate.'</td>
							<td>'.$eventsarrs->eventstarttime.'</td>
							<td>'.$eventsarrs->eventendtime.'</td>
							<td>'.substr($eventsarrs->eventcategory,4).'</td>
						   </tr>';
				}
					$evnshow.='</table></div>';
			}
			else {
				$msg = "<font color='red'>No Result Found</font>";
			}
	}
	echo $evnshow;
	echo $msg;
	die();
}
add_action('wp_ajax_aad_get_results', 'show_post_category');
/*=============================================================================================================
 		* 									Add Admin Menu 													*
 =============================================================================================================*/
function sponsor_main()
{
	echo "
	<h2><u>Sponsor Page</u></h2>		
	<p>You Can Add and Delete Various types of Sponsor images and Link (Media , Concert)</p>
	";
}
add_action("admin_menu","sponsor_func");
function sponsor_func()
{
	
	$url1=plugins_url('images/ss.png', __FILE__);
	add_menu_page( "Sponsor", "Sponsor", "add_users", "sponsor","sponsor_main",$url1);
	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
}
/* =================================================================================================
						 Add Sub Menu in Admin Panel for custom post type
====================================================================================================*/
function Mediasponsor1()
{
	require('mediasponsor.php');
}

add_action("admin_menu","add_admin_mediasponsor");
function add_admin_mediasponsor()
{
	add_submenu_page( "sponsor", "Media Sponsor", "Media Sponsor", "manage_options", "mediasponsor", Mediasponsor1 );
	//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}
/* =================================================================================================
 							Add Sub Menu in Admin Panel for custom post type
====================================================================================================*/
function Concertsponsor1()
{
	require('concertsponsor.php');
}
add_action("admin_menu","add_admin_concertsponsor");
function add_admin_concertsponsor()
{
	add_submenu_page( "sponsor", "Concert Sponsor", "Concert Sponsor", "manage_options", "concertsponsor", Concertsponsor1 );
}
/* =================================================================================================
 						Add Sub Menu in Admin Panel for custom post type
====================================================================================================*/
function Showsponsor1()
{
	require('showsponsor.php');
}
add_action("admin_menu","add_admin_Showsponsor");
function add_admin_Showsponsor()
{
	add_submenu_page( "sponsor", "Show Sponsor List", "Show Sponsor List", "manage_options", "showdelsponsor", Showsponsor1 );
}
?>