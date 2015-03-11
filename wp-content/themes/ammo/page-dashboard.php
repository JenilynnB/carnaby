<?php
	get_header();

	global $post, $wp_query;

	/* include header
	============================*/
	if( isset($blank_page) && $blank_page ){  }
	else{
		get_template_part('template', 'header');
	}

	$content_class = 'col-md-12 col-sm-12';
	$layout = tt_getmeta('page_layout');

	if( in_array($layout, array('left', 'right' )) ){
		$content_class = 'col-md-9';
		$content_class .= $layout=='left' ? ' pull-right' : '';
	}

    global $xoouserultra;

    $module = "";
    $act= "";
    $gal_id= "";
    $page_id= "";
    $view= "";
    $reply= "";
    $post_id ="";


    if(isset($_GET["module"])){	$module = $_GET["module"];	}
    if(isset($_GET["act"])){$act = $_GET["act"];	}
    if(isset($_GET["gal_id"])){	$gal_id = $_GET["gal_id"];}
    if(isset($_GET["page_id"])){	$page_id = $_GET["page_id"];}
    if(isset($_GET["view"])){	$view = $_GET["view"];}
    if(isset($_GET["reply"])){	$reply = $_GET["reply"];}
    if(isset($_GET["post_id"])){	$post_id = $_GET["post_id"];}

    $current_user = $xoouserultra->userpanel->get_user_info();
    
    if($current_user->ID==0){
        wp_redirect(site_url('/'));
    }
    $user_id = $current_user->ID;
    $user_email = $current_user->user_email;
    $first_name = $xoouserultra->userpanel->get_user_meta('first_name');
    $last_name = $xoouserultra->userpanel->get_user_meta('last_name');
    $last_initial = $last_name!='' ? substr($last_name, 0, 1).".": '';
    $user_reviews = BusinessDirectory_RatingsModule::get_reviews_by_user($user_id);
    $num_reviews = sizeof($user_reviews);

    $user_favorites = wpfp_get_users_favorites($user_id);
    $num_favorites = sizeof($user_favorites);
    
    global $xoouserultra;
    
    $friends = $xoouserultra->social->get_friends_list($user_id);
    $num_friends = sizeof($friends);
    $howmany = 5;


?>

