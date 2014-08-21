<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
class rhc_post_info_shortcode {
	function rhc_post_info_shortcode ($shortcode='post_info'){
		add_shortcode($shortcode, array(&$this,'handle_shortcode'));
	}
	
	function handle_shortcode($atts,$content=null,$code=""){
		extract(shortcode_atts(array(
			'width'		=> '0',
			'columns'	=> false,
			'class'		=> '',
			'post_types'=> false,//if specified (comma separated post types), then the shortcode will only render for those post types. usefull when showing mixed post types archives, and you need the shortcode on some post types only.
			'calendarized_only'=>'0'//use 1 to display the fields only if the post is calendarized.			
		), $atts));

		global $post;
		$out='';
		if(property_exists($post,'ID') && $post->ID>0){
			if( $calendarized_only=='1' ){
				if( ''==trim(get_post_meta($post->ID,'fc_start',true)) ){
					return '';
				}
			}
		
			if( false!==$post_types ){
				$arr = explode(',',str_replace(' ','',$post_types));
				if(is_array($arr)&&count($arr)>0){
					if( !in_array($post->post_type,$arr) ){
						return '';
					}
				}
			}
		
			$out = $this->render($post->ID,intval($width),$class,$columns);
		}
		return $out;
	}
	
	function render($post_ID,$width,$class='',$columns=false){
		if(false===$columns){
			$columns = intval(get_post_meta($post_ID,'extra_info_columns',true));
			$columns = $columns<0?$columns=1:$columns;		
		}
		$separators = intval(get_post_meta($post_ID,'extra_info_separators',true));
		$data = get_post_meta($post_ID,'extra_info_data',true);	
		$out='';
		if(is_array($data) && count($data)>0){
			$style = $width=='0'?'width:100%;':"width:{$width}px;";
			$out .= "<div class=\"fe-extrainfo-holder fe-extrainfo-col{$columns} $class\" style=\"$style\"><table>";
			$cells=array();
			foreach($data as $cell){
				//
				/*
				global $rhc_plugin;
				if(in_array($cell->postmeta,array('fc_start','fc_end'))){
					$cell->date_format = $rhc_plugin->get_option('date_format', get_option('date_format'), true  );
				}else if(in_array($cell->postmeta,array('fc_start_time','fc_end_time'))){
					$cell->date_format = $rhc_plugin->get_option('time_format', get_option('time_format'), true  );
				}else if(in_array($cell->postmeta,array('fc_start_datetime','fc_end_datetime'))){
					$cell->date_format = $rhc_plugin->get_option('datetime_format', get_option('date_format').' '.get_option('time_format'), true  );
				}
				*/
				$c= new rhc_post_info_field( (array)$cell );
				$cells[]=$c->render(true);
				
			}
			
			$rows = ceil((count($cells))/$columns);
			for($a=0;$a<$rows;$a++){
				$out.="<tr>";
				for($b=0;$b<$columns;$b++){
					if($cell = array_shift($cells)){
						$out.=$cell;
					}else{
						$out.="<td colspan=2>&nbsp;</td>";
					}
				}
				$out.="</tr>";
			}
			$out .= "</table></div>";
		}
		return do_shortcode($out);
	}
}

class rhc_post_info_field {
	var $id;
	var $type;
	var $label;
	var $value;
	var $taxonomy;
	var $taxonomy_links;
	var $postmeta;
	var $taxonomymeta;
	var $taxonomymeta_field;
	var $render_cb;
	var $post_ID;
	var $date_format;
	function rhc_post_info_field($args){
		global $rhc_plugin;
		
		$taxonomy_links = $rhc_plugin->get_option('taxonomy_links',false,true);
		$taxonomy_links = $taxonomy_links=='1'?true:false;
		
		foreach(array('id'=>'','type'=> '','label'=>'','value'=>'','taxonomy'=>'','postmeta'=>'','taxonomymeta'=>'','taxonomymeta_field'=>'','post_ID'=>false,'date_format'=>false,'render_cb'=>false,'taxonomy_links'=>$taxonomy_links) as $field => $default){
			$this->$field = isset($args[$field])?$args[$field]:$default;
		}
		
		
		$cell = $this;
		if(in_array($cell->postmeta,array('fc_start','fc_end'))){
			$cell->date_format = $rhc_plugin->get_option('date_format', get_option('date_format'), true  );
		}else if(in_array($cell->postmeta,array('fc_start_time','fc_end_time'))){
			$cell->date_format = $rhc_plugin->get_option('time_format', get_option('time_format'), true  );
		}else if(in_array($cell->postmeta,array('fc_start_datetime','fc_end_datetime'))){
			$cell->date_format = $rhc_plugin->get_option('datetime_format', get_option('date_format').' '.get_option('time_format'), true  );
		}		
		
	}
	function get_template($frontend=false){
		if($frontend)return $this->get_template_frontend();
		ob_start();
?>
<div class="widget rhc-extra-info-cell rhcalendar" rel="{index}">	
	<div class="widget-top">
		<div class="widget-title-action">
			<a href="javascript:void(0);" class="ui-icon ui-icon-closethick"></a>
		</div>
		<div class="widget-title ">
			<h4  class="rhc-extra-info-label">{label}:&nbsp;<span class="rhc-extra-info-value"> {value}</span></h4>	
		</div>
	</div>
</div>
<?php	
		$out = ob_get_contents();
		ob_end_clean();
		
		if($this->id!=''){
			$out = str_replace("{id}",sprintf('id="%s"',$this->id),$out);
		}
		
		return $out;
	}
	
