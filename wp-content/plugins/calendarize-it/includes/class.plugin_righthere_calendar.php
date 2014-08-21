<?php

class plugin_righthere_calendar {
	var $id;
	var $tdom;
	var $plugin_code;
	var $options_varname;
	var $options;
	var $calendar_ajax;
	var $uid=0;
	var $debug_menu = false;
	function plugin_righthere_calendar($args=array()){
		//------------
		$defaults = array(
			'id'				=> 'rhc',
			'tdom'				=> 'rhc',
			'plugin_code'		=> 'RHC',
			'options_varname'	=> 'rhc_options',
			'options_parameters'=> array(),
			'options_capability'=> 'manage_options',
			'options_panel_version'	=> '2.0.5',
			'post_info_shortcode'=> 'post_info',
			'debug_menu'		=> false
		);
		foreach($defaults as $property => $default){
			$this->$property = isset($args[$property])?$args[$property]:$default;
		}
		//-----------
		$this->options = get_option($this->options_varname);
		$this->options = is_array($this->options)?$this->options:array();
		//-----------
		$plugins_loaded_hook = '1'==$this->get_option('ignore_wordpress_standard',false,true)?'plugins_loaded':'after_setup_theme';		
		add_action($plugins_loaded_hook,array(&$this,'plugins_loaded'));
		add_action('init',array(&$this,'init'));

		//--- taxonomy metadata support based on code by mitcho (Michael Yoshitaka Erlewine), sirzooro
		add_action('init',array(&$this,'taxonomy_metadata_wpdbfix') );
		add_action('switch_blog',array(&$this,'taxonomy_metadata_wpdbfix'));		
		//--------
		if(is_admin()){
			require_once RHC_PATH.'options-panel/load.pop.php';
			rh_register_php('options-panel',RHC_PATH.'options-panel/class.PluginOptionsPanelModule.php', $this->options_panel_version);
		}
		
		add_filter('rhc-ui-theme',array(&$this,'rhc_ui_theme'),10,1);
		//--
		if('1'==$this->get_option('enable_debug',false,true)){
			$this->debug_menu = true;
		}
	}
	
	function rhc_ui_theme($t){
		$t = array_merge($t,array(''=>'no ui-theme','default'	=> 'Default','smoothness'=> 'UI-Smoothness','sunny'		=> 'UI-Sunny'));
		return $t ;	
	}
	
	function taxonomy_metadata_wpdbfix() {
	  global $wpdb;
	  $wpdb->taxonomymeta = "{$wpdb->prefix}taxonomymeta";
	}
	
	function init(){
		global $wp_version;
		//3.5-beta1-22133
		$version = substr($wp_version,0,3);
		
		wp_enqueue_style( 'fullcalendar-theme', RHC_URL.'ui-themes/default/style.css', array(),'1.8.16');
		//wp_register_style( 'calendarize', RHC_URL.'css/calendarize.css', array(),'1.0.0');
		//wp_register_style( 'fullcalendar', RHC_URL.'fullcalendar/fullcalendar/fullcalendar.css', array(),'1.5.2');
		wp_enqueue_style( 'calendarize', RHC_URL.'style.css', array(),'1.0.2.3');
		wp_register_script( 'jquery-easing', RHC_URL.'js/jquery.easing.1.3.js', array('jquery'),'1.3.0');
		wp_register_script( 'rrecur-parser', RHC_URL.'js/rrecur-parser.js', array('jquery'),'1.1.0.2');	
		wp_register_script( 'fullcalendar', RHC_URL.'fullcalendar/fullcalendar/fullcalendar.js', array('jquery','rrecur-parser'),'1.5.3.1');	
		wp_register_script( 'fechahora', RHC_URL.'js/fechahora.js', array('jquery'),'1.0.0');
		wp_register_script( 'fc_dateformat_helper', RHC_URL.'js/fc_dateformat_helper.js', array('fullcalendar'),'1.0.0');
		wp_register_script( 'calendarize-fcviews', RHC_URL.'js/fullcalendar_custom_views.js', array(),'1.1.3.4');	
		wp_register_script( 'calendarize', RHC_URL.'js/calendarize.js', array('jquery','fullcalendar','jquery-ui-draggable','jquery-ui-dialog','jquery-easing','calendarize-fcviews'),'1.1.3.4');
		wp_enqueue_script( 'rhc-upcoming-widget', RHC_URL.'js/widget_upcoming_events.js', array('jquery','fullcalendar'),'1.1.0.6');	
		
		wp_register_script( 'google-api3', 'http://maps.google.com/maps/api/js?sensor=false&libraries=places', array('jquery'),'3.0');
		wp_register_script( 'rhc_gmap3', RHC_URL.'js/rhc_gmap3.js', array('google-api3'), '1.0.0' );
		
		if('1'==$this->get_option('visibility_check','0',true)){
			wp_enqueue_script( 'rhc-visibility-check', RHC_URL.'js/visibility_check.js', array(),'1.0.0');
		}
		
		if(is_admin()){
			//wp_register_style( 'rhc-options', RHC_URL.'css/pop.css', array(),'1.0.0');
			wp_register_style( 'post-meta-boxes', RHC_URL.'css/post_meta_boxes.css', array(),'1.0.0');
			wp_register_style( 'rhc-admin', RHC_URL.'css/admin_rhc.css', array(),'1.0.0');
			//wp_register_style( 'rhc-jquery-ui', RHC_URL.'css/jquery-ui/righthere-calendar/jquery-ui-1.8.14.custom.css', array(),'1.8.14');
			wp_register_style( 'rhc-jquery-ui', RHC_URL.'ui-themes/default/style.css', array(),'1.8.14');
			wp_register_style( 'calendarize-metabox', RHC_URL.'css/calendarize_metabox.css', array(),'1.0.4');
			
			if($version>=3.5){
				wp_register_script( 'rhc-jquery-ui', RHC_URL.'js/jquery-ui-1.9.0.custom.min.js', array('jquery'),'1.9.0');
			}else{
				wp_register_script( 'rhc-jquery-ui', RHC_URL.'js/jquery-ui-1.8.22.custom.min.js', array('jquery'),'1.8.22');
			}
			
			wp_register_script( 'rhc-jquery-ui-timepicker', RHC_URL.'js/jquery-ui-timepicker-addon.js', array('rhc-jquery-ui'),'0.9.5');
			wp_register_script( 'rhc-admin', RHC_URL.'js/admin_rhc.js', array('rhc-jquery-ui-timepicker'),'1.0.0');				
			//wp_register_script( 'calendarize-metabox', RHC_URL.'js/calendarize_metabox.js', array('jquery'),'1.0.1');			
			wp_register_script( 'calendarize-metabox', RHC_URL.'js/calendarize_metabox_rrule.js', array('jquery'),'1.2.1.1');				
				
		}
		//--
		//wp_enqueue_style('fullcalendar');
		wp_enqueue_script('calendarize');
		//wp_enqueue_style('calendarize');
	}
	
