<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

require_sesskey();


// make sure we are getting a statement
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

// defined in lib.php
$response = send_statement($statement);

// Return the response from the LRS.
if ($response) {
    echo json_encode(['success' => true, 'response' => $response]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to send xAPI statement to the LRS']);
}

