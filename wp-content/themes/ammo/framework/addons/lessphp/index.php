<?php

require_once('lessc.inc.php');

function html2text($str){
    $str = htmlentities($str,ENT_NOQUOTES,'UTF-8',false);
    return $str;
}

function parseLESS(){
    $css = '';
    try{
        $theme_dir = trailingslashit(get_template_directory());
        $parser = new Less_Parser();
        $parser->parseFile( file_require($theme_dir.'assets/less/style.less'), trailingslashit(get_site_url()) );
        $css = $parser->getCss();

        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        $css = str_replace(': ', ':', $css);
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
    }
    catch(Exception $e){
        
    }
    return $css;
}

function get_less_to_css_path(){
    $upload_dir = wp_upload_dir();
    $dir = 'themeton';
    $path = trailingslashit($upload_dir['basedir']).$dir;
    $lessname = trailingslashit($path).wp_get_theme()->template.'.css';
    if( !file_exists($lessname) ){
        themeton_less_init();
    }
    $path = trailingslashit($upload_dir['baseurl']).$dir;
    return trailingslashit($path).wp_get_theme()->template.'.css';
}

function get_ultimate_css_url(){
    $upload_dir = wp_upload_dir();
    $dir_path = themeton_less_mkdir();
    $dir = 'themeton';
    if( $dir_path!==false ){
        global $post;
        $lessname = trailingslashit($dir_path).wp_get_theme()->template.'-'.$post->ID.'.css';
        if( file_exists($lessname) && isset($post->ID) ){
            $path = trailingslashit($upload_dir['baseurl']).$dir;
            $path = trailingslashit($path).wp_get_theme()->template.'-'.$post->ID.'.css';
            return $path;
        }else{
            return false;
        }
    }
    else{
        return false;
    }
}


function themeton_less_init(){
    $dir_path = themeton_less_mkdir();
    if( $dir_path!==false ){
        $lessname = trailingslashit($dir_path).wp_get_theme()->template.'.css';
        if( !file_exists($lessname) ){
            $css_content = parseLESS();
            file_put_contents( $lessname, $css_content );
        }
    }
}


function themeton_less_mkdir(){
    $upload_dir = wp_upload_dir();
    if( wp_is_writable($upload_dir['basedir']) ){
        $dir = 'themeton';
        $path = trailingslashit($upload_dir['basedir']).$dir;
        if(! @is_dir($path)){
            if( wp_mkdir_p($path) ){
                return $path;
            }
        }
        else{
            return $path;
        }
    }
    return false;
}




global $get_less_variables_entry, $get_less_variables_data;
$get_less_variables_entry = false;
$get_less_variables_data = array();

function get_less_variables(){
    $less_options = array();

    global $get_less_variables_entry, $get_less_variables_data;
    if($get_less_variables_entry){
        return $get_less_variables_data;
    }

    @ $file_pointer = fopen( file_require(get_template_directory()."/assets/less/variables.less"), "r");
    if ($file_pointer) {
        while (!feof($file_pointer)) {
            $line = fgets($file_pointer, 999);
            $line = trim($line . '');
            if( substr($line, 0, 2)!="//" && strlen($line)>3 && substr($line, 0, 1)=="@" ){
                $splits = explode(':', $line);
                $variable = trim( str_replace('@', '', $splits[0]) );
                $value = trim($splits[1]);
                if( strpos($value, '//')!==false ){
                    $pos = explode('//', $value);
                    $value = trim($pos[0]);
                }
                $value = str_replace(';', '', $value);

                $less_options[] = array('variable'=>$variable, 'value'=>$value);
            }
            else if( substr($line, 0, 4) == '//**' ){
                $variable = 'less-heading';
                $value = str_replace('//**', '', $line);
                $less_options[] = array('variable'=>$variable, 'value'=>html2text($value));
            }
        }
        fclose($file_pointer);
    }
    $get_less_variables_entry = true;
    $get_less_variables_data = $less_options;
    return $less_options;
}




global $get_less_variables_primary_entry, $get_less_variables_primary_data;
$get_less_variables_primary_entry = false;
$get_less_variables_primary_data = array();

