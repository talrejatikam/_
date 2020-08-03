jQuery(document).ready( function($){
    "use strict";

    var $body = $('body');


    $( document ).on( 'click' , '.ywpar-button-message', function( e ) {

        e.preventDefault();
        var $t = $(this);
        if( $t.hasClass('ywpar-button-percentage-discount') ){
            $t.next().find('form').submit();
        }else{
            $('.ywpar_apply_discounts_container').slideToggle();
        }

    });

    $( document ).on( 'click' , '#ywpar_apply_discounts', function( e ) {
        e.preventDefault();
        $('#ywpar_input_points_check').val(1);
        $(this).parents('form').submit();

    });



    $(document).on( 'qv_loader_stop', function(){
        //if( $body.hasClass('single-product') ){

        $.fn.yith_ywpar_variations();
        //}
    } );

    $(document.body).on('updated_cart_totals, added_to_cart, update_checkout', function () {

        // cart messages

        var $message_container = $('#yith-par-message-cart');

        if ( $message_container.length > 0 ) {

            $.ajax({
                url       : yith_wpar_general.wc_ajax_url.toString().replace('%%endpoint%%', 'ywpar_update_cart_messages'),
                type      : 'POST',
                beforeSend: function () {
                },
                success   : function (res) {
                    if( '' !== res ){
                        $message_container.show().html( res );
                    }else{
                        $message_container.hide();
                    }
                }
            });

        }

        // cart rewards messages

        var $message_reward_container = $('#yith-par-message-reward-cart');

        if( $message_reward_container.length === 0 ){

            var $coupon_form = $('.woocommerce-form-coupon');

            $coupon_form.after('<div id="yith-par-message-reward-cart" class="woocommerce-cart-notice woocommerce-cart-notice-minimum-amount woocommerce-info"></div>');
        }

        if ( $(document).find('#yith-par-message-reward-cart').length > 0 ) {

            $.ajax({
                url       : yith_wpar_general.wc_ajax_url.toString().replace('%%endpoint%%', 'ywpar_update_cart_rewards_messages'),
                type      : 'POST',
                beforeSend: function () {
                },
                success   : function (res) {
                    if( '' !== res ){
                        $(document).find('#yith-par-message-reward-cart').show().html( res );
                    }else{
                        $(document).find('#yith-par-message-reward-cart').hide();
                    }

                }
            });

        }

    });





});