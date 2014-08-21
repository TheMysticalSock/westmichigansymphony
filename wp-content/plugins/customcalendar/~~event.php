<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />

 <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<script>
$(document).ready(function(){
	$('#pstid').change(function(){
		var ssval = $("#pstid").val();
		data = {
			action: 'aad_get_results',
			aad_nonce: ssval
		};
		$.post(ajaxurl, data, function (response){
			$('#showres').html(response);
		});
	});
});
</script>
<?php
global $wpdb;
global $post;
global $ajaxurl;
define('DOING_AJAX', true);
define('WP_ADMIN', true);

if(isset($_POST['submit']))
{
			//$postcat = $_POST['postcat'];
			
			$postcat_array = $_POST["postcat"];
			$no_selected_postcats = count($postcat_array);
			$selected_value_postcat='cat,';
			foreach($postcat_array as $postcat_value){
				$no_selected_postcats--;
				$selected_value_postcat = $selected_value_postcat . $postcat_value;
				if($no_selected_postcats>0)
					$selected_value_postcat =$selected_value_postcat.',';
			}
			//echo $selected_value_postcat;
			//die();
			
			$eventdate = $_POST['evnd'];
			$starttime = $_POST['stime'];
			$endtime = $_POST['etime'];
			$pstid = $_POST['pstid'];
			$total = $_POST['gett'];
			$empty = false;

			$i = 0;
			for ($i=0; $i < $total; $i++) 
			{ 
				if($eventdate[$i]=='')
				{
					$j = $i+1;
					$error1.= 'Event Date '.$j.' cannot left blank<br>';
					$empty = true;
				}
				if($starttime[$i]=='')
				{
					$j = $i+1;
					$error2.= 'Event Start Time '.$j.' cannot left blank<br>';
					$empty = true;
				}
				if($endtime[$i]=='')
				{
					$j = $i+1;
					$error3.= 'Event End Time '.$j.' cannot left blank<br>';
					$empty = true;
				}
				if($selected_value_postcat=='')
				{
					$error4.= 'Please Choose any one Category<br>';
					$empty = true;
				}
				$arr[$i]['eventid']=$pstid;
				$arr[$i]['eventstartdate']=$eventdate[$i];
				$arr[$i]['eventstarttime']=$starttime[$i];
				$arr[$i]['eventendtime']=$endtime[$i];
				$arr[$i]['eventcategory']=$selected_value_postcat;
				
			}
			if(!$empty)
			{
				//print_r($arr);
				foreach ($arr as $value) {
					$wpdb->insert( 
								'wp_eventdate', 
								$value, 
								array('%d',  '%s', '%s', '%s',	'%s') 
							);
				}
				$msg = "<font color='green'>Successfully inserted. </font>";
				$path = site_url().'/wp-admin/edit.php?post_type=events_booking&page=addeventdate';
				echo "<meta http-equiv=\"Refresh\" content=\"1;url=".$path."\">";
			}
}


?>

<script>
$(document).ready(function(){
	$('.wrapper').on('focus', ".selector", function(){
    	$(this).datepicker().datepicker('show');
		true;
	});
	$('.copy').click(
		function(e){
			e.preventDefault();
			var i = $('.group').length;
			if (i<=5) 
			{
				$('.wrapper').append('<div class="group"><label>Event Date: </label> <label class="lnk">Start Time: </label><label class="lnk">End Time: </label><br><input type="text" name="evnd[]" class="selector" /><input type="text" name="stime[]" class="input" /><input type="text" name="etime[]" class="input" /><a href="#" class="remove" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><img src=<?php echo plugins_url('images/cancel.png', __FILE__); ?> /></a></div>');
				//$('.copy').before('<div class="group"><label>Event Date: </label> <label class="lnk">Start Time: </label><label class="lnk">End Time: </label><br><input type="text" name="eventdate[]" class="selector"/><input type="text" name="starttime[]" class="input" /><input type="text" name="endtime[]" class="input" /><a href="#" class="remove" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><img src=<?php echo plugins_url('images/cancel.png', __FILE__); ?> /></a></div>');
			}
		}
	)
	$(".submitbtn").click(function() {
		var iee = $('.group').length;
		$('.gett').val(iee);
	});
	
	
});
</script>
<style type="text/css">
	.copy > img 
	{
	    float: left;
	    height: 20px;
	    padding: 5px 9px 0 4px;
	    width: 20px;
	}
	.remove > img 
	{
    	height: 20px;
    	padding: 1px 0 0 25px;
    	position: absolute;
    	width: 20px;
	}
	.group 
	{
    	margin: 18px 0 0;
	}
	.lnk {
    	padding-left: 165px;
	}
	.input6 {
    	margin-left: 10px;
	}
</style>
<p><h3><u>Concert Date Upload</u></h3></p>
<br><br>
<form name="form" method="post" action="" >
	<p>
		<div>
		
		
   		<h4>Choose Event : </h4>
   		<select name="pstid" id="pstid">
   		<?php
   			$args = array(
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
   	</p>
   	<br><br>
   	<p>
   		<h4>Choose Event to Show Category (<font color="red">* require</font>): </h4>
   		<div id="showres"></div>
   	</p>
   	<br><br><br>
   	<?php 
   	if($empty) 
   	{ ?>
	   	<div style="margin-top: 15px; padding: 20px; width: 240px; border: 1px solid rgb(194, 194, 194);">
	   	 <center><font color="red" style="font-size:20px;">Error !</font></center><br>
	   		<?php if($error1) echo $error1.'<br>';?>
			<?php if($error2) echo $error2.'<br>';?>
			<?php if($error3) echo $error3.'<br>';?>
			<?php if($error4) echo $error4.'<br>';?>
	   	</div>
	   	<?php 
    }
    ?>
	<p>
		<div class="wrapper">
   		<div class="group">
   			<h3><font color="red" >** All the field must required. </font></h3>
   			<label>Event Date: </label>
   			<label class="lnk">Start Time: </label>
   			<label class="lnk">End Time: </label> <br>
   			<input type="text" name="evnd[]" class="selector" id=""/>
   			<input type="text" name="stime[]" class="input" />
   			<input type="text" name="etime[]" class="input" />
		</div>
	</div>
		<a href="#" class="copy" rel=".phone">
   			<img src=<?php echo plugins_url('images/plus.png', __FILE__); ?> /> 
   		</a>
   		<input type="submit" value="Submit" name="submit" class="submitbtn">
   		<input type="hidden" value="" name="gett" class="gett" />
   	</p>
   	
</form>
<br><br><br>
<?php if($msg) echo $msg; ?>

	
	
	
	
	