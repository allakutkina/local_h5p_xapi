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
 * Settings for the logstore_h5p_xapi plugin.
 *
 * @package    logstore_h5p_xapi
 *
 * @copyright  2025 Alla Kutkina, Dr. Björn Rudzewitz,
 *             Hector Research Institute of Education Sciences and Psychology
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('h5p_xapi_settings', get_string('pluginname', 'logstore_h5p_xapi'));
    $settings->add(
    new admin_setting_configtext('logstore_h5p_xapi/lrs_endpoint',
    get_string('lrs_endpoint', 'logstore_h5p_xapi'),
    get_string('lrs_endpoint_desc', 'logstore_h5p_xapi'),
    'https://example.com/lrs', PARAM_URL));

    $settings->add(
    new admin_setting_configtext('logstore_h5p_xapi/lrs_username',
    get_string('lrs_username', 'logstore_h5p_xapi'),
    get_string('lrs_username_desc', 'logstore_h5p_xapi'),
    'username', PARAM_TEXT));

    $settings->add(
    new admin_setting_configtext('logstore_h5p_xapi/lrs_password',
    get_string('lrs_password', 'logstore_h5p_xapi'),
    get_string('lrs_password_desc', 'logstore_h5p_xapi'),
    'password', PARAM_TEXT));

    $settings->add(
        new admin_setting_configcheckbox('logstore_h5p_xapi/id_schema',
        get_string('id_schema', 'logstore_h5p_xapi'),
        get_string('id_schema_desc', 'logstore_h5p_xapi'),
        1)
    );
}

$ADMIN->add('logging', $settings);

if ($hassiteconfig) {
    $ADMIN->add('logging', new admin_externalpage(
        'logstore_h5p_xapi_report',
        get_string('report', 'logstore_h5p_xapi'),
        new moodle_url('/admin/tool/log/store/h5p_xapi/report.php'),
        'moodle/site:config'
    ));
}
