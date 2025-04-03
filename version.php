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
//
// This file is part of the local_h5p_xapi plugin for Moodle
//
// This file contains the version information for the plugin
// and is used by Moodle to manage plugin updates and compatibility.
//
// @package    local_h5p_xapi


//The version file to tell moodle version info about the plugin

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_h5p_xapi';  
$plugin->version = 2025040202;  
$plugin->requires = 2022041900;
$plugin->maturity = MATURITY_STABLE;
$plugin->release = 2025040200;
$plugin->supported = [401, 405];