	function plugins_loaded(){
		require_once RHC_PATH.'includes/class.ui_themes_for_calendarize_it.php';
		require_once RHC_PATH.'includes/functions.template.php';
		require_once RHC_PATH.'includes/function.generate_calendarize_shortcode.php';
		//frontend
		require_once RHC_PATH.'custom-taxonomy-with-meta/taxonomy-metadata.php';  
		require_once RHC_PATH.'custom-taxonomy-with-meta/taxonomymeta_shortcode.php';

		require_once RHC_PATH.'includes/class.shortcode_calendarize.php';
		new shortcode_calendarize();
		
		require_once RHC_PATH.'includes/class.rhc_post_info_shortcode.php';
		new rhc_post_info_shortcode($this->post_info_shortcode);
		
		require_once RHC_PATH.'includes/class.calendar_ajax.php';
		$this->calendar_ajax = new calendar_ajax();

		//widgets
		require_once RHC_PATH.'includes/class.UpcomingEvents_Widget.php';
		add_action( 'widgets_init', create_function( '', 'register_widget( "UpcomingEvents_Widget" );' ) );
		require_once RHC_PATH.'includes/class.EventsCalendar_Widget.php';
		add_action( 'widgets_init', create_function( '', 'register_widget( "EventsCalendar_Widget" );' ) );
		
		//shortcodes
		require_once RHC_PATH.'shortcodes/venues.php';
		new shortcode_venues(RHC_VENUE);
		require_once RHC_PATH.'shortcodes/organizers.php';
		new shortcode_organizers(RHC_ORGANIZER);
		//apply event tempalte
		//require_once RHC_PATH.'includes/class.frontend_event_content_layout.php';
		//new frontend_event_content_layout();
//
		require_once RHC_PATH.'includes/class.rhc_archive_layout_frontend.php';
		new rhc_archive_layout_frontend();			

		if('version1'==$this->get_option('template_integration','version2',true)){
			require_once RHC_PATH.'includes/class.rhc_template_frontend_old.php';
		}else{
			require_once RHC_PATH.'includes/class.rhc_template_frontend.php';
		}
		new rhc_template_frontend();
		
		require_once RHC_PATH.'includes/class.load_event_template.php';
		new load_event_template();
		
		if(is_admin()){			
//			require_once RHC_PATH.'includes/class.post_type_calendar.php';//
//			new post_type_calendar(RHC_EVENTS,array(
//				'capability'=>'edit_rhcevents'
//			));

//
//			require_once RHC_PATH.'includes/class.rhc_archive_layout_settings.php';
//			new rhc_archive_layout_settings($this->id);				
			
			require_once RHC_PATH.'includes/class.rhc_layout_settings.php';
			new rhc_layout_settings($this->id);	

			require_once RHC_PATH.'includes/class.rhc_post_info_settings.php';
			new rhc_post_info_settings($this->id);	
			
			
			require_once RHC_PATH.'includes/class.rhc_settings.php';
			new rhc_settings($this->id);	
			

			
			require_once RHC_PATH.'includes/class.rhc_tax_settings.php';
			new rhc_tax_settings($this->id);		

			$settings = array(				
				'id'					=> $this->id,
				'plugin_id'				=> $this->id,
				'capability'			=> $this->options_capability,
				'options_varname'		=> $this->options_varname,
				'menu_id'				=> 'rhc-options',
				'page_title'			=> __('Options','rhc'),
				'menu_text'				=> __('Options','rhc'),
				'option_menu_parent'	=> 'edit.php?post_type='.RHC_EVENTS,
				//'option_menu_parent'	=> $this->id,
				'notification'			=> (object)array(
					'plugin_version'=> RHC_VERSION,
					'plugin_code' 	=> 'RHC',
					'message'		=> __('Calendar plugin update %s is available! <a href="%s">Please update now</a>','rch')
				),
				'theme'					=> false,
				'stylesheet'			=> 'rhc-options',
				'option_show_in_metabox'=> true,
				'path'			=> RHC_PATH.'options-panel/',
				'url'			=> RHC_URL.'options-panel/'			
			);
			//require_once RHC_PATH.'options-panel/class.PluginOptionsPanelModule.php';	
			do_action('rh-php-commons');	
			$settings['id'] 		= $this->id;
			$settings['menu_id'] 	= $this->id;
			$settings['menu_text'] 	= __('Options','rhc');
			$settings['import_export'] = false;
			$settings['import_export_options'] =false;
			$settings['registration'] = true;
			new PluginOptionsPanelModule($settings);

			//--------
			//require_once RHC_PATH.'includes/class.rhc_calendar_metabox.php';
			require_once RHC_PATH.'includes/class.rhc_calendar_metabox_rrule.php';
			new rhc_calendar_metabox(RHC_EVENTS,$this->debug_menu);
			$post_types = $this->get_option('post_types',array());
			if(is_array($post_types)&&count($post_types)>0){
				foreach($post_types as $post_type){
					new rhc_calendar_metabox($post_type,$this->debug_menu);
				}
			}
			//---	
			require_once RHC_PATH.'includes/class.rhc_post_info_metabox.php';
			new rhc_post_info_metabox(RHC_EVENTS,'edit_'.RHC_CAPABILITY_TYPE);	
			
			if($this->debug_menu){
				require_once RHC_PATH.'includes/class.debug_calendarize.php';
				new debug_calendarize('edit.php?post_type='.RHC_EVENTS);
			}
			
			//--adds metabox for choosing template. not supported yet.
			//require_once RHC_PATH.'includes/class.rhc_event_template_metabox.php';
			//new rhc_event_template_metabox(RHC_EVENTS,$this->debug_menu);
		}
		
		require_once RHC_PATH.'includes/class.righthere_calendar.php';
		new righthere_calendar(array(
			'show_in_menu'=>true
		));
		
		if('1'==$this->get_option('enable_theme_thumb','0',true)){
			add_action('admin_init',array(&$this,'add_events_featured_image'));	
		}
	}
	