	function get_template_frontend(){
		$out = '<td class="fe-extrainfo-label">{label}</td><td class="fe-extrainfo-value">{value}</td>';
		return $out;	
	}
	
	function render($frontend=false){
		//todo load template
		$output = '';
//		$template = "<div class=\"rhc-extra-info-cell widget\"><label class=\"rhc-extra-info-cell-label\">{label}</label><span class=\"rhc-extra-info-cell-value\">{value}</span></div>";
		$template = $this->get_template($frontend);
		$method = 'render_'.$this->type;
		if(method_exists($this,$method)){
			$output = $this->$method($template);
		}	
		
		return $output;
	}
	
	function template_replace($label,$value,$template,$position=''){
		$out = str_replace('{label}',$label,$template);
		return str_replace('{value}',$value,$out);
	}
	
	function render_custom($template){
		$out = str_replace('{label}',$this->label,$template);
		return str_replace('{value}',$this->filter_value($this->value),$out);
	}
	
	function render_taxonomy($template){
		if(intval($this->post_ID)>0){
			$value='';
			$terms = wp_get_object_terms($this->post_ID,$this->taxonomy);
			if(is_array($terms)&&count($terms)>0){
				$t = array();
				foreach($terms as $term){
					if($this->taxonomy_links){
						$t[] = sprintf("<a href=\"%s\" class=\"rhc-taxonomy-link\">%s</a>",get_term_link( $term, $this->taxonomy ),$term->name);					
					}else{
						$t[] = $term->name;
					}
				}
				$value = implode(", ",$t);
			}
			$out = str_replace('{label}',$this->label,$template);
			return str_replace('{value}',$this->filter_value($value),$out);		
		}else{
			return '';
		}
	}
	
	function render_taxonomymeta($template){
		if(intval($this->post_ID)>0){
			$value='';
			
			$t = array();
			$terms = wp_get_object_terms($this->post_ID,$this->taxonomymeta);
			if(is_array($terms)&&count($terms)>0){
				foreach($terms as $term){
					$v = $this->filter_value( get_term_meta($term->term_id, $this->taxonomymeta_field, true) );
					$t[]=apply_filters( sprintf('rhc_post_info_%s_%s',$this->taxonomymeta,$this->taxonomymeta_field) ,$v);
				}
			}
			$value = implode(", ",$t);
			$out = str_replace('{label}', $this->label, $template);
			return str_replace('{value}', $value, $out);		
		}else{
			return '';
		}
	}
	
	function render_postmeta($template){
		if(intval($this->post_ID)>0){
			$value = get_post_meta($this->post_ID,$this->postmeta,true);
			if(is_string($value)){
				$out = str_replace('{label}',$this->label,$template);
				return str_replace('{value}', $this->filter_value($value),$out);				
			}
		}
		return '';
	}
	
	function render_separator(){
		return '<div class="post_extrainfo_separator"></div>';
	}
	
	function filter_value($value){
		if(!in_array(trim($this->date_format),array('',false))){
			$value = $this->filter_handle_repeat($value);
			$value = date($this->date_format,strtotime($value));
		}
		
		if(false!==$this->render_cb && is_callable($this->render_cb)){
			$value = call_user_func( $this->render_cb, $value, $this );
		}	
		return apply_filters('rhc_post_info_value',$value,$this);
	}
	
	function filter_handle_repeat($value){
		if(isset($_REQUEST['event_rdate'])&&''!=$_REQUEST['event_rdate']){
			$arr = explode(',',$_REQUEST['event_rdate']);
			$event_start = $arr[0];
			$event_end = $arr[1];
			if(in_array($this->postmeta,array('fc_start_datetime','fc_start','fc_start_time'))&&!empty($event_start)){
				$ts = strtotime($event_start);
				switch($this->postmeta){
					case 'fc_start_datetime':
						$value = date('Y-m-d H:i:s',$ts);
						break;
					case 'fc_start':
						$value = date('Y-m-d',$ts);
						break;
					case 'fc_start_time':
						$value = date('H:i:s',$ts);
						break;
	
				}
			}
			if(in_array($this->postmeta,array('fc_end_datetime','fc_end','fc_end_time'))&&!empty($event_end)){
				$ts = strtotime($event_end);
				switch($this->postmeta){
					case 'fc_end_datetime':
						$value = date('Y-m-d H:i:s',$ts);
						break;
					case 'fc_end':
						$value = date('Y-m-d',$ts);
						break;
					case 'fc_end_time':
						$value = date('H:i:s',$ts);
						break;
	
				}
			}
		}
		return $value;
	}
}


?>