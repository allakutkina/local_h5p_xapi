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
    
    $statementData = json_decode($statement, true);
    if (empty($statementData) || 
            !array_key_exists('actor', $statementData) || 
            !array_key_exists('verb', $statementData) || 
            !array_key_exists('object', $statementData)) {
        http_response_code(400); 
        echo json_encode(['error' => 'Invalid xAPI statement']);
        exit;
    }
    // Get plugin configuration settings
    $endpoint = get_config('local_h5p_xapi', 'lrs_endpoint') ?? 'https://example.com/lrs';
    $username = get_config('local_h5p_xapi', 'lrs_username') ?? 'username';
    $password = get_config('local_h5p_xapi', 'lrs_password') ?? 'password';
    $use_username = get_config('local_h5p_xapi', 'id_schema') ?? 1;
    $url = $endpoint . '/statements';
    
    // Prepare the xAPI statement
    global $USER;
    $email = $USER->email;
    $user = $USER->username;
    global $PAGE;
    $moodle_url = $PAGE->url->out();


    if ($use_username) {
        $statementData['actor'] = [
            'objectType' => 'Agent',
            'account' => [
                'name' => $user,
                'homePage' => $moodle_url
            ]
        ];
    }
        
    // cURL setup
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode($username . ':' . $password),
        'Content-Type: application/json',
        'X-Experience-API-Version: 1.0.3'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($statementData));

    // Send request and capture the response
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // fix this: it returns not found from the moodle, not the information from the LRS
    if (curl_errno($ch)) {
        // Log error if needed
        mtrace('cURL Error: ' . curl_error($ch)); 
    }
    curl_close($ch);

    // Return both the response and the HTTP code
    return [
        'httpcode' => $httpcode,
        'response' => $response];
}

function store_statement($statement) {
    global $DB;

    // Prepare the statement data for storage
    $data = new stdClass();
    $data->timestamp = time();
    $data->statement_data = $statement;
    // Insert the statement into the database
    $DB->insert_record('local_h5p_xapi', $data);
}

/*
* this function is responsible for re-sending failed xAPI statements
* to the LRS (Learning Record Store).
*/
function resend_statements() {
    //retrieve unsent statements from the database
    global $DB;
    $unsent_statements = $DB->get_records('h5p_xapi');

    foreach ($unsent_statements as $statement) {
        $response = send_statement(json_decode($statement->statement_data));

        if ($response) {
            // Mark the statement as sent
            $statement->sent = 1;
            $DB->update_record('h5p_xapi', $statement);
        }
    }
}

// TODO: how long to I store failed statements? Do I resend them regularly with cron? How is it done with logstore xAPI?