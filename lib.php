<?php

defined('MOODLE_INTERNAL') || die();
/**
 * Callback function to include JavaScript on every page.
 */
function local_h5p_xapi_extend_navigation(global_navigation $navigation) {
    global $PAGE;

    // Add the JavaScript file to every page.
    $PAGE->requires->js('/local/h5p_xapi/sender.js');
}


/**
 * Function to send xAPI statement to the LRS.
 *
 * @param array $statement The xAPI statement to send.
 * @return string The response from the LRS.
 */

function send_statement($statement) {
    // Get plugin configuration settings
    $endpoint = get_config('local_h5p_xapi', 'lrs_endpoint') ?? 'https://example.com/lrs';
    $username = get_config('local_h5p_xapi', 'lrs_username') ?? 'username';
    $password = get_config('local_h5p_xapi', 'lrs_password') ?? 'password';
    
    $url = $endpoint . '/statements';
    
    // Prepare the xAPI statement
    
    
    // cURL setup
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode($username . ':' . $password),
        'Content-Type: application/json',
        'X-Experience-API-Version: 1.0.3'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $statement);

    // Send request and capture the response
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        // Log error if needed
        mtrace('cURL Error: ' . curl_error($ch));
    }
    curl_close($ch);
    
    
    return $response;
}
