<?php
/*
 * Template Name: One Page Template
 */
?>

<?php get_header(); ?>

<?php
	global $smof_data;
	
    $page_links = array();
	$temp_post = $post;
    $temp_query = $wp_query;
	$meta_pages = tt_getmeta('onepages');
	$onepages = explode(',', $meta_pages);
	wp_reset_postdata();
	wp_reset_query();
    $forloop_index = 0;
	foreach ($onepages as $page_id):
		if( $page_id!='' && (int)$page_id > 0 ):
			
			$args = array(
		        'post_type' => 'page',
		        'p' => (int)$page_id
		    );

			$wp_query = new WP_Query( $args );
			while ( $wp_query->have_posts() ):
				$wp_query->the_post();
				
				if( get_post_meta( $post->ID, '_wp_page_template', true )!='page-one-page.php' ){
					$page_links[] = array( 'id'=>get_the_ID(), 'name'=> $post->post_name, 'title'=>get_the_title(), 'link'=>get_permalink() );
		        	$parent_onepage = true;
		            include file_require(dirname(__FILE__).'/template-page.php');
				}
			endwhile;
			wp_reset_postdata();
			wp_reset_query();
	    elseif( $page_id=='0' ):
	        $names = tt_getmeta('onepages_names', $temp_post->ID);
	        $links = tt_getmeta('onepages_links', $temp_post->ID);
	        $arr_name = explode('^', $names);
	        $arr_link = explode('^', $links);
	        $page_links[] = array( 'custom'=>'1', 'title'=>(isset($arr_name[$forloop_index]) ? $arr_name[$forloop_index] : ''), 'link'=>(isset($arr_link[$forloop_index]) ? $arr_link[$forloop_index] : '') );
		endif;
    	$forloop_index++;
	endforeach;
	$post = $temp_post;
	$wp_query = $temp_query;


	/* Hiding main menu if turned on One page menu */
	if( tt_getmeta('onepage_menu') == '1' ){

		echo '<style> #header .navbar-nav{ display:none; } </style>';
		echo '<div id="one_page_menu" style="display:none;">';

		$first = true;

		foreach ($page_links as $tpage) {
			$link = $class = $id = '';
			$class = 'menu-item-';

			if(isset($tpage['custom'])) {
				$class .= 'custom custom';
				$link = $tpage['link'];
			} else {    				
    			$link = '#post-'. $tpage['name'];
    			$id = $tpage['id'];
    			$class .= $id;	    			
			}
			if($first) {$first=false;$class='active '.$class;}
            echo "<li class='$class'>
                    <a href='$link' data-id='". $id ."' data-url='". $tpage['link'] ."' title='". $tpage['title'] ."'>
                        <span class='menu-text'>".$tpage['title']."</span>
                    </a>
                </li>";
        }
        echo '</div>';
	}?>

<div id="onepage-menu">
	<ul>
	<?php
		$first = true;
		foreach ($page_links as $tpage) {
			$class = '';
			if(isset($tpage['custom'])) {continue;}
			if($first) {$first=false;$class='active';}
			echo "<li class='$class'><a href='#post-". $tpage['name'] ."' data-id='". $tpage['id'] ."' data-url='". $tpage['link'] ."' title='". $tpage['title'] ."'><i class='fa-circle'></i></a></li>";
		}
	?>
	</ul>
</div>

<?php get_footer(); ?>