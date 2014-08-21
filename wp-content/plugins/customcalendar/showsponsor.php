<?php
global $wpdb;
global $post;
if(isset($_POST['submit']))
{
	$evenid = $_POST['pstid'];
	$uploads = wp_upload_dir();
	$imgpath = $uploads[url];
	
	$fivesdraftss = $wpdb->get_results( "SELECT * FROM wp_sponsor WHERE eventid = ".$evenid." ");
	$eventcount = $wpdb->get_var( "SELECT count(*) FROM wp_sponsor WHERE eventid = '".$evenid."' ");
	if($eventcount>0)
	{
		$result = "<div class='sponmaindiv'><table class='sponsorcatshow' cellspacing='0' cellpadding='0'><tr><th>Title</th><th>Image</th><th>Link</th><th>Type</th><th>Delete</th></tr>";
		foreach ( $fivesdraftss as $fivesdrafts ) 
			{	
				$result.= "<tr>";
				$result.= "<td>".$fivesdrafts->title."</td>";
		$result.= "<td>".'<img src="'.$imgpath.'/'.$fivesdrafts->image.'" style="width:100px; height:100px;"/>'."</td>";
				$result.= "<td>".$fivesdrafts->link."</td>";
				$result.= "<td>".$fivesdrafts->type."</td>";
				
				$result.= '<td><a href="'.site_url().'/wp-admin/admin.php?page=showdelsponsor&del=yes&id='.$fivesdrafts->id.'"> Delete </a> </td>';
				
				$result.= "</tr>";
			}
			$result.= "</table></div>";
	}
	else {
		$msg = "<font color='red'>No Result Found</font>";
	}
}
if($_GET['del']=='yes' && $_GET['id']!='')
{
	$path = site_url().'/wp-admin/admin.php?page=showdelsponsor';
	$wpdb->query('DELETE FROM wp_sponsor WHERE id ="'.$_GET['id'].'"');
	$msg = "<font color='green'>Successfully Deleted</font>";
	echo "<meta http-equiv=\"Refresh\" content=\"1;url=".$path."\">";
}
?>
<style type="text/css">
	.spnshow table
	{
		width:90%
	}
	.spnshow th
	{
		background: linear-gradient(to top, #ECECEC, #F9F9F9) repeat scroll 0 0 #F1F1F1;
    	border-bottom-color: #DFDFDF;
    	border-top-color: #FFFFFF;
    	text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
    	width: 200px;
	}
	.spnshow td
	{
		text-align: center;
	}
</style>
<h4>Choose Event</h4>
<form name="" action="" method="post">
   		<select name="pstid">
   		<?php
   			$args = array(
					'posts_per_page'		=> -1,
					'post_type'             => 'events_booking',
					'post_status'			=> 'publish',
					'orderby'               => 'date',
					'order'                 => 'DESC'
				);
			$myposts = get_posts($args);
			foreach( $myposts as $post ) : setup_postdata($post); ?>
			<option value="<?php echo $post->ID; ?>" ><?php the_title(); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="submit" name="submit" value="Submit" />
</form>
<?php
if($result)
	echo "<h2> concert List </h2><br>";
	echo $result;

?>
<?php if($msg) echo $msg; ?>