<?php
/**
 * Implements helper functions for YITH WooCommerce Points and Rewards
 *
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

global $yith_ywpar_db_version;

$yith_ywpar_db_version = '1.0.2';

if ( ! function_exists( 'yith_ywpar_db_install' ) ) {
	/**
	 * Install the table yith_ywpar_points_log
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function yith_ywpar_db_install() {
		global $wpdb;
		global $yith_ywpar_db_version;

		$installed_ver = get_option( 'yith_ywpar_db_version' );

		$table_name = $wpdb->prefix . 'yith_ywpar_points_log';

		$charset_collate = $wpdb->get_charset_collate();

		if ( ! $installed_ver ) {
			$sql = "CREATE TABLE $table_name (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `action` VARCHAR (255) NOT NULL,
            `order_id` int(11),
            `amount` int(11) NOT NULL,
            `date_earning` datetime NOT NULL,
            `cancelled` datetime,
            `description` TEXT, 
            PRIMARY KEY (id)
            ) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			add_option( 'yith_ywpar_db_version', $yith_ywpar_db_version );
		}

		if ( version_compare( $installed_ver, '1.0.2', '<=' ) ) {
			$sql  = "SELECT COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table_name'";
			$cols = $wpdb->get_col( $sql );

			if ( is_array( $cols ) && ! in_array( 'cancelled', $cols ) && version_compare( $installed_ver, '1.0.0', '=' ) ) {
				$sql = "ALTER TABLE $table_name ADD `cancelled` datetime";
				$wpdb->query( $sql );
			}
			if ( is_array( $cols ) && ! in_array( 'description', $cols ) && version_compare( $installed_ver, '1.0.1', '=' ) ) {
				$sql = "ALTER TABLE $table_name ADD `description` TEXT";
				$wpdb->query( $sql );
			}
			update_option( 'yith_ywpar_db_version', $yith_ywpar_db_version );
		}

	}
}

if ( ! function_exists( 'yith_ywpar_update_db_check' ) ) {
	/**
	 * Check if the function yith_ywpar_db_install must be installed or updated
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function yith_ywpar_update_db_check() {
		global $yith_ywpar_db_version;

		if ( get_site_option( 'yith_ywpar_db_version' ) !== $yith_ywpar_db_version ) {
			yith_ywpar_db_install();
		}
	}
}

if ( ! function_exists( 'ywpar_options_porting' ) ) {
	/**
	 * Options porting
	 *
	 * @param array $old_options Options.
	 */
	function ywpar_options_porting( $old_options ) {

		foreach ( $old_options as $key => $value ) {

			$key   = 'ywpar_' . $key;
			$key   = apply_filters( 'ywpar_porting_options_key', $key, $value );
			$value = apply_filters( 'ywpar_porting_options_value', $value, $key );

			update_option( $key, $value );
		}
	}
}

/**
 * Conversion rate with default currency
 *
 * @param array $options Options.
 * @param string $currency Currency
 *
 * @return array
 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
 */
function get_conversion_rate_with_default_currency( $options, $currency ) {
	$new_option = array();
	if ( isset( $options['points'] ) ) {
		$new_option[ $currency ] = $options;
	} else {
		$new_option = $options;
	}

	return $new_option;
}

/**
 * Conversion points multilingual
 *
 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
 */
function ywpar_conversion_points_multilingual() {

	$old_conversion = get_option( 'yit_ywpar_multicurrency', false );
	if ( ! $old_conversion ) {
		$default_currency = get_woocommerce_currency();
		$options          = array( 'earn_points_conversion_rate' );

		foreach ( $options as $option_name ) {
			$conversion_role = YITH_WC_Points_Rewards()->get_option( $option_name );
			$new_conversion_role = get_conversion_rate_with_default_currency( $conversion_role, $default_currency );
			YITH_WC_Points_Rewards()->set_option( $option_name, $new_conversion_role );
		}

		update_option( 'yit_ywpar_multicurrency', true );
	}

}

if ( ! function_exists( 'ywpar_coupon_is_valid' ) ) {
	/**
	 * Check if a coupon is valid
	 *
	 * @param WC_Coupon $coupon Coupon.
	 * @param array     $object Object.
	 *
	 * @return bool|WP_Error
	 * @throws Exception Get error message.f
	* @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywpar_coupon_is_valid( $coupon, $object = array() ) {
		if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
			$wc_discounts = new WC_Discounts( $object );
			$valid        = $wc_discounts->is_coupon_valid( $coupon );
			$valid        = is_wp_error( $valid ) ? false : $valid;
		} else {
			$valid = $coupon->is_valid();
		}

		return $valid;
	}
}

if ( ! function_exists( 'remove_ywpar_coupons' ) ) {
	/**
	 * Remove Points and Rewards Coupon.
	 */
	function remove_ywpar_coupons() {
		if ( WC()->cart ) {
			$coupons = WC()->cart->get_applied_coupons();
			foreach ( $coupons as $coupon ) {
				$current_coupon = new WC_Coupon( $coupon );
				if ( YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $current_coupon ) ) {
					WC()->cart->remove_coupon( $coupon );
				}
			}
		}
	}
}

