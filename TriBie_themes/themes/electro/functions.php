<?php
/**
 * electro engine room
 *
 * @package electro
 */

/**
 * Initialize all the things.
 */
require get_template_directory() . '/inc/init.php';


/**
 * Note: Do not add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * http://codex.wordpress.org/Child_Themes
 */
 

function dokan_custom_seller_registration_required_fields( $required_fields ) {
    $required_fields['uid_id'] = __( 'Please enter your Aadhar Number', 'dokan-custom' );

    return $required_fields;
};

add_filter( 'dokan_seller_registration_required_fields', 'dokan_custom_seller_registration_required_fields' );

/**
 * Save custom field data
 *
 * @since 1.0.0
 *
 * @param int   $vendor_id
 * @param array $dokan_settings
 *
 * @return void
 */
function dokan_custom_new_seller_created( $vendor_id, $dokan_settings ) {
    $post_data = wp_unslash( $_POST );

    $uid_id =  $post_data['uid_id'];
    /**
     * This will save gst_id value with the `dokan_custom_uid_id` user meta key
     */
    update_user_meta( $vendor_id, 'dokan_custom_uid_id', $uid_id );
}

add_action( 'dokan_new_seller_created', 'dokan_custom_new_seller_created', 10, 2 );

  /* Add custom profile fields (call in theme : echo $curauth->fieldname;) */ 

add_action( 'dokan_seller_meta_fields', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { ?>

    <?php if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }
        if ( ! user_can( $user, 'dokandar' ) ) {
            return;
        }
         $uid  = get_user_meta( $user->ID, 'dokan_custom_uid_id', true );
     ?>
         <tr>
                    <th><?php esc_html_e( 'Aadhar Number', 'dokan-lite' ); ?></th>
                    <td>
                        <input type="text" name="uid_id" class="regular-text" value="<?php echo esc_attr($uid); ?>"/>
                    </td>
         </tr>
    <?php
 }

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }
    update_usermeta( $user_id, 'dokan_custom_uid_id', $_POST['uid_id'] );
}