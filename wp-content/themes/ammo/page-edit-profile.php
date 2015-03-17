<?php

    get_header();
    if( isset($blank_page) && $blank_page ){  }
    else{
            get_template_part('template', 'header');
    }

    global $xoouserultra;
    $user_id = $current_user->ID;
    
?>

<div class="single normal">

<section class="section profile">
    <div class="container">
	<div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                    
                        <div class="col-md-8" >
                            <div class="edit-form">
                                <div class="edit-form-content">
                                    <?php echo $xoouserultra->userpanel->edit_profile_form();?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center myavatar rounded">
                            <div class="edit-form">
                                <div class="pic" id="uu-backend-avatar-section">
                                    <?php echo $xoouserultra->userpanel->get_user_pic( $user_id, 250, 'avatar', 'rounded', 'dynamic')?>
                                </div>
                                <div class="btnupload">
                                    <a class="btn btn-lg btn-primary" href="#" id="uu-send-private-message" data-id="<?php echo $user_id?>"><span><i class="fa fa-camera"></i></span>&nbsp;&nbsp;<?php echo _e("Update Profile Image", 'xoousers')?></a>
                                </div>
                                <div class="uu-upload-avatar-sect" id="uu-upload-avatar-box">           
                                    <?php echo $xoouserultra->userpanel->avatar_uploader()?>         
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    
<?php get_footer(); ?>
           