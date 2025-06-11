<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.              
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details. 
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin functions for the local_h5p_xapi plugin.
 *
 * @package    local_h5p_xapi
 * @copyright  2025 Alla Kutkina, Dr. Björn Rudzewitz, 
 *             Hector Research Institute of Education Sciences and Psychology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
