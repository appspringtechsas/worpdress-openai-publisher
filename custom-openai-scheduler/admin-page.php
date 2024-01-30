<?php
# Admin page and menu functions

function custom_openai_scheduler_add_admin_menu() {
    add_menu_page(
        'OpenAI Scheduler Settings',
        'OpenAI Scheduler',
        'manage_options',
        'openai-scheduler-settings',
        'custom_openai_scheduler_settings_page'
    );
}

function custom_openai_scheduler_settings_page() {
    // Admin page content
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Handle manual process execution
    if (isset($_POST['manual_process_submit'])) {
        custom_openai_scheduler_generate_and_publish();
        echo '<div class="updated"><p>Manual process executed</p></div>';
    }

    // Handle form submission for settings
    if (isset($_POST['custom_openai_scheduler_submit'])) {
        $frequency = sanitize_text_field($_POST['frequency']);
        $api_key = sanitize_text_field($_POST['api_key']);
        $topics = sanitize_textarea_field($_POST['topics']); // Use sanitize_textarea_field for textarea
        $executed_topics = sanitize_textarea_field($_POST['executed_topics']); // Use sanitize_textarea_field for textarea
        $debug_mode = isset($_POST['debug_mode']) ? 1 : 0; // Check if debug mode checkbox is checked

        update_option('custom_openai_scheduler_frequency', $frequency);
        update_option('custom_openai_scheduler_api_key', $api_key);
        update_option('custom_openai_scheduler_topics', $topics);
        update_option('custom_openai_scheduler_executed_topics', $executed_topics);
        update_option('custom_openai_scheduler_debug_mode', $debug_mode);

        echo '<div class="updated"><p>Settings saved</p></div>';
    }

    $frequency = get_option('custom_openai_scheduler_frequency', 'daily');
    $api_key = get_option('custom_openai_scheduler_api_key', '');
    $topics = get_option('custom_openai_scheduler_topics', '');
    $executed_topics = get_option('custom_openai_scheduler_executed_topics', '');
    $debug_mode = get_option('custom_openai_scheduler_debug_mode', 0); // Default to 0 if not set

    ?>
    <div class="wrap">
        <h2>OpenAI Scheduler Settings</h2>

        <!-- Add a button for manual process execution -->
        <form method="post" action="">
            <input type="submit" name="manual_process_submit" class="button-secondary" value="Run Manual Process" />
        </form>

        <br/>

        <form method="post" action="">
            <label for="frequency">Frequency:</label>
            <select name="frequency" id="frequency">
                <option value="daily" <?php selected($frequency, 'daily'); ?>>Daily</option>
                <option value="hourly" <?php selected($frequency, 'hourly'); ?>>Hourly</option>
                <option value="weekly" <?php selected($frequency, 'weekly'); ?>>Weekly</option>
                <option value="monthly" <?php selected($frequency, 'monthly'); ?>>Monthly</option>
            </select><br><br>

            <label for="api_key">OpenAI API Key:</label>
            <input type="text" name="api_key" id="api_key" value="<?php echo esc_attr($api_key); ?>" /><br><br>

            <label for="topics">Topics (comma-separated):</label>
            <textarea name="topics" id="topics" rows="3"><?php echo esc_textarea($topics); ?></textarea><br><br>

            <label for="executed_topics">Executed Topics:</label>
            <textarea name="executed_topics" id="executed_topics" rows="3"><?php echo esc_textarea($executed_topics); ?></textarea><br><br>

            <label for="debug_mode">
                <input type="checkbox" name="debug_mode" id="debug_mode" <?php checked($debug_mode, 1); ?> />
                Enable Debug Mode
            </label><br><br>

            <input type="submit" name="custom_openai_scheduler_submit" class="button-primary" value="Save Changes" />
        </form>
    </div>
    <?php
}