function get_less_variables_primary(){
    global $get_less_variables_primary_entry, $get_less_variables_primary_data;
    if($get_less_variables_primary_entry){
        return $get_less_variables_primary_data;
    }

    $less_options = array();
    @ $file_pointer = fopen( file_require(get_template_directory()."/assets/less/variables.less"), "r");
    if ($file_pointer) {
        while (!feof($file_pointer)) {
            $line = fgets($file_pointer, 999);
            $line = trim($line . '');
            if( substr($line, 0, 2)!="//" && strlen($line)>3 && substr($line, 0, 1)=="@" ){
                $splits = explode(':', $line);
                $variable = trim( str_replace('@', '', $splits[0]) );
                $value = trim($splits[1]);
                if( strpos($value, '//')!==false ){
                    $pos = explode('//', $value);
                    $value = trim($pos[0]);
                }
                $value = str_replace(';', '', $value);

                $less_options = array_merge($less_options, array($variable=>$value) );
            }
        }
        fclose($file_pointer);
    }
    $get_less_variables_primary_entry = true;
    $get_less_variables_primary_data = $less_options;
    return $less_options;
}

function get_less_var_val($variables, $param){
    $result = '';
    foreach ($variables as $opt) {
        if( $opt['variable']==$param ){
            return $opt['value'];
        }
    }
    return $result;
}



global $theme_options_less_entry, $theme_options_less_entry_data;
$theme_options_less_entry = false;
$theme_options_less_entry_data = array();

function get_theme_options_less_vars(){
    global $theme_options_less_entry, $theme_options_less_entry_data;

    $vars = get_theme_mod("less_theme_variables");
    if( !empty($vars) && !$theme_options_less_entry ){
        $array_var = unserialize(base64_decode($vars));
        $theme_options_less_entry = true;
        if( is_array($array_var) ){
            $theme_options_less_entry_data = $array_var;
            return $array_var;
        }
    }
    return $theme_options_less_entry_data;
}




function get_page_options_less_vars(){
    global $post;
    if( isset($post->ID) ){
        $less_vars = tt_getmeta("less_page_variables", $post->ID);
        if( !empty($less_vars) ){
            $array_var = unserialize(base64_decode($less_vars));
            if( is_array($array_var) ){
                return $array_var;
            }
        }
    }
    return array();
}


// Get LESS Editor Content
function get_less_editor_content( $less_variables ){
    $less_options = array();
    if( !empty($less_variables) ){
        $tmp = unserialize(base64_decode($less_variables));
        if( is_array($tmp) ){
            $less_options = $tmp;
        }
    }

    $less_content = "";
    @ $file_pointer = fopen( file_require(get_template_directory()."/assets/less/variables.less"), "r");
    if ($file_pointer) {
        while (!feof($file_pointer)) {
            $line = fgets($file_pointer, 999);
            $line = trim($line . '');
            if( substr($line, 0, 2)!="//" && strlen($line)>3 && substr($line, 0, 1)=="@" ){
                $splits = explode(':', $line);
                $variable = trim( str_replace('@', '', $splits[0]) );
                $value = $splits[1];
                $identSize = strlen($value)-strlen(ltrim($value));
                $value = trim($value);
                if( strpos($value, '//')!==false ){
                    $pos = explode('//', $value);
                    $value = trim($pos[0]);
                }
                $value = str_replace(';', '', $value);
                if( isset($less_options[$variable]) && $less_options[$variable]!="" )
                    $value = $less_options[$variable];
                $less_content .= "@$variable:". str_repeat(" ", $identSize) ."$value;\n";
                
            }
            else{
                $less_content .= $line."\n";
            }
        }
        fclose($file_pointer);
    }

    return $less_content;
}


// Generate LESS to CSS
function generate_css_from_less_hook($smof_data){

    $less_variables = array();
    if( isset($smof_data['less_editor']) && $smof_data['less_editor']!='' ){
        $lines = explode("\n", $smof_data['less_editor']);
        foreach ($lines as $current) {
            $line = trim($current . '');
            if( substr($line, 0, 2)!="//" && strlen($line)>3 && substr($line, 0, 1)=="@" ){
                $splits = explode(':', $line);
                $variable = trim( str_replace('@', '', $splits[0]) );
                $value = trim($splits[1]);
                if( strpos($value, '//')!==false ){
                    $pos = explode('//', $value);
                    $value = trim($pos[0]);
                }
                $value = str_replace(';', '', $value);
                if( $variable!='' )
                    $less_variables = array_merge($less_variables, array($variable=>$value));
            }
        }
    }
    // Save LESS Variables
    $encoded_str = base64_encode(serialize($less_variables));
    set_theme_mod( "less_theme_variables", $encoded_str );

    build_main_less_to_css($less_variables);
}


