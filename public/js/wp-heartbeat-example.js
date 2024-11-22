jQuery(document).ready(function () {

    //Get the data passed in via the localisation function in enqueue_scripts.php
    let userId = heartbeat_object.user_id;
    let pageId = heartbeat_object.page_id;

    let firstName;
    let lastName;
    let email;

    var pageToDoHeartbeat = 999;
    /**
     * Even though the script is only loaded on a certain page by enqueue_scripts.php, because setting a low heartbeat interval can cause
     * problems if it is always the case, it isn't a bad idea to add another check in the JS itself.
     */
    if (pageId == pageToDoHeartbeat) {
        //The default for the heartbeat is usually between 15 and 120 seconds. But if we need something to happen rapidly we can set it even lower
        wp.heartbeat.interval(7); //Set to 7 seconds

        //This is assuming it is running on a page with a Gravity Form
        firstName = jQuery('#input_9_98_3');
        lastName = jQuery('#input_9_98_6');
        email = jQuery('#input_9_99');
    }

    // Send to the server (once every amount of seconds specified by wp.heartbeat.interval();. Or the default interval for the site)
    jQuery(document).on('heartbeat-send', function (event, data) {
        //Package data from the jQuery to send to the server to be used in the callback function
        data.custom_heartbeat_data = {
            userId: userId,
        };
    });

    // Receive data from the server
    jQuery(document).on('heartbeat-tick', function (event, data) {
        if (data.entity_exists) {
            //If key with data exists, set the value of gravity form fields on the page with data received via the backend
            firstName.val(data.example_entity.first_name);
            lastName.val(data.example_entity.last_name);
            email.val(data.example_entity.email);
        }
    });
});