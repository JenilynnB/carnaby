<?php

getPageSlider(false);



/* Check Title Enabled
==============================*/
if( tt_getmeta('title_show') != '0' ):

	$title_style = '';
	$title_classes = '';
	
	/* prepare variables
	==============================*/
	$teaser = tt_getmeta('teaser');
	$title_bg_color = tt_getmeta('title_bg_color');
	$title_bg =  get_bg_values( tt_getmeta('title_bg') );
	$title_align = tt_getmeta('title_align');
	$title_dark = tt_getmeta('title_color');
	$title_padding = (int)tt_getmeta('title_padding');
	$title_padding_bottom = (int)tt_getmeta('title_padding_bottom');
	$title_attr = '';

	$title_overlay = '';
	$title_overlay_color = tt_getmeta('title_overlay_color');
	$title_overlay_opacity = tt_getmeta('title_overlay_opacity');

	/* set title background color
	==============================*/
	if( $title_bg_color != '' ){
		$title_style .= 'background-color: '.$title_bg_color.';';
	}

	/* set background image
	==============================*/
	if( $title_bg['url']!='' ){
		$title_style .= 'background-image: url('.$title_bg['url'].');';
		$title_style .= 'background-position: '.$title_bg['position'].';';
		if( $title_bg['repeat']=='cover' ){
			$title_style .= "background-repeat: no-repeat; background-size:cover;";
		}
		else{
			$title_style .= 'background-repeat: '.$title_bg['repeat'].';';
		}
		if( $title_bg['attach']!='parallax' ){
			$title_style .= 'background-attachment: '.$title_bg['attach'].';';
		}
		else{
			$title_attr .= '';
		}
	}
	$title_classes .= ' '.get_parallax_class($title_bg['attach']);
	$title_attr .= get_parallax_attr( array('type'=>$title_bg['attach']) ) .' ';

	/* set text align
	==============================*/
	if( $title_align!='' ){
		$title_style .= "text-align:$title_align;";
	}

	/* set dark text
	==============================*/
	if( $title_dark == '1' ){
		$title_classes .= ' dark';
	}

	/* set padding
	==============================*/
	if( $title_padding > 0 ){
		$title_style .= "padding-top:". $title_padding ."px; padding-bottom:". $title_padding_bottom ."px;";
	}

	if( $title_overlay_color!='' && $title_overlay_opacity!='' && $title_overlay_opacity!='0' ){
		$title_overlay = '<div class="title-overlay" style="background-color:'. blox_hex2rgba( $title_overlay_color, floatval($title_overlay_opacity) ) .';"></div>';
	}

	/* woocommerce category description
	==============================*/
	global $woo_term_desc;
	if( isset($woo_term_desc) && !empty($woo_term_desc) ){
		$teaser = wp_strip_all_tags($woo_term_desc);
	}
	if( function_exists('is_shop') && is_shop() && $woo_term_desc=='' ){
		$teaser = '';
	}

?>
<!-- Start Title Section
================================================== -->
<section id="post-title-<?php echo get_the_ID(); ?>" class="page-title section<?php echo $title_classes; ?>" style="<?php echo $title_style; ?>" <?php echo $title_attr; ?>>
	<?php echo $title_overlay; ?>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div>
					<h1><?php the_title(); ?></h1>
					<?php echo $teaser!='' ? '<p class="lead">'.$teaser.'</p>' : ''; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ================================================== 
End Title -->
<?php
endif;
?>