<?php
/*
Plugin Name: Auto SEO Tags
Plugin URI: http://www.couponcomrade.com/Auto-SEO-Tags
Description: Every time a visitor arrives from a search engine their query is added as a tag for the post they visit.
Author: Christopher Doman
Version: 1.0
Author URI: http://www.couponcomrade.com/
*/


function addsometags() {
				
//Don't do anything if we've already got 20 tags
$posttags = get_the_tags();
$count=0;
if ($posttags) {
foreach($posttags as $tag) {
$count++;
if ($count==20) break;
}
}


if ($count<20) {
		global $wpdb;

		$engines['google.'] = 'q=';
		$engines['altavista.com'] = 'q=';
		$engines['search.msn.'] = 'q=';
		$engines['yahoo.'] = 'p=';
		$engines['bing.'] = 'q=';
		$engines['yandex.'] = 'text=';

		$referer = $_SERVER['HTTP_REFERER'];
		$blogtarget = $_SERVER["REQUEST_URI"];
		$ref_arr = parse_url("$referer");
		$ref_host = $ref_arr['host'];

		foreach($engines as $host => $skey){
			if (strpos($ref_host, $host) !== false){
				$res_query = urldecode($ref_arr['query']);
				if (preg_match("/{$engines[$host]}(.*?)&/si",$res_query."&",$matches)){
					$query = trim($matches[1]);
					$target = str_replace("'","''",str_replace(";","",sanitize_title_with_dashes($query)));
					global $post;
					$thePostID = $post->ID;
					wp_add_post_tags($thePostID, $target);
			}
		}
	}
 }
}


add_action('wp_footer', 'addsometags');
	
?>