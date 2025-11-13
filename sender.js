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
 * This script listens for xAPI events from H5P content and sends them to a server-side handler.
 *
 * @package    local_h5p_xapi
 * @copyright  2025 Alla Kutkina, Dr. Björn Rudzewitz, 
 *             Hector Research Institute of Education Sciences and Psychology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Initializes the script when the window loads and sets up event listeners for xAPI events.
 */

window.onload = function() {
    require(['jquery'], function($) {
        if (typeof H5P !== 'undefined' && typeof H5P.externalDispatcher !== 'undefined') {
            // listen for xAPI events from H5P content
            H5P.externalDispatcher.on('xAPI', (event) => {
                console.log("caught xAPI event");
                let statement = event.data.statement;
                statement = addCourseId(statement);
                statement = addTimestamp(statement);
                send($, statement);
            });
        } 
        
        // Check in case there is an Iframe that contains H5P content
        else if (document.getElementsByTagName('iframe').length > 0) {
            // listen for xAPI events from H5P content in an iframe
            var iframes = document.getElementsByTagName('iframe');
            // loop through all iframes
            for (var i = 0; i < iframes.length; i++) { 
                if (iframes[i].src.indexOf('h5p') !== -1) {    
                    iframes[i].contentWindow.H5P.externalDispatcher.on('xAPI', (event) => {
                        let statement = event.data.statement;
                        statement = addCourseId(statement); // Add course ID to the statement
                        statement = addTimestamp(statement);
                        send($, statement);
                    });
                }
            }
        }
        else{

            console.warn('No H5P content here.');
        }
    
    });
}


/**
 * Sends an xAPI statement to the server-side handler.
 *
 * @param {Object} $ - jQuery object.
 * @param {Object} statement - The xAPI statement to send.
 */

function send($, statement) {
    $.ajax({
        url: M.cfg.wwwroot + '/local/h5p_xapi/xapi_handler.php',
        type: 'POST',
        data: {
            sesskey: M.cfg.sesskey, // Include the session key for security
            statement: JSON.stringify(statement)
        },
        success: function (response) {
            console.log('xAPI statement sent:', response);
        },
        error: function (xhr, status, error) {
            console.error('Failed to send xAPI statement:', error);
        }
    });
}

/**
 * Adds the course ID to the xAPI statement's context.
 *
 * @param {Object} statement - The xAPI statement to modify.
 * @returns {Object} The modified xAPI statement with course ID added.
 */

function addCourseId(statement) {
    const courseId = M.cfg.courseId; // Retrieve the course ID from the Moodle configuration
    if (courseId) {
        statement.context.contextActivities = statement.context.contextActivities || {}
        statement.context.contextActivities.grouping = [{id: M.cfg.wwwroot + '/course/view.php?id=' + courseId}];
    }
    return statement;
}

/**
 * Adds a timestamp to the xAPI statement.
 *
 * @param {Object} statement - The xAPI statement to modify.
 * @returns {Object} The modified xAPI statement with timestamp added.
 */
function addTimestamp(statement) {
    const timestamp = new Date().toISOString();
    statement.timestamp = timestamp;
    return statement;
}