<section class="section">
    <div class="content dashboard">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class='row'>
                    <div class="col-md-4">

                        <div class='box-section box-section-outline'>
                            <div class='row'>
                                <div class='col-md-5'>
                                    
                                    <div class="user-avatar">
                                        <?php 
                                            $avatar_url = $xoouserultra->userpanel->get_user_pic_url( $user_id, "", 'avatar', 'rounded', 'dynamic');
                                        ?>
                                        <div class="user-avatar-rounded user-avatar-medium" id="uu-backend-avatar-section" style= 'background-image:url(<?php echo $avatar_url;?>)'>

                                        </div>
                                    </div>
                                </div>
                                <div class='col-md-7'>
                                    <div class='dash-user-info'>
                                        <h4><?php    
                                            echo  $first_name . ' ' . $last_initial;
                                            ?>
                                        </h4>
                                        <a href="<?php echo site_url('/profile');?>" class='btn btn-secondary btn-md'>VIEW PROFILE</a>
                                    </div>
                                </div>
                            </div>

                            <div class="user-stats">

                                <div class="user-stats-reviews">
                                    <span>
                                        <i class='fa fa-fw fa-star star-on'></i>
                                    </span>
                                    <?php 
                                    if($num_reviews==1){
                                        echo $num_reviews. " Review";
                                    }else{
                                        echo $num_reviews. " Reviews";
                                    }
                                    ?>
                                </div>
                                <div class="user-stats-favorites">
                                    <span>
                                        <i class='fa fa-fw fa-heart heart-on'></i>
                                    </span>
                                    <?php 
                                    if($num_favorites==1){
                                        echo $num_favorites. " Favorite"; 
                                    }else{
                                        echo $num_favorites. " Favorites"; 
                                    }
                                    ?>
                                </div>
                            </div>
                        </div> 
                        <div class='box-section box-section-outline'>
                            <?php echo $xoouserultra->userpanel->get_user_backend_menu('dashboard');?>
                        </div>
                        <div class='box-section box-section-outline'>
                            <?php echo $xoouserultra->userpanel->get_user_backend_menu('messages');?>
                        </div>
                        <div class='box-section box-section-outline '>
                            <?php echo $xoouserultra->userpanel->get_user_backend_menu('friends');?>     
                        </div>

                    </div>

                    <div class="col-md-8">
                        
                        <?php 
                        if($module=='' || $module=='dashboard'){
                            
                            ?>
                            <div class='box-section'>
                                <?php
                                // Search Box
                                if( isset($smof_data['search_box']) && $smof_data['search_box'] == 1):
                                    get_search_form();    
                                endif; 
                                ?>
                            </div>
                            
                            <!--
                            <div class='box-section'>
                                <h3>Featured Stores</h3>
                                <?php //echo do_shortcode('[do_widget "Advanced Featured Stores"]'); ?>
                            </div>
                            -->
                        
                            <div class='box-section'>
                                <h3>From Our Blog</h3>
                                <?php echo do_shortcode('[do_widget "Carnaby Recent Posts"]'); ?>
                            </div>
                            
                            <div class='box-section'>
                                <h3>Recent Reviews</h3>
                                <?php
                                //Showing 5 most recent reviews from friends in the last 7 days
                                if($num_friends>0){
                                    $reviews = BusinessDirectory_RatingsModule::get_recent_friend_reviews($friends, 7, 5);
                                }else{
                                    $reviews = array();
                                }
                                $num_friend_reviews = sizeof($reviews);
                                //If there are not 5 reviews from friends in the last 7 days, show reviews from the community
                                if($num_friend_reviews<5){
                                    $additional_reviews = BusinessDirectory_RatingsModule::get_recent_reviews(0, 5 - $num_friend_reviews);
                                    foreach($additional_reviews as $ar){
                                        $reviews[] = $ar;
                                    }
                                }

                                foreach($reviews as $review){
                                    $vars = array();
                                    $vars['rating'] = $review;

                                    $template_path = WPBDP_RATINGS_TEMPLATES_PATH . '/single-review.tpl.php';
                                    echo wpbdp_render_page($template_path, $vars);
                                }

                                ?>
                            </div>
                        
                            
                            <div class='box-section'>
                                <h3>Other Stores You Might Like</h3>
                                <?php echo do_shortcode('[do_widget "Other Stores You Might Like"]'); ?>
                            </div>    
                            
                            

                        
                            
                            <div class='box-section'>
                                <h3>Popular Stores</h3>
                                <?php echo do_shortcode('[do_widget "Popular Stores"]'); ?>
                            </div>    

                            
                        <?php

                        }else if($module=='messages'){
                        ?>     
                            
                            <div class="box-section">
                                <h2>My Messages</h2>
                                <?php  

                                if(!$view && !$reply) 
                                {
                                        $xoouserultra->mymessage->show_usersultra_my_messages();

                                }

                                if(isset($view) && $view>0) 
                                {
                                        //display view box
                                        $xoouserultra->mymessage->show_view_my_message_form($view);


                                }

                                ?>
                            </div>

                             

                       <?php
                        }else if($module=='messages_sent'){

                            ?>
                            <div class="box-section">
                                <h2>My Messages</h2>
                                <?php  
                                    $xoouserultra->mymessage->show_usersultra_my_messages_sent();
                                ?>
                            </div>
                                
                            
                        <?php
                        }else if($module=='friends'){
                               
                           ?>
       
                            <div class="commons-panel xoousersultra-shadow-borers" >
                                
                                <div class="commons-panel-heading">
                                    <h2> <?php  _e('My Friends','xoousers');?> </h2>
                                </div>


                                <div class="commons-panel-content" id="uultra-my-friends-request">

                                <?php  _e('loading ...','xoousers');?>          

                                </div>

                                <div class="commons-panel-content" id="uultra-my-friends-list">                        
                                <?php  _e('loading ...','xoousers');?>


                                </div>


                                <script type="text/javascript">
                                    jQuery(document).ready(function($){		
                                        $.post(ajaxurl, {

                                            action: 'show_friend_request'

                                                }, function (response){									

                                                    $("#uultra-my-friends-request").html(response);
                                                    //alert	(response);
                                                    show_all_friends();										
                                                });

                                    });				


                                    function show_all_friends()
                                    {
                                        $.post(ajaxurl, {
                                           action: 'show_all_my_friends'
                                                }, function (response){									
                                                        $("#uultra-my-friends-list").html(response);										
                                            });		
                                    }
                                </script>
                        
                            </div>
              
                        <?php }?>
       
                    </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</section>


<?php
	get_footer();
?>

