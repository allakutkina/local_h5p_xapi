<?php
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
}

$ADMIN->add('localplugins', $settings);

