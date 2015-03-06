<?php

get_header();

    global $xoouserultra;

    $user_id = $_GET['uu_username'];
    
    if($user_id==null){
        $user_id = get_current_user_id();
        if($user_id==NULL){
            wp_redirect(site_url('/404'));   
        }
    }
    
    $first_name = $xoouserultra->userpanel->get_user_meta('first_name', $user_id);
    $last_name = $xoouserultra->userpanel->get_user_meta('last_name', $user_id);
    $headline = $xoouserultra->userpanel->get_user_meta('headline', $user_id);
    $location = $xoouserultra->userpanel->get_user_meta('location', $user_id);
    $description = $xoouserultra->userpanel->get_user_meta('description', $user_id);
    $loves = $xoouserultra->userpanel->get_user_meta('loves', $user_id);
    $website = $xoouserultra->userpanel->get_user_meta('user_url', $user_id);
    $facebook = $xoouserultra->userpanel->get_user_meta('facebook', $user_id);
    $instagram = $xoouserultra->userpanel->get_user_meta('instagram', $user_id);
    $pinterest = $xoouserultra->userpanel->get_user_meta('pinterest', $user_id);
    $google = $xoouserultra->userpanel->get_user_meta('google', $user_id);
    $twitter = $xoouserultra->userpanel->get_user_meta('twitter', $user_id);
    
    $user_reviews = BusinessDirectory_RatingsModule::get_reviews_by_user($user_id);
    $num_reviews = sizeof($user_reviews);
    
    $user_favorites = wpfp_get_users_favorites($user_id);
    $num_favorites = is_array($user_favorites)? sizeof($user_favorites):0;
    
    $current_user = wp_get_current_user();
?>

<div class="wpbdp-listing wpbdp-listing-single single normal">

<section class="listing-header section profile">
    <div class="container">
	<div class="content clearfix">
            <div class="row">
                <div class="col-md-12">
                
                    <div class="col-md-8 user-profile-info flex">
                        <div class="">

                                <div class="user-avatar">
                                    <?php 
                                        $avatar_url = $xoouserultra->userpanel->get_user_pic_url( $user_id, "150", 'avatar', 'rounded', 'dynamic')
                                    ?>
                                    <div class="user-avatar-rounded" id="uu-backend-avatar-section" style= 'background-image:url(<?php echo $avatar_url;?>)'>

                                    </div>
                                </div>
                        </div>
                        <div class="">
                                <h2><?php    
                                    echo  $first_name . ' ' . $last_name;
                                    ?>
                                </h2>
                                <div class="user_stats">

                                    <div class="user_stats_reviews">
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
                                    <div class="user_stats_favorites">
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
                    </div>
                    <div class="col-md-4 col-sm-12 pull-right profile-actions">

                        <?php if($current_user->id != $user_id&&  is_user_logged_in()): ?>
                            <div class="profile-action">
                                <a href="" class="" data-toggle="modal" data-target="#sendMessageModal" ><span><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;</span><?php echo _e("Send Message", 'xoousers')?></a>
                            </div>
                        <?php elseif(!is_user_logged_in()): ?>
                            <div class="profile-action">
                                <a href= "" class="" data-toggle="modal" data-target="#registrationModal"><span><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;</span><?php echo _e("Send Message", 'xoousers')?></a>  
                            </div>
                        <?php endif; ?>

                        <?php echo $xoouserultra->mymessage->get_send_form( $user_id);?>


                            <?php if($current_user->id != $user_id&&  is_user_logged_in()): ?>
                                <div class="profile-action">
                                    <a class="" id="uu-send-friend-request" href="#" data-user-id="<?php echo $user_id; ?>" title="Send Friend Request"><span><i class="fa fa fa-users fa-lg"></i>&nbsp;&nbsp;Add Friend</span> </a>
                                </div>
                            <?php elseif(!is_user_logged_in()): ?>
                                <div class="profile-action">
                                    <a href= "" class="" data-toggle="modal" data-target="#registrationModal"><span><i class="fa fa fa-users fa-lg"></i>&nbsp;&nbsp;Add Friend</span></a>  
                                </div>
                            <?php endif; ?>

                    </div>
                </div>
            </div>
            <?php if($headline!=''){ ?>
                    <div class="row">
                        <div class="col-md-12 profile-headline">
                            "<?php echo $headline ?>"
                        </div>
                    </div>
                <?php } ?>
        </div>
    </div>      
