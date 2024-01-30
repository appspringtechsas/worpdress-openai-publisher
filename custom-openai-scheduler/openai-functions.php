<?php
# OpenAI API related functions

function custom_openai_generate_content($topic) {
    $api_key = get_option('custom_openai_scheduler_api_key', '');
    $debug_mode = get_option('custom_openai_scheduler_debug_mode', 0);

    // Check if API key is present
    if (empty($api_key)) {
        return null;
    }

    // Your OpenAI API endpoint
    //$openai_endpoint = 'https://api.openai.com/v1/engines/gpt-4.0-turbo/completions';
    $openai_endpoint = "https://localhost/dashboard/";

    // Your input prompt to generate both title and body
    $prompt = "Generate a blog post with title and body on the topic of: $topic";

    // Construct the request body
    $request_body = json_encode([
        'model' => 'gpt-4.0-turbo',
        'prompt' => $prompt,
        'max_tokens' => 500,
    ]);

    if ($debug_mode) {
        // Show the request if debug mode is enabled
        print_r(parse_url(trim($openai_endpoint)));
        echo '<strong>Request:</strong><br>';
        echo '<pre>';
        print_r($request_body);
        echo '</pre>';
    }

    // Make a request to the OpenAI API
    $response = wp_safe_remote_post($openai_endpoint, [
        'body' => $request_body,
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
        'timeout' => 15, // Set your desired timeout value in seconds
    ]);

    $response_code = wp_remote_retrieve_response_code($response);

    if (is_wp_error($response)) {
        // Handle WP_Error, e.g., timeout or other errors
        if ($debug_mode) {
            echo '<strong>Error Response:</strong><br>';
            echo '<pre> Code:' .  $response_code;
            print_r($response);
            echo '</pre>';
        }
        return null;
    }

    

    if ($response_code !== 200) {
        // Handle non-200 response
        if ($debug_mode) {
            echo '<strong>Non-200 Response:</strong><br>';
            echo '<pre>';
            print_r($response);
            echo '</pre>';
        }
        return null;
    }

    // Extract the generated content from the response
    $decoded_response = json_decode(wp_remote_retrieve_body($response), true);
    $generated_content = $decoded_response['choices'][0]['text'];

    if ($debug_mode) {
        // Show debug information
        echo '<strong>Response:</strong><br>';
        echo '<pre>';
        print_r($decoded_response);
        echo '</pre>';
    }

    // Check if the content is not empty
    if (!empty($generated_content)) {
        return $generated_content;
    } else {
        return null;
    }
}


?>
