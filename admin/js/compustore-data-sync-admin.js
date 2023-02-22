(function ($) {
    'use strict';


    jQuery(document).ready(function ($) {
        $('#compu_store_sync_form').submit(function (e) {
            e.preventDefault();

            // Get form data
            let form_data = $(this).serialize();
let compu_map_sync_message = $('#compu_map_message')
            compu_map_sync_message.textContent =''
            // Send form data via AJAX
            $.ajax({
                type: 'POST',
                url: compu_map_sync.ajax_url,
                data: {
                    action: 'compu_map_sync_action',
                    _ajax_nonce: compu_map_sync.compu_msync_nonce,
                    form_data: form_data
                },
                beforeSend: function(response) {

                    compu_map_sync_message.html("Loading.........");
                },
                success: function (response) {
                    // Handle response from server
                    console.log(response.data);
                    compu_map_sync_message.html(response.data)
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });

        });
    });

})(jQuery);
