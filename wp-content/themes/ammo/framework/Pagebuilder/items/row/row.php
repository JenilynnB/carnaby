<?php

function get_blox_column_width_parser($size){
	if( $size == '1/1' ){
		return 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
	}
	else if( $size == '1/5' ){
		return 'col-xs-12 col-sm-12 col-md-3 col-lg-3';
	}
	else if( $size == '1/4' ){
		return 'col-xs-12 col-sm-6 col-md-3 col-lg-3';
	}
	else if( $size == '1/3' ){
		return 'col-xs-12 col-sm-4 col-md-4 col-lg-4';
	}
	else if( $size == '2/5' ){
		return 'col-xs-12 col-sm-6 col-md-6 col-lg-6';
	}
	else if( $size == '1/2' ){
		return 'col-xs-12 col-sm-6 col-md-6 col-lg-6';
	}
	else if( $size == '3/4' ){
		return 'col-xs-12 col-sm-6 col-md-8 col-lg-9';
	}
	else if( $size == '2/3' ){
		return 'col-xs-12 col-sm-8 col-md-8 col-lg-8';
	}
	else if( $size == '4/5' ){
		return 'col-xs-12 col-sm-12 col-md-2 col-lg-2';
	}
	else if( $size == '1/6' ){
		return 'col-xs-12 col-sm-12 col-md-2 col-lg-2';
	}
	else if( $size == '5/6' ){
		return 'col-xs-12 col-sm-12 col-md-8 col-lg-9';
	}
	else{
		return 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
	}
}

function blox_parse_row_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'columns' => '',
		'fullwidth' => '',
		'color' => '',
		'image' => '',
		'bg_repeat' => '',
		'bg_position' => '',
		'bg_attach' => '',
		'text_light' => '',
		'extra_class' => '',
		'video_active' => '',
		'video_m4v' => '',
		'video_webm' => '',
		'poster' => '',
		'no_padding' => '',
		'padding_top' => '',
		'padding_bottom' => '',
		'overlay_color' => '',
		'overlay_opacity' => '',
		'affix' => ''
	), $atts ) );

	$attrs = '';
	if( $image!='' && $fullwidth=='true' ){
		$attrs .= "background-image: url($image); background-position: $bg_position;";
		$attrs .= ( $bg_repeat=='cover' ? "background-size: cover;" : "background-repeat: $bg_repeat;" );
		$attrs .= ( $bg_attach=='parallax' ? "" : "background-attachment: $bg_attach;" );
	}
	
	if( $fullwidth=='true' ){
		if( $color!='' ){ $attrs .= "background-color: $color;"; }
		$extra_class .= ' '.get_parallax_class($bg_attach);
		$extra_class .= $text_light=='1' ? ' light' : '';
	}

	$data_attr = get_parallax_attr( array('type'=>$bg_attach, 'position'=>$bg_position) );

	if( $affix=='1' ){
		$extra_class .= ' affix-element';
		$data_attr .= ' role="complementary" ';
	}

	$no_padding_col = $no_padding=='1' ? 'no-padding-columns' : '';
	$padding_style = '';
	$padding_style .= $padding_top!='' ? 'padding-top:'. (int)$padding_top .'px;' : '';
	$padding_style .= $padding_bottom!='' ? 'padding-bottom:'. (int)$padding_bottom .'px;' : '';

	$overlay_html = '';
	if( $overlay_color!='' && $overlay_opacity!='' ){
		$overlay_html = '<div class="section-overlay" style="background-color:'. blox_hex2rgba( $overlay_color, floatval($overlay_opacity) ) .';"></div>';
	}

	// video background
	if( $fullwidth=='true' && $video_active=='1' ){

		wp_enqueue_style('mediaelement-css', 		get_template_directory_uri().'/assets/plugins/mediaelement/mediaelementplayer.min.css');
		wp_enqueue_script('mediaelement-js',        get_template_directory_uri().'/assets/plugins/mediaelement/mediaelement-and-player.min.js');
		
		$video_bg = '';
		if( $overlay_color!='' ){ $video_bg .= "background-color: ".blox_hex2rgba($overlay_color, floatval($overlay_opacity)).";"; }
		if( $image!='' ){ $video_bg .= "background-image: url($image);"; }
		return '<div class="section-fullwidth section-video-wrapper '.$extra_class.'" style="background-image:url('.$poster.');'. $padding_style .'">
					<div class="section-video hidden-xs hidden-sm" style="background-image:url('.$poster.');">
						<video controls="controls" preload="auto" loop="true" autoplay="true">
							<source type="video/mp4" src="'.$video_m4v.'" />
							<source type="video/webm" src="'.$video_webm.'" />
							<object width="1900" height="1060" type="application/x-shockwave-flash" data="'.get_template_directory_uri().'/assets/plugins/mediaelement/flashmediaelement.swf">
								<param name="movie" value="'.get_template_directory_uri().'/assets/plugins/mediaelement/flashmediaelement.swf" />
								<param name="flashvars" value="controls=true&file='.$video_m4v.'" />
							</object>
						</video>
					</div>
					<div class="section-background" style="'.$video_bg.'"></div>
					<div class="container"><div class="section-normal"><div class="row blox-row '.$no_padding_col.'">'.do_shortcode($content).'</div></div></div>
				</div>';
	}
	else if( $fullwidth=='true' ){
		return '<div class="section-fullwidth '.$extra_class.'" style="'. $padding_style . $attrs .'" '.$data_attr.'>
					'. $overlay_html .'
					<div class="container"><div class="section-normal"><div class="row blox-row '.$no_padding_col.'">'.do_shortcode($content).'</div></div></div>
				</div>';
	}

	return '<div class="section-normal '.$extra_class.'" style="'. $padding_style . $attrs .'"><div class="row blox-row '.$no_padding_col.'">'.do_shortcode($content).'</div></div>';
}
add_shortcode( 'blox_row', 'blox_parse_row_hook' );
add_shortcode( 'blox_row_inner', 'blox_parse_row_hook' );


