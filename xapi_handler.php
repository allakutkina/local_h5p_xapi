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
 * @copyright  2025 Alla Kutkina, Dr. Björn Rudzewitz, 
 *             Hector Research Institute of Education Sciences and Psychology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

require_sesskey();
$statement = required_param('statement', PARAM_RAW);

// make sure we are getting a valid statement
$statementData = json_decode($statement, true);
if (empty($statementData) || 
        !array_key_exists('actor', $statementData) || 
        !array_key_exists('verb', $statementData) || 
        !array_key_exists('object', $statementData)) {
    http_response_code(400); 
    echo json_encode(['error' => 'Invalid xAPI statement']);
    exit;
}
//store the statement in the database if the statement is not accepted by LRS
if (!$response) {
    store_statement($statement);
}

// Send the statement to the LRS (function defined in lib.php).
$response = send_statement($statement);

// Return the response from the LRS.
if ($response) {
    echo json_encode(['success' => true, 'response' => $response]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to send xAPI statement to the LRS']);
    store_statement($statement); // Store the statement in case of failure
}


