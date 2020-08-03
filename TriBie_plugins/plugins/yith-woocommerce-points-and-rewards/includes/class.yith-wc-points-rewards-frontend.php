<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Points and Rewards Frontend
 *
 * @class   YITH_WC_Points_Rewards_Frontend
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Points_Rewards_Frontend' ) ) {

	/**
	 * Class YITH_WC_Points_Rewards_Frontend
	 */
	class YITH_WC_Points_Rewards_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Points_Rewards_Frontend
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Points_Rewards_Frontend
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

			add_action( 'woocommerce_before_my_account', array( $this, 'my_account_points' ) );
			// Add messages on cart or checkout if them are enabled
			add_action( 'template_redirect', array( $this, 'show_messages' ), 30 );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );

			/** REDEEM  */
			// check if user can redeem points and add the rewards messages if are enabled
			if ( YITH_WC_Points_Rewards()->get_option( 'enabled_rewards_cart_message' ) == 'yes' ) {
				add_action( 'woocommerce_before_cart', array( $this, 'print_rewards_message_in_cart' ) );
				add_action( 'woocommerce_before_checkout_form', array( $this, 'print_rewards_message_in_cart' ) );
				add_action( 'wc_ajax_ywpar_update_cart_rewards_messages', array( $this, 'print_rewards_message' ) );
			}

		}


		/**
		 * Enqueue Scripts and Styles
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function enqueue_styles_scripts() {

			wp_enqueue_script(
				'ywpar_frontend',
				YITH_YWPAR_ASSETS_URL . '/js/frontend' . YITH_YWPAR_SUFFIX . '.js',
				array(
					'jquery',
				),
				YITH_YWPAR_VERSION,
				true
			);
			wp_enqueue_style( 'ywpar_frontend', YITH_YWPAR_ASSETS_URL . '/css/frontend.css' );

			$script_params = array(
				'ajax_url'    => admin_url( 'admin-ajax' ) . '.php',
				'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			);

			wp_localize_script( 'ywpar_frontend', 'yith_wpar_general', $script_params );
		}

		/**
		 * Print rewards message in cart/checkout page
		 *
		 * @return  mixed
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function print_rewards_message_in_cart() {

			$coupons = WC()->cart->get_applied_coupons();

			// the message will not showed if the coupon is just applied to cart
			if ( YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $coupons ) ) {
				return '';
			}

			$message = $this->get_rewards_message();
			if ( $message ) {
				// APPLY_FILTER : yith_par_messages_class: filtering the classes of messages in cart/checkout
				$yith_par_message_classes = apply_filters(
					'yith_par_messages_class',
					array(
						'woocommerce-cart-notice',
						'woocommerce-cart-notice-minimum-amount',
						'woocommerce-info',
					)
				);
				$classes                  = count( $yith_par_message_classes ) > 0 ? implode( ' ', $yith_par_message_classes ) : '';
				 printf( '<div id="yith-par-message-reward-cart" class="%s">%s</div>', esc_attr( $classes ),  $message  ) ;
			}

		}

		/**
		 * @return mixed
		 * @author  Andrea Frascaspata
		 * @since   1.1.3
		 */
		private function get_rewards_message() {
			// DO_ACTION : ywpar_before_rewards_message : action triggered before the rewards message
			do_action( 'ywpar_before_rewards_message' );
			$message = '';

			if ( is_user_logged_in() ) {
				$message      = YITH_WC_Points_Rewards()->get_option( 'rewards_cart_message' );
				$plural       = YITH_WC_Points_Rewards()->get_option( 'points_label_plural' );
				$max_discount = YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();

				if ( ! $max_discount ) {
					return '';
				}

				// APPLY_FILTER : ywpar_hide_value_for_max_discount: hide the message if $max_discount is < 0
				$max_discount_2 = apply_filters( 'ywpar_hide_value_for_max_discount', $max_discount );
				if ( $max_discount > 0 ) {

					$max_points = YITH_WC_Points_Rewards_Redemption()->get_max_points();

					if ( $max_points == 0 ) {
						return '';
					}

					$message = str_replace( '{points_label}', $plural, $message );
					$message = str_replace( '{max_discount}', wc_price( $max_discount ), $message );
					$message = str_replace( '{points}', $max_points, $message );
					$message .= ' <a class="ywpar-button-message">' . __( 'Apply Discount', 'yith-woocommerce-points-and-rewards' ) . '</a>';
					$message .= '<div class="clear"></div><div class="ywpar_apply_discounts_container"><form class="ywpar_apply_discounts" method="post">' . wp_nonce_field( 'ywpar_apply_discounts', 'ywpar_input_points_nonce' ) . '
                                <input type="hidden" name="ywpar_points_max" value="' . $max_points . '">
                                <input type="hidden" name="ywpar_max_discount" value="' . $max_discount_2 . '">
                                <input type="hidden" name="ywpar_rate_method" value="fixed">
                                <p class="form-row form-row-first">
                                    <input type="text" name="ywpar_input_points" class="input-text"  id="ywpar-points-max" value="' . $max_points . '">
                                    <input type="hidden" name="ywpar_input_points_check" id="ywpar_input_points_check" value="0">
                                </p>
                                <p class="form-row form-row-last">
                                    <input type="submit" class="button" name="ywpar_apply_discounts" id="ywpar_apply_discounts" value="' . __( 'Apply Discount', 'yith-woocommerce-points-and-rewards' ) . '">
                                </p>
                                <div class="clear"></div>
                            </form></div>';
				}
				// DO_ACTION : ywpar_after_rewards_message : action triggered after the rewards message
				do_action( 'ywpar_after_rewards_message' );
			}

			return $message;
		}

		/**
		 * Show messages on cart or checkout page if the options are enabled
		 */
		public function show_messages() {
			add_action( 'woocommerce_before_cart', array( $this, 'print_messages_in_cart' ) );
		}

		/**
		 * Print a message in cart/checkout page or in my account pay order page.
		 *
		 * @return  void
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function print_messages_in_cart() {

			$points_earned = false;

			$message = $this->get_cart_message( $points_earned );

			if ( ! empty( $message ) ) {
				// APPLY_FILTER : yith_par_messages_class: filtering the classes of messages in cart/checkout
				$yith_par_message_classes = apply_filters(
					'yith_par_messages_class',
					array(
						'woocommerce-cart-notice',
						'woocommerce-cart-notice-minimum-amount',
						'woocommerce-info',
					)
				);
				$classes                  = count( $yith_par_message_classes ) > 0 ? implode( ' ', $yith_par_message_classes ) : '';
				printf( '<div id="yith-par-message-cart" class="%s">%s</div>', esc_attr( $classes ), wp_kses_post( $message ) );
			}
		}

		/**
		 * Return the message to show on cart or checkout for point to earn.
		 *
		 * @param int $total_points
		 *
		 * @return string
		 * @since   1.1.3
		 * @author  Andrea Frascaspata
		 *
		 */
		private function get_cart_message( $total_points = 0 ) {

			$page = is_checkout() ? 'checkout' : 'cart';

			$message  = YITH_WC_Points_Rewards()->get_option( 'cart_message' );
			$singular = YITH_WC_Points_Rewards()->get_option( 'points_label_singular' );
			$plural   = YITH_WC_Points_Rewards()->get_option( 'points_label_plural' );

			if ( $total_points == 0 ) {
				$total_points = YITH_WC_Points_Rewards_Earning()->calculate_points_on_cart();
				if ( $total_points == 0 ) {
					return '';
				}
			}

			$conversion_method = YITH_WC_Points_Rewards_Redemption()->get_conversion_method();
			$discount          = '';

			$discount = '';
			if ( $conversion_method == 'fixed' ) {
				$conversion  = YITH_WC_Points_Rewards_Redemption()->get_conversion_rate_rewards();
				$point_value = $conversion['money'] / $conversion['points'];
				$discount    = $total_points * $point_value;
			}

			$message = str_replace( '{points}', $total_points, $message );
			$message = str_replace( '{points_label}', ( $total_points > 1 ) ? $plural : $singular, $message );
			$message = str_replace( '{price_discount_fixed_conversion}', isset( $discount ) ? wc_price( $discount ) : '', $message );
			$message = str_replace( '{price_discount_fixed_conversion}', ( isset( $discount ) && $discount != '' ) ? wc_price( $discount ) : '', $message );

			// APPLY_FILTER : ywpar_cart_message_filter: filtering the cart messages in cart/checkout
			return apply_filters( 'ywpar_cart_message_filter', $message, $total_points, $discount );
		}


		/**
		 * Add points section to my-account page
		 *
		 * @return  void
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function my_account_points() {
			wc_get_template( 'myaccount/my-points-view.php', array(), '', YITH_YWPAR_DIR . 'templates/' );
		}

		/**
		 * @since   1.1.3
		 * @author  Andrea Frascaspata
		 */
		public function print_rewards_message() {
			$coupons = WC()->cart->get_applied_coupons();

			// the message will not showed if the coupon is just applied to cart
			if ( YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $coupons ) ) {
				return '';
			}
			echo $this->get_rewards_message();
		}


	}


}

/**
 * Unique access to instance of YITH_WC_Points_Rewards_Frontend class
 *
 * @return \YITH_WC_Points_Rewards_Frontend
 */
function YITH_WC_Points_Rewards_Frontend() {
	return YITH_WC_Points_Rewards_Frontend::get_instance();
}