function blox_parse_column_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'width' => '1/1',
		'bgcolor' => '',
		'text_color' => 'default-text',
		'bgimage' => '',
		'bg_repeat' => '',
		'bg_position' => '',
		'bg_attach' => '',
		'extra_class' => '',
		'padding_top' => '',
		'padding_bottom' => '',
		'padding_left' => '',
		'padding_right' => ''
	), $atts ) );

	$col_adds = '';
	if( $bgcolor!='' || $bgimage!='' ){
		$extra_class = $extra_class!='' ? $extra_class.' ' : $extra_class;
		$extra_class .= 'blox-column-wrapper '.$text_color;

		$style = $bgcolor!="" ? "background-color:$bgcolor;" : "";
		$style .= $bgimage!="" ? "background-image:url($bgimage);" : '';
		$style .= $bg_position!="" ? "background-position:$bg_position;" : '';
		$style .= $bg_attach!="" ? "background-attachment:$bg_attach;" : '';
		$style .= $bg_repeat!="" && $bg_repeat!='cover' ? "background-repeat:$bg_repeat;" : '';
		$style .= $bg_repeat=='cover' ? "background-repeat:left top;background-size:cover;" : '';

		$col_adds = '<div class="blox-column-before" style="'.$style.'"></div>';
	}

	$col_style = '';
	if( !empty($padding_top) || !empty($padding_bottom) || !empty($padding_left) || !empty($padding_right) ){
		$col_style .= !empty($padding_top) ? 'padding-top:'.str_replace('px', '', trim($padding_top)).'px;' : '';
		$col_style .= !empty($padding_bottom) ? 'padding-bottom:'.str_replace('px', '', trim($padding_bottom)).'px;' : '';
		$col_style .= !empty($padding_left) ? 'padding-left:'.str_replace('px', '', trim($padding_left)).'px;' : '';
		$col_style .= !empty($padding_right) ? 'padding-right:'.str_replace('px', '', trim($padding_right)).'px;' : '';
	}


	$extra_class = $extra_class!='' ? ' '.$extra_class : $extra_class;
	
	return '<div class="'.get_blox_column_width_parser($width).$extra_class.'">'.$col_adds.'<div class="blox-column-content" style="'.$col_style.'">'.do_shortcode($content).'</div>'.'</div>';
	//tt_fluid_block
}
add_shortcode( 'blox_column', 'blox_parse_column_hook' );
add_shortcode( 'blox_column_inner', 'blox_parse_column_hook' );








?>