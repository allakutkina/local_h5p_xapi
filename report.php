<?php
// filepath: /home/kutkina/work/plugin/h5p_xapi/report.php
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
 * Report page for local_h5p_xapi plugin.
 *
 * @package    local_h5p_xapi
 * @copyright  2025 Alla Kutkina, Dr. Björn Rudzewitz,
 *             Hector Research Institute of Education Sciences and Psychology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/h5p_xapi/lib.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_url(new moodle_url('/local/h5p_xapi/report.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_h5p_xapi') . ' - ' . get_string('report', 'local_h5p_xapi'));
$PAGE->set_heading(get_string('pluginname', 'local_h5p_xapi') . ' - ' . get_string('report', 'local_h5p_xapi'));

global $DB, $OUTPUT;

// Handle individual resend.
$resendid = optional_param('resendid', 0, PARAM_INT);
$resendresult = '';
if ($resendid) {
    $record = $DB->get_record('local_h5p_xapi', ['id' => $resendid]);
    if ($record) {
        $result = send_statement($record->statement_data);
        if ($result && isset($result['httpcode']) && $result['httpcode'] == 200) {
            $resendresult = $OUTPUT->notification(get_string('resend_success', 'local_h5p_xapi'), 'notifysuccess');
        } else {
            $resendresult = $OUTPUT->notification(get_string('resend_failed', 'local_h5p_xapi') . ': ' . ($result['response'] ?? ''), 'notifyproblem');
        }
    }
}

// Handle batch resend.
if (optional_param('resendbatch', 0, PARAM_INT)) {
    $records = $DB->get_records('local_h5p_xapi');
    $success = 0;
    $fail = 0;
    foreach ($records as $record) {
        $result = send_statement($record->statement_data);
        if ($result && isset($result['httpcode']) && $result['httpcode'] == 200) {
            $success++;
        } else {
            $fail++;
        }
    }
    $resendresult .= $OUTPUT->notification(get_string('batch_resend_result', 'local_h5p_xapi', ['success' => $success, 'fail' => $fail]), $fail ? 'notifyproblem' : 'notifysuccess');
}

// Handle individual remove.
$removeid = optional_param('removeid', 0, PARAM_INT);
$removeresult = '';
if ($removeid) {
    if ($DB->record_exists('local_h5p_xapi', ['id' => $removeid])) {
        $DB->delete_records('local_h5p_xapi', ['id' => $removeid]);
        $removeresult = $OUTPUT->notification(get_string('statement_removed', 'local_h5p_xapi'), 'notifysuccess');
    }
}

// Handle clear all.
if (optional_param('clearall', 0, PARAM_INT)) {
    $DB->delete_records('local_h5p_xapi');
    $removeresult .= $OUTPUT->notification(get_string('all_statements_removed', 'local_h5p_xapi'), 'notifysuccess');
}

// Fetch all records.
$records = $DB->get_records('local_h5p_xapi');

// Output page.
echo $OUTPUT->header();

if (!empty($resendresult)) {
    echo $resendresult;
}

echo $OUTPUT->heading(get_string('report', 'local_h5p_xapi'));

// Batch resend and clear all buttons.
echo '<form method="post" action="' . $PAGE->url . '" style="display:inline">';
echo '<input type="hidden" name="resendbatch" value="1" />';
echo '<button type="submit">' . get_string('resend_all', 'local_h5p_xapi') . '</button>';
echo '</form> ';

echo '<form method="post" action="' . $PAGE->url . '" style="display:inline; margin-left:10px;">';
echo '<input type="hidden" name="clearall" value="1" />';
echo '<button type="submit" onclick="return confirm(\'' . get_string('confirm_clear_all', 'local_h5p_xapi') . '\');">' . get_string('clear_all', 'local_h5p_xapi') . '</button>';
echo '</form>';

// Table of statements.
$table = new html_table();
$table->head = [
    get_string('id', 'local_h5p_xapi'),
    get_string('timestamp', 'local_h5p_xapi'),
    get_string('statement', 'local_h5p_xapi'),
    get_string('actions', 'local_h5p_xapi')
];

foreach ($records as $record) {
    $actions = '<form method="post" action="' . $PAGE->url . '" style="display:inline">';
    $actions .= '<input type="hidden" name="resendid" value="' . $record->id . '" />';
    $actions .= '<button type="submit">' . get_string('resend', 'local_h5p_xapi') . '</button>';
    $actions .= '</form> ';

    $actions .= '<form method="post" action="' . $PAGE->url . '" style="display:inline">';
    $actions .= '<input type="hidden" name="removeid" value="' . $record->id . '" />';
    $actions .= '<button type="submit" onclick="return confirm(\'' . get_string('confirm_remove', 'local_h5p_xapi') . '\');">' . get_string('remove', 'local_h5p_xapi') . '</button>';
    $actions .= '</form>';

    $table->data[] = [
        $record->id,
        userdate($record->timestamp),
        '<pre style="max-width:400px;overflow:auto;">' . s($record->statement_data) . '</pre>',
        $actions
    ];
}

echo html_writer::table($table);

echo $OUTPUT->footer();