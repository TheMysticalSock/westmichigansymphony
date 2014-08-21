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

	
	global $wpdb;

	 $data_terms = $wpdb->get_results("select wt.* from wp_terms wt inner join wp_term_taxonomy wpt on wt.term_id = wpt.term_id 
inner join wp_term_relationships wptr on wptr.term_taxonomy_id = wpt.term_taxonomy_id 
inner join	x_wp_extra xwe on wptr.object_id = xwe.post_id where ex_id='$theID'");

 $terms_post = $wpdb->get_results("select post_id from x_wp_extra where ex_id='$theID'");
	

?>
<div id="fct-item-template" style="display:none;" class="fct-tooltip">
	<div class="fct-arrow-holder">
		<div class="fct-arrow"></div>
		<div class="fct-arrow-border"></div>		
	</div>
	<div class="fct-main">
		<div class="fct-header">
			<div class="fc-close-tooltip"><a href="javascript:void(0);"></a></div>
			<div class="fc-title"></div>
            <h5><span class="fc-event-list-subtitle fc-event-term taxonomy-<?php echo RHC_CONCERT_TYPE?>"><?php echo $data_terms[0]->name; ?></span></h5>
            
 <h5> 
		
		  
      </h5>
		</div>
		<div class="fct-body">
        <div class="fc-image"></div>
			<div class="fc-start"></div>			
			<div class="fc-description" style="clear:both;"></div>
              <div class="tool-tip-buttons">
               <a class="buy-tickets" href="https://www.choicesecure03.net/mainapp/eventschedule.aspx?Clientid=WestMichiganSymphony" target="_blank">buy tickets</a>
               <a class="goto tool-tip-event-info"></a>
            </div>
        </div>
		<div class="fct-footer">
			<div class="fc-social"></div>
		</div>	
	</div>
</div>


