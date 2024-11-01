(function( $ ) {
	'use strict';

    // execute when the DOM is ready
    $(document).ready(function () {

/*        $('#acf_fb_form_settings').insertAfter('#acf_location');
        $('#acf_fb_form_settings').insertAfter('#acf-field-group-locations');
        var is_pro = jQuery('#acf_fb_is_pro').val();*/

        // js 'click' event triggered on the wpp_add_price field
        $(document).on( 'click', '#wpp_add_price', function () {

        	var add_price = 'add';   
        	var row_max = $('#row_max').val();
        	row_max++;

            var num_price = 0;
            var add_row = true;

            $('.wpp-prices tbody tr').each( function(e) {
                ++num_price;

                if (num_price >= 2) {
                    add_row = false;
                    $('.wpp-prices tbody tr td.wpp_product_add input').attr('disabled', 'disabled');
                    $('.wpp-prices tbody tr td.wpp_product_add input').attr('id', '#');
                }
            });
        	
            if (add_row) {
            	$.post(
            		wpp_meta_box_obj.url,
            		{
                       action: 'wpp_price_ajax',             // POST data, action
                       _add_price: add_price, 			// POST data, _add_price     
                       _row_max: row_max, 				// POST data, _row_max
                   	},
            		function(data) {            			
            			if (data) {
                            //console.log(data);
                            $('.wpp-prices tbody').append(data);
                            $('#row_max').val(row_max);
            			} else {
                            console.log('no-data');
            			}
            		},
            		'html'
            	);
            }
        });

        // js 'click' event triggered on the wpp_remove_price field
        $(document).on( 'click', '#wpp_remove_price', function () {

            $('.wpp-prices tbody tr td.wpp_product_add input').removeAttr('disabled');
            $('.wpp-prices tbody tr td.wpp_product_add input').attr('id', 'wpp_add_price');
            

        	var remove_price = 'remove';
        	var cur_row = $(this).closest('tr').attr('id');
        	$('.wpp-prices tbody tr').remove('#' + cur_row);
        	
        });

    });

})( jQuery );