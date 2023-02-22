(function ($) {
    'use strict';
    jQuery(document).ready(function ($) {
        $('#compu_store_sync_form').submit(function (e) {
            e.preventDefault();

            // Get form data
            let form_data = $(this).serialize();
            let compu_map_sync_message = $('#compu_map_message')
            compu_map_sync_message.textContent = ''
            // Send form data via AJAX
            $.ajax({
                type: 'POST',
                url: compu_map_sync.ajax_url,
                data: {
                    action: 'compu_map_sync_action',
                    _ajax_nonce: compu_map_sync.compu_msync_nonce,
                    form_data: form_data
                },
                beforeSend: function (response) {
                    $('#compu_menu_sync').prop('disabled', true);
                    $('#loading_div').show()
                    compu_map_sync_message.html("Hang on tight. This process might take some time to finish 😒.........");

                },
                success: function (response) {
console.log(response)
                    compu_map_sync_message.html(response.data)
                    $('#compu_menu_sync').prop('disabled', false);
                    $('#loading_div').hide()

                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });

        });
    });

})(jQuery);
