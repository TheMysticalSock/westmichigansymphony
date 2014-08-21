<?php 
	/*	
	*	Goodlayers Function File
	*	---------------------------------------------------------------------
	*	This file include all of important function and features of the theme
	*	to make it available for later use.
	*	---------------------------------------------------------------------
	*/



// Breadcrumb Function (Aaron)	
function dimox_breadcrumbs() {
	$showOnHome = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
	$delimiter = '/'; // delimiter between crumbs
	$home = 'Home'; // text for the 'Home' link
	$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
	$before = '<span class="current">'; // tag before the current crumb
	$after = '</span>'; // tag after the current crumb
	
	// Testing... (10/2/2013-AV)
	$zPostID = get_the_ID();
	// echo $zPostID;
	
	global $post;
	$homeLink = get_bloginfo('url');
	
	if(is_home() || is_front_page()) {
		if($showOnHome == 1) echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a></div>';
	}
	else {
		echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
		
		if(is_category()) {
      $thisCat = get_category(get_query_var('cat'), false);
      if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
      echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;
		}
		elseif(is_search()) {
      echo $before . 'Search results for "' . get_search_query() . '"' . $after;
		}
		elseif(is_day()) {
			// echo 'zisday';
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('d') . $after;
		}
		elseif(is_month()) {
			// echo 'zismonth';
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('F') . $after;
		}
		elseif(is_year()) {
			// echo 'zisyear';
      echo $before . get_the_time('Y') . $after;
		}
		elseif(is_single() && !is_attachment()) {
			// echo 'zissingle';
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
        if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
        echo $cats;
        if ($showCurrent == 1) echo $before . get_the_title() . $after;
      }
		}
		elseif(!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
			// echo 'zisntsingle';
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;
		}
		elseif(is_attachment()) {
			// echo 'zisattachment';
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
		}
		elseif(is_page() && !$post->post_parent) {
			// echo 'zispage';
      if ($showCurrent == 1) echo $before . get_the_title() . $after;
		}
		elseif(is_page() && $post->post_parent) {
			// Check for event ID (10/2/2013-AV)
			if(isset($_GET['evnid'])) {
				// Grab the event ID
				$zEventID = $_GET['evnid'];
				// Check if this is a The Block event
				$zCount = zIsTheBlock($zEventID);
				if($zCount == 1) {
					// Declare the parent ID for The Block
					$parent_id = 440;
				}
				else {
					$parent_id = $post->post_parent;
				}
			}
			else {
				// Grab the parent ID from the db
				$parent_id  = $post->post_parent;
			}
			// Create the breadcrumb as usual
			$breadcrumbs = array();
			while($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id  = $page->post_parent;
			}
			
			$breadcrumbs = array_reverse($breadcrumbs);
			for ($i = 0; $i < count($breadcrumbs); $i++) {
				echo $breadcrumbs[$i];
				
				if($i != count($breadcrumbs) - 1) echo ' ' . $delimiter . ' ';
			}
			
			if($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
		}
		elseif(is_tag()) {
      echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
 
    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . 'Articles posted by ' . $userdata->display_name . $after;
 
    } elseif ( is_404() ) {
      echo $before . 'Error 404' . $after;
    }
 
    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }
 
    echo '</div>';
 
  }
} // end dimox_breadcrumbs()
 
 
// move content down!
function add_my_script() {
wp_enqueue_script(
    'move_content_down', // name your script so that you can attach other scripts and de-register, etc.
    get_template_directory_uri() . '/js/move_content_down.js', // this is the location of your script file
    array('jquery') // this array lists the scripts upon which your script depends
);
}

// Creating a new function that will test whether or not an event is part of The Block (10/2/2013-AV)
  function zIsTheBlock($id) {
	  try {
		  // Connect to the db
		  $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
		  // Prepare the query
		  $query = $pdo->prepare("SELECT `id`
		    FROM `wp_eventdate`
			WHERE `id` = :id
			  AND `eventcategory` LIKE '%Block'
			LIMIT 1");
		  // Bind our variables
		  $query->bindParam(':id', $id);
		  // Execute the query
		  $query->execute();
		  // Check if any results where returned
		  $count = $query->rowCount();
		  // Disconnect from the db
		  $pdo = null;
	  }
	  // Error-catching
	  catch(PDOException $e) {
		  echo 'Is The Block function failed: ' . $e->getMessage() . "\n";
		  echo 'Please try again later. If the problem persists, please contact <a href="mailto:helpdesk@next-it.net?subject=Is The Block Function - West Michigan Symphony Orchestra">Next I.T.</a>';
		  die();
	  }
	  // Return the array
	  return $count;
  }