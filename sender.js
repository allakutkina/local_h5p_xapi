
window.onload = function() {
    require(['jquery'], function($) {
        if (typeof H5P !== 'undefined') {
            // listen for xAPI events from H5P content
            H5P.externalDispatcher.on('xAPI', (event) => {
                console.log("caught xAPI event");
                const statement = event.data.statement;
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
                        const statement = event.data.statement;
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
