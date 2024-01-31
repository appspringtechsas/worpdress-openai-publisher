<?php
# Scheduler functions

function  custom_openai_scheduler_activate() {
    wp_schedule_event(time(), get_option('custom_openai_scheduler_frequency', 'daily'), 'custom_openai_scheduler_event_hook');
}

function custom_openai_scheduler_deactivate() {
    wp_clear_scheduled_hook('custom_openai_scheduler_event_hook');
}

function custom_openai_scheduler_generate_and_publish() {
    $topics = get_option('custom_openai_scheduler_topics', '');
    $executed_topics = get_option('custom_openai_scheduler_executed_topics', '');

    if (empty($topics)) {
        return;
    }

    $topics_array = explode(',', $topics);

    // Take a topic from the list
    $selected_topic = array_shift($topics_array);

    // Generate content for the selected topic
    $generated_content = custom_openai_generate_content($selected_topic);

    if ($generated_content) {
        // Create a new post
        $post_id = wp_insert_post([
            'post_title' => $selected_topic, // Set the post title to the suggested title by GPT-4
            'post_content' => $generated_content,
            'post_status' => 'publish',
            'post_type' => 'post',
        ]);

        // Check if the post was created successfully
        if ($post_id) {
            // Add the post to the menu
            //$menu_name = 'Blog'; // Replace with the desired menu name
            //$menu_item_id = custom_openai_add_to_menu($post_id, $menu_name);

            // Move the topic to the executed topics list
            custom_openai_move_to_executed_topics($selected_topic);

            // Update the remaining topics in the settings
            custom_openai_update_remaining_topics($topics_array);
        }
    }
}

function custom_openai_add_to_menu($post_id, $menu_name, $menu_item_id) {
    // Find the menu item under the specified menu
    $menu_items = wp_get_nav_menu_items($menu_name);
    $parent_menu_item = null;

    foreach ($menu_items as $menu_item) {
        if ($menu_item->ID == $menu_item_id) {
            $parent_menu_item = $menu_item;
            break;
        }
    }

    if (!$parent_menu_item) {
        return new WP_Error('menu_not_found', 'Parent menu item not found.');
    }

    // Add the new post to the menu
    $menu_order = 0;
    $nav_item_data = [
        'menu-item-object-id' => $post_id,
        'menu-item-db-id' => 0,
        'menu-item-url' => get_permalink($post_id),
        'menu-item-title' => get_the_title($post_id),
        'menu-item-status' => 'publish',
        'menu-item-type' => 'post_type',
        'menu-item-object' => 'post',
        'menu-item-parent-id' => $parent_menu_item->ID,
        'menu-item-position' => ++$menu_order,
    ];

    return wp_update_nav_menu_item(0, 0, $nav_item_data);
}

function custom_openai_move_to_executed_topics($selected_topic) {
    $executed_topics = get_option('custom_openai_scheduler_executed_topics', '');
    $executed_topics_array = explode(',', $executed_topics);
    $executed_topics_array[] = $selected_topic;
    update_option('custom_openai_scheduler_executed_topics', implode(',', $executed_topics_array));
}

function custom_openai_update_remaining_topics($topics_array) {
    update_option('custom_openai_scheduler_topics', implode(',', $topics_array));
}


?>
