<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script>
$(document).ready(function(){
  	$('.copy').click(
		function(e){
			e.preventDefault();
			var i = $('.group').length;
			if (i<=5) 
			{
				$('.copy').before('<div class="group"><label>Title:</label><label class="lnk">Link:</label><label class="lnk">Image:</label><br><input type="text" name="title[]" class="input"/><input type="text" name="link[]" class="input" /><input type="file" name="image[]" class="input6" /><a href="#" class="remove" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><img src=<?php echo plugins_url('images/cancel.png', __FILE__); ?> /></a></div>');
			}
		}
	)
	$(".submitbtn").click(function() {
		var iee = $('.group').length;
		$('.gett').val(iee);
	});
  	$('#pstid').change(function(){
		var ssval = $("#pstid").val();
		data = {
			action: 'aad_get_results',
			aad_nonce: ssval,
			spon:'sponsor'
		};
		$.post(ajaxurl, data, function (response){
			$('#showres').html(response);
		});
	});
});

</script>
<?php
//echo "he-";
global $wpdb;
global $post;
global $ajaxurl;
define('DOING_AJAX', true);
define('WP_ADMIN', true);

if(isset($_POST['submit']))
{
			
			$postcat_array = $_POST["posttid"];
			$no_selected_postcats = count($postcat_array);
			$selected_value_postcat='id,';
			
			foreach($postcat_array as $postcat_value){
				$no_selected_postcats--;
				$selected_value_postcat = $selected_value_postcat . $postcat_value;
				if($no_selected_postcats>0)
					$selected_value_postcat =$selected_value_postcat.',';
			}
			/* echo $selected_value_postcat;
			die(); */
			
			$title = $_POST['title'];
			$link = $_POST['link'];
			$image = $_FILES['image']['name'];
			$total = $_POST['gett'];
			$pstid = $_POST['pstid'];
			$empty = false;
			
			$imagees = $_FILES["image"];
			$no_selected_image= count($imagees['tmp_name']);
			$selected_value_image='';
			$j=0;
			foreach($imagees['tmp_name'] as $image_value)
			{
				$uploads = wp_upload_dir();
				$imgpath = $uploads[path];
				$imgpath = str_replace("'\'","/",$imgpath);
				$imgpath = $imgpath.'/'.$imagees['name'][$j];
				
				if(move_uploaded_file($image_value, $imgpath))
					$msg = '<font color="green">Image Uploaded Successfully. </font><br>';
				else
				{
					$error5 = 'Image Not Uploaded Successfully';
					$empty = true;
				}
				$j++;
			}
			
			for ($i=0; $i < $total; $i++)
			{
				if($title[$i]=='')
				{
				$j = $i+1;
					$error1.= 'Title in '.$j.' cannot left blank<br>';
						$empty = true;
				}
				if($link[$i]=='')
				{
					$j = $i+1;
					$error2.= 'Link in '.$j.' cannot left blank<br>';
					$empty = true;
				}
				if($image[$i]=='')
				{
					$j = $i+1;
					$error3.= 'Please choose image in '.$j.'or cannot left blank<br>';
					$empty = true;
				}
				if($selected_value_postcat=='')
				{
					$error4.= 'Please Choose any one Category<br>';
					$empty = true;
				}
				if($pstid=='')
				{
					$error6.= 'Please choose an event<br>';
					$empty = true;
				}
				$arr[$i]['title']=$title[$i];
				$arr[$i]['link']=$link[$i];
				$arr[$i]['image']=$image[$i];
				$arr[$i]['eventdateid']=$selected_value_postcat;;
				$arr[$i]['type']='guestartist';
				$arr[$i]['eventid']=$pstid;
			}
			if(!$empty)
			{
				//die();
				foreach ($arr as $value) {
					$wpdb->insert( 
									'wp_sponsor', 
									$value, 
									array('%s',	'%s', '%s',	'%s','%s','%s') 
								);
				}
				$msg.= "<font color='green'>Successfully inserted. </font>";
				$path = site_url().'/wp-admin/admin.php?page=guestartistsponsor';
				echo "<meta http-equiv=\"Refresh\" content=\"1;url=".$path."\">";
			}
}
?>


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
<p><h3><u>Guest Artist Sponsor Upload</u></h3></p>
<form name="form" method="post" action="" enctype="multipart/form-data">
	<p>
   		<h4>Choose Event</h4>
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
			/*echo "<pre>";
			print_r($myposts);
			echo "</pre>";*/
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
   	<br><br>
   	<?php 
   	if($empty) 
   	{ ?>
	   	<div style="margin-top: 15px; padding: 20px; width: 240px; border: 1px solid rgb(194, 194, 194);">
	   	 <center><font color="red" style="font-size:20px;">Error !</font></center><br>
	   		<?php if($error1) echo $error1.'<br>';?>
			<?php if($error2) echo $error2.'<br>';?>
			<?php if($error3) echo $error3.'<br>';?>
			<?php if($error4) echo $error4.'<br>';?>
			<?php if($error5) echo $error5.'<br>';?>
			<?php if($error6) echo $error6.'<br>';?>
	   	</div>
	   	<?php 
    }
    ?>
	<p>
   		<div class="group">
   			<label>Title: </label> <label class="lnk">Link: </label><label class="lnk">Image: </label> <br>
   			<input type="text" name="title[]" class="input"/>
   			<input type="text" name="link[]" class="input" />
   			<input type="file" name="image[]" class="input" />
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