	function add_events_featured_image(){
		add_theme_support( 'post-thumbnails' );
	}
	
	function get_option($name,$default='',$default_if_empty=false){
		$value = isset($this->options[$name])?$this->options[$name]:$default;
		if($default_if_empty){
			$value = ''==$value?$default:$value;
		}
		return $value;
	}	
	
	function get_intervals(){//deprecated
		return array(
					''			=> __('Never(Not a recurring event)','rhc'),
					'1 DAY'		=> __('Every day','rhc'),
					'1 WEEK'	=> __('Every week','rhc'),
					'2 WEEK'	=> __('Every 2 weeks','rhc'),
					'1 MONTH'	=> __('Every month','rhc'),
					'1 YEAR'	=> __('Every year','rhc')
				);
	}	
	
	function get_rrule_freq(){
		return apply_filters('get_rrule_freq',array(
					''							=> __('Never(Not a recurring event)','rhc'),
					/*'FREQ=DAILY;INTERVAL=1;COUNT=1'	=> __('Arbitrary repeat dates','rhc'),*/
					'FREQ=DAILY;INTERVAL=1'	=> __('Every day','rhc'),
					'FREQ=WEEKLY;INTERVAL=1'	=> __('Every week','rhc'),
					'FREQ=WEEKLY;INTERVAL=2'	=> __('Every 2 weeks','rhc'),
					'FREQ=MONTHLY;INTERVAL=1'	=> __('Every month','rhc'),
					'FREQ=YEARLY;INTERVAL=1'	=> __('Every year')
				));
	}
	
	function get_template_path($file=''){
		$path = RHC_PATH.'templates/default/'.$file;
		return apply_filters('rhc_template_path',$path,$file);
	}
	
	function get_settings_path($file=''){
		$path = RHC_PATH.'settings/default/'.$file;
		return apply_filters('rhc_settings_path',$path,$file);
	}
}
?>