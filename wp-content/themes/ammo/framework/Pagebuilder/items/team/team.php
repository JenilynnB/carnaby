<?php

function blox_parse_team_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'image' => '',
                'ratio' => '1x1',
                'link' => '',
                'member_name' => 'Member name',
                'position' => 'CEO / Founder',
                'social' => '',
                'skin' => '',
                'animation' => '',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));


    $is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
    $visibility = str_replace(',', ' ', $visibility);
    $extra_class .= ' '.$visibility;
    
    $prelink = ($link != '') ? '<a href="'.$link.'" target="_blank">' : '';
    $afterlink = ($link != '') ? '</a>' : '';
    
    $links = '';
    if ($social != '') {
        $arr = explode(',', $social);
        $links .= "<ul class='member-social list-inline'>";
        foreach ($arr as $value) {
            $link = trim($value);
            if (strpos($link, 'facebook:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('facebook:', '', $link)) . '" class="facebook" target="_blank"><i class="fa-facebook"></i></a></li>';
            }
            if (strpos($link, 'twitter:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('twitter:', '', $link)) . '" class="twitter" target="_blank"><i class="fa-twitter"></i></a></li>';
            }
            if (strpos($link, 'googleplus:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('googleplus:', '', $link)) . '" class="googleplus" target="_blank"><i class="fa-google-plus"></i></a></li>';
            }
            if (strpos($link, 'email:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('email:', '', $link)) . '" class="email" target="_blank"><i class="fa-envelope"></i></a></li>';
            }
            if (strpos($link, 'pinterest:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('pinterest:', '', $link)) . '" class="pinterest" target="_blank"><i class="fa-pinterest"></i></a></li>';
            }
            if (strpos($link, 'linkedin:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('linkedin:', '', $link)) . '" class="linkedin" target="_blank"><i class="fa-linkedin"></i></a></li>';
            }
            if (strpos($link, 'link:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('link:', '', $link)) . '" class="link" target="_blank"><i class="fa-link"></i></a></li>';
            }
            if (strpos($link, 'youtube:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('youtube:', '', $link)) . '" class="youtube" target="_blank"><i class="fa-youtube"></i></a></li>';
            }
            if (strpos($link, 'dribbble:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('dribbble:', '', $link)) . '" class="dribbble" target="_blank"><i class="fa-dribbble"></i></a></li>';
            }
            if (strpos($link, 'instagram:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('instagram:', '', $link)) . '" class="instagram" target="_blank"><i class="fa-instagram"></i></a></li>';
            }
            if (strpos($link, 'flickr:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('flickr:', '', $link)) . '" class="flickr" target="_blank"><i class="fa-flickr"></i></a></li>';
            }
            if (strpos($link, 'skype:') > -1) {
                $links .= '<li><a href="' . trim(str_replace('skype:', '', $link)) . '" class="skype" target="_blank"><i class="fa-skype"></i></a></li>';
            }
        }
        $links .= "</ul>";
    }
    

    $img_w = 520;
    $img_h = 520;
    $img_h = $ratio=='4x3' ? (int)($img_w*0.75) : $img_h;
    $img_h = $ratio=='4x5' ? (int)($img_w*1.25) : $img_h;
    $img_h = $ratio=='2x3' ? (int)($img_w*1.5) : $img_h;
    $img_h = $ratio=='3x4' ? (int)($img_w*1.333333) : $img_h;

    $result = "<div class='blox-element team-member $is_animate $skin $extra_class' data-animate='$animation'>
                    <div class='member-image'>
                    $prelink
                        <img class='img-responsive' src='" . blox_aq_resize($image, $img_w, $img_h, true) . "' alt='".($member_name!='' ? $member_name : 'Team Member')."' />
                    $afterlink
                    </div>
                    <h3 class='member-name'>$member_name <small>$position</small></h3>
                    <div class='member-content'><p>$content</p></div>
                    $links
                </div>";
    
    return $result;
}

add_shortcode('blox_team', 'blox_parse_team_hook');

?>