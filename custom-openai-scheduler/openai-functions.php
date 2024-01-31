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
    $openai_endpoint = 'https://api.openai.com/v1/chat/completions';
    //$openai_endpoint = "https://localhost/dashboard/";

    // Your input prompt to generate both title and body
    $messages = [
        [
            "role" => "user",
            "content" => "Generate a blog post with title and body on the topic of: $topic"
        ]
    ];
    
    // Construct the request body
    $request_body = json_encode([
        'model' => 'gpt-4',
        'messages' => $messages,
        'max_tokens' => 2500,
    ]);

    if ($debug_mode) {
        // Show the request if debug mode is enabled
        print_r(parse_url(trim($openai_endpoint)));
        echo '<strong>Request:</strong><br>';
        echo '<pre>';
        print_r($request_body);
        echo '</pre>';
    }

    /*// Make a request to the OpenAI API
    $response = wp_safe_remote_post($openai_endpoint, [
        'body' => $request_body,
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
        'timeout' => 30, // Set your desired timeout value in seconds
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
*/
    // Extract the generated content from the response
    //$decoded_response = json_decode(wp_remote_retrieve_body($response), true);
    
    $decoded_response = [
        "choices" => [
            ["message" => ["content" => "Title: Unleashing the Power of Salesforce Commerce Cloud for 
            Business Success\nIf there is ever a time in history when eCommerce is vital for business success"]]
        ]
    ];
    $generated_content = $decoded_response['choices'][0]['message']["content"];

    if ($debug_mode) {
        // Show debug information
        echo '<strong>Response:</strong><br>';
        echo '<pre>';
        print_r($decoded_response);
        echo "\ncontent is:\n";
        print_r($generated_content);
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
