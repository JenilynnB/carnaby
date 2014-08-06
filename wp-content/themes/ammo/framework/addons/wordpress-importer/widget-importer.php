<?php

function tt_widget_importer($data){
    global $wp_registered_sidebars;
    global $wp_registered_widget_controls;
    
    $widget_controls = $wp_registered_widget_controls;

    // Get all available widgets site supports
    $available_widgets = array();
    foreach ( $widget_controls as $widget ) {
        if ( ! empty( $widget['id_base'] ) && !isset( $available_widgets[$widget['id_base']] ) ){
            $available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
            $available_widgets[$widget['id_base']]['name'] = $widget['name'];
        }
    }
    // Get all existing widget instances
    $widget_instances = array();
    foreach ( $available_widgets as $widget_data ) {
        $widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
    }

    // Loop import data's sidebars
    foreach ( $data as $sidebar_id => $widgets ){
        if ( 'wp_inactive_widgets' == $sidebar_id ){ continue; }
        if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
            $sidebar_available = true;
            $use_sidebar_id = $sidebar_id;
        } else {
            $sidebar_available = false;
            $use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
        }

        // Loop widgets
        foreach ( $widgets as $widget_instance_id => $widget ){
            $fail = false;
            // Get id_base (remove -# from end) and instance ID number
            $id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
            $instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );
            // Does site support this widget?
            if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
                $fail = true;
            }

            if ( ! $fail && isset( $widget_instances[$id_base] ) ){
                // Get existing widgets in this sidebar
                $sidebars_widgets = get_option( 'sidebars_widgets' );
                $sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

                // Loop widgets with ID base
                $single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
                foreach ( $single_widget_instances as $check_id => $check_widget ) {
                    // Is widget in same sidebar and has identical settings?
                    if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {
                        $fail = true;
                        break;
                    }
                }
            }

            if ( ! $fail ) {

                // Add widget instance
                $single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
                $single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
                $single_widget_instances[] = (array) $widget; // add it

                    // Get the key it was given
                    end( $single_widget_instances );
                    $new_instance_id_number = key( $single_widget_instances );

                    // If key is 0, make it 1
                    // When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
                    if ( '0' === strval( $new_instance_id_number ) ) {
                        $new_instance_id_number = 1;
                        $single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
                        unset( $single_widget_instances[0] );
                    }

                    // Move _multiwidget to end of array for uniformity
                    if ( isset( $single_widget_instances['_multiwidget'] ) ) {
                        $multiwidget = $single_widget_instances['_multiwidget'];
                        unset( $single_widget_instances['_multiwidget'] );
                        $single_widget_instances['_multiwidget'] = $multiwidget;
                    }

                    // Update option with new widget
                    update_option( 'widget_' . $id_base, $single_widget_instances );

                // Assign widget instance to sidebar
                $sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
                $new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
                $sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
                update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data
            }
        }
    }
    
}

?>