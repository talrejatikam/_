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
	'labels_title'          => array(
		'name' => __( 'Labels Settings', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_labels_title',
	),

	'points_label_singular' => array(
		'name'      => __( 'Singular label replacing "point"', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Point', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_points_label_singular',
	),

	'points_label_plural'   => array(
		'name'      => __( 'Plural label replacing "points"', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Points', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_points_label_plural',
	),

	'labels_title_end'      => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_labels_title_end',
	),
);

return array( 'labels' => $section1 );
