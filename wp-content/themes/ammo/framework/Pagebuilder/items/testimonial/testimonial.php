<?php

function blox_testimonial_hook( $atts, $content=null ) {
    extract( shortcode_atts( array(
        'title' => '',
        'type' => 'single_color',
        'color' => 'transparent',
        'animation' => '',
        'extra_class' => ''
    ), $atts ) );

    $animate_class = get_blox_animate_class($animation);

    if( $type == 'full_color' ){
        return '<div class="blox-element blox-testimonial '.$type.' '.$animate_class. ' '.$extra_class.'">
                    <div class="quote-wrapper">
                        '.do_shortcode($content).'
                    </div>
                </div>';
    }

    return '<div class="blox-element blox-testimonial '.$type.' '.$animate_class.' '.$extra_class.'">
                <div class="quote-wrapper">
                    '.do_shortcode($content).'
                </div>
            </div>';
}
add_shortcode( 'blox_testimonial', 'blox_testimonial_hook' );


function blox_testimonial_item_hook( $atts, $content=null ) {
    extract( shortcode_atts( array(
        'author' => '',
        'position' => '',
        'company' => '',
        'image' => ''
    ), $atts ) );

    $img = $image!='' ? "<span class='quote-image'><img src='".blox_aq_resize($image, 50, 50, true)."' alt='Author'/></span>" : '';
    $noimageclass = $img == '' ? 'no-image-quote' :'';
    return '<div class="quote-item">
                '.$img.'
                <blockquote class="quote-text '.$noimageclass.'"><p>'.do_shortcode($content).'</p><footer>'.$author.' <span class="company">'.$company.'</span> <span class="position">'.$position.'</span></footer></blockquote>
                <div class="clearfix"></div>
            </div>';
}
add_shortcode( 'blox_testimonial_item', 'blox_testimonial_item_hook' );


?>