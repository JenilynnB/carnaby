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
                        
                        <input type="hidden" value="<?php echo $page_id?>" name="page_id" id="page_id" />

                        <?php echo do_shortcode("[usersultra_my_account]");?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
 

<?php
	get_footer();
?>