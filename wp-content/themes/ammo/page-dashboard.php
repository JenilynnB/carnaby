<?php
	get_header();
?>


<?php
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
?>


<?php
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

$user_id = $current_user->ID;
$user_email = $current_user->user_email;

$user_reviews = BusinessDirectory_RatingsModule::get_reviews_by_user($user_id);
$num_reviews = sizeof($user_reviews);

$user_favorites = wpfp_get_users_favorites($user_id);
$num_favorites = sizeof($user_favorites);

$howmany = 5;


?>

<section class="primary section" id="post-<?php echo get_the_ID(); ?>">
    <div class="container">
	<div class="row">
            <div class="<?php echo $content_class; ?>">
                <div class="row">
                    <div class="content">
                        <div class="col-md-12 single-content">
                            <div class="col-md-4">

                            <div class="myavatar rounded">

                                <div class="pic" id="uu-backend-avatar-section">

                                    <?php echo $xoouserultra->userpanel->get_user_pic( $user_id, "", 'avatar', 'rounded', 'dynamic')?>

                                </div>
                            </div>
                            <h2><?php    
                                echo  $first_name . ' ' . $last_name;
                                ?>
                            </h2>
                            <div class="user_stats">

                                <div class="user_stats_reviews">
                                    <?php echo $num_reviews. " Reviews"; ?>
                                </div>
                                <div class="user_stats_favorites">
                                    <?php echo $num_favorites. " Favorites"; ?>
                                </div>
                            </div>
                            </div>

                            <div class="col-md-8">
                                
                                <?php 
                                // Search Box
                                if( isset($smof_data['search_box']) && $smof_data['search_box'] == 1):
                                    get_search_form();    
                                endif; 
                                
                                echo do_shortcode('[do_widget "Advanced Featured Stores"]');
                                echo do_shortcode('[do_widget "Popular Stores"]');
                                echo do_shortcode('[do_widget ": Recent Posts"]');
                                
                                ?>
                                
                                Featured Stores
                                Latest from Our Blog
                                Some other Stores You Might Like
                                Recent Reviews
                                Popular Stores
                            </div>
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

