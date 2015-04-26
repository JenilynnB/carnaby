<?php
class WPBDP_Admin_Listing_Fields_Metabox {
    private $listing = null;

    public function __construct( &$listing ) {
        $this->listing = $listing;
    }

    public function render() {
        $this->listing_fields();
        $this->listing_images();
    }

    private function listing_fields() {
        $formfields_api = wpbdp_formfields_api();
        $post_values = wpbdp_getv( $_POST, 'listingfields', array() );

        echo wp_nonce_field( plugin_basename( __FILE__ ), 'wpbdp-listing-fields-nonce' );

        echo '<div style="border-bottom: solid 1px #dedede; padding-bottom: 10px;">';
        echo sprintf( '<strong>%s</strong>', _x( 'Listing Fields', 'admin', 'WPBDM' ) );
        echo '<div style="padding-left: 10px;">';
        foreach ($formfields_api->find_fields( array( 'association' => 'meta' ) ) as $field ) {
            $value = isset( $post_values[ $field->get_id() ] ) ? $field->convert_input( $post_values[ $field->get_id() ] ) : $field->value( $this->listing->get_id() );
            echo $field->render( $value, 'admin-submit' );
        }
        echo '</div>';
        echo '</div>';
        echo '<div class="clear"></div>';
    }

    private function listing_images() {
        if ( ! current_user_can( 'edit_posts' ) )
            return;

        $images = $this->listing->get_images( 'ids' );
        $thumbnail_id = $this->listing->get_thumbnail_id();
        $womens_thumb_id = $this->listing->get_thumbnail_id("women");
        $mens_thumb_id = $this->listing->get_thumbnail_id("men");
        $kids_thumb_id = $this->listing->get_thumbnail_id("kids");
        $girls_thumb_id = $this->listing->get_thumbnail_id("girls");
        $boys_thumb_id = $this->listing->get_thumbnail_id("boys");
        $baby_thumb_id = $this->listing->get_thumbnail_id("baby");
        

        // Current images.
        echo '<h4>' . _x( 'Current Images', 'templates', 'WPBDM' ) . '</h4>';
        echo '<span>When manually uploading screenshots, ensure that the file type is "jpg" and the filename does not contain the word "screenshot". Otherwise your file will be overwritten by the uploading service. </span>';
        echo '<div id="no-images-message" style="' . ( $images ? 'display: none;' : '' ) . '">' . _x( 'There are no images currently attached to the listing.', 'templates', 'WPBDM' ) . '</div>';
        echo '<div id="wpbdp-uploaded-images" class="cf">';
        
        //echo "women:".$womens_thumb_id." men: ".$mens_thumb_id." kids: ".$kids_thumb_id." girls: ".$girls_thumb_id." boys: ".$boys_thumb_id." baby: ".$baby_thumb_id;
        $has_women = false;
        $has_men = false;
        $has_kids = false;
        $has_girls = false;
        $has_boys = false;
        $has_baby = false;
        
        //$categories = get_the_terms($this->listing->get_id(), WPBDP_CATEGORY_TAX);
        $categories = get_top_apparel_categories_with_kids($this->listing->get_id());
        foreach($categories as $c){
            if($c->slug=="women"){$has_women = true;}
            if($c->slug=="men"){$has_men = true;}
            if($c->slug=="kids-baby"){$has_kids = true;}
            if($c->slug=="girls"){$has_girls = true;}
            if($c->slug=="boys"){$has_boys = true;}
            if($c->slug=="baby"){$has_baby = true;}
        }

        
        foreach ( $images as $image_id ):
            echo wpbdp_render( 'submit-listing/images-single',
                           array( 'image_id' => $image_id,
                                  'has_women' => $has_women,
                                  'has_men' => $has_men,
                                  'has_kids' => $has_kids,
                                  'has_girls' => $has_girls,
                                  'has_boys' => $has_boys,
                                  'has_baby' => $has_baby,
                                  'is_womens_thumb' => ($womens_thumb_id == $image_id),
                                  'is_mens_thumb' => ($mens_thumb_id == $image_id),
                                  'is_kids_thumb' => ($kids_thumb_id == $image_id),
                                  'is_girls_thumb' => ($girls_thumb_id == $image_id),
                                  'is_boys_thumb' => ($boys_thumb_id == $image_id),
                                  'is_baby_thumb' => ($baby_thumb_id == $image_id),
                                  'is_thumbnail' => ( 1 == count( $images ) || $thumbnail_id == $image_id ) ),
                           false );
        endforeach;
        echo '</div>';

        echo wpbdp_render( 'submit-listing/images-upload-form',
                           array( 'admin' => true, 'listing_id' => $this->listing->get_id() ),
                           false );
    }

    public static function metabox_callback( $post ) {
        $listing = WPBDP_Listing::get( $post->ID );

        if ( ! $listing )
            return '';

        $instance = new self( $listing );
        return $instance->render();
    }
}
