<?php require_once('Connections/x_wsso.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

	  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
	
		  switch ($theType) {
			case "text":
			  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			  break;    
			case "long":
			case "int":
			  $theValue = ($theValue != "") ? intval($theValue) : "NULL";
			  break;
			case "double":
			  $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
			  break;
			case "date":
			  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			  break;
			case "defined":
			  $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			  break;
		  }
		  return $theValue;
	}
}

$theID = get_the_ID();

	mysql_select_db($database_x_wsso, $x_wsso);
	$query_get_conductor = "SELECT * FROM x_wp_extra_conductor WHERE ex_id = $theID";
	$get_conductor = mysql_query($query_get_conductor, $x_wsso) or die(mysql_error());
	$row_get_conductor = mysql_fetch_assoc($get_conductor);
	$totalRows_get_conductor = mysql_num_rows($get_conductor);
	
	mysql_select_db($database_x_wsso, $x_wsso);
$query_get_dates = "SELECT * FROM x_wp_extra_dates WHERE ex_id = $theID";
$get_dates = mysql_query($query_get_dates, $x_wsso) or die(mysql_error());
$row_get_dates = mysql_fetch_assoc($get_dates);
$totalRows_get_dates = mysql_num_rows($get_dates);
	
	mysql_select_db($database_x_wsso, $x_wsso);
	$query_get_extra_data = "SELECT * FROM x_wp_extra_conductor_detail WHERE ex_id = $theID";
	$get_extra_data = mysql_query($query_get_extra_data, $x_wsso) or die(mysql_error());
	$row_get_extra_data = mysql_fetch_assoc($get_extra_data);
	$totalRows_get_extra_data = mysql_num_rows($get_extra_data);
	
	mysql_select_db($database_x_wsso, $x_wsso);
$query_get_sponsor = "SELECT * FROM x_wp_extra_sponsor WHERE ex_id = $theID";
$get_sponsor = mysql_query($query_get_sponsor, $x_wsso) or die(mysql_error());
$row_get_sponsor = mysql_fetch_assoc($get_sponsor);
$totalRows_get_sponsor = mysql_num_rows($get_sponsor);

$time = strtotime("-1 year", time());
$xDate = date("Y", $time);
$xDate = $xDate . "/" . date("Y");

?>


<div class="event-page-wrapper">
<div class="back-to-calendar">
<a href="/staging/calendar/">< TO THE SCHEDULE <?=$xDate  ?></a>
</div>
  <div class="event-page-left">
    <div class="event-page-header-wrap">
      <div class="event-page-header">
        <h2> <?php echo get_the_title(); ?></h2>
        <?
		$product_terms = wp_get_object_terms($theID, 'concert_type');
		if(!empty($product_terms)){
		  if(!is_wp_error( $product_terms )){
			foreach($product_terms as $term){
			  echo '<h6>'.$term->name.'</h6>'; 
			}
		  }
		}        
		?>        
      </div>
      <div class="event-page-featuring">
        <h2><?php echo $row_get_conductor['conductor']; ?></h2>
        <h2><?php echo $row_get_extra_data['conductor']; ?></h2>
      </div>
    </div>
    <div class="event-page-dates">
      <h2>Dates and Tickets</h2>
      
      <?php $xC = 1; do { $xC++; ?>
        <div class="event-page-info" style="float:left; <? if ($xC % 2) { ?> margin-right: 0;<? } ?>">
          <?php echo $row_get_dates['date_info']; ?>
          <div class="event-page-buy-tickets">
            <p><a class="buy-tickets" href="https://www.choicesecure03.net/mainapp/eventschedule.aspx?Clientid=WestMichiganSymphony" target="_blank">buy tickets</a></p>
          </div>
        </div>
        <?php } while ($row_get_dates = mysql_fetch_assoc($get_dates)); ?>
         
    </div>
    <div class="event-page-program-info-wrapper">
      <h2>Program</h2>
      <div class="event-page-program-info">
        <div class="event-page-program-featuring">
          <?php echo $Excerpt  ; ?>
        </div>
        <div class="event-page-program-description"><?php echo $content; ?></div>
      </div>
    </div>
  </div>
  <div class="event-page-right">
    <div class="event-page-image"><img src="/staging/extra_images/<?php echo $row_get_conductor['conductor_image']; ?>" alt="<?php echo $row_get_conductor['conductor']; ?>"><span><?php echo $row_get_conductor['conductor']; ?></span> </div>
    <div class="event-page-sponsors">
      <p class="sponsor-p">Concert Sponsor:</p>
      <?php do { ?>
        <a href="<?php echo $row_get_sponsor['sponsor_url']; ?>"><img src="/staging/extra_images/<?php echo $row_get_sponsor['sponsor_image']; ?>" alt="<?=$row_get_sponsor['sponsor_name'] ?>"></a>
        <?php } while ($row_get_sponsor = mysql_fetch_assoc($get_sponsor)); ?>
      </div>
  </div>
</div>
<!-- This is what was here when I got here. (AM - Yesterday) -->
<div class="single-event-gmap-holder" style="display:none;"> [single_venue_gmap width=960 height=250] </div>
<?php
mysql_free_result($get_conductor);

mysql_free_result($get_dates);

mysql_free_result($get_extra_data);

mysql_free_result($get_sponsor);
?>
