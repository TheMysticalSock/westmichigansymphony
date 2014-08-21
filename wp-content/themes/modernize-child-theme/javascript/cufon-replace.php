jQuery(document).ready(function(){
	<?php 
		global $all_font;

		$used_font = substr(get_option(THEME_SHORT_NAME.'_header_font'), 2);
		
		if($used_font != 'default -'){
			if($all_font[$used_font]['type'] == 'Cufon'){
				echo "Cufon.replace(jQuery('h1, h2, h3, h4, h5, h6, .gdl-title').not('.nivo-caption .gdl-title'), {fontFamily: '" . $used_font . "' , hover: true});";
			}
		}
		
		$used_font = substr(get_option(THEME_SHORT_NAME.'_stunning_text_font'), 2);
		
		if($used_font != 'default -'){
			if($all_font[$used_font]['type'] == 'Cufon'){
				echo "Cufon.replace('.stunning-text-title', {fontFamily: '" . $used_font . "'});";
			}
		}
	?>
    
// the following controls the Main Menu Navigation animation to push content down while menu is open
	    var settings = {
	        timeout: 500,
	        over: expandContainer,
	        out: resetContainer
	    };
	    jQuery(document).ready(function () {
	        jQuery("ul.megaMenu").hoverIntent(settings);
	    });
	    // expand the height of the container
	    function expandContainer() {
	        jQuery('div#move-content-down').animate({
	            "height": 320
	        }, 400);
	    }
	    // reset the height of the container
	    function resetContainer() {
	        jQuery('div#move-content-down').animate({
	            "height": 0
	        }, 400);
	    }
        
	});
// the folling is Google Analytics    
    
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-23310544-1']);
	_gaq.push(['_trackPageview']);
	(function () {
	    var ga = document.createElement('script');
	    ga.type = 'text/javascript';
	    ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0];
	    s.parentNode.insertBefore(ga, s);
	})();