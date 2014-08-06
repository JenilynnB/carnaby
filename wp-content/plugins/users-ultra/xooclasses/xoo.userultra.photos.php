<?php
class XooUserPhoto {

	function __construct() 
	{
		$this->ini_photo_module();
		
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ));		
		add_action( 'wp_ajax_add_new_gallery',  array( $this, 'add_new_gallery' ));
		add_action( 'wp_ajax_add_new_video',  array( $this, 'add_new_video' ));
		
		
		add_action( 'wp_ajax_reload_galleries',  array( $this, 'reload_galleries' ));
		add_action( 'wp_ajax_reload_photos',  array( $this, 'reload_photos' ));
		add_action( 'wp_ajax_rest_set_uploaded_image', array( $this, 'ajax_upload_images' ));
		add_action( 'wp_ajax_ajax_upload_avatar', array( $this, 'ajax_upload_avatar' ));
		
		add_action( 'wp_ajax_delete_photo', array( $this, 'delete_photo' ));
		add_action( 'wp_ajax_delete_gallery', array( $this, 'delete_gallery' ));
		add_action( 'wp_ajax_delete_video', array( $this, 'delete_video' ));
		
		
		add_action( 'wp_ajax_edit_gallery', array( $this, 'edit_gallery' ));
		add_action( 'wp_ajax_edit_gallery_confirm', array( $this, 'edit_gallery_confirm' ));
		
		add_action( 'wp_ajax_edit_photo', array( $this, 'edit_photo' ));
		add_action( 'wp_ajax_edit_photo_confirm', array( $this, 'edit_photo_confirm' ));
		
		add_action( 'wp_ajax_edit_video_confirm', array( $this, 'edit_video_confirm' ));
		
		
		
		add_action( 'wp_ajax_edit_video', array( $this, 'edit_video' ));
		
		
		
		
		add_action( 'wp_ajax_set_as_main_photo', array( $this, 'set_as_main_photo' ));
		add_action( 'wp_ajax_sort_photo_list', array( $this, 'sort_photo_list' ));
		add_action( 'wp_ajax_sort_gallery_list', array( $this, 'sort_gallery_list' ));
		
		add_action( 'wp_ajax_reload_videos', array( $this, 'reload_videos' ));
		
		
		 add_filter( 'query_vars',   array(&$this, 'userultra_uid_query_var') );
		
		
		
		
		
		
	}
	
	
	
	public function ini_photo_module()
	{
		global $wpdb;

			// Create table
			$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'usersultra_galleries (
				`gallery_id` bigint(20) NOT NULL auto_increment,
				`gallery_user_id` int(11) NOT NULL ,
				`gallery_name` varchar(60) NOT NULL,				
				`gallery_order` int(11) NOT NULL ,	
				`gallery_private` int(1) NOT NULL ,	
				`gallery_only_friends` int(1) NOT NULL ,				
				`gallery_desc` text NOT NULL,			
				PRIMARY KEY (`gallery_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
		// Create table photos
			$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'usersultra_photos (
				`photo_id` bigint(20) NOT NULL auto_increment,
				`photo_gal_id` bigint(20) NOT NULL ,								
				`photo_tags` varchar(200) NULL, 				
				`photo_name` varchar(200) NOT NULL,				
				`photo_desc` varchar(200) NULL, 				
				`photo_mini` varchar(200) NOT NULL,
				`photo_thumb` varchar(200) NOT NULL,
				`photo_large` varchar(200) NOT NULL,
				`photo_main` int(1) NOT NULL,	
				`photo_order` int(11) NOT NULL,			
				PRIMARY KEY (`photo_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
		//udpate table
		$this->update_photos_table();
		
		// Create phot gategories table
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'usersultra_photo_categories (
				`photo_cat_id` bigint(20) NOT NULL auto_increment,						
				`photo_cat_name` varchar(100) NOT NULL,					
				PRIMARY KEY (`photo_cat_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
		// Create photo rel
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'usersultra_photo_cat_rel (
				`photo_rel_id` bigint(20) NOT NULL auto_increment,
				`photo_rel_cat_id` bigint(20) NOT NULL ,						
				`photo_rel_photo_id` bigint(20) NOT NULL,
				PRIMARY KEY (`photo_rel_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
		
		// Create table videos
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'usersultra_videos (
				`video_id` bigint(20) NOT NULL auto_increment,
				`video_user_id` bigint(20) NOT NULL ,				
				`video_name` varchar(200) NOT NULL,
				`video_type` varchar(50) NOT NULL,
				`video_unique_vid` varchar(50) NOT NULL,
				`video_order` int(11) NOT NULL,						
				PRIMARY KEY (`video_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
		
		
	}
	
	function update_photos_table()
	{
		global $wpdb;
		
		//2014-03-08 added fields photo_category, photo_desc, photo_tags
				
		$sql ='SHOW columns from ' . $wpdb->prefix . 'usersultra_photos where field="photo_desc" ';		
		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	//photo_desc
			$sql = 'Alter table  ' . $wpdb->prefix . 'usersultra_photos add column photo_desc varchar (200) ; ';
			$wpdb->query($sql);
		}
		
		$sql ='SHOW columns from ' . $wpdb->prefix . 'usersultra_photos where field="photo_tags" ';		
		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	//photo_tags
			$sql = 'Alter table  ' . $wpdb->prefix . 'usersultra_photos add column photo_tags varchar (200) ; ';
			$wpdb->query($sql);
		}
		
		/*Tnhis has been added*/		
		$sql ='SHOW columns from ' . $wpdb->prefix . 'usersultra_photo_cat_rel where field="photo_rel_id" ';		
		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	//photo_gategories
			$sql = 'Alter table  ' . $wpdb->prefix . 'usersultra_photo_cat_rel add column photo_rel_id bigint(20) NOT NULL AUTO_INCREMENT,  ADD PRIMARY KEY (photo_rel_id) ; ';
			$wpdb->query($sql);
			
		}
	
		
		
	}
	
	public function userultra_uid_query_var( $query_vars )
	{
		$query_vars[] = 'uu_photokeyword';
		$query_vars[] = 'searchphoto';
		return $query_vars;
	}
	
	function add_styles()
	{
	
		wp_enqueue_script( 'jquery-ui-sortable' );
		
	}
	
	
	
	/*Add New Video*/
	public function add_new_video ()
	{
		global $wpdb;
		
		$user_id = get_current_user_id();
		$video_name = sanitize_text_field($_POST['video_name']); 
		$video_id= sanitize_text_field($_POST['video_id']);
		$video_type= sanitize_text_field($_POST['video_type']);
		
		if (is_user_logged_in() && isset($user_id) && isset($video_name)) 
		{
			
			
			$new_message = array(
						'video_id'        => NULL,
						'video_user_id'   => $user_id,
						
						'video_name'   => $video_name,
						'video_unique_vid'   => $video_id,
						'video_type'   => $video_type
						
					);
					// insert into database
					if ( $wpdb->insert( $wpdb->prefix . 'usersultra_videos', $new_message, array( '%d', '%s', '%s', '%s' , '%s' ) ) )
					{
					
					
					}
			
		} //end user loged in
		
	}
	/*Add New Gallery*/
	public function add_new_gallery ()
	{
		global $wpdb;
		
		$user_id = get_current_user_id();
		$gall_name = sanitize_text_field($_POST['gall_name']); 
		$gall_description= sanitize_text_field($_POST['gall_desc']);
		
		if (is_user_logged_in() && isset($user_id) && isset($gall_name)) 
		{
			
			
			$new_message = array(
						'gallery_id'        => NULL,
						'gallery_user_id'   => $user_id,
						
						'gallery_name'   => $gall_name,
						'gallery_desc'   => $gall_description
						
					);
					// insert into database
					if ( $wpdb->insert( $wpdb->prefix . 'usersultra_galleries', $new_message, array( '%d', '%s', '%s', '%s' ) ) )
					{
					
					
					}
			
		} //end user loged in
		
	}
	
	/*Photo Grid*/
	public function show_photo_grid($atts)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
		extract( shortcode_atts( array(
		
			'photo_type' => 'photo_large', //photo_thumb
			'searcher_type' => 'tags', //tags,advanced							
			
			'display_photo_rating' => 'yes',
			'display_photo_tags' => 'yes', 	
			'display_photo_excerpt' => 'yes',
			'display_photo_categories' => 'yes', 
			'display_photo_name' => 'yes', 
			'photos_per_page' => 8, 			
			'photo_border' => 'none', //rounded, none
			'photo_width' => '',
			'photo_open_type' => 'single_page', //lightbox, single_page
			'box_border' => 'nonborder', //rounded, nonborder
			'box_shadow' => '', //shadow
			'box_width' => '20%',
			'display' => 'in-line',
			
		), $atts ) );
		
		$from = "";
		$to = $photos_per_page;
		
		$page = 1;
		
		//box width
		if($box_width!="")
		{
			$b_width = "style='width:".$box_width."'";
		
		}		
		//photo width
		if($photo_width!="")
		{
			$photo_width = "style='width:".$photo_width."'";
		
		}
		
		$key = "";		
		if(isset($_GET["usersultra_searchphoto"]))
		{
			$key = $_GET["usersultra_searchphoto"];
		}
		
		$page = "";		
		if(isset($_GET["ultra-page"]))
		{
			$page = $_GET["ultra-page"];
		
		}else{
			
			$page = 1;	
		
		}
		
		$cat_list = "";		
		if(isset($_GET["uultra_cat_search"]))
		{
			$cat_list = $this->get_select_cat_list($_GET["uultra_cat_search"] );
		
		}
				
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder');
		
		$total_items = $this->get_photos_searching_total($key, $cat_list ); 
		
		$totalPages = ceil($total_items/$photos_per_page);
				
		$html="";
		
		//calculate from to values
		
		if($from>$totalPages)
		{
			$from = $totalPages;	
		}
		
		if($page == ""){$from = 0;}else{$from = $page;}
		
		if($from<= 1) 
		{
			$from =0;
		}else{
			
			if(($photos_per_page * $page)-$photos_per_page>= $total_items) 
			{
				$from = $totalPages-$photos_per_page;
				
			}else{
				
				$from = ($photos_per_page * $page)-$photos_per_page;
			}
			
			
		}

				
		$rows = $this->get_photos_searching($key, $cat_list , $from, $to);	
		
	
		$html.='<div class="uultra-photogrid-photos">
			
			<ul>';
			
					
		$html.='<div class="usersultra-searcher">
    
         <form method="get" action="">';
         
        $html.=' <input type="text" name="usersultra_searchphoto" id="usersultra_searchphoto" value="'. get_query_var('searchphoto').'" placeholder="'. __('Search images...','xoousers').'" />';
		
		//adanced
		
		
		 
        
		$html.=' <button type="submit" class="" title="'. __('Search','xoousers').'">'.__('Search','xoousers').'</button>';
		
		
         if($searcher_type=="advanced")
		{	
			
			//get categories			
			 $html.='<p> '. __('Filter by category: ','xoousers').''.$this->get_photo_category_check().'</p>';
		
		
		}
         
        $html.='   </form></div>';
		
		$html .=' <div class="usersultra-paginate top_display">'.$this->get_pages($total_items,$page, $photos_per_page).'</div>';
		
		$html .="<h1>".__('Total found: ','xoousers'). $total_items. "</h1>";
		   
		if ( empty( $rows ) )
		{
		
		
		}else{
			
			
                    
			
			foreach ( $rows as $photo )
			{
				
				//get thumbnail
				$user_id =$photo->gallery_user_id;
				
				if($photo_type=="photo_mini")
				{
					$file=$photo->photo_mini;			
				
				}elseif($photo_type=="photo_thumb"){
					
					$file=$photo->photo_thumb;
				
				}elseif($photo_type=="photo_large"){
					
					$file=$photo->photo_large;
					
				}
				
				if($photo_open_type=="lightbox")
				{
					$ddbox = 'data-lightbox="example-1"';	
					$photo_link = 	 $site_url.$upload_folder."/".$user_id."/".$photo->photo_large;		
				
			
				}else{
					
					$ddbox = '';	
					
					$photo_link = $xoouserultra->userpanel->public_profile_get_photo_link($photo->photo_id, $user_id);
					
				
				}
				
				
					
				$thumb = $site_url.$upload_folder."/".$user_id."/".$file;
					
								
				$html.= "<li id='".$photo->photo_id."' class='".$box_border." ".$box_shadow." ".$display."'  ".$b_width." >
										
				<a href='".$photo_link."' class='' ".$ddbox." title='".$photo->photo_name."'><img src='".$thumb."' class='".$photo_border."' ".$photo_width."/> </a>";
				
				if($display_photo_name == "yes")	
				{
					$html.= "<p>".$photo->photo_name."</p>";
					
				}
				
				if($display_photo_tags == "yes")	
				{
					$html.= "<p>".$photo->photo_tags."</p>";					
				}
					
				if($display_photo_rating == "yes")	
				{
					$html.= "<div class='ratebox'>";
					$html.= $xoouserultra->rating->get_rating($photo->photo_id,"photo_id");
					$html.= "</div>";
					
				}
									
					$html.= "</li>";	
							
			
			}
			
			$html.='</ul></div>';
			
		}
		
		return $html;
	
	
	}
	
	public function get_select_cat_list ($cat_list_array) 
	{
		$cat_list = "";
		
		if(isset($cat_list_array))
		{
			$categories =$cat_list_array;
			$catcount = count($categories);
			$i = 0;
			
			foreach ( $categories as $cat )
			{
				$cat_list .=$cat;
				
				if($i<$catcount-1)$cat_list .=",";
				
				$i++;				
			
			}
		}
		
		return $cat_list;
	
	}
	
	public function get_photo_category_check () 
	{
		global $wpdb, $xoouserultra;
		
		$rows = $this->get_photo_categories();
		
		$html = "";
		
		if ( !empty( $rows ) )
		{
			foreach ( $rows as $cate )
			{
				$html .= '<label><input type="checkbox" name="uultra_cat_search[]" value="'.$cate->photo_cat_id.'" id="uultra_cat_search_'.$cate->photo_cat_id.'" />'.$cate->photo_cat_name.'</label>';			
			
			}
		
				
	    }
		
		
		return $html ;	
	
	}
	
	public function get_photo_categories () 
	{
		global $wpdb, $xoouserultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . 'usersultra_photo_categories ORDER BY photo_cat_name ASC  ' ;
		$res = $wpdb->get_results($sql);
		return $res ;	
	
	}
	
	
	public function get_photos_searching ($key, $cat_list , $from, $to) 
	{
		global $wpdb, $xoouserultra;
		
		$sql = 'SELECT photo.*, u.ID, gal.gallery_id,  gal.gallery_user_id,
		 rate.ajaxrating_votesummary_photo_id,
		  rate.ajaxrating_votesummary_total_score ';	  
		  
		if($cat_list!="")
		{
			$sql .= ", catrel.* ";	
		} 
		  
		$sql .= ' FROM ' . $wpdb->prefix . 'usersultra_photos photo  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_galleries gal ON (gal.gallery_id = photo.photo_gal_id)";		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users u ON (u.ID = gal.gallery_user_id)";
		
		if($cat_list!="")
		{
			$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_photo_cat_rel catrel ON (catrel.photo_rel_photo_id = photo.photo_id)";	
		}
		
		$sql .= " LEFT JOIN ".$wpdb->prefix ."usersultra_ajaxrating_votesummary rate ON (rate.ajaxrating_votesummary_photo_id = photo.photo_id)";
				
		$sql .= " WHERE gal.gallery_id = photo.photo_gal_id AND  u.ID = gal.gallery_user_id ";	
		
		if($key!="")
		{
			$sql .= " AND  photo.photo_tags LIKE '%".$key."%' ";		
		}
		
		
		if($cat_list!="")
		{
			$sql .= " AND  catrel.photo_rel_cat_id IN (".$cat_list.") ";		
		}
		
		$sql .= " GROUP BY photo.photo_id ";
		
		$sql .= " ORDER BY rate.ajaxrating_votesummary_total_score DESC  ";
		
		if($from != "" && $to != ""){	$sql .= " LIMIT $from,$to"; }
	 	if($from == 0 && $to != ""){	$sql .= " LIMIT $from,$to"; }
		
		//echo $sql;
		
		$res = $wpdb->get_results($sql);
		
	
		return $res;
	
	}
	
	public function get_photos_searching_total ($key, $cat_list ) 
	{
		global $wpdb, $xoouserultra;
		
		$sql = 'SELECT count(*) as total, photo.*, u.ID, gal.gallery_id,  gal.gallery_user_id,
		 rate.ajaxrating_votesummary_photo_id,
		  rate.ajaxrating_votesummary_total_score ';	  
		  
		if($cat_list!="")
		{
			$sql .= ", catrel.* ";	
		} 
		  
		$sql .= ' FROM ' . $wpdb->prefix . 'usersultra_photos photo  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_galleries gal ON (gal.gallery_id = photo.photo_gal_id)";		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users u ON (u.ID = gal.gallery_user_id)";
		
		if($cat_list!="")
		{
			$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_photo_cat_rel catrel ON (catrel.photo_rel_photo_id = photo.photo_id)";	
		}
		
		$sql .= " LEFT JOIN ".$wpdb->prefix ."usersultra_ajaxrating_votesummary rate ON (rate.ajaxrating_votesummary_photo_id = photo.photo_id)";
				
		$sql .= " WHERE gal.gallery_id = photo.photo_gal_id AND  u.ID = gal.gallery_user_id ";	
		
		if($key!="")
		{
			$sql .= " AND  photo.photo_tags LIKE '%".$key."%' ";		
		}
		
		
		if($cat_list!="")
		{
			$sql .= " AND  catrel.photo_rel_cat_id IN (".$cat_list.") ";		
		}
		
		//$sql .= " GROUP BY photo.photo_id ";
				
		$rows = $wpdb->get_results($sql);
		
		if ( !empty( $rows ) )
		{
			foreach ( $rows as $photo )
			{
				$total = $photo->total;			
			
			}
		
		}else{
			
			$total = 0;	
			
	    }
			
		return $total;
	
	}
	
	public function get_pages($photo_count,$page, $list_perpage)
	{
			
		//calculates pages
		//$photo_count = $user_count_query->get_results();
		
		$total_pages = ceil($photo_count / $list_perpage);
		
		
		$big = 999999999; // need an unlikely integer
		$arr = paginate_links( array(
					'base'         => @add_query_arg('ultra-page','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('Previous','xoousers'),
					'next_text'    => __('Next','xoousers'),
					'type'         => 'plain',
				));
	return $arr;
	
	}
	
	/*Top Rated Photos*/
	public function show_top_rated_photos($atts)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
		extract( shortcode_atts( array(
		
			'photo_type' => 'photo_thumb', //photo_thumb
			'size' => '', 					
			'howmany' => '6', // how many pictures to display
			'display_photo_rating' => 'yes', 			
			'photo_border' => 'rounded',
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',
			
		), $atts ) );
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder'); 
				
		$html="";
		
		$sql = ' SELECT photo.*, u.ID, gal.gallery_id,  gal.gallery_user_id,
		 rate.ajaxrating_votesummary_photo_id,
		  rate.ajaxrating_votesummary_total_score 
		  
		  FROM ' . $wpdb->prefix . 'usersultra_photos photo  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_galleries gal ON (gal.gallery_id = photo.photo_gal_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users u ON (u.ID = gal.gallery_user_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_ajaxrating_votesummary rate ON (rate.ajaxrating_votesummary_photo_id = photo.photo_id)";
				
		$sql .= " WHERE gal.gallery_id = photo.photo_gal_id AND rate.ajaxrating_votesummary_photo_id = photo.photo_id AND u.ID = gal.gallery_user_id ORDER BY rate.ajaxrating_votesummary_total_score DESC  LIMIT $howmany";	
			
		$rows = $wpdb->get_results($sql);
		
		
		if ( empty( $rows ) )
		{
		
		
		}else{
			
			$html.='<div class="uultra-toprated-photos">
			
			<ul>';
			
			
			foreach ( $rows as $photo )
			{
				
				//get thumbnail
				$user_id =$photo->gallery_user_id;
				
				if($photo_type=="photo_mini")
				{
					$file=$photo->photo_mini;			
				
				}elseif($photo_type=="photo_thumb"){
					
					$file=$photo->photo_thumb;
				
				}elseif($photo_type=="photo_large"){
					
					$file=$photo->photo_large;
					
				}
					
				$thumb = $site_url.$upload_folder."/".$user_id."/".$file;
					
								
				$html.= "<li id='".$photo->photo_id."' class='".$box_border." ".$box_shadow." ".$display."' >
										
				<a href='".$xoouserultra->userpanel->public_profile_get_photo_link($photo->photo_id, $user_id)."' class='' ><img src='".$thumb."' class='rounded'/> </a>";
					
				if($display_photo_rating == "yes")	
				{
						
						$html.= "<div class='ratebox'>";
						$html.= $xoouserultra->rating->get_rating($photo->photo_id,"photo_id");
						$html.= "</div>";
					
				}					
					
					$html.= "</li>";	
							
			
			}
			
			$html.='</ul></div>';
			
		}
		
		return $html;
	
	
	}
	
	public function get_user_photos($user_id,  $howmany)
	{
		
		global $wpdb, $xoouserultra;
		
		$sql = ' SELECT photo.*, u.ID, gal.gallery_id,  gal.gallery_user_id		
		  
		  FROM ' . $wpdb->prefix . 'usersultra_photos photo  ' ;
		  		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_galleries gal ON (gal.gallery_id = photo.photo_gal_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users u ON (u.ID = gal.gallery_user_id)";		
						
		$sql .= " WHERE gal.gallery_id = photo.photo_gal_id AND u.ID = gal.gallery_user_id AND u.ID= '".$user_id."' ORDER BY photo.photo_order ASC  LIMIT $howmany";	
			
		
		$rows = $wpdb->get_results($sql);
		
		
		
		return $rows ;
	
	
	}
	
	/*Latest Photos Private*/
	public function show_latest_photos_private($howmany)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
		$logged_user_id = get_current_user_id();
		
		
		$photo_type= 'photo_thumb';
		$display_photo_rating= 'yes';
				
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder'); 
				
		$html="";
		
		$sql = ' SELECT photo.*, u.ID, gal.gallery_id,  gal.gallery_user_id
		
		  
		  FROM ' . $wpdb->prefix . 'usersultra_photos photo  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_galleries gal ON (gal.gallery_id = photo.photo_gal_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users u ON (u.ID = gal.gallery_user_id)";
		
						
		$sql .= " WHERE gal.gallery_id = photo.photo_gal_id AND u.ID = gal.gallery_user_id AND u.ID= ".$logged_user_id." ORDER BY photo.photo_id DESC  LIMIT $howmany";	
		
		//echo $sql;
			
		$rows = $wpdb->get_results($sql);
		
		
		if ( empty( $rows ) )
		{
		
		
		}else{
			
			$html.='<div class="uultra-latest-photos-private">
			
			<ul>';
			
			
			foreach ( $rows as $photo )
			{
				
				//get thumbnail
				$user_id =$photo->gallery_user_id;
				
				if($photo_type=="photo_mini")
				{
					$file=$photo->photo_mini;			
				
				}elseif($photo_type=="photo_thumb"){
					
					$file=$photo->photo_thumb;
				
				}elseif($photo_type=="photo_large"){
					
					$file=$photo->photo_large;
					
				}
					
				$thumb = $site_url.$upload_folder."/".$user_id."/".$file;
					
								
				$html.= "<li id='".$photo->photo_id."' class='".$box_border." ".$box_shadow." ".$display."' >
										
				<a href='".$xoouserultra->userpanel->public_profile_get_photo_link($photo->photo_id, $user_id)."' class='' ><img src='".$thumb."' class='rounded'/> </a>";
					
				if($display_photo_rating == "yes")	
				{
						
						//$html.= "<div class='ratebox'>";
						//$html.= $xoouserultra->rating->get_rating($photo->photo_id,"photo_id");
						//$html.= "</div>";
					
				}					
					
					$html.= "</li>";	
							
			
			}
			
			$html.='</ul></div>';
			
		}
		
		return $html;
	
	
	}
	
	/*Latest Photos*/
	public function show_latest_photos($atts)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
		extract( shortcode_atts( array(
		
			'photo_type' => 'photo_thumb', //photo_thumb
			'size' => '', 					
			'howmany' => '4', // how many pictures to display
			'display_photo_rating' => 'yes', 			
			'photo_border' => 'rounded',
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',
			
		), $atts ) );
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder'); 
				
		$html="";
		
		$sql = ' SELECT photo.*, u.ID, gal.gallery_id,  gal.gallery_user_id
		
		  
		  FROM ' . $wpdb->prefix . 'usersultra_photos photo  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_galleries gal ON (gal.gallery_id = photo.photo_gal_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users u ON (u.ID = gal.gallery_user_id)";
		
						
		$sql .= " WHERE gal.gallery_id = photo.photo_gal_id AND u.ID = gal.gallery_user_id ORDER BY photo.photo_id DESC  LIMIT $howmany";	
			
		$rows = $wpdb->get_results($sql);
		
		
		if ( empty( $rows ) )
		{
		
		
		}else{
			
			$html.='<div class="uultra-latest-photos">
			
			<ul>';
			
			
			foreach ( $rows as $photo )
			{
				
				//get thumbnail
				$user_id =$photo->gallery_user_id;
				
				if($photo_type=="photo_mini")
				{
					$file=$photo->photo_mini;			
				
				}elseif($photo_type=="photo_thumb"){
					
					$file=$photo->photo_thumb;
				
				}elseif($photo_type=="photo_large"){
					
					$file=$photo->photo_large;
					
				}
					
				$thumb = $site_url.$upload_folder."/".$user_id."/".$file;
					
								
				$html.= "<li id='".$photo->photo_id."' class='".$box_border." ".$box_shadow." ".$display."' >
										
				<a href='".$xoouserultra->userpanel->public_profile_get_photo_link($photo->photo_id, $user_id)."' class='' ><img src='".$thumb."' class='rounded'/> </a>";
					
				if($display_photo_rating == "yes")	
				{
						
						$html.= "<div class='ratebox'>";
						$html.= $xoouserultra->rating->get_rating($photo->photo_id,"photo_id");
						$html.= "</div>";
					
				}					
					
					$html.= "</li>";	
							
			
			}
			
			$html.='</ul></div>';
			
		}
		
		return $html;
	
	
	}
	
	/*Latest Photos*/
	public function show_promoted_photos($atts)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
		extract( shortcode_atts( array(
		
			'photo_list' => '', //photo list
			'photo_type' => 'photo_thumb', //photo_thumb
			'size' => '', 					
			'howmany' => '1', // how many pictures to display
			'display_photo_rating' => 'yes', 			
			'photo_border' => 'rounded',
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',
			
		), $atts ) );
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder'); 
				
		$html="";
		
		$sql = ' SELECT photo.*, u.ID, gal.gallery_id,  gal.gallery_user_id
		
		  
		  FROM ' . $wpdb->prefix . 'usersultra_photos photo  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_galleries gal ON (gal.gallery_id = photo.photo_gal_id)";		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users u ON (u.ID = gal.gallery_user_id)";
		
						
		$sql .= " WHERE photo.photo_id IN (".$photo_list.") AND gal.gallery_id = photo.photo_gal_id AND u.ID = gal.gallery_user_id ORDER BY photo.photo_id DESC  ";	
		
		
		
			
		$rows = $wpdb->get_results($sql);
		
		
		if ( empty( $rows ) )
		{
		
		
		}else{
			
			$html.='<div class="uultra-promote-photos">
			
			<ul>';
			
			
			foreach ( $rows as $photo )
			{
				
				//get thumbnail
				$user_id =$photo->gallery_user_id;
				
				if($photo_type=="photo_mini")
				{
					$file=$photo->photo_mini;			
				
				}elseif($photo_type=="photo_thumb"){
					
					$file=$photo->photo_thumb;
				
				}elseif($photo_type=="photo_large"){
					
					$file=$photo->photo_large;
					
				}
					
				$thumb = $site_url.$upload_folder."/".$user_id."/".$file;
					
								
				$html.= "<li id='".$photo->photo_id."' class='".$box_border." ".$box_shadow." ".$display."' >
										
				<a href='".$xoouserultra->userpanel->public_profile_get_photo_link($photo->photo_id, $user_id)."' class='' ><img src='".$thumb."' class='rounded'/> </a>";
					
				if($display_photo_rating == "yes")	
				{
						
						$html.= "<div class='ratebox'>";
						$html.= $xoouserultra->rating->get_rating($photo->photo_id,"photo_id");
						$html.= "</div>";
					
				}					
					
					$html.= "</li>";	
							
			
			}
			
			$html.='</ul></div>';
			
		}
		
		return $html;
	
	
	}
	
	/*Reload Galleries - Users Dashboard*/
	public function reload_galleries ()
	{
		global $wpdb, $xoouserultra;
		
				
		$html="";
		
		$user_id = get_current_user_id();
		
		if (is_user_logged_in() && isset($user_id)) 
		{
			$galleries = $wpdb->get_results( 'SELECT `gallery_id`, `gallery_user_id`, `gallery_name`, `gallery_desc`  FROM ' . $wpdb->prefix . 'usersultra_galleries WHERE `gallery_user_id` = "' . $user_id . '" ORDER BY `gallery_order` ASC' );
			
			
			if ( empty( $galleries ) )
			{
				$html.= '<p>' .__( 'You have no galleries yet.', 'xoousers' ). '</p>';
			
			}else{
				$n = count( $galleries );
				$num_unread = 0;
				foreach ( $galleries as $gall )
				{
					//get main picture
					$thumb = $this->get_main_picture($gall->gallery_id);
					
					//get amount of pictures
					$amount_pictures = $this->get_total_pictures_of_gal($gall->gallery_id);
					
					
					echo "<li class='xoousersultra-shadow-borers' id='".$gall->gallery_id."'>
					
					<div class='pe_icons_gal'>
					<a href='#resp_del_gallery' data-id='".$gall->gallery_id."' class='delete' id='".$gall->gallery_id."' alt='delete' title='".__( 'delete', 'xoousers' )."'></a>
					
					<a href='#resp_edit_gallery' data-id='".$gall->gallery_id."' class='edit' id='".$gall->gallery_id."' alt='edit' title='".__( 'edit', 'xoousers' )."'></a>
					</div>	
					
					<div class='usersultra-photo-name'>
					
						<a href='".$xoouserultra->userpanel->get_internal_links('photos-files','gal_id',$gall->gallery_id)."' >".$gall->gallery_name." </a>
					
					</div>
				
					<a href='".$xoouserultra->userpanel->get_internal_links('photos-files','gal_id',$gall->gallery_id)."' class=''  ><img src='".$thumb."' /> </a>
					
					
					
					<p class='usersultra-amount_pictures'>".$amount_pictures." ".__( 'Picture(s)', 'xoousers' )."</p>
					
					<p>".$gall->gallery_desc."</p>
					
					<div class='uultra-gallery-edit' id='gallery-edit-div-".$gall->gallery_id."'>
					</div>
					</li>";	
					
				}
			}
			
					   
		    die ($html);
		} //end user loged in
		
	}
	
	/*Reload Videos*/
	public function reload_videos ()
	{
		global $wpdb, $xoouserultra;
		
				
		$html="";
		
		$user_id = get_current_user_id();
		
		if (is_user_logged_in() && isset($user_id)) 
		{
			$videos = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'usersultra_videos WHERE `video_user_id` = "' . $user_id . '" ORDER BY `video_order` ASC' );
			
			
			if ( empty( $videos ) )
			{
				$html.= '<p>' .__( 'You have no videos yet.', 'xoousers' ). '</p>';
			
			}else{
				$n = count( $videos );
				$num_unread = 0;
				foreach ( $videos as $video )
				{								
					
					$html .= "<li class='xoousersultra-shadow-borers' id='".$video->video_id."'>
					
					<div class='pe_icons_gal'>
					<a href='#resp_del_video' data-id='".$video->video_id."' class='delete' id='".$video->video_id."' alt='delete' title='".__( 'delete', 'xoousers' )."'></a><a href='#resp_edit_video' data-id='".$video->video_id."' class='edit' id='".$video->video_id."' alt='edit' title='".__( 'edit', 'xoousers' )."'></a>
					</div>";
					
					
					$html .= '<div class="embed-container">';
					
					switch($video->video_type): case "youtube":
                        
						$html .= '<iframe width="99%" src="http://www.youtube.com/embed/'.$video->video_unique_vid.'?autohide=1&modestbranding=1&showinfo=0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
							
					 break; 
					 
					 case "vimeo": 
					 
							$html .= '<iframe src="http://player.vimeo.com/video/'.$video->video_unique_vid.'?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="99%"  frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
					 endswitch;
						 
						 
					$html .= "</div> ";
					
					$html .= "<div><p>".$video->video_name."</p></div>"   ;
					
					$html .= "<div class='uultra-video-edit' id='video-edit-div-".$video->video_id."'> "   ;
					
					         					
					
					$html .= "</li>";	
					
				}
			}
			
					   
		    echo $html;
			die();
		} //end user loged in
		
	}
	
	/*Reload Videos*/
	public function reload_videos_public ($user_id)
	{
		global $wpdb, $xoouserultra;
		
				
		$html="";
			
		
		if (isset($user_id)) 
		{
			$videos = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'usersultra_videos WHERE `video_user_id` = "' . $user_id . '" ORDER BY `video_order` ASC' );
			
			
			if ( empty( $videos ) )
			{
				$html.= '<p>' .__( 'The user has not added videos yet.', 'xoousers' ). '</p>';
			
			}else{
				$n = count( $videos );
				$num_unread = 0;
				foreach ( $videos as $video )
				{								
					
					$html .= "<li class='xoousersultra-shadow-borers' id='".$video->video_id."'>
					
					<div class='pe_icons_gal'>
					<a href='#resp_del_gallery' data-id='".$video->video_id."' class='delete' id='".$video->video_id."' alt='delete' title='".__( 'delete', 'xoousers' )."'></a>
					</div>";
					
					
					$html .= '<div class="embed-container">';
					
					switch($video->video_type): case "youtube":
                        
						$html .= '<iframe width="100%" src="http://www.youtube.com/embed/'.$video->video_unique_vid.'?autohide=1&modestbranding=1&showinfo=0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
						
						 $html .= "<p class='social_v'><i class='fa fa-youtube-square fa-3x'></i></p> ";
							
					 break; 
					 
					 case "vimeo": 
					 
							$html .= '<iframe src="http://player.vimeo.com/video/'.$video->video_unique_vid.'?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="100%"  frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
							
							 $html .= "<p class='social_v'><i class='fa fa-vimeo-square fa-3x'></i></p> ";
					 endswitch;
					 
					 
						
						 
					$html .= "</div> ";
					
					$html .= "<div><p>".$video->video_name."</p></div>"   ;            					
					
					$html .= "</li>";	
					
				}
			}
			
			
		} //end user loged in
		
		echo $html;		   
		
		
	}
	
	/*Reload Galleries*/
	public function reload_galleries_public ($user_id, $gallery_type=null)
	{
		global $wpdb , $xoouserultra;
				
		$html="";		
		
		if (isset($user_id)) 
		{
			$galleries = $wpdb->get_results( 'SELECT `gallery_id`, `gallery_user_id`, `gallery_name`, `gallery_desc`  FROM ' . $wpdb->prefix . 'usersultra_galleries WHERE `gallery_user_id` = "' . $user_id . '" ORDER BY `gallery_order` ASC' );
			
			
			if ( empty( $galleries ) )
			{
				$html.= '<p>' .__( 'You have no galleries yet.', 'xoousers' ). '</p>';
			
			}else{
				$n = count( $galleries );
				$num_unread = 0;
				foreach ( $galleries as $gall )
				{
					//get main picture
					$thumb = $this->get_main_picture_public($gall->gallery_id);
					
					//get amount of pictures
					$amount_pictures = $this->get_total_pictures_of_gal($gall->gallery_id);
					
					
					echo "<li class='xoousersultra-shadow-borers' id='".$gall->gallery_id."'>
					
										
					<div class='usersultra-photo-name'>
					
						<a href='". $xoouserultra->userpanel->public_profile_get_album_link($gall->gallery_id, $user_id)."' >".$gall->gallery_name." </a>
					
					</div>
				
					<a href='".$xoouserultra->userpanel->public_profile_get_album_link($gall->gallery_id, $user_id)."' class=''  ><img src='".$thumb."' class='rounded' /> </a>
					
					
					
					<p class='usersultra-amount_pictures'>".$amount_pictures." ".__( 'Picture(s)', 'xoousers' )."</p>
					
					<p class='galdesc'>".$gall->gallery_desc."</p>
					</li>";	
					
				}
			}
		   
		    return $html;
		} //end user loged in
		
	}
	
	function sort_photo_list() 
	{
		global $wpdb;
	
		$order = explode(',', $_POST['order']);
		$counter = 0;
		foreach ($order as $item_id) 
		{
		
			$query = "UPDATE " . $wpdb->prefix ."usersultra_photos SET photo_order = '$counter' WHERE  `photo_id` = '$item_id' ";						
		    $wpdb->query( $query );
		
			$counter++;
		}
		die(1);
	}
	
	function sort_gallery_list() 
	{
		global $wpdb;
		$user_id = get_current_user_id();
	
		$order = explode(',', $_POST['order']);
		$counter = 0;
		foreach ($order as $item_id) 
		{
		
			$query = "UPDATE " . $wpdb->prefix ."usersultra_galleries SET gallery_order = '$counter' WHERE  `gallery_id` = '$item_id' AND   `gallery_user_id` = '$user_id' ";						
		    $wpdb->query( $query );
		
			$counter++;
		}
		die(1);
	}

	
	public function get_total_pictures_of_gal ($gal_id)
	{
		global $wpdb, $xoouserultra;
		
		$total = 0;
		

		$photos = $wpdb->get_results( 'SELECT count(*) as total  FROM ' . $wpdb->prefix . 'usersultra_photos WHERE `photo_gal_id` = "' . $gal_id . '" ' );
		
		
		
		foreach ( $photos as $photo )
		{
			$total = $photo->total;
			//print_r($photo);
							
		}
				
		
		return $total;
	}
	
	public function get_gallery ($gal_id)
	{
		global $wpdb, $xoouserultra;
		

		$photos = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_galleries WHERE `gallery_id` = ' . $gal_id . ' ' );
		
		
		foreach ( $photos as $photo )
		{
			return $photo;
							
		}
		
		
	}
	
	public function get_gallery_public ($gal_id, $user_id)
	{
		global $wpdb, $xoouserultra;
		

		$photos = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_galleries WHERE `gallery_id` = ' . $gal_id . '  AND  `gallery_user_id` = ' . $user_id . '  ' );
		
		
		foreach ( $photos as $photo )
		{
			return $photo;
							
		}
		
		
	}
	
	
	
	public function get_photo ($photo_id, $user_id)
	{
		global $wpdb, $xoouserultra;
		
		$sql = 'SELECT  photo.*, gal.gallery_user_id , gal.gallery_id , gal.gallery_private  
		               
					   FROM ' . $wpdb->prefix . 'usersultra_photos photo
					   
					    RIGHT JOIN ' . $wpdb->prefix . 'usersultra_galleries gal ON (gal.gallery_id = photo.photo_gal_id )
						
					   WHERE gal.gallery_user_id = ' . $user_id . ' AND gal.gallery_id = photo.photo_gal_id  AND  photo.photo_id =  '.$photo_id.' ';
		

		$photos = $wpdb->get_results( $sql );
					   
					 //  echo $sql;
		
		
		foreach ( $photos as $photo )
		{
			return $photo;
							
		}
		
		
	}
	
	public function get_main_picture_public ($gal_id)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
		
		
		$current_gal = $this->get_gallery($gal_id);
		$user_id = $current_gal->gallery_user_id;
		
		
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder'); 
		
		$photos = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_photos WHERE `photo_gal_id` = "' . $gal_id . '" AND `photo_main` = 1 ' );
		
		if ( empty( $photos ) )
			{
				$thumb = xoousers_url."templates/".xoousers_template."/img/no-photo.png";
			
			}else{
				
				foreach ( $photos as $photo )
				{
					//get gallery					
					
					$thumb = $site_url.$upload_folder."/".$user_id."/".$photo->photo_thumb;
				
				
				}
		    }			
		
		
		
		return $thumb;
	}
	
	
	
	
	
	public function get_main_picture ($gal_id)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
		$user_id = get_current_user_id();
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder'); 
		
		$photos = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_photos WHERE `photo_gal_id` = "' . $gal_id . '" AND `photo_main` = 1 ' );
		
		if ( empty( $photos ) )
			{
				$thumb = xoousers_url."templates/".xoousers_template."/img/no-photo.png";
			
			}else{
				
				foreach ( $photos as $photo )
				{
					//get gallery
					
					
					$thumb = $site_url.$upload_folder."/".$user_id."/".$photo->photo_thumb;
				
				
				}
		    }			
		
		
		
		return $thumb;
	}
	
	public function edit_photo ()
	{
		global $wpdb, $xoouserultra;
		
		$user_id = get_current_user_id();
		$photo_id = $_POST["photo_id"];
		
		if($photo_id!="")
		{
		
			$res = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_photos WHERE `photo_id` = ' . $photo_id . '  ' );
			
			$html="";
			foreach ( $res as $photo )
			{
				
				$html .="<p>".__( 'Name', 'xoousers' )."</p>";
				
				$html .="<p><input type='text' value='".$photo->photo_name."' class='xoouserultra-input' id='uultra_photo_name_edit_".$photo->photo_id."'></p>";
				
				$html .="<p>".__( 'Description', 'xoousers' )."</p>";				
				$html .="<p><input type='text' value='".$photo->photo_desc."' class='xoouserultra-input' id='uultra_photo_desc_edit_".$photo->photo_id."'></p>";
				
				$html .="<p>".__( 'Tags', 'xoousers' )."</p>";				
				$html .="<p><input type='text' value='".$photo->photo_tags."' class='xoouserultra-input' id='uultra_photo_tags_edit_".$photo->photo_id."'></p>";
				
				$html .="<p>".__( 'Category', 'xoousers' )."</p>";
				$html .="<p><select class='xoouserultra-input' id='uultra_photo_category_edit_".$photo->photo_id."'>	";
				
				//get all categories of this photo				
				$res = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'usersultra_photo_cat_rel WHERE  `photo_rel_photo_id` = ' . $photo_id . ' ' );
				
				$array_rel = array();
				foreach ( $res as $crel )
				{
					$array_rel[]=$crel->photo_rel_cat_id;
				
				}
				
				//loop categories
				$categories = $this->get_photo_categories();
				foreach ( $categories as $c )
				{
					$selected = "";
					
					if(in_array($c->photo_cat_id, $array_rel)) $selected= ' selected="selected" ';
					
					$html .= "<option value='".$c->photo_cat_id."' ".$selected." >".$c->photo_cat_name."</option>";
				 
				}
  
  $html .="</select>
				
				</p>";
				
				
				
				$html .="<p><input type='button' class='xoouserultra-button btn-photo-close' value='".__( 'Close', 'xoousers' )."' data-id= ".$photo->photo_id."> <input type='button'  class='xoouserultra-button btn-photo-conf' data-id= ".$photo->photo_id." value='".__( 'Save', 'xoousers' )."'> </p>";
				
								
			}		
			
					
		}
		
		echo $html;
		die();
		
	}
	
	
	public function edit_photo_confirm ()
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
				
		$user_id = get_current_user_id();
		
		$photo_id = $_POST["photo_id"];
		
		$photo_name = sanitize_text_field($_POST["photo_name"]);
		$photo_desc = sanitize_text_field($_POST["photo_desc"]);
		$photo_tags = sanitize_text_field($_POST["photo_tags"]);
		$photo_category = sanitize_text_field($_POST["photo_category"]);
		
			
		
		if($photo_id!="")
		{
			$query = "UPDATE " . $wpdb->prefix ."usersultra_photos SET `photo_name` = '$photo_name', `photo_desc` = '$photo_desc'  , `photo_tags` = '$photo_tags'  WHERE  `photo_id` = '$photo_id' ";
			$wpdb->query( $query );		
			
			//update categories table
			$query = "DELETE FROM " . $wpdb->prefix ."usersultra_photo_cat_rel   WHERE  `photo_rel_photo_id` = '$photo_id' ";
			$wpdb->query( $query );
			
			//only one cate for free
			$new_array = array(
						'photo_rel_cat_id'     => $photo_category,
						'photo_rel_photo_id'   => $photo_id						
						
						
					);
					
			// insert into database
			$wpdb->insert( $wpdb->prefix . 'usersultra_photo_cat_rel', $new_array, array( '%d', '%s'));
					
			
					
		}	
		
		die();
		
		
	}
	
	public function check_if_photo_cat ($photo_id, $cat_id)
	{
		global $wpdb, $xoouserultra;
		
		$res = $wpdb->get_results( 'SELECT count(*) as total  FROM ' . $wpdb->prefix . 'usersultra_photo_cat_rel WHERE `photo_rel_cat_id` = ' . $cat_id . ' AND `photo_rel_photo_id` = ' . $photo_id . ' ' );
		
		foreach ( $res as $rel )
		{
			return $rel->total;
		
		}
		
		
	}
	
	public function edit_gallery ()
	{
		global $wpdb, $xoouserultra;
		
		$user_id = get_current_user_id();
		$gal_id = $_POST["gal_id"];
		
		if($gal_id!="")
		{
		
			$res = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_galleries WHERE `gallery_id` = ' . $gal_id . ' AND `gallery_user_id` = ' . $user_id . ' ' );
			
			$html="";
			foreach ( $res as $gal )
			{
				$pulic = "";
				$registered = "";
				$friends = "";
				
				if($gal->gallery_private==0){ $pulic = "selected='selected'";}				
				if($gal->gallery_private==1){$registered = "selected='selected'";}
				if($gal->gallery_private==2){$friends = "selected='selected'";}
				
				$html .="<p>".__( 'Name', 'xoousers' )."</p>";
				
				$html .="<p><input type='text' value='".$gal->gallery_name."' class='xoouserultra-input' id='uultra_gall_name_edit_".$gal->gallery_id."'></p>";
				
				$html .="<p>".__( 'Description', 'xoousers' )."</p>";				
				$html .="<p><input type='text' value='".$gal->gallery_desc."' class='xoouserultra-input' id='uultra_gall_desc_edit_".$gal->gallery_id."'></p>";
				
				$html .="<p>".__( 'Visibility', 'xoousers' )."</p>";
				$html .="<p><select class='xoouserultra-input' id='uultra_gall_visibility_edit_".$gal->gallery_id."'>				
				 <option value='0' ".$pulic." >Public</option>
  <option value='1' ".$registered.">Only Registered</option>
  <option value='2' ".$friends.">Only Friends</option>
  
  </select>
				
				</p>";
				
				
				
				$html .="<p><input type='button' class='xoouserultra-button btn-gallery-close-conf' value='".__( 'Close', 'xoousers' )."' data-id= ".$gal->gallery_id."> <input type='button'  class='xoouserultra-button btn-gallery-conf' data-id= ".$gal->gallery_id." value='".__( 'Save', 'xoousers' )."'> </p>";
				
								
			}		
			
					
		}
		
		echo $html;
		die();
		
	}
	
	
	
	public function edit_gallery_confirm ()
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
				
		$user_id = get_current_user_id();
		$gal_id = $_POST["gal_id"];
		
		$gal_name = sanitize_text_field($_POST["gal_name"]);
		$gal_desc = sanitize_text_field($_POST["gal_desc"]);
		
		//$gal_name = $_POST["gal_name"];
		//$gal_desc = $_POST["gal_desc"];
		$gal_visibility = $_POST["gal_visibility"];
		
		
		
		if($gal_id!="")
		{
			$query = "UPDATE " . $wpdb->prefix ."usersultra_galleries SET `gallery_name` = '$gal_name', `gallery_desc` = '$gal_desc'  , `gallery_private` = '$gal_visibility'  WHERE  `gallery_id` = '$gal_id' AND `gallery_user_id` = '$user_id' ";		
			
			
			$wpdb->query( $query );	
					
		}	
		
		die();
		
		
	}
	
	public function edit_video_confirm ()
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
				
		$user_id = get_current_user_id();
		
		$video_id = $_POST["video_id"];
		
		$video_name = sanitize_text_field($_POST["video_name"]);
		$video_unique_id = sanitize_text_field($_POST["video_unique_id"]);
		$video_type = sanitize_text_field($_POST["video_type"]);
		
	
		
		if($video_id!="")
		{
			$query = "UPDATE " . $wpdb->prefix ."usersultra_videos SET `video_name` = '$video_name', `video_unique_vid` = '$video_unique_id'  , `video_type` = '$video_type'  WHERE  `video_id` = '$video_id' AND `video_user_id` = '$user_id' ";		
			
			
			$wpdb->query( $query );	
					
		}	
		
		die();
		
		
	}
	
	
	public function edit_video ()
	{
		global $wpdb, $xoouserultra;
		
		$user_id = get_current_user_id();
		$video_id = $_POST["video_id"];
		
		if($video_id!="")
		{
		
			$res = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_videos WHERE `video_id` = ' . $video_id . ' AND `video_user_id` = ' . $user_id . ' ' );
			
			$html="";
			foreach ( $res as $gal )
			{
				$pulic = "";
				$registered = "";
				$friends = "";
				
				if($gal->video_type=='youtube'){ $youtube = "selected='selected'";}				
				if($gal->video_type=='vimeo'){$vimeo = "selected='selected'";}
				
				$html .="<p>".__( 'Name', 'xoousers' )."</p>";
				
				$html .="<p><input type='text' value='".$gal->video_name."' class='xoouserultra-input' id='uultra_video_name_edit_".$gal->video_id."'></p>";
				
				$html .="<p>".__( 'Video ID', 'xoousers' )."</p>";				
				$html .="<p><input type='text' value='".$gal->video_unique_vid."' class='xoouserultra-input' id='uultra_video_id_edit_".$gal->video_id."'></p>";
				
				$html .="<p>".__( 'Type', 'xoousers' )."</p>";
				$html .="<p><select class='xoouserultra-input' id='uultra_video_type_edit_".$gal->video_id."'>				
				 
  <option value='youtube' ".$youtube.">Youtube</option>
  <option value='vimeo' ".$vimeo.">Vimeo</option>
  
  </select>
				
				</p>";
				
				
				
				$html .="<p><input type='button' class='xoouserultra-button btn-video-close-conf' value='".__( 'Close', 'xoousers' )."' data-id= ".$gal->video_id."> <input type='button'  class='xoouserultra-button btn-video-edit-conf' data-id= ".$gal->video_id." value='".__( 'Save', 'xoousers' )."'> </p>";
				
								
			}		
			
					
		}
		
		echo $html;
		die();
		
	}
	
	public function delete_gallery ()
	{
		global $wpdb, $xoouserultra;
		
		$user_id = get_current_user_id();
		$gal_id = $_POST["gal_id"];
		
		
		//get photo
		
		if($gal_id!="")
		{
		
			$photos = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_photos WHERE `photo_gal_id` = ' . $gal_id . ' ' );
			
			
			foreach ( $photos as $photo )
			{
				$this->delete_photo_files ($photo);
								
			}		
			
			//delete gallery from db
			$query = "DELETE FROM " . $wpdb->prefix ."usersultra_galleries WHERE  `gallery_id` = '$gal_id' ";						
			$wpdb->query( $query );	
		
		}
		
	}
	
	public function delete_video ()
	{
		global $wpdb, $xoouserultra;
		
		$user_id = get_current_user_id();
		$video_id = $_POST["video_id"];
		
		
		//get photo
		
		if($video_id!="")
		{
			
			//delete  from db
			$query = "DELETE FROM " . $wpdb->prefix ."usersultra_videos WHERE  `video_id` = '$video_id'  AND video_user_id = '$user_id' ";						
			$wpdb->query( $query );	
		
		}
		
	}
	
	public function delete_photo ()
	{
		global $wpdb, $xoouserultra;
		
		$user_id = get_current_user_id();
		$photo_id = $_POST["photo_id"];
		
		
		//get photo
		
		$photos = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_photos WHERE `photo_id` = ' . $photo_id . ' ' );
		
		
		foreach ( $photos as $photo )
		{
			$this->delete_photo_files ($photo);
							
		}		
		
		//delete photo from db
		$query = "DELETE FROM " . $wpdb->prefix ."usersultra_photos WHERE  `photo_id` = '$photo_id' ";						
		$wpdb->query( $query );	
		
	}
	
	public function delete_photo_files ($photo)
	{
		global $wpdb, $xoouserultra;
		
		$o_id = get_current_user_id();
		
		$path_pics = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
		
		$pathBig = $path_pics."/".$o_id."/".$photo->photo_large;					
		$pathSmall=$path_pics."/".$o_id."/".$photo->photo_thumb;	
		$pathMini=$path_pics."/".$o_id."/".$photo->photo_mini;
		
		//delete	
		
		if(file_exists($pathBig))
		{
			unlink($pathBig);
		}
		
		if(file_exists($pathSmall))
		{
			unlink($pathSmall);
		}
		
		if(file_exists($pathMini))
		{
			unlink($pathMini);
		}
		
				
	}
	
	public function set_as_main_photo ()
	{
		global $wpdb, $xoouserultra;
		
		$user_id = get_current_user_id();
		$photo_id = $_POST["photo_id"];		
		$gal_id = $_POST["gal_id"];
		//set all to 0
		
		$query = "UPDATE " . $wpdb->prefix ."usersultra_photos SET photo_main = '0' WHERE  `photo_gal_id` = '$gal_id' ";						
		$wpdb->query( $query );
		
		//set to main
		$query = "UPDATE " . $wpdb->prefix ."usersultra_photos SET photo_main = '1' WHERE  `photo_id` = '$photo_id' AND  `photo_gal_id` = '$gal_id' ";						
		$wpdb->query( $query );
		
		 die();
		
	}
	
	
	
	public function get_photos_of_gal_public ($gal_id, $display_photo_rating, $gallery_type = null)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$html="";
		
		$site_url = site_url()."/";
		
		
		$current_gal = $this->get_gallery($gal_id);
		$user_id = $current_gal->gallery_user_id;
		 		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder'); 
		
		if (isset($gal_id)) 
		{
			$photos = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_photos WHERE `photo_gal_id` = "' . $gal_id . '" ORDER BY `photo_order` ASC' );
			
					
			
			if ( empty( $photos ) )
			{
				$html.= '<p>' .__( 'You have no photos in this gallery yet.', 'xoousers' ). '</p>';
			
			}else{
				$n = count( $photos );
				$num_unread = 0;
				foreach ( $photos as $photo )
				{
					//get thumbnail
					
					$thumb = $site_url.$upload_folder."/".$user_id."/".$photo->photo_thumb;
					$large = $site_url.$upload_folder."/".$user_id."/".$photo->photo_large;
								
					$html.= "<li id='".$photo->photo_id."' >";
					
					
					
					if($gallery_type=="lightbox")
					{
						
						$html .="<a href='".$large."' class='' data-lightbox='example-1' data-title='".$photo->photo_desc."'><img src='".$thumb."' class='rounded'/> </a>";
					
				    
					}else{
						
						$html .="<a href='".$xoouserultra->userpanel->public_profile_get_photo_link($photo->photo_id, $user_id)."' class='' ><img src='".$thumb."' class='rounded'/> </a>";
					
						
						
					}
										
					
					
					
					
					if($display_photo_rating == "yes")	
					{
						
						$html.= "<div class='ratebox'>";
						$html.= $xoouserultra->rating->get_rating($photo->photo_id,"photo_id");
						$html.= "</div>";
					
					}
					
					
					$html.= "</li>";	
					
					
			
					
				}
			}
		   
		    return $html;
		} //end user loged in
		
	}
	
	public function get_single_photo($photo_id, $user_id, $display_photo_rating, $display_photo_description)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$html="";
		$error=false;	
		
		
		$site_url = site_url()."/";
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder'); 
		
		//get photo
		$current_photo = $this->get_photo($photo_id, $user_id);
		
		if ($current_photo->gallery_private == 1) 
		{
			$error=true;			
			$html.= '<p>' .__( 'You have to be logged in to see this photo.', 'xoousers' ). '</p>';
		
		}
		
		if ($current_photo->gallery_only_friends == 1) 
		{
			$error=true;			
			$html.= '<p>' .__( 'This photo is accesible only for friends of the user.', 'xoousers' ). '</p>';
		
		}
		
		if ($current_photo->photo_id == "") 
		{
			$error=true;			
			$html.= '<p>' .__( 'This photo could not be displayed.', 'xoousers' ). '</p>';
		
		}
		
		
		if (!$error) 
		{
			$thumb = $site_url.$upload_folder."/".$user_id."/".$current_photo->photo_large;								
			$html.= "<a href='#' class='' ><img src='".$thumb."' class='rounded'/> </a>";
			
			
			if($display_photo_description == "yes")	
			{
				
				$html.= "<div class='uutra-photo-desc'>";
				$html.= $current_photo->photo_desc;
				$html.= "</div>";
			}	
			
			//add rating?
			
			if($display_photo_rating == "yes")	
			{
				
				$html.= "<div class='ratebox'>";
				$html.= $xoouserultra->rating->get_rating($current_photo->photo_id,"photo_id");
				$html.= "</div>";
			
			
			}
			
			
				   
		} 
		
		return $html;
		die();
	}
	
	
	
	public function reload_photos ()
	{
		
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$html="";
		
		$user_id = get_current_user_id();
		$gal_id = $_POST["gal_id"];
		
		$site_url = site_url()."/";
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder'); 
		
		if (is_user_logged_in() && isset($user_id)) 
		{
			$photos = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_photos WHERE `photo_gal_id` = "' . $gal_id . '" ORDER BY `photo_order` ASC' );
			
					
			
			if ( empty( $photos ) )
			{
				$html.= '<p>' .__( 'You have no photos in this gallery yet.', 'xoousers' ). '</p>';
			
			}else{
				$n = count( $photos );
				$num_unread = 0;
				foreach ( $photos as $photo )
				{
					//get thumbnail
					
					$thumb = $site_url.$upload_folder."/".$user_id."/".$photo->photo_thumb;
					$large = $site_url.$upload_folder."/".$user_id."/".$photo->photo_large;
					
					$main = "";
					if($photo->photo_main==1)
					{
						$main = "<div class='pe_main_picture'>".__( 'MAIN PHOTO', 'xoousers' )."</div>";
						
					}
					
					echo "<li id='".$photo->photo_id."' >
					<div class='pe_icons'><a href='#resp_del_photo' data-id='".$photo->photo_gal_id."' class='delete' id='".$photo->photo_id."' alt='delete'></a><a href='#resp_set_main' data-id='".$photo->photo_gal_id."' class='' id='".$photo->photo_id."'></a>
					
					<a href='#resp_edit_photo' data-id='".$photo->photo_id."' class='edit' id='".$photo->photo_id."' alt='edit' title='".__( 'edit', 'xoousers' )."'></a>
					</div>					
					".$main."
					<a href='".$large."' class='' data-lightbox='example-1' ><img src='".$thumb."' /> </a>
					
					<div class='uultra-photo-edit' id='photo-edit-div-".$photo->photo_id."'>
					</div>
					
					</li>";	
					
				}
			}
		   
		    die ($html);
		} //end user loged in
		
	}
	
	
	
	
	function setFileType($filename)
	{
		
		$fileTypes['swf'] = 'application/x-shockwave-flash'; 
		$fileTypes['pdf'] = 'application/pdf'; 
		$fileTypes['exe'] = 'application/octet-stream'; 
		$fileTypes['zip'] = 'application/zip'; 
		$fileTypes['doc'] = 'application/msword'; 
		$fileTypes['xls'] = 'application/vnd.ms-excel'; 
		$fileTypes['ppt'] = 'application/vnd.ms-powerpoint'; 
		$fileTypes['gif'] = 'image/gif'; 
		$fileTypes['png'] = 'image/png'; 
		$fileTypes['jpeg'] = 'image/jpg'; 
		$fileTypes['jpg'] = 'image/jpg'; 
		$fileTypes['rar'] = 'application/rar';     
		 
		$fileTypes['ra'] = 'audio/x-pn-realaudio'; 
		$fileTypes['ram'] = 'audio/x-pn-realaudio'; 
		$fileTypes['ogg'] = 'audio/x-pn-realaudio'; 
		 
		$fileTypes['wav'] = 'video/x-msvideo'; 
		$fileTypes['wmv'] = 'video/x-msvideo'; 
		$fileTypes['avi'] = 'video/x-msvideo'; 
		$fileTypes['asf'] = 'video/x-msvideo'; 
		$fileTypes['divx'] = 'video/x-msvideo'; 
	
		$fileTypes['mp3'] = 'audio/mpeg'; 
		$fileTypes['mp4'] = 'audio/mpeg'; 
		$fileTypes['mpeg'] = 'video/mpeg'; 
		$fileTypes['mpg'] = 'video/mpeg'; 
		$fileTypes['mpe'] = 'video/mpeg'; 
		$fileTypes['mov'] = 'video/quicktime'; 
		$fileTypes['swf'] = 'video/quicktime'; 
		$fileTypes['3gp'] = 'video/quicktime'; 
		$fileTypes['m4a'] = 'video/quicktime'; 
		$fileTypes['aac'] = 'video/quicktime'; 
		$fileTypes['m3u'] = 'video/quicktime'; 
		
		$ext = strtolower(end(explode('.',$filename))); 			
		return $fileTypes[$ext];
			
		
	}
	
	
	
	public function post_media_display( $gal_id ) 
	{ 
        
               // Uploading functionality trigger:
	// (Most of the code comes from media.php and handlers.js)
	      $template_dir = get_template_directory_uri();
?>
		
		<div id="uploadContainer" style="margin-top: 10px;">
			
			
			<!-- Uploader section -->
			<div id="uploaderSection" style="position: relative;">
				<div id="plupload-upload-ui" class="hide-if-no-js">
                
					<div id="drag-drop-area">
						<div class="drag-drop-inside">
							<p class="drag-drop-info"><?php	_e('Drop files here', 'xoousers') ; ?></p>
							<p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
							<p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button" /></p>
														
						</div>
                        
                        <div id="progressbar"></div>                 
                         <div id="symposium_filelist" class="cb"></div>
					</div>
				</div>
                
                 
			
			</div>
            
           
		</div>

		<?php
			
			$plupload_init = array(
				'runtimes'            => 'html5,silverlight,flash,html4',
				'browse_button'       => 'plupload-browse-button',
				'container'           => 'plupload-upload-ui',
				'drop_element'        => 'drag-drop-area',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'multi_selection'	  => false,
				'max_file_size'       => wp_max_upload_size().'b',
				//'max_file_size'       => get_option('drag-drop-filesize').'b',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				'filters'             => array(array('title' => __('Allowed Files', 'xoousers'), 'extensions' => "jpg,png,gif,bmp,jpeg")),
				'multipart'           => true,
				'urlstream_upload'    => true,

				// Additional parameters:
				'multipart_params'    => array(
					'_ajax_nonce' => wp_create_nonce('photo-upload'),
					'action'      => 'rest_set_uploaded_image', // The AJAX action name
					'gal_id'	  => $gal_id
				),
			);
			
			//print_r($plupload_init);

			// Apply filters to initiate plupload:
			$plupload_init = apply_filters('plupload_init', $plupload_init); ?>

			<script type="text/javascript">
			
				jQuery(document).ready(function($){
					
					// Create uploader and pass configuration:
					var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

					// Check for drag'n'drop functionality:
					uploader.bind('Init', function(up){
						var uploaddiv = $('#plupload-upload-ui');
						
						// Add classes and bind actions:
						if(up.features.dragdrop){
							uploaddiv.addClass('drag-drop');
							$('#drag-drop-area')
								.bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
								.bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

						} else{
							uploaddiv.removeClass('drag-drop');
							$('#drag-drop-area').unbind('.wp-uploader');
						}

					});

					
					// Init ////////////////////////////////////////////////////
					uploader.init(); 
					
					// Selected Files //////////////////////////////////////////
					uploader.bind('FilesAdded', function(up, files) {
						jQuery.each(files, function(i, file) {
							jQuery('#symposium_filelist').append('<div class="addedFile" id="' + file.id + '">' + file.name + '</div>');
						});
						up.refresh(); 
						uploader.start();
					});
					
					// A new file was uploaded:
					uploader.bind('FileUploaded', function(up, file, response){
						
						//reload files list											
						 $.post(ajaxurl, {
									action: 'reload_photos', gal_id: '<?php echo $gal_id?>'
									
									}, function (response){									
																
									$("#usersultra-photolist").html(response);									
														
							});
					
					});
					
					// Error Alert /////////////////////////////////////////////
					uploader.bind('Error', function(up, err) {
						alert("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : "") + "");
						up.refresh(); 
					});
					
					// Progress bar ////////////////////////////////////////////
					uploader.bind('UploadProgress', function(up, file) {
						var progressBarValue = up.total.percent;
						
						jQuery('#progressbar').fadeIn().progressbar({
							value: progressBarValue
						});
						
						jQuery('#progressbar .ui-progressbar-value').html('<span class="progressTooltip">' + up.total.percent + '%</span>');
					});
					
					// Close window after upload ///////////////////////////////
					uploader.bind('UploadComplete', function() {
						
						jQuery('.uploader').fadeOut('slow');						
						jQuery('#progressbar').fadeIn().progressbar({
							value: 0
						});
						
						
					});
					
					
					
				});
				
					
			</script>
			
		<?php
	}
	
	public function CreateDir($root){

               if (is_dir($root))        {

                        $retorno = "0";
                }else{

                        $oldumask = umask(0);
                        $valrRet = mkdir($root,0777);
                        umask($oldumask);


                        $retorno = "1";
                }

    }
	
	
    public function createthumb($imagen,$newImage,$toWidth, $toHeight,$extorig)
	{             				
				
                 $ext=strtolower($extorig);
                 switch($ext)
                  {
                   case 'png' : $img = imagecreatefrompng($imagen);
                   break;
                   case 'jpg' : $img = imagecreatefromjpeg($imagen);
                   break;
                   case 'jpeg' : $img = imagecreatefromjpeg($imagen);
                   break;
                   case 'gif' : $img = imagecreatefromgif($imagen);
                   break;
                  }

               
                $width = imagesx($img);
                $height = imagesy($img);  
				

				
				$xscale=$width/$toWidth;
				$yscale=$height/$toHeight;
				
				// Recalculate new size with default ratio
				if ($yscale>$xscale){
					$new_w = round($width * (1/$yscale));
					$new_h = round($height * (1/$yscale));
				}
				else {
					$new_w = round($width * (1/$xscale));
					$new_h = round($height * (1/$xscale));
				}
				
				
				
				if($width < $toWidth)  {
					
					$new_w = $width;	
				
				//}else {					
					//$new_w = $current_w;			
				
				}
				
				if($height < $toHeight)  {
					
					$new_h = $height;	
				
				//}else {					
					//$new_h = $current_h;			
				
				}
			
				
				
				
                $dst_img = imagecreatetruecolor($new_w,$new_h);
				
				/* fix PNG transparency issues */                       
				imagefill($dst_img, 0, 0, IMG_COLOR_TRANSPARENT);         
				imagesavealpha($dst_img, true);      
				imagealphablending($dst_img, true); 				
                imagecopyresampled($dst_img,$img,0,0,0,0,$new_w,$new_h,imagesx($img),imagesy($img));
               
                
				
				 switch($ext)
                  {
                   case 'png' : $img = imagepng($dst_img,"$newImage",9);
                   break;
                   case 'jpg' : $img = imagejpeg($dst_img,"$newImage",100);
                   break;
                   case 'jpeg' : $img = imagejpeg($dst_img,"$newImage",100);
                   break;
                   case 'gif' : $img = imagegif($dst_img,"$newImage");
                   break;
                  }
				  
				   imagedestroy($dst_img);	
				
				
				
                return true;

        }
	
	// File upload handler:
	function ajax_upload_images()
	{
		global $xoouserultra;
		global $wpdb;
		
		// Check referer, die if no ajax:
		check_ajax_referer('photo-upload');
		
		/// Upload file using Wordpress functions:
		$file = $_FILES['async-upload'];
		
		
		$original_max_width = $xoouserultra->get_option('media_photo_large_width'); 
        $original_max_height =$xoouserultra->get_option('media_photo_large_height'); 
		
	
		$thumb_max_width = $xoouserultra->get_option('media_photo_thumb_width'); 
        $thumb_max_height = $xoouserultra->get_option('media_photo_thumb_height'); 
		
		
		$mini_max_width = $xoouserultra->get_option('media_photo_mini_width'); 
        $mini_max_height =$xoouserultra->get_option('media_photo_mini_height'); 
		
		$o_id = get_current_user_id();
		
		$gal_id = $_POST['gal_id'];		
		
		$info = pathinfo($file['name']);
		$real_name = $file['name'];
        $ext = $info['extension'];
		$ext=strtolower($ext);
		
		$rand = $this->genRandomString();
		
		$rand_name = $rand."_".session_id()."_".time(); 
		
		$path_pics = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
			
			
		if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif') 
		{
			if($o_id != '')
			{
				
				   if(!is_dir($path_pics."/".$o_id."")) {
						$this->CreateDir($path_pics."/".$o_id);								   
					}					
										
					$pathBig = $path_pics."/".$o_id."/".$rand_name.".".$ext;					
					$pathSmall=$path_pics."/".$o_id."/"."thumb_".$rand_name.".".$ext;	
					$pathMini=$path_pics."/".$o_id."/"."mini_".$rand_name.".".$ext;	
				
					
					
					
					if (copy($file['tmp_name'], $pathBig)) 
					{
						
						//check max width
												
						list( $source_width, $source_height, $source_type ) = getimagesize($pathBig);
						
						if($source_width > $original_max_width) 
						{
							//resize
							if ($this->createthumb($pathBig, $pathBig, $original_max_width, $original_max_height,$ext)) 
							{
								$old = umask(0);
								chmod($pathBig, 0777);
								umask($old);
														
							}
						
						
						}
						
					
							
						//mini
						
						if ($this->createthumb($pathBig, $pathMini, $mini_max_width, $mini_max_height,$ext)) 
						{					
							
							$old = umask(0);
							chmod($pathMini, 0777);
							umask($old);
							
												
						
						}
						
						//thumb
						
						if ($this->createthumb($pathBig, $pathSmall, $thumb_max_width, $thumb_max_height,$ext)) 
						{					
							
							$old = umask(0);
							chmod($pathSmall, 0777);
							umask($old);
							
						
						}
						
						$pic1 = $rand_name.".".$ext;
						$pic2 = "thumb_".$rand_name.".".$ext;
						$pic3 = "mini_".$rand_name.".".$ext;
						
						$order = 1;
						
						//check if there is main picture
						
						$photos = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_photos WHERE `photo_gal_id` = "' . $gal_id . '" AND `photo_main` = 1 ' );
						
						$ismain = 0;
						if ( empty( $photos ) )
			            {
							$ismain = 1;
						}
						
						//update database
						$query = "INSERT INTO " . $wpdb->prefix ."usersultra_photos (`photo_gal_id`,`photo_name`, `photo_large`, `photo_thumb` ,`photo_mini` ,`photo_order`, `photo_main`) VALUES ('$gal_id','$real_name','$pic1','$pic2', '$pic3','$order', '$ismain')";						
						$wpdb->query( $query );
						
						
					}
									
					
			     }  		
			
			  
			
        } // image type
		exit;
		
	}
	
	// File upload handler:
	function ajax_upload_avatar()
	{
		global $xoouserultra;
		global $wpdb;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
		// Check referer, die if no ajax:
		check_ajax_referer('photo-upload');
		
		/// Upload file using Wordpress functions:
		$file = $_FILES['async-upload'];
		
		
		$original_max_width = $xoouserultra->get_option('media_avatar_width'); 
        $original_max_height =$xoouserultra->get_option('media_avatar_height'); 
		
		if($original_max_width=="" || $original_max_height==80)
		{			
			$original_max_width = 100;			
			$original_max_height = 100;
			
		}
		
			
		$o_id = get_current_user_id();
		
				
		$info = pathinfo($file['name']);
		$real_name = $file['name'];
        $ext = $info['extension'];
		$ext=strtolower($ext);
		
		$rand = $this->genRandomString();
		
		$rand_name = "avatar_".$rand."_".session_id()."_".time(); 
		
		$path_pics = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
			
			
		if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif') 
		{
			if($o_id != '')
			{
				
				   if(!is_dir($path_pics."/".$o_id."")) {
						$this->CreateDir($path_pics."/".$o_id);								   
					}					
										
					$pathBig = $path_pics."/".$o_id."/".$rand_name.".".$ext;						
					
					
					if (copy($file['tmp_name'], $pathBig)) 
					{
						$upload_folder = $xoouserultra->get_option('media_uploading_folder');				
						$path = $site_url.$upload_folder."/".$o_id."/";
						
						//check max width
												
						list( $source_width, $source_height, $source_type ) = getimagesize($pathBig);
						
						if($source_width > $original_max_width) 
						{
							//resize
							if ($this->createthumb($pathBig, $pathBig, $original_max_width, $original_max_height,$ext)) 
							{
								$old = umask(0);
								chmod($pathBig, 0777);
								umask($old);
														
							}
						
						
						}
						
						
						
						$new_avatar = $rand_name.".".$ext;
						
						$new_avatar_url = $path.$rand_name.".".$ext;
						
						
						
						//check if there is another avatar						
						$user_pic = get_user_meta($o_id, 'user_pic', true);						
						
						if ( $user_pic!="" )
			            {
							//there is a pending avatar - delete avatar																					
							$o_id = get_current_user_id();		
							$path_pics = $site_url.$xoouserultra->get_option('media_uploading_folder');
							
							$path_avatar = $path_pics."/".$o_id."/".$user_pic;					
														
							//delete								
							if(file_exists($path_avatar))
							{
								unlink($path_avatar);
							}
							
							//update meta
							update_user_meta($o_id, 'user_pic', $new_avatar);

							
							
							
						}else{
							
							//update meta
							update_user_meta($o_id, 'user_pic', $new_avatar);
												
						
						}
						
						//update user meta
						
						
						
					}
									
					
			     }  		
			
			  
			
        } // image type
		
		// Create response array:
		$uploadResponse = array('image' => $new_avatar_url);
		
		// Return response and exit:
		echo json_encode($uploadResponse);
		
		//echo $new_avatar_url;
		die();
		
	}
	
	public function genRandomString() 
	{
		$length = 5;
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";
		
		$real_string_legnth = strlen($characters) ;
		//$real_string_legnth = $real_string_legnth– 1;
		$string="ID";
		
		for ($p = 0; $p < $length; $p++)
		{
			$string .= $characters[mt_rand(0, $real_string_legnth-1)];
		}
		
		return strtolower($string);
	}
	
	
}
$key = "photogallery";
$this->{$key} = new XooUserPhoto();