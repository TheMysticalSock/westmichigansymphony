<?php
global $wpdb;
global $post;
$path = site_url().'/wp-admin/edit.php?post_type=events_booking&page=alleventdate';
if(isset($_POST['submit']))
{
	
	$eventsarr = $wpdb->get_results( "SELECT * FROM wp_eventdate WHERE eventid = '".$_POST['pstid']."' ");
	$eventcount = $wpdb->get_var( "SELECT count(*) FROM wp_eventdate WHERE eventid = '".$_POST['pstid']."' ");
	if($eventcount>0)
	{
		$evnshow = '<div class="sponmaindiv">
				<table class="sponsorcatshow" cellspacing="0" cellpadding="0"> 
					<tr>
						<th>Event Date </th>
				        <th>Event Start Time</th>
						<th>Event End Time </th>
						<th>Event Category </th>
						<th>Delete </th>
					</tr>';
		foreach ($eventsarr as $eventsarrs)
		{
			
			$evnshow.='<tr>
					<td>'.$eventsarrs->eventstartdate.'</td>
					<td>'.$eventsarrs->eventstarttime.'</td>
					<td>'.$eventsarrs->eventendtime.'</td>
					<td>'.substr($eventsarrs->eventcategory,4).'</td>
					<td><a href="'.$path.'&type=del&evnid='.$eventsarrs->id.'">Delete</a></td>
				   </tr>';
		}
			$evnshow.='</table>';
	}
	else {
		$msg = "<font color='red'>No Result Found</font>";
	}
}
if($_GET['type']=='del'&& $_GET['evnid']!='')
{
	$res = $wpdb->query('DELETE FROM wp_eventdate WHERE id ="'.$_GET['evnid'].'"');
	if($res){
		$msg = "<font color='green'>Successfully Deleted</font>";
		echo "<meta http-equiv=\"Refresh\" content=\"0;url=".$path."\">";
	}
}
?>
<p>
<!-- ===================================================================================
							1st  form show
======================================================================================= -->
<form action="" method="post" >
   		<h4>Choose Event : </h4>
   		<select name="pstid" id="pstid">
   		<?php
   			$args = array(
					'posts_per_page'		=> -1,
					'post_type'             => 'events_booking',
					'post_status'			=> 'publish',
					'orderby'               => 'date',
					'order'                 => 'DESC'
				);
			$myposts = get_posts($args);
			echo '<option value="">Choose An Event</option>';
			foreach( $myposts as $post ) : setup_postdata($post); ?>
			<option value="<?php echo $post->ID; ?>" ><?php the_title(); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="submit" name="submit" value="submit" />
</form>
<br>
<br>
</p>
<?php if($evnshow) echo $evnshow;?>
<br>
<br>
<?php if($msg) echo $msg;?>

	
	