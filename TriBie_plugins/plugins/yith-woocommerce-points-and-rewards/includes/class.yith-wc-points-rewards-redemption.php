<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Points and Rewards
 *
 * @class   YITH_WC_Points_Rewards_Redemption
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Points_Rewards_Redemption' ) ) {

	/**
	 * Class YITH_WC_Points_Rewards_Redemption
	 */
	class YITH_WC_Points_Rewards_Redemption {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Points_Rewards_Redemption
		 */
		protected static $instance;

		/**
		 * @var string
		 */
		protected $label_coupon_prefix = 'ywpar_discount';

		/**
		 * @var string
		 */
		protected $coupon_type = 'fixed_cart';
		/**
		 * @var string
		 */
		protected $current_coupon_code = '';

		/**
		 * @var int
		 */
		protected $max_points = 0;

		/**
		 * @var int
		 */
		protected $max_discount = 0;

		/**
		 * @var array
		 */
		protected $args = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Points_Rewards_Redemption
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

			// register the coupon and the point used at checkout
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'add_order_meta' ), 10 );

			// remove points if are used in order
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'deduce_order_points' ), 20 );
			add_action( 'woocommerce_order_status_failed', array( $this, 'remove_redeemed_order_points' ) );
			add_action( 'woocommerce_removed_coupon', array( $this, 'clear_current_coupon' ) );

			add_action( 'woocommerce_order_status_changed', array( $this, 'clear_ywpar_coupon_after_create_order' ), 10, 2 );

			add_action( 'wp_loaded', array( $this, 'apply_discount' ), 30 );
			add_action( 'woocommerce_cart_item_removed', array( $this, 'update_discount' ) );
			add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'update_discount' ) );
			add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'update_discount' ) );
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'update_discount' ), 99 );

			if ( is_user_logged_in() ) {
				// add_filter( 'woocommerce_coupon_message', array( $this, 'coupon_rewards_message' ), 15, 3 );
				add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'coupon_label' ), 10, 2 );
			}

			add_action( 'wp_loaded', array( $this, 'ywpar_set_cron' ) );
			add_action( 'ywpar_clean_cron', array( $this, 'clear_coupons' ) );
		}

		/**
		 * Remove the coupons after that the order is created
		 *
		 * @param WC_Order $order
		 *
		 * @param $status_from
		 *
		 * @return void
		 */
		public function clear_ywpar_coupon_after_create_order( $order, $status_from ) {
			if ( $status_from != 'pending' ) {
				return;
			}
			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}
			$coupon_used = $order->get_coupon_codes();
			if ( $coupon_used ) {
				foreach ( $coupon_used as $coupons_code ) {
					$coupon = new WC_Coupon( $coupons_code );
					if ( $this->check_coupon_is_ywpar( $coupon ) ) {
						$coupon->delete();
					}
				}
			}
		}

		/**
		 * Remove the coupons created dinamically
		 *
		 * @param string $coupon_code The coupon code removed
		 *
		 * @return void
		 */
		public function clear_current_coupon( $coupon_code ) {
			$current_coupon = $this->get_current_coupon();
			if ( $current_coupon instanceof WC_Coupon && $current_coupon->get_code() == $coupon_code ) {
				$current_coupon->delete();
			}
		}

		/**
		 * Add the redeemed points when an order is cancelled
		 * *
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param $order_id
		 *
		 * @return void
		 */
		public function remove_redeemed_order_points( $order_id ) {
			$order           = wc_get_order( $order_id );
			$redemped_points = yit_get_prop( $order, '_ywpar_redemped_points', true );
			$discount_amount = yit_get_prop( $order, '_ywpar_coupon_amount' );

			if ( '' === $redemped_points ) {
				return;
			}

			$customer_user = $order->get_customer_id();
			$points        = $redemped_points;
			$action        = ( current_action() == 'woocommerce_order_fully_refunded' ) ? 'order_refund' : 'order_' . $order->get_status();

			if ( $customer_user ) {
				$current_point                 = get_user_meta( $customer_user, '_ywpar_user_total_points', true );
				$current_discount_total_amount = get_user_meta( $customer_user, '_ywpar_user_total_discount', true );
				update_user_meta( $customer_user, '_ywpar_user_total_discount', $current_discount_total_amount - $discount_amount );

				$new_point = $current_point + $points;
				update_user_meta( $customer_user, '_ywpar_user_total_points', $new_point > 0 ? $new_point : 0 );

				// update the user meta rewarded points
				$this->set_user_rewarded_points( $customer_user, $points );

				YITH_WC_Points_Rewards()->register_log( $customer_user, $action, $order_id, $points );
				$order->add_order_note( sprintf( __( 'Added %1$d %2$s for order %3$s.', 'yith-woocommerce-points-and-rewards' ), $points, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ), YITH_WC_Points_Rewards()->get_action_label( $action ) ), 0 );
			}
		}

		/**
		 * Apply the discount to cart after that the user set the number of points
		 * *
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 * @return void
		 * @throws WC_Data_Exception
		 */
		public function apply_discount() {
			if ( wp_verify_nonce( 'ywpar_input_points_nonce', 'ywpar_apply_discounts' ) || ! is_user_logged_in() || ! isset( $_POST['ywpar_rate_method'] ) || ! isset( $_POST['ywpar_points_max'] ) || ! isset( $_POST['ywpar_max_discount'] ) || ( isset( $_POST['coupon_code'] ) && $_POST['coupon_code'] != '' ) ) {
				return;
			}
			$posted = $_POST;
			$this->apply_discount_calculation( $posted );
		}

		/**
		 * @param      $posted
		 * @param bool   $apply_coupon
		 *
		 * @throws Exception
		 * @throws WC_Data_Exception
		 */
		public function apply_discount_calculation( $posted, $apply_coupon = true ) {

			$max_points   = $posted['ywpar_points_max'];
			$max_discount = $posted['ywpar_max_discount'];
			$discount     = 0;

			if ( $posted['ywpar_rate_method'] == 'fixed' ) {

				if ( ! isset( $posted['ywpar_input_points_check'] ) || $posted['ywpar_input_points_check'] == 0 ) {
					return;
				}

				$input_points = $posted['ywpar_input_points'];

				if ( $input_points == 0 ) {
					return;
				}

				$input_points       = ( $input_points > $max_points ) ? $max_points : $input_points;
				$conversion         = $this->get_conversion_rate_rewards();
				$input_max_discount = $input_points / $conversion['points'] * $conversion['money'];
				// check that is not lg than $max discount
				$input_max_discount = ( $input_max_discount > $max_discount ) ? $max_discount : $input_max_discount;

				if ( $input_max_discount > 0 ) {
					WC()->session->set( 'ywpar_coupon_code_points', $input_points );
					WC()->session->set( 'ywpar_coupon_code_discount', $input_max_discount );
					$discount = $input_max_discount;
					$discount = apply_filters( 'ywpar_adjust_discount_value', $discount );
				};

			}

			WC()->session->set( 'ywpar_coupon_posted', $posted );

			// apply the coupon in cart
			if ( $apply_coupon && $discount ) {

					$coupon = $this->get_current_coupon();
					$is_new = $coupon->get_amount() === '0';

				if ( $coupon->get_discount_type() !== 'fixed_cart' ) {
					$coupon->set_discount_type( 'fixed_cart' );
				}

				if ( $coupon->get_amount() !== $discount ) {
					$coupon->set_amount( $discount );
				}

					$valid = ywpar_coupon_is_valid( $coupon, WC()->cart );

				if ( ! $valid ) {
					$args = array(
						'id'             => false,
						'discount_type'  => 'fixed_cart',
						'individual_use' => false,
						'free_shipping'  => false,
						'usage_limit'    => $this->get_usage_limit(),
					);

					$coupon->add_meta_data( 'ywpar_coupon', 1 );
					$coupon->read_manual_coupon( $coupon->get_code(), $args );

				}

				if ( $is_new || ! empty( $coupon->get_changes() ) ) {
					$coupon->save();
				}
					$coupon_label = $coupon->get_code();

				if ( ywpar_coupon_is_valid( $coupon, WC()->cart ) && ! WC()->cart->has_discount( $coupon_label ) ) {
					WC()->cart->add_discount( $coupon_label );
					$this->update_discount();
				}
			}
		}

		/**
		 * Update the coupon code points and discount
		 *
		 * @since  1.3.0
		 * @author Emanuela Castorina
		 * @return void
		 * @throws WC_Data_Exception
		 */
		public function update_discount() {
			$applied_coupons = WC()->cart->get_applied_coupons();

			if ( $coupon = $this->check_coupon_is_ywpar( $applied_coupons ) ) {
				$posted               = WC()->session->get( 'ywpar_coupon_posted' );
				$ex_tax               = apply_filters( 'ywpar_exclude_taxes_from_calculation', false );
				$coupon_real_discount = WC()->cart->get_coupon_discount_amount( $coupon->get_code(), $ex_tax );
				$max_discount         = $this->calculate_rewards_discount( $coupon_real_discount );

				if ( $max_discount ) {
					$max_points                   = $this->get_max_points();
					$posted['ywpar_max_discount'] = $max_discount;
					$posted['ywpar_points_max']   = $max_points;
					// todo:add undo action
					$apply_coupon = true;// ( current_filter() == 'woocommerce_cart_item_removed' );
					$this->apply_discount_calculation( $posted, $apply_coupon );

				} else {
					WC()->cart->remove_coupon( $coupon->get_code() );
				}
			}
		}

		/**
		 * Return the coupon code
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 * @return string
		 */
		public function get_coupon_code_prefix() {
			return apply_filters( 'ywpar_label_coupon', $this->label_coupon_prefix );
		}

		/**
		 * Return the coupon code attributes
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param $args
		 * @param $code
		 *
		 * @return array
		 */
		function create_coupon_discount( $args, $code ) {

			if ( $code == $this->get_coupon_code_prefix() ) {

				$this->args = array(
					'amount'           => $this->get_discount_amount(),
					'coupon_amount'    => $this->get_discount_amount(), // 2.2
					'apply_before_tax' => 'yes',
					'type'             => $this->coupon_type,
					'free_shipping'    => false,
					'individual_use'   => 'no',
				);

				return $this->args;

			}

			return $args;
		}

		/**
		 * Set the coupon label in cart
		 *
		 * @since    1.0.0
		 * @author   Emanuela Castorina
		 *
		 * @param $string
		 * @param $coupon
		 *
		 * @return string
		 * @internal param $label
		 */
		public function coupon_label( $string, $coupon ) {
			$points_coupon_label = apply_filters( 'ywpar_coupon_label', __( 'Redeem points', 'yith-woocommerce-points-and-rewards' ) );
			return $this->check_coupon_is_ywpar( $coupon ) ? esc_html( $points_coupon_label ) : $string;
		}

		/**
		 * Return the discount amount
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 * @return float
		 */
		public function get_discount_amount() {
			$discount = 0;
			if ( WC()->session !== null ) {
				$discount = WC()->session->get( 'ywpar_coupon_code_discount' );
			}

			return $discount;
		}

		/**
		 * Register the coupon amount and points in the post meta of order
		 * if there's a rewards
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param $order_id
		 */
		public function add_order_meta( $order_id ) {
			$order = wc_get_order( $order_id );

			if ( version_compare( WC()->version, '3.7.0', '<' ) ) {
				$used_coupons = $order->get_used_coupons();
			} else {
				$used_coupons = $order->get_coupon_codes();
			}

			// check if the coupon was used in the order
			if ( ! $coupon = $this->check_coupon_is_ywpar( $used_coupons ) ) {
				return;
			}

			yit_save_prop(
				$order,
				array(
					'_ywpar_coupon_amount' => WC()->session->get( 'ywpar_coupon_code_discount' ),
					'_ywpar_coupon_points' => WC()->session->get( 'ywpar_coupon_code_points' ),
				),
				false,
				true
			);
		}

		/**
		 * Deduct the point from the user total points
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since    1.0.0
		 * @author   Emanuela Castorina
		 *
		 * @param $order
		 *
		 * @return void
		 * @internal param $order_id
		 */
		public function deduce_order_points( $order ) {
			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			$customer_user = $order->get_customer_id();
			$used_coupons  = $order->get_coupon_codes();

			// check if the coupon was used in the order
			if ( ! $coupon = $this->check_coupon_is_ywpar( $used_coupons ) ) {
				return;
			}

			$points          = yit_get_prop( $order, '_ywpar_coupon_points' );
			$discount_amount = yit_get_prop( $order, '_ywpar_coupon_amount' );
			$redemped_points = yit_get_prop( $order, '_ywpar_redemped_points' );

			if ( $redemped_points != '' ) {
				return;
			}

			if ( $customer_user ) {
				$current_point                 = (float) get_user_meta( $customer_user, '_ywpar_user_total_points', true );
				$current_discount_total_amount = (float) get_user_meta( $customer_user, '_ywpar_user_total_discount', true );

				$new_point = ( $current_point - $points > 0 ) ? ( $current_point - $points ) : 0;

				update_user_meta( $customer_user, '_ywpar_user_total_points', $new_point );
				update_user_meta( $customer_user, '_ywpar_user_total_discount', $current_discount_total_amount + $discount_amount );
				wp_cache_flush();

				if ( apply_filters( 'ywpar_update_wp_cache', false ) ) {
					$cached_user_meta                               = wp_cache_get( $customer_user, 'user_meta' );
					$cached_user_meta['_ywpar_user_total_points']   = array( $new_point );
					$cached_user_meta['_ywpar_user_total_discount'] = array( $current_discount_total_amount + $discount_amount );
					$result = wp_cache_set( $customer_user, $cached_user_meta, 'user_meta' );
				}

				yit_save_prop( $order, '_ywpar_redemped_points', $points, false, true );

				YITH_WC_Points_Rewards()->register_log( $customer_user, 'redeemed_points', yit_get_prop( $order, 'id' ), - $points );
				$this->set_user_rewarded_points( $customer_user, $points );

				$order->add_order_note( sprintf( __( '%1$d %2$s to get a reward', 'yith-woocommerce-points-and-rewards' ), - $points, __( 'Points', 'yith-woocommerce-points-and-rewards' ) ), 0 );
			}

		}

		/**
		 * Return the conversion rate rewards
		 *
		 * @param string $currency
		 * @return float
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function get_conversion_rate_rewards( $currency = '' ) {

			$current_currency     = apply_filters( 'ywpar_multi_currency_current_currency', get_woocommerce_currency() );
			$currency             = empty( $currency ) ? $current_currency : $currency;
			$conversions          = YITH_WC_Points_Rewards()->get_option( 'rewards_conversion_rate' );
			$conversion           = isset( $conversions[ $currency ] ) ? $conversions[ $currency ] : array(
				'money'  => 0,
				'points' => 0,
			);
			$conversion           = apply_filters( 'ywpar_rewards_conversion_rate', $conversion );
			$conversion['money']  = ( empty( $conversion['money'] ) ) ? 1 : $conversion['money'];
			$conversion['points'] = ( empty( $conversion['points'] ) ) ? 1 : $conversion['points'];

			return apply_filters( 'ywpar_rewards_conversion_rate', $conversion );
		}

		/**
		 * Get the rewarded points of a user from the user meta if exists or from the database if
		 * do not exist. In this last case the value is saved on the user meta
		 *
		 * @param $user_id
		 * @return int
		 * @since 1.3.0
		 */
		public function get_user_rewarded_points( $user_id ) {
			global $wpdb;

			$rewarded_points = get_user_meta( $user_id, '_ywpar_rewarded_points', true );
			if ( '' === $rewarded_points ) {
				$table_name      = $wpdb->prefix . 'yith_ywpar_points_log';
				$query           = "SELECT SUM(pl.amount) FROM $table_name as pl where pl.user_id = $user_id AND ( pl.action IN ( 'redeemed_points', 'order_refund', 'admin_action') AND pl.amount < 0 )";
				$rewarded_points = $wpdb->get_var( $query );

				$rewarded_points = is_null( $rewarded_points ) ? 0 : absint( $rewarded_points );
				update_user_meta( $user_id, '_ywpar_rewarded_points', $rewarded_points );
			}

			return (int) $rewarded_points;

		}

		/**
		 * Set user rewarded points, add $rewarded_points to the user meta '_ywpar_rewarded_points'
		 *
		 * @since 1.3.0
		 *
		 * @param int $user_id
		 * @param int $rewarded_point
		 *
		 * @return void
		 */
		public function set_user_rewarded_points( $user_id, $rewarded_point ) {
			$new_rewarded_points = $rewarded_point + $this->get_user_rewarded_points( $user_id );
			update_user_meta( $user_id, '_ywpar_rewarded_points', $new_rewarded_points );
			wp_cache_flush();
		}

		/**
		 * Calculate the points of a product/variation for a single item
		 *
		 * @param float $discount_amount
		 *
		 * @return  int $points
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function calculate_rewards_discount( $discount_amount = 0.0 ) {

			$user_id       = get_current_user_id();
			$points_usable = get_user_meta( $user_id, '_ywpar_user_total_points', true );

			if ( $points_usable <= 0 ) {
				return false;
			}

			$items = WC()->cart->get_cart();

			$this->max_discount = 0;
			$this->max_points   = 0;

			$conversion = $this->get_conversion_rate_rewards();

			// get the items of cart
			foreach ( $items as $item => $values ) {
				$product_id       = ( isset( $values['variation_id'] ) && $values['variation_id'] != 0 ) ? $values['variation_id'] : $values['product_id'];
				$item_price       = apply_filters( 'ywpar_calculate_rewards_discount_item_price', ywpar_get_price( $values['data'] ), $values, $product_id );
				$product_discount = $this->calculate_product_max_discounts( $product_id, $item_price );
				if ( $product_discount != 0 ) {
					$this->max_discount += $product_discount * $values['quantity'];
				}
			}

			if ( apply_filters( 'ywpar_exclude_taxes_from_calculation', false ) ) {
				$subtotal = ( (float) WC()->cart->get_subtotal() - (float) WC()->cart->get_discount_total() ) + $discount_amount;
			} else {
				$subtotal = ( ( (float) WC()->cart->get_subtotal() + (float) WC()->cart->get_subtotal_tax() ) - ( (float) WC()->cart->get_discount_total() + (float) WC()->cart->get_discount_tax() ) ) + $discount_amount;
			}

			if ( $subtotal <= $this->max_discount ) {
				$this->max_discount = $subtotal;
			}
			$this->max_discount = apply_filters( 'ywpar_set_max_discount_for_minor_subtotal', $this->max_discount, $subtotal );
			$this->max_discount = apply_filters( 'ywpar_calculate_rewards_discount_max_discount_fixed', $this->max_discount );
			$appfun             = apply_filters( 'ywpar_approx_function', 'ceil' );
			$this->max_points   = call_user_func( $appfun, $this->max_discount / $conversion['money'] * $conversion['points'] );

			if ( $this->max_points > $points_usable ) {
				$this->max_points   = $points_usable;
				$this->max_discount = $this->max_points / $conversion['points'] * $conversion['money'];
			}

			$this->max_discount = apply_filters( 'ywpar_calculate_rewards_discount_max_discount', $this->max_discount, $this, $conversion );
			$this->max_points   = apply_filters( 'ywpar_calculate_rewards_discount_max_points', $this->max_points, $this, $conversion );

			return $this->max_discount;
		}

		/**
		 * Calculate the max discount of a product.
		 *
		 * Check if some option is set on product or category if not the
		 * general conversion will be used.
		 *
		 * @param int $product_id
		 *
		 * @param int $price
		 *
		 * @return float|mixed|string
		 */
		public function calculate_product_max_discounts( $product_id, $price = 0 ) {
			$product      = wc_get_product( $product_id );
			$max_discount = ywpar_get_price( $product );
			return apply_filters( 'ywpar_calculate_product_max_discounts', $max_discount );
		}

		/**
		 * Return the conversion method that can be used in the cart fore rewards
		 *
		 * @since   1.1.3
		 * @author  Emanuela Castorina
		 * @return  string
		 */
		public function get_conversion_method() {
			return 'fixed';
		}

		/**
		 * Return the max points that can be used in the cart fore rewards
		 * must be called after the function calculate_points_and_discount
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 * @return  int
		 */
		public function get_max_points() {
			return apply_filters( 'ywpar_rewards_max_points', $this->max_points );
		}

		/**
		 * Return the max discount that can be used in the cart fore rewards
		 * must be called after the function calculate_points_and_discount
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 * @return  float
		 */
		public function get_max_discount() {
			return apply_filters( 'ywpar_rewards_max_discount', $this->max_discount );
		}

		/**
		 * Check if a YWPAR Coupons is in the list
		 *
		 * @param array|WC_Coupon $coupon_list
		 *
		 * @return bool|WC_Coupon
		 */
		public function check_coupon_is_ywpar( $coupon_list ) {

			if ( is_array( $coupon_list ) && ! empty( $coupon_list ) ) {
				foreach ( $coupon_list as $coupon_in_cart_code ) {
					$coupon_in_cart = new WC_Coupon( $coupon_in_cart_code );
					if ( $coupon_in_cart ) {
						$meta = $coupon_in_cart->get_meta( 'ywpar_coupon' );
						if ( ! empty( $meta ) ) {
							return $coupon = $coupon_in_cart;
						}
					}
				}
			} elseif ( $coupon_list instanceof WC_Coupon ) {
				$var1 = $coupon_list->get_meta( 'ywpar_coupon' );

				return ! empty( $var1 );
			}

			return false;
		}

		/**
		 * Return the coupon to apply
		 *
		 * @return WC_Coupon
		 */
		public function get_current_coupon() {

			if ( empty( $this->current_coupon_code ) ) {
				// check if in the cart
				$coupons_in_cart = WC()->cart->get_applied_coupons();

				foreach ( $coupons_in_cart as $coupon_in_cart_code ) {
					if ( $this->check_coupon_is_ywpar( $coupon_in_cart_code ) ) {
						$this->current_coupon_code = $coupon_in_cart_code;
						break;
					}
				}
			}

			if ( empty( $this->current_coupon_code ) ) {
				if ( is_user_logged_in() ) {
					$this->current_coupon_code = apply_filters( 'ywpar_coupon_code', $this->label_coupon_prefix . '_' . get_current_user_id(), $this->label_coupon_prefix );
				}
			}

			$coupon = empty( $this->current_coupon_code ) ? false : new WC_Coupon( $this->current_coupon_code );

			return $coupon;
		}

		/**
		 * Set cron to clear coupon
		 */
		public function ywpar_set_cron() {
			if ( ! wp_next_scheduled( 'ywpar_clean_cron' ) ) {
				$duration = apply_filters( 'ywpar_set_cron_time', 'daily' );
				wp_schedule_event( time(), $duration, 'ywpar_clean_cron' );
			}
		}

		/**
		 * Clear coupons after use
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		function clear_coupons() {

			$args = array(
				'post_type'       => 'shop_coupon',
				'posts_per_pages' => - 1,
				'meta_key'        => 'ywpar_coupon',
				'meta_value'      => 1,
				'date_query'      => array(
					array(
						'column' => 'post_date_gmt',
						'before' => '1 day ago',
					),
				),
			);

			$coupons = get_posts( $args );

			if ( ! empty( $coupons ) ) {
				foreach ( $coupons as $coupon ) {
					wp_delete_post( $coupon->ID, true );
				}
			}
		}

		/**
		 * Returns the usage limit parameter to do a coupon. The function check the option 'other_coupons'.
		 * if this option is equal to 'ywpar' usage limit will be equal 1
		 *
		 * @return bool
		 */
		protected function get_usage_limit() {
			return 1;
		}
	}
}

/**
 * Unique access to instance of YITH_WC_Points_Rewards_Redemption class
 *
 * @return \YITH_WC_Points_Rewards_Redemption
 */
function YITH_WC_Points_Rewards_Redemption() {
	return YITH_WC_Points_Rewards_Redemption::get_instance();
}
