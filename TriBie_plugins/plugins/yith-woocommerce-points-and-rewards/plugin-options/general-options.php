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
	'general_title'        => array(
		'name' => __( 'General Settings', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_general_option',
	),

	'enabled'              => array(
		'name'      => __( 'Enable Points and Rewards', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable the plugin.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enabled',
	),
	'reset_points'         => array(
		'name'         => __( 'Reset Points', 'yith-woocommerce-points-and-rewards' ),
		'desc'         => __( 'Click on the button to reset all the points earned and redeemed by customers', 'yith-woocommerce-points-and-rewards' ),
		'type'         => 'yith-field',
		'yith-type'    => 'text-button',
		'button-class' => 'ywrac_reset_points',
		'button-name'  => __( 'Reset Points', 'yith-woocommerce-points-and-rewards' ),
		'id'           => 'ywpar_reset_points',
	),

	'general_settings_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_general_option_end',
	),

);

return array( 'general' => $section1 );
