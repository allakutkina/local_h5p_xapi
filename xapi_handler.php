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
 * Handles incoming xAPI statements and forwards them to the LRS.
 *
 * @package    local_h5p_xapi
 * 
 * @copyright  2025 Alla Kutkina, Dr. Björn Rudzewitz, 
 *             Hector Research Institute of Education Sciences and Psychology
 * 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_login();
require_sesskey();
$PAGE->set_url(new moodle_url('/local/h5p_xapi/xapi_handler.php'));
$PAGE->set_context(context_system::instance());

$statement = required_param('statement', PARAM_RAW);

// Send the statement to the LRS (function defined in lib.php).
$result = send_statement($statement);

// Return the response from the LRS.
// If returns error, store in the db

if ($result) {
    if ($result['httpcode'] == 200) {
        echo json_encode(['success' => true, 'response' => $result['response']]);
    }
    else {
        echo json_encode(['success' => false, 'response' => $result['response']]);
        store_statement($statement); // Store the statement in case of failure
    }
} else {
    http_response_code(503); // Service Unavailable
    echo json_encode(['error' => 'LRS is unavailable or did not respond']);
    store_statement($statement); // Store the statement in case of failure
}