/* Build CSS from LESS */
function build_main_less_to_css($less_variables){
    try{
        $theme_dir = trailingslashit(get_template_directory());
        $parser = new Less_Parser();
        $parser->parseFile( file_require($theme_dir.'assets/less/style.less'), trailingslashit(get_site_url()) );

        $modified_vars = array();
        $less_options = get_less_variables();
        foreach ($less_options as $opt){
            if( $opt['variable']!='less-heading' ){
                if( isset($less_variables[$opt['variable']]) && $less_variables[$opt['variable']]!="" && $less_variables[$opt['variable']]!=$opt['value'] ){
                    $modified_vars = array_merge( $modified_vars, array( $opt['variable']=>trim($less_variables[$opt['variable']]) ) );
                }
            }
        }

        $parser->ModifyVars($modified_vars);
        $css = $parser->getCss();

        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        $css = str_replace(': ', ':', $css);
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);


        $dir_path = themeton_less_mkdir();
        if( $dir_path!==false ){
            $lessname = trailingslashit($dir_path).wp_get_theme()->template.'.css';
            file_put_contents( $lessname, $css );
        }
    }
    catch(Exception $e){
        // wp_die( $e->getMessage() );
    }
}


function generate_css_ultimate_page( $page_id, $params ){
    try{
        if( !empty($params) && isset($params['up_less_editor']) && !empty($params['up_less_editor']) && !empty($page_id) ){
            $theme_dir = trailingslashit(get_template_directory());
            $parser = new Less_Parser();
            $parser->parseFile( file_require($theme_dir.'assets/less/style.less'), trailingslashit(get_site_url()) );

            $less_variables = array();
            $lines = explode("\n", $params['up_less_editor']);
            foreach ($lines as $current) {
                $line = trim($current . '');
                if( substr($line, 0, 2)!="//" && strlen($line)>3 && substr($line, 0, 1)=="@" ){
                    $splits = explode(':', $line);
                    $variable = trim( str_replace('@', '', $splits[0]) );
                    $value = trim($splits[1]);
                    if( strpos($value, '//')!==false ){
                        $pos = explode('//', $value);
                        $value = trim($pos[0]);
                    }
                    $value = str_replace(';', '', $value);
                    $value = trim($value)=='@gray-light' ? '#FFF' : $value;
                    if( $variable!='' )
                        $less_variables = array_merge($less_variables, array($variable=>$value));
                }
            }

            $less_variables['base-font-body'] = isset($params['up_body_font']) && !empty($params['up_body_font']) ? '"'.$params['up_body_font'].'"' : '"'.$less_variables['base-font-body'].'"';
            $less_variables['base-font-heading'] = isset($params['up_heading_font']) && !empty($params['up_heading_font']) ? '"'.$params['up_heading_font'].'"' : '"'.$less_variables['base-font-heading'].'"';
            $less_variables['base-font-menu'] = isset($params['up_menu_font']) && !empty($params['up_menu_font']) ? '"'.$params['up_menu_font'].'"' : '"'.$less_variables['base-font-menu'].'"';
            
            $less_variables['header-height'] = isset($params['up_header_height']) && !empty($params['up_header_height']) ? ((int)$params['up_header_height']).'px' : $less_variables['header-height'];
            
            
            // Save LESS Variables
            $encoded_str = base64_encode(serialize($less_variables));
            update_post_meta($page_id, "_less_page_variables", $encoded_str);


            $modified_vars = array();
            $less_options = get_less_variables();
            foreach ($less_options as $opt){
                if( $opt['variable']!='less-heading' ){
                    $var_name = $opt['variable'];
                    
                    $var_value = trim($less_variables[$var_name]);
                    $var_value = str_replace("\'", "'", $var_value);
                    $var_value = str_replace('\"', '"', $var_value);
                    $var_value = str_replace('\\', '', $var_value);
                    
                    $opval = trim($opt['value']);
                    $opval = str_replace("\'", "'", $opval);
                    $opval = str_replace('\"', '"', $opval);
                    $opval = str_replace('\\', '"', $opval);

                    if( isset( $less_variables[$var_name] ) && !empty($var_value) && $var_value!=$opval ){
                        $modified_vars = array_merge( $modified_vars, array( $opt['variable']=>$var_value ) );
                    }
                }
            }
            
            $parser->ModifyVars($modified_vars);
            $css = $parser->getCss();

            $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
            $css = str_replace(': ', ':', $css);
            $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);


            $dir_path = themeton_less_mkdir();
            if( $dir_path!==false ){
                $lessname = trailingslashit($dir_path).wp_get_theme()->template.'-'.$page_id.'.css';
                file_put_contents( $lessname, $css );
            }
        }
    }
    catch(Exception $e){
        wp_die( $e->getMessage() );
    }
}



?>