// mmk-filter/js/mmk-filter.js

jQuery(document).ready(function($) {
    $('#make').change(function() {
        var make = $(this).val();
        var data = {
            action: 'mmk_get_models',
            make: make,
        };

        $('#model').prop('disabled', true).html('<option value="">' + mmk_ajax.loading_text + '</option>');
        $('#year').prop('disabled', true).html('<option value="">' + mmk_ajax.select_year_text + '</option>');
        $('#filter-button').prop('disabled', true);

        if (make) {
            $.post(mmk_ajax.ajax_url, data, function(response) {
                $('#model').prop('disabled', false).html(response);
            });
        } else {
            $('#model').prop('disabled', true).html('<option value="">' + mmk_ajax.select_model_text + '</option>');
        }
    });

    $('#model').change(function() {
        var make = $('#make').val();
        var model = $(this).val();
        var data = {
            action: 'mmk_get_years',
            make: make,
            model: model,
        };

        $('#year').prop('disabled', true).html('<option value=""><?php _e( 'Loading...', 'mmk' ); ?></option>');
        $('#filter-button').prop('disabled', true);

        if (model) {
            $.post(mmk_ajax.ajax_url, data, function(response) {
                $('#year').prop('disabled', false).html(response);
            });
        } else {
            $('#year').prop('disabled', true).html('<option value=""><?php _e( 'Select Year', 'mmk' ); ?></option>');
        }
    });

    $('#year').change(function() {
        var year = $(this).val();
        if (year) {
            $('#filter-button').prop('disabled', false);
        } else {
            $('#filter-button').prop('disabled', true);
        }
    });

    $('#filter-button').click(function(e) {
        e.preventDefault();
        var make = $('#make').val();
        var model = $('#model').val();
        var year = $('#year').val();

        // Redirect to shop page with query parameters
        window.location.href = mmk_ajax.shop_url + '?make=' + make + '&model=' + model + '&year=' + year;
    });
});
