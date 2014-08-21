<?
$myrows = $wpdb->get_results("SELECT wp_posts.ID, wp_posts.post_content, wp_posts.post_title, wp_posts.post_status, wp_posts.post_type, wp_postmeta.meta_key, DATE_FORMAT(NOW(),'%m-%d-%Y') AS nDate, DATE_FORMAT(wp_postmeta.meta_value,'%m-%d-%Y') AS xDate 
FROM wp_posts INNER JOIN wp_postmeta ON wp_posts.ID = wp_postmeta.post_id
GROUP BY wp_posts.ID, wp_posts.post_content, wp_posts.post_title, wp_posts.post_status, wp_posts.post_type, wp_postmeta.meta_key, xDate
HAVING wp_posts.post_status='publish' AND wp_posts.post_type='events' AND wp_postmeta.meta_key='fc_start' AND xDate > nDate");
?>

<div class="fc-event-list-container"> 
  <!-- you can replace with custom content to be displayed before the items -->
  <? foreach ( $myrows as $xCal ) { 
$xMeta = $wpdb->get_row("SELECT meta_value FROM wp_postmeta WHERE meta_key = '_thumbnail_id' AND (((post_id)='" . $xCal->ID . "'))");
$xStart = $wpdb->get_row("SELECT meta_value FROM wp_postmeta WHERE meta_key = 'fc_start_time' AND (((post_id)='" . $xCal->ID . "'))");
$xColor = $wpdb->get_row("SELECT meta_value FROM wp_postmeta WHERE meta_key = 'fc_color' AND (((post_id)='" . $xCal->ID . "'))");
$xExc = $wpdb->get_row("SELECT * FROM wp_posts WHERE (((id)='" . $xCal->ID . "'))");
$xImg = $wpdb->get_row("SELECT wp_postmeta.meta_value FROM wp_postmeta WHERE (((wp_postmeta.post_id)=".$xMeta->meta_value.") AND ((wp_postmeta.meta_key)='_wp_attached_file'));");
$xDay = explode('-',$xCal->meta_value);

?>
  <div class="fc-event-list-holder">
    <div class="fc-event-list-no-events fc-remove">
      <div class="fc-no-list-events-message"></div>
    </div>
    <div class="fc-event-list-item fc-remove">
      <div class="event-list-start-image">
        <h5><span class="fc-event-list-subtitle fc-start"><? echo @date("l", mktime(0, 0, 0, $xDay[1], $xDay[2], $xDay[0])); ?><br />
          <? echo @date("F", mktime(0, 0, 0, $xDay[1], $xDay[2], $xDay[0])); ?><br />
          <?=$xDay[2] ?>
          <br>
          <?=$xStart->meta_value; ?>
          </span></h5>
        <div class="fc-event-list-featured-image" style="border-left:<?=$xColor->meta_value ?>; border-left-width: 5px; border-left-style: solid;"> <a class="fc-event-link" href="/staging/<?=$xCal->post_type ?>/<? echo trim(str_replace(' ','-',$xCal->post_title)); ?>"><img class="fc-event-list-image" src="/staging/wp-content/uploads/<?=$xImg->meta_value; ?>" /></a> </div>
      </div>
      <div class="fc-event-list-content"> <a class="buy-tickets" href="https://www.choicesecure03.net/mainapp/eventschedule.aspx?Clientid=WestMichiganSymphony" target="_blank">buy tickets</a>
        <h4><a class="fc-event-link fc-event-list-title" href="/staging/<?=$xCal->post_type ?>/<? echo trim(str_replace(' ','-',$xCal->post_title)); ?>"><? echo $xCal->post_title; ?></a></h4>
        <p><? echo nl2br(substr($xExc->post_content,0,450)); ?>... <a href="/staging/<?=$xCal->post_type ?>/<? echo trim(str_replace(' ','-',$xCal->post_title)); ?>">More></a></p>
        <div class="fc-event-list-description"></div>
      </div>
      <div class="fc-event-list-clear"></div>
    </div>
  </div>
  <? } ?>
</div>
