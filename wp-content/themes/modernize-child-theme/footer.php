		
	</div> <!-- container -->
    <div class="footer-wrapper">
			<?php $gdl_show_footer = get_option(THEME_SHORT_NAME.'_show_footer','enable'); ?>
			
			<?php if( $gdl_show_footer == 'enable' ){ ?>
				<div class="footer-wrapper-gimmick"></div>
			<?php } ?>
			
			
			<!-- Get Footer Widget -->
			<?php if( $gdl_show_footer == 'enable' ){ ?>
				<div class="container mt0">
					<div class="footer-widget-wrapper">
						<?php
							$gdl_footer_class = array(
								'footer-style1'=>array('1'=>'four columns', '2'=>'four columns', '3'=>'four columns', '4'=>'four columns'),
								'footer-style2'=>array('1'=>'eight columns', '2'=>'four columns', '3'=>'four columns', '4'=>'display-none'),
								'footer-style3'=>array('1'=>'four columns', '2'=>'four columns', '3'=>'eight columns', '4'=>'display-none'),
								'footer-style4'=>array('1'=>'one-third column', '2'=>'one-third column', '3'=>'one-third column', '4'=>'display-none'),
								'footer-style5'=>array('1'=>'two-thirds column', '2'=>'one-third column', '3'=>'display-none', '4'=>'display-none'),
								'footer-style6'=>array('1'=>'one-third column', '2'=>'two-thirds column', '3'=>'display-none', '4'=>'display-none'),
								);
							$gdl_footer_style = get_option(THEME_SHORT_NAME.'_footer_style', 'footer-style1');
						 
							for( $i=1 ; $i<=4; $i++ ){
								echo '<div class="' . $gdl_footer_class[$gdl_footer_style][$i] . ' mt0">';
								dynamic_sidebar('Footer ' . $i);
								echo '</div>';
							}
						?>
						<br class="clear">
					</div>
				</div> 
			<?php } ?>
			
			<?php $gdl_show_copyright = get_option(THEME_SHORT_NAME.'_show_copyright','enable'); ?>
			
			<!-- Get Copyright Text -->
			<?php if( $gdl_show_copyright == 'enable' ){ ?>
				<div class="copyright-wrapper">
					<div class="copyright-left">
						<?php echo do_shortcode( __(get_option(THEME_SHORT_NAME.'_copyright_left_area'), 'gdl_front_end') ); ?>
					</div> 
					<div class="copyright-right">
						<?php echo do_shortcode( __(get_option(THEME_SHORT_NAME.'_copyright_right_area'), 'gdl_front_end') ); ?>
					</div> 
					<div class="clear"></div>
				</div>
			<?php } ?>
		</div><!-- footer-wrapper -->
</div> <!-- body-wrapper -->
	
<?php wp_footer(); ?>
	<script type="text/javascript">
	
        /* jQuery('#megaUber li:first').hover(function()
		 {
			 jQuery('#move-content-down').addClass('zheight');
			  //alert('over');
		 },function(){
			 jQuery('#move-content-down').removeClass('zheight');
			 })
    });*/
	jQuery(document).ready(function(e) {
		 jQuery('#megaUber li').hover(function(){
		 		//var getId = 'menu-item-1067';
				if(jQuery(this).hasClass('ss-nav-menu-item-0')){
					jQuery('#move-content-down').addClass('zheight');
					}
				else{
					jQuery('#move-content-down').removeClass('zheight');
					}
			})
    });
		
	</script>
<script type="text/javascript"> 	
	<?php include ("javascript/cufon-replace.php" ); ?>
	</script>
</body>
</html>