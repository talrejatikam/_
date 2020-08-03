/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH Infinite Scrolling Premium
 * @version 1.0.0
 */

jQuery(document).ready( function($) {
    "use strict";

    var block_loader    = ( typeof yith_ywpar_admin !== 'undefined' ) ? yith_ywpar_admin.block_loader : false;


    /****
     * remove a row in custom type field
     ****/
    $(document).on('click', '.extrapoint-options .ywpar-remove-row', function () {
        var $t = $(this),
            current_row = $t.closest('.extrapoint-options');
        current_row.remove();
    });

    /****
     * add a row in custom type field
     ****/
    $(document).on('click', '#yith_woocommerce_points_and_rewards_extra-points .ywpar-add-row', function() {
        var $t = $(this),
            wrapper = $t.closest('.yith-plugin-fw-field-wrapper'),
            current_option = $t.closest('.extrapoint-options'),
            current_index = parseInt( current_option.data('index')),
            clone = current_option.clone(),
            options = wrapper.find('.extrapoint-options'),
            max_index = 1;

        options.each(function(){
            var index = $(this).data('index');
            if( index > max_index ){
                max_index = index;
            }
        });

        var new_index = max_index + 1;
        clone.attr( 'data-index', new_index );

        var fields = clone.find("[name*='list']");
        fields.each(function(){
            var $t = $(this),
                name = $t.attr('name'),
                id =  $t.attr('id'),

                new_name = name.replace('[list]['+current_index+']', '[list]['+new_index+']'),
                new_id = id.replace('[list]['+current_index+']', '[list]['+new_index+']');

            $t.attr('name', new_name);
            $t.attr('id', new_id);
            $t.val('');

        });

        clone.find('.ywpar-remove-row').removeClass('hide-remove');
        clone.find('.chosen-container').remove();

        wrapper.append(clone);

    });


    /**
     * Reset points to all customer
     */
    $('.ywrac_reset_points').on('click', function(e) {
        e.preventDefault();

        var conf = confirm( yith_ywpar_admin.reset_points_confirm );

        if( ! conf ){
            return false;
        }

        var container   = $(this).closest('.yith-plugin-fw-field-wrapper');

        container.find('.response').remove();

        if (block_loader) {
            container.block({
                message   : null,
                overlayCSS: {
                    background: 'transparent',
                    opacity   : 0.5,
                    cursor    : 'none'
                }
            });
        }

        $.ajax({
            type    : 'POST',
            url     : yith_ywpar_admin.ajaxurl,
            dataType: 'json',
            data    : 'action=ywpar_reset_points&security=' + yith_ywpar_admin.reset_points,
            success : function (response) {
                container.unblock();
                container.append('<span class="response">'+response+'</span>');
            }
        });

    });





});
