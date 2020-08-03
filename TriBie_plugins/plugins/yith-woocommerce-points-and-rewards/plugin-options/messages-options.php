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

$section1 = array(
	// MESSAGE ON CART
	'message_on_cart_title'        => array(
		'name' => __( 'Show Message on Cart page', 'yith-woocommerc e-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_message_on_cart_title',
	),

	'enabled_cart_message'         => array(
		'name'      => __( 'Show Message in Cart', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enabled_cart_message',
	),

	'cart_message'                 => array(
		'name'      => __( 'Cart Message', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'yith-type' => 'textarea',
		'type'      => 'yith-field',
		'default'   => __( 'If you proceed to checkout, you will earn <strong>{points}</strong> {points_label}!', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_cart_message',
		'deps'      => array(
			'id'    => 'ywpar_enabled_cart_message',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'message_on_cart_title_end'    => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_message_on_cart_title_end',
	),

	'message_reward_title'         => array(
		'name' => __( 'Show Reward Message in Cart/Checkout', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_message_reward_title',
	),

	'enabled_rewards_cart_message' => array(
		'name'      => __( 'Show Reward Message in Cart/Checkout', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enabled_rewards_cart_message',
	),

	'rewards_cart_message'         => array(
		'name'      => __( 'Reward Message on Cart/Checkout page', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'yith-type' => 'textarea',
		'type'      => 'yith-field',
		'default'   => __( 'Use <strong>{points}</strong> {points_label} for a <strong>{max_discount}</strong> discount on this order!', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_rewards_cart_message',
		'deps'      => array(
			'id'    => 'ywpar_enabled_rewards_cart_message',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'message_reward_title_end'     => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_message_reward_title_end',
	),


);

return array( 'messages' => $section1 );
