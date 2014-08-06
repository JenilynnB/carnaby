<?php

function tt_revo_importer(){
    if( class_exists('UniteFunctionsRev') ){
        
        global $wpdb;
        
        $revo_directory = get_stylesheet_directory().'/framework/addons/wordpress-importer/files/revsliders/';
        $revo_files = array();
        foreach( glob( $revo_directory . '*.txt' ) as $filename ) {
            $filename = basename($filename);
            $revo_files[] = get_stylesheet_directory_uri().'/framework/addons/wordpress-importer/files/revsliders/'.$filename;
        }
        foreach( $revo_files as $rev_file ){
            $get_revo_file = wp_remote_get( $rev_file );
            $slider_data = unserialize( $get_revo_file['body'] );
            $slider_params = $slider_data["params"];

            /*
            if(isset($slider_params["background_image"])) {
                $slider_params["background_image"] = UniteFunctionsWPRev::getImageUrlFromPath($slider_params["background_image"]);
            }
            */

            $json_params = json_encode($slider_params);

            $revoSliderInstance = array();
            $revoSliderInstance["params"] = $json_params;
            $revoSliderInstance["title"] = UniteFunctionsRev::getVal($slider_params, "title", $slider_params['title']);
            $revoSliderInstance["alias"] = UniteFunctionsRev::getVal($slider_params, "alias", $slider_params['alias']);

            $wpdb->insert(GlobalsRevSlider::$table_sliders, $revoSliderInstance);
            $sliderID = mysql_insert_id();

            //create all slides
            $revoSlides = $slider_data["slides"];
            foreach($revoSlides as $slide){
                
                $params = $slide["params"];
                $layers = $slide["layers"];
                
                //convert params images:
                if(isset($params["image"])) {
                    // $params["image"] = UniteFunctionsWPRev::getImageUrlFromPath($params["image"]);
                    $params["image"] = $slider_params["background_image"].$params["image"];
                }
                
                //convert layers images:
                foreach($layers as $key=>$layer){                   
                    if(isset($layer["image_url"])){
                        // $layer["image_url"] = UniteFunctionsWPRev::getImageUrlFromPath($layer["image_url"]);
                        $layer["image_url"] = $slider_params["background_image"].$layer["image_url"];
                        $layers[$key] = $layer;
                    }
                }
                
                //create new slide
                $SlideInstance = array();
                $SlideInstance["slider_id"] = $sliderID;
                $SlideInstance["slide_order"] = $slide["slide_order"];              
                $SlideInstance["layers"] = json_encode($layers);
                $SlideInstance["params"] = json_encode($params);

                $wpdb->insert(GlobalsRevSlider::$table_slides,$SlideInstance);
            }
        }

    }
}

?>