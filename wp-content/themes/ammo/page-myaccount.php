<?php
	get_header();
        global $xoouserultra;
        
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

<section class="primary section">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="content">
                    <div class="account-settings account-settings-container">
                        <?php
                        //cutom message

                        $message_custom = $xoouserultra->get_option('messaging_private_all_users');

                        if($message_custom!="")
                        {
                                echo "<p><div class='uupublic-ultra-info'><p>".$message_custom."</p></div></p>";

                        }

                        ?>

                        <input type="hidden" value="<?php echo $page_id?>" name="page_id" id="page_id" />

                        <div class="box-section">
                            <h3> <?php  _e('Update Password','xoousers');?>  </h3>                     


                            <form method="post" name="uultra-close-account" >
                                <div class="alert alert-danger" id="password_reset_error" style="display:none"></div>
                                <div class="alert alert-success" id="password_reset_success" style="display:none"></div>
                                <p><?php  _e('Type your New Password','xoousers');?></p>
                                             <p><input type="password" name="p1" id="p1" /></p>

                                 <p><?php  _e('Re-type your New Password','xoousers');?></p>
                                             <p><input type="password"  name="p2" id="p2" /></p>

                                <p><input type="button" name="xoouserultra-backenedb-eset-password" id="xoouserultra-backenedb-eset-password" class="xoouserultra-button" value="<?php  _e('RESET PASSWORD','xoousers');?>" /></p>

                                
                            </form>
                        </div>
                        <div class="box-section">

                            <h3> <?php  _e('Update Email','xoousers');?>  </h3> 


                            <form method="post" name="uultra-change-email" >
                                <div class="alert alert-danger" id="email_reset_error" style="display:none"></div>
                                <div class="alert alert-success" id="email_reset_success" style="display:none"></div>
                                <p><?php  _e('Type your New Email','xoousers');?></p>
                                             <p><input type="text" name="email" id="email" value="<?php echo $user_email?>" /></p>

                                <p><input type="button" name="xoouserultra-backenedb-update-email" id="xoouserultra-backenedb-update-email" class="xoouserultra-button" value="<?php  _e('UPDATE EMAIL','xoousers');?>" /></p>

                                
                            </form>


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