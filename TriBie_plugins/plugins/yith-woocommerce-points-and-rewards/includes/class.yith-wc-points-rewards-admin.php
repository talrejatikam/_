<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements admin features of YITH WooCommerce Points and Rewards
 *
 * @class   YITH_WC_Points_Rewards_Admin
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Points_Rewards_Admin' ) ) {

	/**
	 * Class YITH_WC_Points_Rewards_Admin
	 */
	class YITH_WC_Points_Rewards_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Points_Rewards_Admin
		 */

		protected static $instance;

		/**
		 * @var Panel $_panel Object
		 */
		protected $_panel;

		/**
		 * @var string $_premium Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-points-and-rewards/';

		/**
		 * @var string Panel page
		 */
		protected $_panel_page = 'yith_woocommerce_points_and_rewards';

		/**
		 * @var string Doc Url
		 */
		public $doc_url = 'https://docs.yithemes.com/yith-woocommerce-points-and-rewards/';

		public $plugin_options = 'yit_ywpar_options';

		/**
		 * @var Wp List Table
		 */
		public $cpt_obj;



		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Points_Rewards_Admin
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

			$this->create_menu_items();

			// Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWPAR_DIR . '/' . basename( YITH_YWPAR_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			add_action( 'yit_panel_ywpar-options-conversion', array( $this, 'admin_options_conversion' ), 10, 2 );

			// custom styles and javascripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11 );

			/* Ajax action for reset points */
			add_action( 'wp_ajax_ywpar_reset_points', array( $this, 'reset_points' ) );
			add_action( 'wp_ajax_nopriv_ywpar_reset_points', array( $this, 'reset_points' ) );

		}

		/**
		 * Reset points from administrator points
		 *
		 * @return void
		 * @since 1.1.1
		 * @author Emanuela Castorina
		 */
		public function reset_points() {

			check_ajax_referer( 'reset_points', 'security' );
			YITH_WC_Points_Rewards()->reset_points();

			// from 1.1.1
			$response = __( 'Done!', 'yith-woocommerce-points-and-rewards' );

			wp_send_json( $response );
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles_scripts() {
			if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_woocommerce_points_and_rewards' ) ) {
				wp_enqueue_style( 'yith_ywpar_backend', YITH_YWPAR_ASSETS_URL . '/css/backend.css', YITH_YWPAR_VERSION );
				wp_enqueue_script(
					'yith_ywpar_admin',
					YITH_YWPAR_ASSETS_URL . '/js/ywpar-admin' . YITH_YWPAR_SUFFIX . '.js',
					array(
						'jquery',
					),
					YITH_YWPAR_VERSION,
					true
				);
				wp_enqueue_script( 'jquery-blockui', YITH_YWPAR_ASSETS_URL . '/js/jquery.blockUI.min.js', array( 'jquery' ), false, true );

				wp_localize_script(
					'yith_ywpar_admin',
					'yith_ywpar_admin',
					array(
						'ajaxurl'              => admin_url( 'admin-ajax.php' ),
						'reset_points'         => wp_create_nonce( 'reset_points' ),
						'reset_points_confirm' => __( 'Are you sure that want reset all points? This process is irreversible', 'yith-woocommerce-points-and-rewards' ),
						'block_loader'         => apply_filters( 'yith_ywpar_block_loader_admin', YITH_YWPAR_ASSETS_URL . '/images/block-loader.gif' ),
						'reset_point_message'  => __( 'Do you want to reset points for user', 'yith-woocommerce-points-and-rewards' ),
					)
				);
			}
		}

		/**
		 * Create Menu Items
		 *
		 * Print admin menu items
		 *
		 * @since  1.0
		 * @author Emanuela Castorina
		 */

		private function create_menu_items() {

			// Add a panel under YITH Plugins tab
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'yith_ywpar_premium_tab', array( $this, 'premium_tab' ) );
			add_action( 'yith_ywpar_customers', array( $this, 'customers_tab' ) );
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */

		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'customers' => __( 'Customer Points', 'yith-woocommerce-points-and-rewards' ),
				'general'   => __( 'Settings', 'yith-woocommerce-points-and-rewards' ),
				'points'    => __( 'Point Settings', 'yith-woocommerce-points-and-rewards' ),
				'labels'    => __( 'Labels', 'yith-woocommerce-points-and-rewards' ),
				'messages'  => __( 'Messages', 'yith-woocommerce-points-and-rewards' ),
				'premium'   => __( 'Premium Version', 'yith-woocommerce-points-and-rewards' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => _x( 'YITH WooCommerce Points and Rewards', 'Plugin name, do not translate', 'yith-woocommerce-points-and-rewards' ),
				'menu_title'       => _x( 'Points and Rewards', 'Plugin name, do not translate', 'yith-woocommerce-points-and-rewards' ),
				'capability'       => 'manage_options',
				'parent'           => 'ywpar',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWPAR_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_YWRAQ_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

			add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'get_yith_panel_custom_template' ), 10, 2 );
			add_filter( 'yith_plugin_fw_wc_panel_pre_field_value', array( $this, 'get_value_of_custom_type_field' ), 10, 2 );

			$this->save_default_options();

		}

		/**
		 * @param $template
		 * @param $field
		 *
		 * @return string
		 */
		public function get_yith_panel_custom_template( $template, $field ) {
			$custom_option_types = array(
				'options-conversion',
				'options-extrapoints',
			);
			$field_type          = $field['type'];
			if ( isset( $field['type'] ) && in_array( $field['type'], $custom_option_types ) ) {
				$template = YITH_YWPAR_TEMPLATE_PATH . "/panel/types/{$field_type}.php";
			}

			return $template;
		}

		/**
		 * @param $value
		 * @param $field
		 *
		 * @return mixed
		 */
		public function get_value_of_custom_type_field( $value, $field ) {
			$custom_option_types = array(
				'options-conversion',
				'options-extrapoints',
			);

			if ( isset( $field['type'] ) && in_array( $field['type'], $custom_option_types ) ) {
				$value = get_option( $field['id'], $field['default'] );
			}

			return $value;
		}


		/**
		 * Update setting from the old version to the new one.
		 */
		public function save_default_options() {

			$options                = maybe_unserialize( get_option( 'yit_ywpar_options', array() ) );
			$current_option_version = get_option( 'yit_ywpar_option_version', '0' );
			$forced                 = isset( $_GET['update_ywpar_options'] ) && $_GET['update_ywpar_options'] == 'forced';
			$multicurrency          = get_option( 'yit_ywpar_multicurrency' );

			if ( version_compare( $current_option_version, YITH_YWPAR_VERSION, '>=' ) && ! $forced ) {
				return;
			}

			$new_option = array_merge( $this->_panel->get_default_options(), (array) $options );
			update_option( 'yit_ywpar_options', $new_option );
			update_option( 'yit_ywpar_option_version', YITH_YWPAR_VERSION );

			if ( false === $multicurrency ) {
				ywpar_conversion_points_multilingual();
			}

			ywpar_options_porting( $options );

			update_option( 'yit_ywpar_option_version', YITH_YWPAR_VERSION );
		}


		/**
		 * Template for admin section
		 *
		 * @since  1.0.0
		 * @access public
		 * @author Emanuela Castorina
		 *
		 * @param $option
		 * @param $db_value
		 */
		public function admin_options_conversion( $option, $db_value ) {
			include YITH_YWPAR_TEMPLATE_PATH . '/panel/types/ywpar-options-conversion.php';
		}


		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */

		public function premium_tab() {
			$premium_tab_template = YITH_YWPAR_TEMPLATE_PATH . '/admin/' . $this->_premium;

			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}
		}




		/**
		 * Customers Tab Template
		 *
		 * Load the customers tab template on admin page
		 *
		 * @return   void
		 * @since    1.0.0
		 * @author   Emanuela Castorina
		 */
		public function customers_tab() {
			$points = 0;
			$type   = 'view';
			if ( isset( $_REQUEST['action'] ) && isset( $_REQUEST['user_id'] ) ) {
				$user_id = $_REQUEST['user_id'];
				$type    = 'customer';
				$link    = remove_query_arg( array( 'action', 'user_id' ) );

				$this->cpt_obj = new YITH_WC_Points_Rewards_Customer_History_List_Table();
			} else {
				$this->cpt_obj = new YITH_WC_Points_Rewards_Customers_List_Table();
			}

			$customers_tab = YITH_YWPAR_TEMPLATE_PATH . '/admin/customers-tab.php';
			if ( file_exists( $customers_tab ) ) {
				include_once $customers_tab;
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 */

		public function action_links( $links ) {
			if ( function_exists( 'yith_add_action_links' ) ) {
				$links = yith_add_action_links( $links, $this->_panel_page, false );
			}

			return $links;
		}


		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $new_row_meta_args
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @param string            $init_file
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWPAR_FREE_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_YWPAR_SLUG;
			}

			return $new_row_meta_args;
		}



		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return $this->_premium_landing;
		}




	}
}

/**
 * Unique access to instance of YITH_WC_Points_Rewards_Admin class
 *
 * @return \YITH_WC_Points_Rewards_Admin
 */
function YITH_WC_Points_Rewards_Admin() {
	return YITH_WC_Points_Rewards_Admin::get_instance();
}
