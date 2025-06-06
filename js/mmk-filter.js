// mmk-product-filter/js/mmk-filter.js

jQuery(document).ready(function($) {
    // When the Category dropdown changes
    $('#category').change(function() {
        var category = $(this).val();
        var data = {
            action: 'mmk_get_makes',
            category: category,
        };

        // Disable and reset Make, Model, Year dropdowns and the Filter button
        $('#make').prop('disabled', true).html('<option value="">' + mmk_ajax.loading_text + '</option>');
        $('#model').prop('disabled', true).html('<option value="">' + mmk_ajax.select_model_text + '</option>');
        $('#year').prop('disabled', true).html('<option value="">' + mmk_ajax.select_year_text + '</option>');
        $('#filter-button').prop('disabled', true);

        if (category) {
            // AJAX call to get Makes based on selected Category
            $.post(mmk_ajax.ajax_url, data, function(response) {
                $('#make').prop('disabled', false).html(response);
            });
        } else {
            // If no Category is selected, reset the Make dropdown
            $('#make').prop('disabled', true).html('<option value="">' + mmk_ajax.select_make_text + '</option>');
        }
    });

    // When the Make dropdown changes
    $('#make').change(function() {
        var make = $(this).val();
        var category = $('#category').val();
        var data = {
            action: 'mmk_get_models',
            category: category,
            make: make,
        };

        // Disable and reset Model and Year dropdowns and the Filter button
        $('#model').prop('disabled', true).html('<option value="">' + mmk_ajax.loading_text + '</option>');
        $('#year').prop('disabled', true).html('<option value="">' + mmk_ajax.select_year_text + '</option>');
        $('#filter-button').prop('disabled', true);

        if (make) {
            // AJAX call to get Models based on selected Make and Category
            $.post(mmk_ajax.ajax_url, data, function(response) {
                $('#model').prop('disabled', false).html(response);
            });
        } else {
            // If no Make is selected, reset the Model dropdown
            $('#model').prop('disabled', true).html('<option value="">' + mmk_ajax.select_model_text + '</option>');
        }
    });

    // When the Model dropdown changes
    $('#model').change(function() {
        var make = $('#make').val();
        var model = $(this).val();
        var category = $('#category').val();
        var data = {
            action: 'mmk_get_years',
            category: category,
            make: make,
            model: model,
        };

        // Disable and reset Year dropdown and the Filter button
        $('#year').prop('disabled', true).html('<option value="">' + mmk_ajax.loading_text + '</option>');
        $('#filter-button').prop('disabled', true);

        if (model) {
            // AJAX call to get Years based on selected Make, Model, and Category
            $.post(mmk_ajax.ajax_url, data, function(response) {
                $('#year').prop('disabled', false).html(response);
            });
        } else {
            // If no Model is selected, reset the Year dropdown
            $('#year').prop('disabled', true).html('<option value="">' + mmk_ajax.select_year_text + '</option>');
        }
    });

    // When the Year dropdown changes
    $('#year').change(function() {
        var year = $(this).val();
        if (year) {
            // Enable the Filter button when Year is selected
            $('#filter-button').prop('disabled', false);
        } else {
            // Disable the Filter button if Year is not selected
            $('#filter-button').prop('disabled', true);
        }
    });

    // When the Filter button is clicked
    $('#filter-button').click(function(e) {
        e.preventDefault();
        var category = $('#category').val();
        var make = $('#make').val();
        var model = $('#model').val();
        var year = $('#year').val();

        // Ensure the shop URL ends with a slash
        var shopUrl = mmk_ajax.shop_url;
        if (!shopUrl.endsWith('/')) {
            shopUrl += '/';
        }

        // Build the query parameters
        var queryParams = [];

        if (category) queryParams.push('mmk_category=' + encodeURIComponent(category));
        if (make) queryParams.push('mmk_make=' + encodeURIComponent(make));
        if (model) queryParams.push('mmk_model=' + encodeURIComponent(model));
        if (year) queryParams.push('mmk_year=' + encodeURIComponent(year));

        // Redirect to shop page with query parameters
        window.location.href = shopUrl + '?' + queryParams.join('&');
    });
});
