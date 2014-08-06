<?php global $smof_data; 
if ( isset($smof_data['footer']) && $smof_data['footer'] == 1) {
    $layout = isset($smof_data['footer_layout']) ? $smof_data['footer_layout'] : 3;
    switch ($layout) {
        case 1:
            $col = 1;
            $percent = array('col-xs-12 col-sm-12 col-md-12 col-lg-12');
            break;
        case 2:
            $col = 2;
            $percent = array(
                'col-xs-12 col-sm-6 col-md-6 col-lg-6',
                'col-xs-12 col-sm-6 col-md-6 col-lg-6');
            break;
        case 3:
            $col = 3;
            $percent = array(
                'col-xs-12 col-sm-12 col-md-6 col-lg-6',
                'col-xs-12 col-sm-6 col-md-3 col-lg-3',
                'col-xs-12 col-sm-6 col-md-3 col-lg-3');
            break;
        case 4:
            $col = 3;
            $percent = array(
                'col-xs-12 col-sm-6 col-md-3 col-lg-3',
                'col-xs-12 col-sm-6 col-md-3 col-lg-3',
                'col-xs-12 col-sm-12 col-md-6 col-lg-6 pull-right');
            break;
        case 5:
            $col = 3;
            $percent = array(
                'col-md-4 col-sm-4',
                'col-md-4 col-sm-4',
                'col-md-4 col-sm-4');
            break;
        case 6:
            $col = 4;
            $percent = array(
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6');
            break;
        default:
            $col = 4;
            $percent = array(
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6',
                'col-md-3 col-sm-6');
            break;
    }

?>
	<!-- Start Footer
	================================================== -->
	<footer id="footer" class="section">
	
		<div class="container">
			<div class="row">
			
                <?php 
                for ($i = 1; $i <= $col; $i++) {
                    echo "<div class='footer_widget_container footer_column_$i ".$percent[$i - 1]."'>";
                    dynamic_sidebar('sidebar_metro_footer' . $i);
                    echo '</div>';
                } ?>

			</div>
		</div>
	
	</footer>
	<!-- ================================================== 
	End Footer -->
<?php } ?>

<?php if (isset($smof_data['sub_footer']) && $smof_data['sub_footer'] == 1) { ?>
	<!-- Start Sub-Footer
	================================================== -->
	<div class="sub-footer">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
                    <div class="top-bar-left">
					   <?php tt_bar_content($smof_data['sub_footer_left'], true); ?>
                    </div>
				</div>
				<div class="col-md-6">
                    <div class="top-bar-right text-right">
					   <?php tt_bar_content($smof_data['sub_footer_right'], true); ?>
                    </div>
				</div>
			</div>
		</div>
	</div>
	<!-- ================================================== 
	End Sub-Footer -->

<?php } ?>
    <?php tt_trackingcode(); ?>

    </div><!-- end wrapper -->

    <span class="gototop">
        <i class="fa fa-angle-up"></i>
    </span>

    <!--[if lt IE 9]>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/plugins/respond.min.js"></script>
    <![endif]-->

    <?php wp_footer(); ?>


</body>
</html>