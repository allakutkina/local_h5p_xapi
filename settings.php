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
 * Settings for the local_h5p_xapi plugin.
 *
 * @package    local_h5p_xapi
 *
 * @copyright  2025 Alla Kutkina, Dr. Björn Rudzewitz,
 *             Hector Research Institute of Education Sciences and Psychology
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings = new admin_settingpage('h5p_xapi_settings', get_string('pluginname', 'local_h5p_xapi'));

if ($hassiteconfig) {
    $settings->add(
    new admin_setting_configtext('local_h5p_xapi/lrs_endpoint',
    get_string('lrs_endpoint', 'local_h5p_xapi'),
    get_string('lrs_endpoint_desc', 'local_h5p_xapi'),
    'https://example.com/lrs', PARAM_URL));

    $settings->add(
    new admin_setting_configtext('local_h5p_xapi/lrs_username',
    get_string('lrs_username', 'local_h5p_xapi'),
    get_string('lrs_username_desc', 'local_h5p_xapi'),
    'username', PARAM_TEXT));

    $settings->add(
    new admin_setting_configtext('local_h5p_xapi/lrs_password',
    get_string('lrs_password', 'local_h5p_xapi'),
    get_string('lrs_password_desc', 'local_h5p_xapi'),
    'password', PARAM_TEXT));

    // Add ID schema setting

    $settings->add(
        new admin_setting_configcheckbox('local_h5p_xapi/id_schema',
        get_string('id_schema', 'local_h5p_xapi'),
        get_string('id_schema_desc', 'local_h5p_xapi'),
        1)
    );
}

$ADMIN->add('localplugins', $settings);

