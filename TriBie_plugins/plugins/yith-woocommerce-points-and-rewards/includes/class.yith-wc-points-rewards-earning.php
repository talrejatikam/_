<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Points and Rewards
 *
 * @class   YYITH_WC_Points_Rewards_Earning
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Points_Rewards_Earning' ) ) {

	/**
	 * Class YITH_WC_Points_Rewards_Earning
	 */
	class YITH_WC_Points_Rewards_Earning {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Points_Rewards_Earning
		 */
		protected static $instance;


		/**
		 * Single instance of the class
		 *
		 * @var bool
		 */
		protected $points_applied = false;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Points_Rewards_Earning
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			// add point when
			add_action( 'woocommerce_payment_complete', array( $this, 'add_order_points' ) );
			add_action( 'woocommerce_order_status_processing', array( $this, 'add_order_points' ) );
			add_action( 'woocommerce_order_status_completed', array( $this, 'add_order_points' ) );

			add_action( 'woocommerce_checkout_order_processed', array( $this, 'save_points_earned_from_cart' ) );

			// remove point when the order is refunded or cancelled
			add_action( 'woocommerce_order_status_refunded', array( $this, 'remove_points' ) );
			add_action( 'woocommerce_order_status_cancelled', array( $this, 'remove_points' ) );

		}

		/**
		 * Save the points that are in the cart in a post meta of the order
		 *
		 * @param   int $order_id
		 *
		 * @since   1.5.0
		 * @author  Emanuela Castorina
		 * @return  void
		 */
		public function save_points_earned_from_cart( $order_id ) {
			$points_from_cart = $this->calculate_points_on_cart();
			$order            = wc_get_order( $order_id );
			yit_save_prop( $order, 'ywpar_points_from_cart', $points_from_cart );
		}


		/**
		 * Calculate the total points in the carts
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 * @param bool $integer
		 *
		 * @return int $points
		 */
		public function calculate_points_on_cart( $integer = true ) {

			$items = WC()->cart->get_cart();

			$tot_points = 0;
			foreach ( $items as $item => $values ) {
				$product_point        = $this->calculate_product_points( $values['data'], false );
				$total_product_points = $product_point * $values['quantity'];
				if ( WC()->cart->applied_coupons && YITH_WC_Points_Rewards()->get_option( 'remove_points_coupon' ) == 'yes' && isset( WC()->cart->discount_cart ) && WC()->cart->discount_cart > 0 ) {
					if ( $values['line_subtotal'] ) {
						$total_product_points = ( $values['line_total'] / $values['line_subtotal'] ) * $total_product_points;
					}
				}

				$tot_points += $total_product_points;
			}

			$tot_points = ( $tot_points < 0 ) ? 0 : $tot_points;

			if ( $integer ) {

				if ( apply_filters( 'ywpar_floor_points', false ) ) {
					$tot_points = floor( $tot_points );
				} else {
					$tot_points = round( $tot_points );
				}
			}

			return apply_filters( 'ywpar_calculate_points_on_cart', $tot_points );
		}

		/**
		 * Calculate the points of a product/variation for a single item
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 * @param WC_Product|int $product
		 * @param bool           $integer
		 *
		 * @param string         $currency
		 *
		 * @return int $points
		 */
		public function calculate_product_points( $product, $integer = true, $currency = '' ) {

			if ( is_numeric( $product ) ) {
				$product = wc_get_product( $product );
			}

			if ( ! is_object( $product ) ) {
				return 0;
			}

			$points = $this->get_point_earned( $product, 'product', false );

			if ( $integer ) {

				if ( apply_filters( 'ywpar_floor_points', false ) ) {
					$points = floor( $points );
				} else {
					$points = round( $points );
				}
			}

			// Let third party plugin to change the points earned for this product
			return apply_filters( 'ywpar_get_product_point_earned', $points, $product );
		}

		/**
		 * Return the global points of an object
		 *
		 * @param        $object
		 * @param string $type
		 * @param bool   $integer
		 *
		 * @param string $currency
		 * @return int
		 * @author  Emanuela Castorina
		 *
		 * @since   1.0.0
		 */
		public function get_point_earned( $object, $type = 'order', $integer = false, $currency = '' ) {

			$conversion = $this->get_conversion_option( $currency );

			$price = 0;
			switch ( $type ) {
				case 'order':
					$price = $object->get_total();
					break;
				case 'product':
					$price = ( get_option( 'woocommerce_tax_display_cart' ) == 'excl' ) ? yit_get_price_excluding_tax( $object ) : yit_get_price_including_tax( $object );
					break;
				default:
			}

			$price  = apply_filters( 'ywpar_get_point_earned_price', $price, $currency, $object );
			$points = (float) $price / $conversion['money'] * $conversion['points'];

			return $integer ? round( $points ) : $points;
		}

		/**
		 * Add points to the order from order_id
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 * @param $order_id
		 *
		 * @return void
		 */
		public function add_order_points( $order_id ) {

			$order = wc_get_order( $order_id );

			$customer_user = $order->get_customer_id();
			$is_set        = yit_get_prop( $order, '_ywpar_points_earned', true );

			// return if the points are just calculated
			if ( is_array( $this->points_applied ) && in_array( $order_id, $this->points_applied ) || $is_set != '' ) {
				return;
			}

			$currency   = yit_get_prop( $order, 'currency' );
			$tot_points = yit_get_prop( $order, 'ywpar_points_from_cart', true );

			// update order meta and add note to the order
			yit_save_prop(
				$order,
				array(
					'_ywpar_points_earned'     => $tot_points,
					'_ywpar_conversion_points' => $this->get_conversion_option( $currency ),
				)
			);

			$this->points_applied[] = $order_id;
			$plural                 = YITH_WC_Points_Rewards()->get_option( 'points_label_plural' );
			$order->add_order_note( sprintf( __( 'Customer earned %1$d %2$s for this purchase.', 'yith-woocommerce-points-and-rewards' ), $tot_points, $plural ), 0 );

			if ( $customer_user > 0 ) {
				YITH_WC_Points_Rewards()->add_point_to_customer( $customer_user, $tot_points, 'order_completed', '', $order_id );
			}

		}

		/**
		 * Add Point to the user.
		 *
		 * @param      $user_id
		 * @param      $points
		 * @param      $action
		 * @param      $order_id
		 *
		 * @param bool $register_log
		 * @return void
		 * @deprecated
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 */
		public function add_points( $user_id, $points, $action, $order_id, $register_log = true ) {
			YITH_WC_Points_Rewards()->add_point_to_customer( $user_id, $points, $action, '', $order_id );
		}

		/**
		 * Calculate approx the points of product inside an order.
		 *
		 * @param      $product_id
		 * @param bool $integer
		 * @param      $order_item
		 *
		 * @param      $currency
		 * @return int
		 */
		public function calculate_product_points_in_order( $product_id, $integer = true, $order_item, $currency ) {

			$qty                 = $order_item['qty'] ? $order_item['qty'] : 1;
			$product             = wc_get_product( $product_id );
			$points_from_price   = $this->get_point_earned_from_price( $order_item['line_subtotal'] / $qty, $integer, $currency );
			$points_from_product = false;
			if ( $product instanceof WC_Product ) {
				$points_from_product = $this->calculate_product_points( $product, $currency );
			}

			if ( $points_from_product !== false ) {
				$points = apply_filters( 'ywpar_get_calculate_product_points_in_order', min( $points_from_price, $points_from_product ), $points_from_price, $points_from_product, $product_id, $integer, $order_item );
			} else {
				$points = $points_from_price;
			}

			return $points;
		}


		/**
		 * Return the global points of an object from price
		 *
		 * @param      $price
		 * @param bool $integer
		 *
		 * @param      $currency
		 * @return int
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 */
		public function get_point_earned_from_price( $price, $integer = false, $currency ) {
			$conversion = $this->get_conversion_option( $currency );

			$points = $price / $conversion['money'] * $conversion['points'];

			return $integer ? round( $points ) : $points;
		}

		/**
		 * Return the global points of an object
		 *
		 * @param string $currency
		 * @return  array
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function get_conversion_option( $currency = '' ) {

			$default_currency = apply_filters( 'ywpar_multi_currency_current_currency', get_woocommerce_currency() );
			$currency         = empty( $currency ) ? $default_currency : $currency;

			$conversions = YITH_WC_Points_Rewards()->get_option( 'earn_points_conversion_rate' );
			$conversion  = isset( $conversions[ $currency ] ) ? $conversions[ $currency ] : array(
				'money'  => 0,
				'points' => 0,
			);

			$conversion['money']  = ( empty( $conversion['money'] ) ) ? 1 : $conversion['money'];
			$conversion['points'] = ( empty( $conversion['points'] ) ) ? 1 : $conversion['points'];

			return apply_filters( 'ywpar_conversion_points_rate', $conversion );
		}

		/**
		 * @param $order_id
		 */
		public function remove_points( $order_id ) {
			$order        = wc_get_order( $order_id );
			$point_earned = yit_get_prop( $order, '_ywpar_points_earned', true );

			if ( $point_earned == '' ) {
				return;
			}

			$user_id = method_exists( $order, 'get_customer_id' ) ? $order->get_customer_id() : yit_get_prop( $order, '_customer_user', true );

			$points = $point_earned;

			// order total refunded
			if ( $order->get_status() == 'refunded' ) {
				$points = $point_earned - $this->get_point_earned( $order );
			}

			if ( $user_id > 0 ) {
				$current_point = get_user_meta( $user_id, '_ywpar_user_total_points', true );
				update_user_meta( $user_id, '_ywpar_user_total_points', $current_point - $points );
				YITH_WC_Points_Rewards()->register_log( $user_id, 'order_' . $order->get_status(), $order_id, - $points );
			}

		}



	}


}

/**
 * Unique access to instance of YITH_WC_Points_Rewards_Earning class
 *
 * @return \YITH_WC_Points_Rewards_Earning
 */
function YITH_WC_Points_Rewards_Earning() {
	return YITH_WC_Points_Rewards_Earning::get_instance();
}

