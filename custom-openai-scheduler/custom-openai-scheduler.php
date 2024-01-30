<?php
/*
Plugin Name: Custom OpenAI Scheduler
Description: Schedule tasks to generate and publish articles using OpenAI GPT-4.
Version: 1.0
Author: Your Name
*/

require_once plugin_dir_path(__FILE__) . 'admin-page.php';
require_once plugin_dir_path(__FILE__) . 'scheduler-functions.php';
require_once plugin_dir_path(__FILE__) . 'openai-functions.php';

# Initialize the plugin
function custom_openai_scheduler_init() {
    # Add activation/deactivation hooks
    register_activation_hook(__FILE__, 'custom_openai_scheduler_activate');
    register_deactivation_hook(__FILE__, 'custom_openai_scheduler_deactivate');

    # Add scheduling hook
    add_action('custom_openai_scheduler_event_hook', 'custom_openai_scheduler_generate_and_publish');

    # Load admin page
    add_action('admin_menu', 'custom_openai_scheduler_add_admin_menu');

    add_filter( 'http_request_host_is_external', function( $external, $host, $url ) {
        print_r($external);
        print_r($host);
        print_r($url);
        if(strcasecmp($host,"api.openai.com") == 0 || strcasecmp($host,"localhost") == 0){
            print_r("is open ai:" . $host) ;
            return true;
        }
        print_r("is NOT open ai");
        return false;
    }, 10 , 3);
}


# Run the initialization function
custom_openai_scheduler_init();
