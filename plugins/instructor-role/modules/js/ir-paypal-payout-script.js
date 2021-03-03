jQuery(document).ready(function() {
    // Display instructor paypal email on toggling payment method to paypal payouts.
    jQuery('.ir-commission-payment-method').on('change', function() {
        if ( 'paypal-payout' == jQuery(this).val() ) {
            jQuery( '.ir-payout-email' ).show();
        } else {
            jQuery( '.ir-payout-email' ).hide();
        }
    });

    // Make manual/payout commission payment
    jQuery('#ir_pay_click').click(function (e) {
        e.preventDefault();
        var update_commission = jQuery(this);
        update_commission.parent().find('.wdm_ajax_loader').show();
        var total_paid = parseFloat(jQuery('#wdm_total_amount_paid_price').val());
        var amount_paid = parseFloat(jQuery('#wdm_amount_paid_price').val());
        var enter_amount = parseFloat(jQuery('#wdm_pay_amount_price').val());
        var instructor_id = jQuery('#instructor_id').val();
        var payment_method = jQuery('.ir-commission-payment-method:checked').val();
        var ir_nonce = jQuery('#ir_nonce').val();
        // ir_commission_paypal_payout_payment

        // Validate empty amount
        if ('' == enter_amount || 0 >= enter_amount || isNaN(enter_amount)) {
            alert(wdm_commission_data.enter_amount);
            update_commission.parent().find('.wdm_ajax_loader').hide();
            return false;
        }

        // Validate amount value
        if (enter_amount > amount_paid) {
            alert(wdm_commission_data.enter_amount_less_than);
            update_commission.parent().find('.wdm_ajax_loader').hide();
            return false;
        }

        // Validate amount value
        if (enter_amount > amount_paid) {
            alert(wdm_commission_data.enter_amount_less_than);
            update_commission.parent().find('.wdm_ajax_loader').hide();
            return false;
        }

        // Validate payment method
        if ( ! payment_method.length ) {
            alert( ir_commission_data.payment_method_empty );
            update_commission.parent().find('.wdm_ajax_loader').hide();
            return false;
        }

        if ( 'manual' == payment_method ) {
            jQuery.ajax({
                method: 'post',
                url : wdm_commission_data.ajax_url,
                dataType:'JSON',
                data : {
                    action : 'wdm_amount_paid_instructor',
                    total_paid : total_paid,
                    amount_tobe_paid : amount_paid,
                    enter_amount : enter_amount,
                    instructor_id : instructor_id
                },
                success :  function (response) {
                    jQuery.each(response,function ( index,val ) {
                        switch (index) {
                            case "error":
                                alert(val);
                                update_commission.parent().find('.wdm_ajax_loader').hide();
                              break;
                            case "success":
                                jQuery('#wdm_total_amount_paid_price').attr('value',val.total_paid);
                                jQuery('#wdm_amount_paid_price').attr('value',val.amount_tobe_paid);
                                jQuery('#wdm_pay_amount_price').attr('value','');
                                jQuery( '#wdm_pay_amount_price' ).val('');
                                jQuery('#wdm_total_amount_paid').text(val.total_paid);
                                jQuery('#wdm_amount_paid').text(val.amount_tobe_paid);
                                update_commission.parent().find('.wdm_ajax_loader').hide();
                                if (val.amount_tobe_paid == 0) {
                                    jQuery('#wdm_pay_click').remove();
                                }
                                alert(wdm_commission_data.added_successfully);
                            break;
                        }
                    });
                }
            });
        } else {
            jQuery.ajax({
                method: 'post',
                url : wdm_commission_data.ajax_url,
                dataType:'JSON',
                data : {
                    action : 'ir_payout_transaction',
                    total_paid : total_paid,
                    amount_tobe_paid : amount_paid,
                    enter_amount : enter_amount,
                    instructor_id : instructor_id,
                    ir_nonce : ir_nonce
                },
                success :  function (response) {
                    jQuery.each(response,function (index,val) {
                        switch (index) {
                            case "error":
                                alert(val);
                                update_commission.parent().find('.wdm_ajax_loader').hide();
                              break;
                            case "success":
                                jQuery('#wdm_total_amount_paid_price').attr('value',val.paid_earnings);
                                jQuery('#wdm_amount_paid_price').attr('value',val.unpaid_earnings);
                                jQuery('#wdm_pay_amount_price').attr('value','');
                                jQuery('#wdm_total_amount_paid').text(val.paid_earnings);
                                jQuery('#wdm_amount_paid').text(val.unpaid_earnings);
                                update_commission.parent().find('.wdm_ajax_loader').hide();
                                if (val.unpaid_earnings == 0) {
                                    jQuery('#ir_pay_click').remove();
                                }
                                alert(wdm_commission_data.added_successfully);
                            break;
                        }
                    });
                },
                error : function( jqXHR, exception ) {
                    var msg = '';
                    if ( 0 === jqXHR.status ) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    alert(msg);
                },
                timeout: 10000
            });
        }
    });

    // Toggle payout row details
    jQuery( '.ir-payout-row' ).on( 'click', function() {
        var $row = jQuery( this );
        var batch_id = $row.find('.ir-payout-batch-id').data('batch-id');

        // Check if data already fetched
        if ( jQuery( '.footable-row-detail .ir-payout-transactions-details-'+ batch_id ).length ) {
            return;
        }

        jQuery.ajax({
            url: ir_commission_data.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: 'ir-get-payout-transaction-details',
                batch_id: batch_id,
                ir_nonce: jQuery('#ir_get_payout_nonce').val()
            },
            complete: function() {
                var $black_screen = jQuery('.footable-row-detail .ir-black-screen-' + batch_id );
                $black_screen.css('display', 'none');
            },
            success: function( response ) {
                if ( 'success' == response.type ) {
                    jQuery( '.footable-row-detail .ir-payout-transactions-details-'+ batch_id ).html( response.html );
                }
            },
            error : function( jqXHR, exception ) {
                var msg = '';
                if ( 0 === jqXHR.status ) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            },
            timeout: 10000
        });
    });
});
