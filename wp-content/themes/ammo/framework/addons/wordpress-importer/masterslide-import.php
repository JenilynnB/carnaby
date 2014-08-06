<?php


if( class_exists('MSP_Importer') ){


    class MasterSliderImporter extends MSP_Importer{

        public function import_master_sliders(){

            $directory = get_stylesheet_directory().'/framework/addons/wordpress-importer/files/mastersliders/';
            $master_files = array();
            if( is_dir($directory) ){
                foreach( glob( $directory . '*.json' ) as $filename ) {
                    $filename = basename($filename);
                    $master_files[] = get_stylesheet_directory_uri().'/framework/addons/wordpress-importer/files/mastersliders/'.$filename;
                }
            }

            foreach( $master_files as $master_file ){
                $file_contents = wp_remote_get( $master_file );
                $file_content = isset($file_contents['body']) ? $file_contents['body'] : "";
                if( !empty($file_content) ){
                    ob_start();
                    $file_content = trim($file_content);
                    $this->import_data($file_content);
                    ob_clean();
                }
            }
            
        }
    }

}

?>