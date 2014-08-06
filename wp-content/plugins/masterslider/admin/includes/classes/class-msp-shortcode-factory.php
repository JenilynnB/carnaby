<?php

/**
* 
*/
class MSP_Shortcode_Factory {
	
	public  $parsed_slider_data = array();
	private $post_id;
	private $post_slider_args   = array();

	function __construct() {
		
	}

	public function set_data( $parsed_data ) {
		
		$this->parsed_slider_data = $parsed_data;
	}

	/**
	 * Get generated ms_slider shortcode
	 * 
	 * @return string  [ms_slider] shortcode or empty string on error
	 */
	public function get_ms_slider_shortcode( $the_content = '' ){

		if( ! isset( $this->parsed_slider_data['setting'] ) )
			return '';

		$shortcode_name = 'ms_slider';

		// get the parsed slider setting
		$setting = $this->parsed_slider_data['setting'];

		// create ms_slider shortcode
		$attrs = '';
		foreach ( $setting as $attr => $attr_value ) {
			$attrs .= sprintf( '%s="%s" ', $attr, esc_attr( $attr_value ) );
		}

		// get ms_slides shortcodes(s)
		if( 'post' == $this->parsed_slider_data['setting']['slider_type'] ) {
			$the_content = $this->get_post_slider_ms_slides_shortcode();
		
		} elseif( 'wc-product' == $this->parsed_slider_data['setting']['slider_type'] ) {
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) )
				return __( 'Please install and activate WooCommerce plugin.', MSWP_TEXT_DOMAIN );

