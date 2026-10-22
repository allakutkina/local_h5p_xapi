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
 *
 * Plugin functions for the local_h5p_xapi plugin.
 *
 * @package    local_h5p_xapi
 *
 * @copyright  2025 Alla Kutkina, Dr. Björn Rudzewitz,
 *             Hector Research Institute of Education Sciences and Psychology
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once($CFG->libdir . '/filelib.php');


/**
 * Callback function to include JavaScript on every page.
 * @package local_h5p_xapi
 */
function local_h5p_xapi_extend_navigation(global_navigation $navigation) {
    global $PAGE;

    // Add the JavaScript file to every page.
    $PAGE->requires->js('/local/h5p_xapi/sender.js');
}


/**
 * Function to send xAPI $statement to the LRS.
 *
 * @param array $statement The xAPI statement to send.
 * @return array The response from the LRS.
 */
function local_h5p_xapi_send_statement($statement) {
    $statementdata = json_decode($statement, true);
    if (empty($statementdata) ||
            !array_key_exists('actor', $statementdata) ||
            !array_key_exists('verb', $statementdata) ||
            !array_key_exists('object', $statementdata)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid xAPI statement']);
        exit;
    }
    // Get plugin configuration settings.

    $endpoint = get_config('local_h5p_xapi', 'lrs_endpoint') ?? 'https://example.com/lrs';
    $username = get_config('local_h5p_xapi', 'lrs_username') ?? 'username';
    $password = get_config('local_h5p_xapi', 'lrs_password') ?? 'password';
    $useusername = get_config('local_h5p_xapi', 'id_schema') ?? 1;
    $url = $endpoint . '/statements';

    // Prepare the xAPI statement.

    //Problem: When an admin clicks "Resend" in report.php, the actor becomes the administrator, not the original student

    global $USER;
    $email = $USER->email;
    $user = $USER->username;
    global $PAGE;
    $moodleurl = $PAGE->url->out();

    if ($useusername) {
        $statementdata['actor'] = [
            'objectType' => 'Agent',
            'account' => [
                'name' => $user,
                'homePage' => $moodleurl,
            ],
        ];
    } else {
        $statementdata['actor'] = [
            'objectType' => 'Agent',
            'mbox' => $email,
        ];
    }


    // CURL setup.
    $curl = new \curl();

    // Set the request headers.
    $curl->setHeader([
        'Authorization: Basic ' . base64_encode($username . ':' . $password),
        'Content-Type: application/json',
        'X-Experience-API-Version: 1.0.3',
    ]);

    // Set cURL options.
    $curl->setopt([
        'CURLOPT_TIMEOUT' => 5,
    ]);

    // Encode the xAPI statement as JSON.
    $jsondata = json_encode($statementdata);

    if ($jsondata === false) {
        throw new \moodle_exception('errorencoding', 'core');
    }

    // Send the POST request.
    $response = $curl->post($url, $jsondata);

    // Retrieve the HTTP response code.
    $info = $curl->get_info();
    $httpcode = $info['http_code'] ?? 0;

    // Check the result.
    if ($curl->get_errno()) {
        debugging(
            'cURL error: ' . $curl->error,
            DEBUG_DEVELOPER
        );
    } else if ($httpcode < 200 || $httpcode >= 300) {
        debugging(
            'xAPI request failed. HTTP status: ' . $httpcode .
            '. Response: ' . $response,
            DEBUG_DEVELOPER
        );
    }
}

/**
 * Methods that store data in database
 * @param string statment
 */
function local_h5p_xapi_store_statement($statement) {
    global $DB;

    // Prepare the statement data for storage.

    $data = new stdClass();
    $data->timestamp = time();
    $data->statement_data = $statement;
    // Insert the statement into the database.
    $DB->insert_record('local_h5p_xapi', $data);
}

/**
 * this function is responsible for re-sending failed xAPI statements
 * to the LRS (Learning Record Store).
 */
function local_h5p_xapi_resend_statements() {
    // Retrieve unsent statements from the database.

    global $DB;
    $unsentstatements = $DB->get_records('local_h5p_xapi');

    foreach ($unsentstatements as $statement) {
        $response = local_h5p_xapi_send_statement(json_decode($statement->statement_data));

        if ($response) {
            // Instead of marking as sent, we delete the record.
            $DB->delete_records('local_h5p_xapi', ['id' => $statement->id]);
        }
    }
}