/**
 * WooCommerce Multilingual - MultiCurrency
 */
if ( function_exists( 'wcml_is_multi_currency_on' ) && wcml_is_multi_currency_on() ) {

	add_filter( 'ywpar_multi_currency_current_currency', 'ywpar_multi_currency_current_currency', 10 );
	if ( ! function_exists( 'ywpar_multi_currency_current_currency' ) ) {
		/**
		 * Get current currency.
		 *
		 * @param string $currency Currency.
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_multi_currency_current_currency( $currency ) {
			global $woocommerce_wpml;
			$client_currency = $woocommerce_wpml->multi_currency->get_client_currency();

			return ! empty( $client_currency ) ? $client_currency : $currency;
		}
	}

	add_filter( 'ywpar_get_active_currency_list', 'ywpar_get_active_currency_list' );
	if ( ! function_exists( 'ywpar_get_active_currency_list' ) ) {
		/**
		 * Return the list of active currencies.
		 *
		 * @param array $currencies
		 *
		 * @return array
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_get_active_currency_list( $currencies ) {
			global $woocommerce_wpml;
			$multi_currencies = $woocommerce_wpml->multi_currency->get_currencies( 'include_default = true' );
			if ( $multi_currencies ) {
				$currencies = array_keys( $multi_currencies );
			}

			return $currencies;
		}
	}

	add_action( 'woocommerce_coupon_loaded', 'remove_wcml_filter', 1 );
	if ( ! function_exists( 'remove_wcml_filter' ) ) {

		/**
		 * Remove wcml filter when a coupon is loaded
		 *
		 * @param $coupon
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function remove_wcml_filter( $coupon ) {
			global $woocommerce_wpml;

			if ( YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $coupon ) ) {
				remove_action(
					'woocommerce_coupon_loaded',
					array(
						$woocommerce_wpml->multi_currency->coupons,
						'filter_coupon_data',
					),
					10
				);
			}
		}
	}

	add_action( 'wcml_switch_currency', 'ywpar_wcml_remove_ywpar_coupons' );
	/**
	 * @param string $code
	 * @param string $cookie_lang
	 * @param string $original
	 */
	function ywpar_wcml_remove_ywpar_coupons( $code = '', $cookie_lang = '', $original = '' ) {
		$action = current_action();
		switch ( $action ) {

			case 'wcml_user_switch_language':
				if ( ! empty( $code ) && $code != $cookie_lang ) {
					remove_ywpar_coupons();
				}

				break;
			case 'wcml_switch_currency':
				remove_ywpar_coupons();
				break;

		}
	}
}

if ( class_exists( 'WC_Aelia_CurrencySwitcher' ) ) {

	add_filter( 'ywpar_get_active_currency_list', 'ywpar_aelia_get_active_currency_list' );
	if ( ! function_exists( 'ywpar_aelia_get_active_currency_list' ) ) {

		/**
		 * Return the list of active currencies.
		 *
		 * @param array $currencies
		 *
		 * @return array
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */

		function ywpar_aelia_get_active_currency_list( $currencies ) {
			$settings_controller = WC_Aelia_CurrencySwitcher::settings();
			$enabled_currencies  = $settings_controller->get_enabled_currencies();
			$currencies          = ! empty( $enabled_currencies ) ? $enabled_currencies : $currencies;

			return $currencies;
		}
	}

	add_action( 'woocommerce_coupon_get_amount', 'remove_aelia_filter_woocommerce_coupon_get_amount', 1, 2 );
	/**
	 * @param $amount
	 * @param $coupon
	 * @return mixed
	 */
	function remove_aelia_filter_woocommerce_coupon_get_amount( $amount, $coupon ) {
		$is_par = YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $coupon );
		if ( $is_par ) {
			remove_action( 'woocommerce_coupon_get_amount', array( WC_Aelia_CurrencyPrices_Manager::Instance(), 'woocommerce_coupon_get_amount' ), 5 );
		}
		return $amount;
	}
}