			$the_content = $this->get_wc_slider_ms_slides_shortcode();
		
		} else {
			$the_content = $this->get_ms_slides_shortcode();
		}

		return sprintf( '[%1$s %2$s]%3$s%4$s[/%1$s]', $shortcode_name, $attrs, "\n", $the_content );
	}



	public function get_ms_slide_shortcode( $slide ){

		if( ! isset( $slide ) || empty( $slide ) )
			return '';

		$shortcode_name = 'ms_slide';

		// stores shortcode attributes
		$attrs = '';

		foreach ( $slide as $attr => $attr_value ) {
			if( 'layers' == $attr || 'layer_ids' == $attr || 'ishide' == $attr )
				continue;

			if( 'src' == $attr && in_array( $this->parsed_slider_data['setting']['slider_type'], array( "flickr", "facebook", "instagram" ) ) ) {
				$attrs .= sprintf( '%s="%s" ', $attr, '{{image}}' );

			} elseif( 'alt' == $attr && in_array( $this->parsed_slider_data['setting']['slider_type'], array( "flickr", "facebook", "instagram" ) ) ) {
				$attrs .= sprintf( '%s="%s" ', $attr, '{{title}}' );

			} elseif( 'title' == $attr && in_array( $this->parsed_slider_data['setting']['slider_type'], array( "flickr", "facebook", "instagram" ) ) ) {
				$attrs .= sprintf( '%s="%s" ', $attr, '{{title}}' );

			} elseif( 'thumb' == $attr && ! empty( $attr_value ) && in_array( $this->parsed_slider_data['setting']['slider_type'], array( "flickr", "facebook", "instagram" ) ) ) {
				$attrs .= sprintf( '%s="%s" ', $attr, '{{thumb}}' );

			} else {
				$attrs .= sprintf( '%s="%s" ', $attr, esc_attr( $attr_value ) );
			}
		}

		if( 'true' == $this->parsed_slider_data['setting']['crop'] ){
			$attrs .= sprintf( '%s="%s" ', 'crop_width' , esc_attr( $this->parsed_slider_data['setting']['width']  ) );
			$attrs .= sprintf( '%s="%s" ', 'crop_height', esc_attr( $this->parsed_slider_data['setting']['height'] ) );
		}

		// collect all shortcode output
		$the_content = '';

		// generate slide_info shortcode if slideinfo control is added
		if( 'image-gallery' == $this->parsed_slider_data['setting']['template'] || 
		    ( isset( $this->parsed_slider_data['setting']['slideinfo'] ) && 'true' == $this->parsed_slider_data['setting']['slideinfo'] ) 
		   ){
			if( ! empty( $slide['info'] ) )
				$the_content .= $this->get_ms_slide_info_shortcode( $slide['info'] );
			else
				$the_content .= $this->get_ms_slide_info_shortcode( "&nbsp;" );
		}

		$the_content .= $this->get_ms_layers_shortcode( $slide['layers'] );

		return sprintf( '[%1$s %2$s]%4$s%3$s[/%1$s]%4$s', $shortcode_name, $attrs, $the_content, "\n" );
	}



	public function get_ms_slides_shortcode() {

		if( ! isset( $this->parsed_slider_data['slides'] ) )
			return '';

		$slides = $this->parsed_slider_data['slides'];

		$shortcodes = '';

		foreach ( $slides as $slide ) {
			if( 'true' != $slide['ishide'] )
				$shortcodes .= $this->get_ms_slide_shortcode( $slide );
		}

		return $shortcodes;
	}



	public function get_ms_slide_info_shortcode( $the_content = '' ){

		if( empty( $the_content ) )
			return '';

		return sprintf( '[%1$s]%2$s[/%1$s]', 'ms_slide_info', $the_content )."\n";
	}



	public function get_ms_layer_shortcode( $layer ){

		if( ! isset( $layer ) || empty( $layer ) )
			return '';

		$shortcode_name = 'ms_layer';

		$attrs = '';
		foreach ( $layer as $attr => $attr_value ) {
			
			if( 'content' == $attr ) 
				continue;

			if( 'parallax' == $attr && 'off' == $this->parsed_slider_data['setting']['parallax_mode'] )
				continue;

			// users can add {{original-image}} and {{slide-image}} in layer link to link layer to current slide image
			if( 'link' == $attr ){

				if( in_array( $this->parsed_slider_data['setting']['slider_type'], array( 'post', 'wc-product' ) ) ) {
					$attr_value = preg_replace_callback( '/{{[\w-]+}}/', array( $this, 'do_template_tag' ), $attr_value );
				
				} elseif( '{{slide-image-url}}' == $attr_value ){
					$factory = msp_get_parser();
					$slide  = $factory->get_parent_of_layer( $layer['id'] );
					$attr_value = msp_get_the_absolute_media_url( $slide['src_full'] );
				}
			}

			$attrs .= sprintf( '%s="%s" ', $attr, esc_attr( $attr_value ) );
		}

		$content = $layer['content'];
		if( in_array( $this->parsed_slider_data['setting']['slider_type'], array( 'post', 'wc-product' ) ) ) {
			$content = preg_replace_callback( '/{{[\w-]+}}/', array( $this, 'do_template_tag' ), $content );
		}

		return sprintf( '[%1$s %2$s]%4$s%3$s[/%1$s]%4$s', $shortcode_name, $attrs, $content, "\n" );
	}



	public function get_ms_layers_shortcode( $layers ){

		if( ! isset( $layers ) || empty( $layers ) )
			return '';

		$shortcodes = '';

		foreach ( $layers as $layer ) {
			$shortcodes .= $this->get_ms_layer_shortcode( $layer );
		}

		return $shortcodes;
	}



	public function get_post_slider_ms_slides_shortcode() {

		if( ! isset( $this->parsed_slider_data['slides'] ) )
			return '';

		$slides = $this->parsed_slider_data['slides'];


		$query = array();
		
		$query['image_from']     = $this->parsed_slider_data['setting']['ps_image_from'];
		$query['excerpt_length'] = $this->parsed_slider_data['setting']['ps_excerpt_len'];

		if( ! empty( $this->parsed_slider_data['setting']['ps_post_type'] ) )
			$query['post_type'] = $this->parsed_slider_data['setting']['ps_post_type'];
		
		$query['orderby'] = $this->parsed_slider_data['setting']['ps_orderby'];

		$query['order']   = $this->parsed_slider_data['setting']['ps_order'];
		
		$query['posts_per_page'] = $this->parsed_slider_data['setting']['ps_post_count'];

		if( ! empty( $this->parsed_slider_data['setting']['ps_posts_not_in'] ) ) {
			$posts_not_in = explode( ',', $this->parsed_slider_data['setting']['ps_posts_not_in'] );
			$query['post__not_in'] = array_filter( $posts_not_in );
		}
		
		$query['offset'] = $this->parsed_slider_data['setting']['ps_offset'];

		$taxs_data = array();

		if( ! empty( $this->parsed_slider_data['setting']['ps_tax_term_ids'] ) ) {
			$taxs_data = explode( ',', $this->parsed_slider_data['setting']['ps_tax_term_ids'] );
		}

		$tax_query   = array();
		
		$PS = msp_get_post_slider_class();
		$query['tax_query'] = $PS->get_tax_query( $taxs_data );

		$this->post_slider_args = $query;



		$slides_shortcode = '';
		
		$th_wp_query = $PS->get_query_results( $query );


		if( $th_wp_query->have_posts() ):  while ( $th_wp_query->have_posts() ) : $th_wp_query->the_post(); 

			$slide_content = '';
			$attrs 		   = '';
			$this->post_id = $th_wp_query->post->ID;

			// generate slide_info shortcode if slideinfo control is added
			if(  isset( $this->parsed_slider_data['setting']['slideinfo'] ) && 'true' == $this->parsed_slider_data['setting']['slideinfo'] ) {

				if( ! empty( $slides['0']['info'] ) ){
					$slide_info = preg_replace_callback( '/{{[\w-]+}}/', array( $this, 'do_template_tag' ), $slides['0']['info'] );
				} else {
					$slide_info = "&nbsp;";
				}

				$slide_content .= $this->get_ms_slide_info_shortcode( $slide_info );
			}


			if( empty( $this->parsed_slider_data['setting']['ps_slide_bg'] ) ) {
				$the_media = msp_get_auto_post_thumbnail_src( $th_wp_query->post, $query['image_from'] );
			} else {
				$the_media = $this->parsed_slider_data['setting']['ps_slide_bg'];
			}
			$attrs .= sprintf( '%s="%s" ', 'src', esc_attr( $the_media ) );


			if( $this->parsed_slider_data['setting']['ps_link_slide'] ) {
				$attrs .= sprintf( '%s="%s" ', 'link', get_the_permalink( $th_wp_query->post->ID ) );
			}

			$attrs .= sprintf( '%s="%s" ', 'title', get_the_title( $th_wp_query->post->ID ) );
			
			$attrs .= sprintf( '%s="%s" ', 'alt'  , get_the_title( $th_wp_query->post->ID ) );

			$attrs .= sprintf( '%s="%s" ', 'target' , $this->parsed_slider_data['setting']['ps_link_target'] );

			$attrs .= sprintf( '%s="%s" ', 'delay' , $slides['0']['delay'] );


			if( ( 'true' == $this->parsed_slider_data['setting']['thumbs'] ) ){

				if( 'thumbs' == $this->parsed_slider_data['setting']['thumbs_type'] ) {

					if( ! empty( $the_media ) ) {

						// set custom thumb size if slider template is gallery
						if( 'image-gallery' == $this->parsed_slider_data['setting']['template']  )
							$thumb = msp_get_the_resized_image_src( $the_media, 175, 140, true );
						else
							$thumb = msp_get_the_resized_image_src( $the_media, $this->parsed_slider_data['setting']['thumbs_width'], $this->parsed_slider_data['setting']['thumbs_height'], true );

						$thumb = msp_get_the_relative_media_url( $thumb );
						$attrs .= sprintf( '%s="%s" ', 'thumb' , $thumb );

					} else {
						$tab    = '<div class=&quot;ms-thumb-alt&quot;>' . get_the_title( $th_wp_query->post->ID ) . '</div>';
						$attrs .= sprintf( '%s="%s" ', 'tab' , $tab );
					}

				} elseif( 'tabs' == $this->parsed_slider_data['setting']['thumbs_type'] ) {
					$tab    = get_the_title( $th_wp_query->post->ID );
					$attrs .= sprintf( '%s="%s" ', 'tab' , $tab );
				}

			}
			

			$slide_content .= $this->get_ms_layers_shortcode( $slides['0']['layers'] );
			
			$slides_shortcode .= sprintf( '[%1$s %2$s]%4$s%3$s[/%1$s]%4$s', 'ms_slide', $attrs, $slide_content, "\n" );

			endwhile; 
		endif;
		
		return $slides_shortcode;
	}




	public function get_wc_slider_ms_slides_shortcode() {

		if( ! isset( $this->parsed_slider_data['slides'] ) )
			return '';

		$slides = $this->parsed_slider_data['slides'];


		$query = array();
		
		$query['image_from']     = $this->parsed_slider_data['setting']['ps_image_from'];
		$query['excerpt_length'] = $this->parsed_slider_data['setting']['ps_excerpt_len'];

		$query['only_featured'] = $this->parsed_slider_data['setting']['wc_only_featured'];
		$query['only_instock']  = $this->parsed_slider_data['setting']['wc_only_instock'];
		$query['only_onsale']   = $this->parsed_slider_data['setting']['wc_only_onsale'];

		if( ! empty( $this->parsed_slider_data['setting']['ps_post_type'] ) )
			$query['post_type'] = $this->parsed_slider_data['setting']['ps_post_type'];
		
		$query['orderby'] = $this->parsed_slider_data['setting']['ps_orderby'];

		$query['order']   = $this->parsed_slider_data['setting']['ps_order'];
		
		$query['posts_per_page'] = $this->parsed_slider_data['setting']['ps_post_count'];

		if( ! empty( $this->parsed_slider_data['setting']['ps_posts_not_in'] ) ) {
			$posts_not_in = explode( ',', $this->parsed_slider_data['setting']['ps_posts_not_in'] );
			$query['post__not_in'] = array_filter( $posts_not_in );
		}
		
		$query['offset'] = $this->parsed_slider_data['setting']['ps_offset'];

		$taxs_data = array();

		if( ! empty( $this->parsed_slider_data['setting']['ps_tax_term_ids'] ) ) {
			$taxs_data = explode( ',', $this->parsed_slider_data['setting']['ps_tax_term_ids'] );
		}

		$tax_query   = array();
		
		$wcs = msp_get_wc_slider_class();
		$query['tax_query'] = $wcs->get_tax_query( $taxs_data );

		$this->post_slider_args = $query;


		$slides_shortcode = '';
		
		$th_wp_query = $wcs->get_query_results( $query );


		if( $th_wp_query->have_posts() ):  while ( $th_wp_query->have_posts() ) : $th_wp_query->the_post(); 

			$product = get_product( $th_wp_query->post );

			$slide_content = '';
			$attrs 		   = '';
			$this->post_id = $th_wp_query->post->ID;

			// generate slide_info shortcode if slideinfo control is added
			if(  isset( $this->parsed_slider_data['setting']['slideinfo'] ) && 'true' == $this->parsed_slider_data['setting']['slideinfo'] ) {

				if( ! empty( $slides['0']['info'] ) ){
					$slide_info = preg_replace_callback( '/{{[\w-]+}}/', array( $this, 'do_template_tag' ), $slides['0']['info'] );
				} else {
					$slide_info = "&nbsp;";
				}

				$slide_content .= $this->get_ms_slide_info_shortcode( $slide_info );
			}


			if( empty( $this->parsed_slider_data['setting']['ps_slide_bg'] ) ) {
				$the_media = msp_get_auto_post_thumbnail_src( $th_wp_query->post, $query['image_from'] );
			} else {
				$the_media = $this->parsed_slider_data['setting']['ps_slide_bg'];
			}
			$attrs .= sprintf( '%s="%s" ', 'src', esc_attr( $the_media ) );


			if( $this->parsed_slider_data['setting']['ps_link_slide'] ) {
				$attrs .= sprintf( '%s="%s" ', 'link', get_the_permalink( $th_wp_query->post->ID ) );
			}

			$attrs .= sprintf( '%s="%s" ', 'title', get_the_title( $th_wp_query->post->ID ) );
			
			$attrs .= sprintf( '%s="%s" ', 'alt'  , get_the_title( $th_wp_query->post->ID ) );

			$attrs .= sprintf( '%s="%s" ', 'target' , $this->parsed_slider_data['setting']['ps_link_target'] );

			$attrs .= sprintf( '%s="%s" ', 'delay' , $slides['0']['delay'] );
			

			if( ( 'true' == $this->parsed_slider_data['setting']['thumbs'] ) ){

				if( 'thumbs' == $this->parsed_slider_data['setting']['thumbs_type'] ) {

					if( ! empty( $the_media ) ) {

						// set custom thumb size if slider template is gallery
						if( 'image-gallery' == $this->parsed_slider_data['setting']['template']  )
							$thumb = msp_get_the_resized_image_src( $the_media, 175, 140, true );
						else
							$thumb = msp_get_the_resized_image_src( $the_media, $this->parsed_slider_data['setting']['thumbs_width'], $this->parsed_slider_data['setting']['thumbs_height'], true );

						$thumb = msp_get_the_relative_media_url( $thumb );
						$attrs .= sprintf( '%s="%s" ', 'thumb' , $thumb );

					} else {
						$tab    = '<div class=&quot;ms-thumb-alt&quot;>' . get_the_title( $th_wp_query->post->ID ) . '</div>';
						$attrs .= sprintf( '%s="%s" ', 'tab' , $tab );
					}

				} elseif( 'tabs' == $this->parsed_slider_data['setting']['thumbs_type'] ) {
					$tab    = get_the_title( $th_wp_query->post->ID );
					$attrs .= sprintf( '%s="%s" ', 'tab' , $tab );
				}

			}


			$slide_content .= $this->get_ms_layers_shortcode( $slides['0']['layers'] );
			
			$slides_shortcode .= sprintf( '[%1$s %2$s]%4$s%3$s[/%1$s]%4$s', 'ms_slide', $attrs, $slide_content, "\n" );

			endwhile; 
		endif;
		
		return $slides_shortcode;
	}



	public function do_template_tag( $matches ){
		if( ! isset( $matches['0'] ) )
			return $matches;

		$tag_name = preg_replace('/[{}]/', '', $matches['0'] );
		$tag_name = msp_get_template_tag_value( $tag_name, $this->post_id, $this->post_slider_args );
		
		return is_array( $tag_name ) ? implode( ',', $tag_name ) : $tag_name;
	}

}