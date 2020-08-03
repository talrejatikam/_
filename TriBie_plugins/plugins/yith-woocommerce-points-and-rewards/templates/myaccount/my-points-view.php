<?php
/**
 * My Points
 *
 * Shows total of user's points account page
 *
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$points = get_user_meta( get_current_user_id(), '_ywpar_user_total_points', true );
$points = ( '' === $points ) ? 0 : $points;
?>
<h2><?php echo wp_kses_post( apply_filters( 'ywpar_my_account_my_points_title', __( 'My Points', 'yith-woocommerce-points-and-rewards' ) ) ); ?></h2>

<p><?php wp_kses_post( printf( _n( 'You have <strong>%s point</strong>', 'You have <strong>%s points</strong>', esc_html( $points ), 'yith-woocommerce-points-and-rewards' ), esc_html( $points ) ) ); ?></p>