if ( class_exists( 'WOOCS_STARTER' ) ) {

	add_filter( 'ywpar_get_active_currency_list', 'ywpar_woocommerce_currency_switcher_currency_list' );
	if ( ! function_exists( 'ywpar_woocommerce_currency_switcher_currency_list' ) ) {

		/**
		 * Return the list of active currencies.
		 *
		 * @param array $currencies
		 *
		 * @return array
		 * @since  1.5.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocommerce_currency_switcher_currency_list( $currencies ) {
			global $WOOCS;  // phpcs:disable WordPress.NamingConventions
			$enabled_currencies = array_keys( $WOOCS->get_currencies() );  // phpcs:disable WordPress.NamingConventions
			$currencies         = ! empty( $enabled_currencies ) ? $enabled_currencies : $currencies;

			return $currencies;
		}
	}

	add_action( 'ywpar_before_currency_loop', 'ywpar_woocommerce_currency_switcher_before_currency_loop' );
	if ( ! function_exists( 'ywpar_woocommerce_currency_switcher_before_currency_loop' ) ) {
		/**
		 * @since  1.5.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocommerce_currency_switcher_before_currency_loop() {
			global $WOOCS;  // phpcs:disable WordPress.NamingConventions
			remove_filter( 'woocommerce_currency_symbol', array( $WOOCS, 'woocommerce_currency_symbol' ), 9999 );  // phpcs:disable WordPress.NamingConventions
		}
	}

	add_action( 'ywpar_after_rewards_message', 'ywpar_woocs_after_rewards_message' );
	if ( ! function_exists( 'ywpar_woocs_after_rewards_message' ) ) {
		/**
		 * @since 1.5.2
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocs_after_rewards_message() {
			global $WOOCS;  // phpcs:disable WordPress.NamingConventions
			add_filter( 'wc_price_args', array( $WOOCS, 'wc_price_args' ), 9999 );  // phpcs:disable WordPress.NamingConventions
			if ( ! $WOOCS->is_multiple_allowed ) {  // phpcs:disable WordPress.NamingConventions
				add_filter( 'raw_woocommerce_price', array( $WOOCS, 'raw_woocommerce_price' ), 9999 );  // phpcs:disable WordPress.NamingConventions
			}

		}
	}

	add_filter( 'ywpar_get_point_earned_price', 'ywpar_woocs_convert_price', 10, 2 );
	add_filter( 'ywpar_calculate_rewards_discount_max_discount_fixed', 'ywpar_woocs_convert_price', 10, 1 );
	if ( ! function_exists( 'ywpar_woocs_convert_price' ) ) {
		/**
		 * Convert price
		 *
		 * @param float $price Price.
		 * @param string $currency Currency.
		 *
		 * @return float|int
		 * @since  1.5.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocs_convert_price( $price, $currency = '' ) {
			global $WOOCS;  // phpcs:disable WordPress.NamingConventions
			if ( $WOOCS->is_multiple_allowed ) {  // phpcs:disable WordPress.NamingConventions
				return $price; }
			$currencies = $WOOCS->get_currencies();
			$currency   = empty( $currency ) ? $WOOCS->current_currency : $currency;
			if ( isset( $currencies[ $currency ] ) ) {
				$price = $price * $currencies[ $currency ]['rate'];
			}

			return $price;
		}
	}

	add_filter( 'ywpar_hide_value_for_max_discount', 'ywpar_woocs_hide_value_for_max_discount' );
	if ( ! function_exists( 'ywpar_woocs_hide_value_for_max_discount' ) ) {
		/**
		 * Hide value form max discount
		 * @param float $discount Discount.
		 *
		 * @return int
		 * @since  1.5.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocs_hide_value_for_max_discount( $discount ) {
			global $WOOCS;  // phpcs:disable WordPress.NamingConventions
			if ( $WOOCS->is_multiple_allowed ) {  // phpcs:disable WordPress.NamingConventions
				$currencies = $WOOCS->get_currencies();  // phpcs:disable WordPress.NamingConventions
				return $WOOCS->back_convert( $discount, $currencies[ $WOOCS->current_currency ]['rate'] );  // phpcs:disable WordPress.NamingConventions
			}
			remove_all_filters( 'ywpar_calculate_rewards_discount_max_discount_fixed' );
			remove_all_filters( 'ywpar_calculate_rewards_discount_max_discount_percentual' );

			return YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
		}
	}

	add_filter( 'ywpar_adjust_discount_value', 'ywpar_woocs_adjust_discount_value' );
	if ( ! function_exists( 'ywpar_woocs_adjust_discount_value' ) ) {
		/**
		 * Return the discount adjusted.
		 *
		 * @param float $discount Discount.
		 * @return int
		 * @since  1.5.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocs_adjust_discount_value( $discount ) {
			global $WOOCS; // phpcs:disable WordPress.NamingConventions
			if ( $WOOCS->is_multiple_allowed ) {  // phpcs:disable WordPress.NamingConventions
				$currencies = $WOOCS->get_currencies();  // phpcs:disable WordPress.NamingConventions
				$discount   = $WOOCS->back_convert( $discount, $currencies[ $WOOCS->current_currency ]['rate'] );  // phpcs:disable WordPress.NamingConventions
			}
			return $discount;
		}
	}
}

if ( ! function_exists( 'ywpar_get_price' ) ) {
	/**
	 * Get the price.
	 *
	 * @param WC_Product $product Product.
	 * @param int        $qty Quantity.
	 * @param string     $price Price.
	 * @return float|string
	 */
	function ywpar_get_price( $product, $qty = 1, $price = '' ) {

		if ( '' === $price && $product instanceof WC_Product ) {
			$price = $product->get_price();
		}

		$tax_display_mode = apply_filters( 'ywpar_get_price_tax_on_points', get_option( 'woocommerce_tax_display_shop', 'incl' ) );
		$display_price    =   'incl' === $tax_display_mode ? yit_get_price_including_tax( $product, $qty, $price ) : yit_get_price_excluding_tax( $product, $qty, $price );

		return $display_price;
	}
}