</section>
<section class="primary section ">
    <div class="container">
	<div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-8">       
                            <div class="box-section profile-user-reviews">

                                <h3><?php echo $first_name."'s Reviews"." (".$num_reviews.")" ?></h3>

                                <?php 

                                    foreach($user_reviews as $review){
                                        $vars = array();
                                        $vars['rating'] = $review;

                                        $template_path = WPBDP_RATINGS_TEMPLATES_PATH . '/single-review.tpl.php';
                                        echo wpbdp_render_page($template_path, $vars);
                                    }
                                    if(sizeof($user_reviews)==0){
                                        echo "No reviews here yet!";
                                    }

                                ?> 
                            </div>
                            <div class="box-section profile-favorite-listings">
                                <h3><?php echo $first_name;?>'s Favorite Stores (<?php echo $num_favorites;?>)</h3>
                                <?php echo do_shortcode("[wp-favorite-posts user_id='".$user_id."']"); ?>
                            </div>
                        </div>
                        <div class="col-md-4">

                            <div class="profile-sidebar-info">
                                <?php if($description != "" || $location !="" || $website != "" || $hometown !="" || $loves !="" || $facebook != "" ||$instagram != "" || $pinterest !="" || $google != "" || $twitter!="" || $current_user->id==$user_id){ ?>

                                <h3>About Me</h3>
                                <?php }?>
                                <table class="listing-cat-info">
                                    <tbody>
                                        <tr class="listing-category-row">

                                                <?php

                                                    if($current_user->id==$user_id || $description != "" ){?>
                                                        <td>
                                                        <label>My Style</label>
                                                        </td>
                                                <?php    
                                                    }
                                                    if($description != ""){
                                                        echo "<td>".$description."</td>";
                                                    }elseif($current_user->id==$user_id ){
                                                        echo "<td><a href='".site_url('/myaccount/?module=profile')."'>Add this</a></td>";
                                                    }
                                                ?>
                                        </tr>
                                        <tr class="listing-category-row">

                                                <?php
                                                    if($current_user->id==$user_id || $location != "" ){?>
                                                        <td>
                                                            <label>My Location</label>
                                                        </td>
                                                <?php    
                                                    }
                                                    if($location != ""){
                                                        echo "<td>".$location."</td>";
                                                    }elseif($current_user->id==$user_id ){
                                                        echo "<td><a href='".site_url('/myaccount/?module=profile')."'>Add this</a></td>";
                                                    }
                                                ?>

                                        </tr>
                                        <tr class="listing-category-row">

                                                <?php
                                                    if($current_user->id==$user_id || $hometown != "" ){?>
                                                        <td>
                                                        <label>My Hometown</label>
                                                        </td>
                                                <?php    
                                                    }
                                                    if($hometown != ""){
                                                        echo "<td>".$hometown."</td>";
                                                    }elseif($current_user->id==$user_id ){
                                                        echo "<td><a href='".site_url('/myaccount/?module=profile')."'>Add this</a></td>";
                                                    }
                                                ?>

                                        </tr>
                                        <tr class="listing-category-row">


                                                <?php
                                                    if($current_user->id==$user_id || $website != "" ){?>
                                                        <td>
                                                        <label>My Favorite Website</label>
                                                        </td>
                                                <?php    
                                                    }
                                                    if($website != ""){
                                                        echo "<td>".$website."</td>";
                                                    }elseif($current_user->id==$user_id ){
                                                        echo "<td><a href='".site_url('/myaccount/?module=profile')."'>Add this</a></td>";
                                                    }
                                                ?>

                                        </tr>
                                        <tr class="listing-category-row">

                                                <?php
                                                    if($current_user->id==$user_id || $loves != "" ){?>
                                                        <td>
                                                        <label>Things I Love</label>
                                                        </td>
                                                <?php    
                                                    }
                                                    if($loves != ""){
                                                        echo "<td>".$loves."</td>";
                                                    }elseif($current_user->id==$user_id ){
                                                        echo "<td><a href='".site_url('/myaccount/?module=profile')."'>Add this</a></td>";
                                                    }
                                                ?>

                                        </tr>
                                        <tr class="listing-category-row">

                                                <?php
                                                    if($current_user->id==$user_id || $facebook != "" ){?>
                                                        <td>
                                                        <label>Facebook Profile</label>
                                                        </td>
                                                <?php    
                                                    }
                                                    if($facebook != ""){
                                                        echo "<td>".$facebook."</td>";
                                                    }elseif($current_user->id==$user_id ){
                                                        echo "<td><a href='".site_url('/myaccount/?module=profile')."'>Add this</a></td>";
                                                    }
                                                ?>

                                        </tr>
                                        <tr class="listing-category-row">

                                                <?php
                                                    if($current_user->id==$user_id || $instagram != "" ){?>
                                                        <td>
                                                        <label>Instagram</label>
                                                        </td>
                                                <?php    
                                                    }
                                                    if($instagram != ""){
                                                        echo "<td>".$instagram."</td>";
                                                    }elseif($current_user->id==$user_id ){
                                                        echo "<td><a href='".site_url('/myaccount/?module=profile')."'>Add this</a></td>";
                                                    }
                                                ?>

                                        </tr>
                                        <tr class="listing-category-row">

                                                <?php
                                                    if($current_user->id==$user_id || $pinterest != "" ){?>
                                                        <td>
                                                        <label>Pinterest</label>
                                                        </td>
                                                <?php    
                                                    }
                                                    if($pinterest != ""){
                                                        echo "<td>".$pinterest."</td>";
                                                    }elseif($current_user->id==$user_id ){
                                                        echo "<td><a href='".site_url('/myaccount/?module=profile')."'>Add this</a></td>";
                                                    }
                                                ?>

                                        </tr>
                                        <tr class="listing-category-row">

                                                <?php
                                                    if($current_user->id==$user_id || $twitter != "" ){?>
                                                        <td><label>Twitter</label></td>
                                                <?php    
                                                    }
                                                    if($twitter != ""){
                                                        echo "<td>".$twitter."</td>";
                                                    }elseif($current_user->id==$user_id ){
                                                        echo "<td><a href='".site_url('/myaccount/?module=profile')."'>Add this</a></td>";
                                                    }
                                                ?>

                                        </tr>
                                        <tr class="listing-category-row">

                                                <?php
                                                    if($current_user->id==$user_id || $google != "" ){?>
                                                        <td><label>Google+</label></td>
                                                <?php    
                                                    }
                                                    if($google != ""){
                                                        echo "<td>".$google."</td>";
                                                    }elseif($current_user->id==$user_id ){
                                                        echo "<td><a href='".site_url('/myaccount/?module=profile')."'>Add this</a></td>";
                                                    }
                                                ?>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
<!-- End Content
================================================== -->


<?php get_footer(); ?>