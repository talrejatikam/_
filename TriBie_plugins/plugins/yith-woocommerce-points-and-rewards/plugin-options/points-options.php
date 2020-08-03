<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$currency = get_woocommerce_currency();
$section1 = array(
	'points_title'                => array(
		'name' => __( 'Points and rewards settings', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_points_title',
	),


	'earn_points_conversion_rate' => array(
		'name'      => __( 'Assign points for every product purchased', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __(
			'Choose how many points per product will be earned based on the currency.
Please, note: points are awarded on a product basis and not on the cart total. ',
			'yith-woocommerce-points-and-rewards'
		),
		'yith-type' => 'options-conversion',
		'type'      => 'yith-field',
		'default'   => array(
			$currency => array(
				'points' => 1,
				'money'  => 10,
			),
		),
		'id'        => 'ywpar_earn_points_conversion_rate',
	),
	'remove_points_coupon'        => array(
		'name'      => __( 'Remove points when coupons are used', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'If you use coupons, their value will be removed from cart total and consequently points gained will be reduced as well.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'id'        => 'ywpar_remove_points_coupon',
		'default'   => 'yes',
	),

	'points_title_end'            => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_points_title_end',
	),

	'rewards_point_option'        => array(
		'name' => __( 'How to award discounts when customers use their available points', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_rewards_point_option',
	),


	'enable_rewards_points'       => array(
		'name'      => __( 'Enable points redemption', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'If you disable this option, you will still be able to manage points manually.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enable_rewards_points',
	),


	'rewards_conversion_rate'     => array(
		'name'      => __( 'Reward Conversion Rate', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Choose how to calculate the discount when customers use their available points.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'options-conversion',
		'type'      => 'yith-field',
		'class'     => 'fixed_method',
		'default'   => array(
			$currency => array(
				'points' => 100,
				'money'  => 1,
			),
		),
		'id'        => 'ywpar_rewards_conversion_rate',
	),


	'rewards_point_option_end'    => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_rewards_point_option_end',
	),


);

return apply_filters( 'ywpar_points_settings', array( 'points' => $section1 ) );
