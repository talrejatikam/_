<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Points and Rewards
 *
 * @class   YITH_WC_Points_Rewards
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Points_Rewards' ) ) {

	class YITH_WC_Points_Rewards {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Points_Rewards
		 */
		protected static $instance;

		public $plugin_options = 'yit_ywpar_options';


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Points_Rewards
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
		 * @return mixed
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			if ( ! $this->is_enabled() ) {
				return false;
			}

		}

		/**
		 * Empty the table of log and delete the post meta to order and usermeta to users
		 *
		 * @return void
		 */
		public function reset_points() {
			global $wpdb;

			$table_name = $wpdb->prefix . 'yith_ywpar_points_log';

			$user_meta = "'" . implode( "','", $this->get_usermeta_list() ) . "'";
			$post_meta = "'" . implode( "','", $this->get_ordermeta_list() ) . "'";

			$wpdb->query( "TRUNCATE TABLE $table_name" );
			$wpdb->query( "DELETE FROM {$wpdb->usermeta}  WHERE {$wpdb->usermeta}.meta_key IN( {$user_meta} )" );
			$wpdb->query( "DELETE FROM {$wpdb->postmeta}  WHERE {$wpdb->postmeta}.meta_key IN( {$post_meta} )" );

			delete_option( 'yith_ywpar_porting_done' );

		}

		/**
		 * Returns the list of all usermeta used be plugin
		 *
		 * @return array
		 * @since 1.1.3
		 */
		public function get_usermeta_list() {
			$usermeta = array( '_ywpar_user_total_points', '_ywpar_user_total_discount', '_ywpar_extrapoint', '_ywpar_rewarded_points', '_ywpar_used_points', '_ywpar_extrapoint_counter' );

			return apply_filters( 'ywpar_usermeta_list', $usermeta );
		}

		/**
		 * Returns the list of all postmeta of orders used be plugin
		 *
		 * @return array
		 * @since 1.1.3
		 */
		public function get_ordermeta_list() {
			$ordermeta = array( '_ywpar_points_earned', '_ywpar_conversion_points', '_ywpar_total_points_refunded' );

			return apply_filters( 'ywpar_ordermeta_list', $ordermeta );
		}
		/**
		 * Load YIT Plugin Framework
		 *
		 * @since  1.0.0
		 * @return boolean
		 * @author Emanuela Castorina
		 */
		public function is_enabled() {

			$enabled = $this->get_option( 'enabled' );

			if ( $enabled == 'yes' ) {
				return true;
			}

			return false;
		}


		/**
		 * Load YIT Plugin Framework
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Emanuela Castorina
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}


		/**
		 * Add a record inside the table of log
		 *
		 * @param            $user_id
		 * @param            $action
		 * @param            $order_id
		 * @param            $amount
		 * @param bool|false $data_earning
		 * @param bool|false $expired
		 * @param string     $description
		 */
		public function register_log( $user_id, $action, $order_id, $amount, $data_earning = false, $expired = false, $description = '' ) {
			global $wpdb;
			$date       = apply_filters( 'ywpar_points_registration_date', date_i18n( 'Y-m-d H:i:s' ) );
			$table_name = $wpdb->prefix . 'yith_ywpar_points_log';
			$args       = array(
				'user_id'      => $user_id,
				'action'       => $action,
				'order_id'     => $order_id,
				'amount'       => $amount,
				'date_earning' => ( $data_earning ) ? $data_earning : $date,
				'description'  => $description,
			);

			if ( $expired ) {
				$args['cancelled'] = $date;
			}

			$wpdb->insert( $table_name, $args );
		}
		/**
		 * Get options from db
		 *
		 * @access  public
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 * @param $option string
		 * @param $value  mixed
		 *
		 * @return mixed
		 */
		public function get_option( $option, $value = false ) {

			// new version
			$db_value = get_option( 'ywpar_' . $option, $value );

			if ( $db_value !== false ) {
				$value = $db_value;
			} else {
				// get all options
				$options = get_option( $this->plugin_options, $value );
				if ( isset( $options[ $option ] ) ) {
					$value = $options[ $option ];
				}
			}

			return $value;
		}

		/**
		 * Set options
		 *
		 * @access  public
		 * @since   1.3.0
		 * @author  Emanuela Castorina
		 *
		 * @param $option string
		 * @param $value  mixed
		 */
		public function set_option( $option, $value ) {
			// new_version
			update_option( 'ywpar_' . $option, $value );
		}


		/**
		 * @param $user_id
		 * @param $points_to_add
		 * @param $action
		 * @param $description
		 * @param string        $order_id
		 * @param string        $data_earning
		 * @param bool          $expired
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_point_to_customer( $user_id, $points_to_add, $action, $description = '', $order_id = '', $data_earning = '', $expired = false ) {
			$current_point = get_user_meta( $user_id, '_ywpar_user_total_points', 1 );
			$current_point = empty( $current_point ) ? 0 : (int) $current_point;
			// add the new points to the total points of customer
			$p = $current_point + $points_to_add;
			// APPLY_FILTER : ywpar_disable_negative_point: disable or not negative points
			if ( apply_filters( 'ywpar_disable_negative_point', true, $user_id, $p, $action, $order_id ) ) {
				$p = $p > 0 ? $p : 0;
			}
			update_user_meta( $user_id, '_ywpar_user_total_points', $p );
			$this->register_log( $user_id, $action, $order_id, $points_to_add, $data_earning, $expired, $description );

			// if is a negative value add these points to the user meta value where rewarded points are stored.
			if ( $points_to_add < 0 ) {
				YITH_WC_Points_Rewards_Redemption()->set_user_rewarded_points( $user_id, $points_to_add );
			}
		}

	}


}

/**
 * Unique access to instance of YITH_WC_Points_Rewards class
 *
 * @return \YITH_WC_Points_Rewards
 */
function YITH_WC_Points_Rewards() {
	return YITH_WC_Points_Rewards::get_instance();
}

