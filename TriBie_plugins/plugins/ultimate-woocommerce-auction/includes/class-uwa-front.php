<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) 
	exit;
 /**
  * Front Side  Class
  *
  * Handles generic Front functionality and AJAX requests.
  *
  * @package Ultimate WooCommerce Auction
  * @author Nitesh Singh 
  * @since 1.0
  */
  
class UWA_Front {
	
	private static $instance;
	
	public $uwa_types;	
	public $uwa_item_condition;
	
	/**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
	 
    public static function get_instance() {
		
        if ( null === self::$instance ) {
			
            self::$instance = new self();
			
        }
		
        return self::$instance;
    }
	
	
	public function __construct() {	
		
		
		if ( ! is_admin() || defined('UWA_DOING_AJAX') ) {
			
			// Bidding Area On single product page		
			add_action( 'woocommerce_single_product_summary', array($this,'woocommerce_uwa_auction_bid'), 25 );
			
			// Product Add to cart
			add_action( 'woocommerce_auction_add_to_cart', array($this,'woocommerce_uwa_auction_add_to_cart'), 30 );
			
			if (is_user_logged_in()) {
				//Pay Now Button for auction winner
				add_action( 'woocommerce_single_product_summary', array($this,'woocommerce_uwa_auction_pay'), 26 );
				
				//Pay Now Button for auction winner loop/shop page
		        add_action('woocommerce_after_shop_loop_item', array($this,'uwa_pay_now_winner_fun'), 60);
			}
		}
	
		
		add_filter( 'post_class', array($this,'uwa_extra_div_class_start'));
		
		//Add To cart item
		add_action('wp_loaded', array($this,'uwa_add_product_to_cart'));		
		
		//Auction Product Badge shop/loop
		add_action('woocommerce_before_shop_loop_item_title',array($this,'uwa_auction_bage_fun'), 60);
		
		//Auction Product Badge for Winner shop/loop
		add_action('woocommerce_before_shop_loop_item_title',array($this,'uwa_auction_bage_fun_winning'), 60);		
		
		//Auction Product Badge single auction page
		add_filter('woocommerce_single_product_image_html', array($this, 'uwa_auction_badge_single_product'), 60);					
		//Auction Type
		$this->uwa_types =  array('normal' => __('Normal', 'woo_ua'), 'reverse' => __('Reverse', 'woo_ua'));
		
		//Auction Condition
		$this->uwa_item_condition =  array('new' => __('New', 'woo_ua'), 'used' => __('Used', 'woo_ua'));
		
	
		//Total Bids Place Section On Auction Detail Page
		if( get_option( 'woo_ua_auctions_bids_section_tab' ) == 'yes' ) {
		
			add_action('woocommerce_product_tabs', array($this, 'uwa_auction_bids_tab'), 10);			
		
		}
		
		//Review Section On Auction Detail Page
		if( get_option( 'woo_ua_auctions_bids_reviews_tab' ) !== 'yes' ) {
			
			//add_action('woocommerce_product_tabs', array($this, 'uwa_remove_product_reviews_tab'), 98);
					
		}
		
		//Private Message Section On Auction Detail Page		
		if( get_option( 'woo_ua_auctions_private_message' ) == 'yes' ) {
		
			add_action('woocommerce_product_tabs', array($this, 'uwa_auction_private_msg_tab'));
		
			//Ajax For Private Message		
			
			add_action("wp_ajax_send_private_message_process", array($this, "send_private_message_process_ajax"));
			
			add_action("wp_ajax_nopriv_send_private_message_process", array($this, "send_private_message_process_ajax"));
		
		}
		
		//Watchlist Section On Auction Detail Page		
		if( get_option( 'woo_ua_auctions_watchlists' ) == 'yes' ) {
		
			//for Single page
			add_action('ultimate_woocommerce_auction_before_bid_form', array($this, 'add_watchlist_button'), 10);
			
			//for shop/loop 
			add_action('woocommerce_after_shop_loop_item', array($this, 'add_to_watchlist_loop'), 90);
			
			add_action("uwa_ajax_watchlist", array($this, "uwa_ajax_watchlist_auction"));	
			
		}
		
		// Ajax Action to cehck auction finish or not		
		add_action("wp_ajax_expired_auction", array($this, "uwa_ajax_finish_auction_fun"));		
		add_action("uwa_ajax_expired_auction", array($this, "uwa_ajax_finish_auction_fun"));
		
		
		//Product Query modification
		add_action('woocommerce_product_query', array($this, 'uwa_delete_from_woocommerce_product_query'), 2);
		
		//Last Activity Timestamps
		add_action('ultimate_woocommerce_auction_place_bid', array($this, 'update_last_activity_timestamp'), 1);
		add_action('ultimate_woocommerce_auction_delete_bid', array($this, 'update_last_activity_timestamp'), 1);
		add_action('ultimate_woocommerce_auction_close', array($this, 'update_last_activity_timestamp'), 1);
		add_action('ultimate_woocommerce_auction_started', array($this, 'update_last_activity_timestamp'), 1);
		
		
		//Ajax Check Auction Live Status 
		add_action("wp_ajax_get_live_stutus_auction", array($this, "uwa_get_live_stutus_auction_callback"));
		add_action("wp_ajax_nopriv_get_live_stutus_auction", array($this, "uwa_get_live_stutus_auction_callback"));
		add_action("uwa_ajax_get_live_stutus_auction", array($this, "uwa_get_live_stutus_auction_callback"));
		
		//Modify is_purchasable 
		add_filter('woocommerce_is_purchasable', array($this, 'is_purchasable'), 10, 2);
		
		//Redirect Auction page After login
		add_action('woocommerce_login_form_end', array($this,
			'add_redirect_after_login') );

		/* Redirect Auction page After Registration */
		add_action('woocommerce_register_form_end', array($this,
			'add_redirect_after_register') );
		
		//remove action product expired/schedule 
		add_action('woocommerce_product_query', array($this, 'pre_get_posts'), 99, 2);
		
		
	}	
	
	/**
	 * Auction Page template
	 *
	 * Add the auction template
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */	
	public function woocommerce_uwa_auction_bid() {
		
		global $product;
		
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction')
			
			wc_get_template( 'single-product/uwa-bid.php' );
	}
	
	/**
	 *  Auction Product Add to Cart Area.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */
	public function woocommerce_uwa_auction_add_to_cart() {
		
		global $product;		
		
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction')
			
			wc_get_template( 'single-product/add-to-cart/uwa-auction.php' );
	}
	
	/**
	 *  Auction Product Pay Now Button Single Page.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */	
	public function woocommerce_uwa_auction_pay() {
		
		global $product;
		
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction')
			
			wc_get_template( 'single-product/uwa-pay.php' );
	}
	
	/**
	 *  Auction Product Pay Now Button Shop/loop.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */	
	public	function uwa_pay_now_winner_fun() {
		
		wc_get_template('loop/uwa-pay-button.php');
		
	}	
	
	/**
	 *  Auction Product  Add to Cart After Pay Now Button Click.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */	
	public function uwa_add_product_to_cart() {

		if (!is_admin()) {

			if (!empty($_GET['pay-uwa-auction'])) {

				$current_user = wp_get_current_user();

				$product_id = $_GET['pay-uwa-auction'];
				$product_data = wc_get_product($product_id);

				if (!$product_data) {
					wp_redirect(home_url());
					exit;
				}
				if (!is_user_logged_in()) {
					header('Location: ' . wp_login_url(WC()->cart->get_checkout_url() . '?pay-uwa-auction=' . $product_id));
					exit;
				}
				if ($current_user->ID != $product_data->get_woo_ua_auction_current_bider()) {
					wc_add_notice(sprintf(__('You can not buy this auction because you have not won it!', 'woo_ua'), $product_data->get_title()), 'error');
					return false;
				}
				WC()->cart->add_to_cart($product_id);
				wp_safe_redirect(remove_query_arg(array('pay-uwa-auction', 'quantity', 'product_id'), WC()->cart->get_checkout_url()));
				exit;
			}
		}
	}	
	
	/**
	 * Add Auction Badge for Auction Product Shop/loop.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */		
	public	function uwa_auction_bage_fun() {
			global $product;
			
			if (  method_exists( $product, 'get_type') && $product->get_type() == 'auction' ) {
				
				echo '<span class="uwa_auction_bage_icon"  ></span>';
			 
			}
	}
	
	/**
	 * Add Auction Badge for Auction Product Page.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */			
	public function uwa_auction_badge_single_product( $output ){
		   global $product;
		   
			if (  method_exists( $product, 'get_type') && $product->get_type() == 'auction' ) {
				
				echo	$output .= '<span class="uwa_auction_bage_icon"  ></span>';
				
			}
			
		return $output;
	}	
			
	/**
	 * Add Auction Badge for Winner Shop/loop.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */	
	public	function uwa_auction_bage_fun_winning() {
		  global $product;
		  
			if (is_user_logged_in()) {	
			
					if (  method_exists( $product, 'get_type') && $product->get_type() == 'auction' ) {
						
						$user_id  = get_current_user_id();

						if ( $user_id == $product->get_woo_ua_auction_current_bider() && !$product->get_woo_ua_auction_closed() ) {
							
							echo '<span class="uwa_winning" data-auction_id="'.$product->get_id().'" data-user_id="'.get_current_user_id().'">'.__( 'Winning!', 'woo_ua' ).'</span>';

						}
					}
				
			}

	}
	
	/**
	 * Add Bids Tab Single Page.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */	
	public function uwa_auction_bids_tab($tabs) {
			global $product;
				if(method_exists( $product, 'get_type') && $product->get_type() == 'auction') {
					
					$tabs['uwa_auction_bids_history'] = array(
						'title' => __('Bids', 'woo_ua'),
						'priority' =>25,
						'callback' => array($this, 'uwa_auction_bids_tab_callback'),
						
					);
				}
				
			return $tabs;
	}
	
	/**
	 * Auction call back from bids_tab.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */		
	public function uwa_auction_bids_tab_callback($tabs) {
	
		wc_get_template('single-product/tabs/uwa-bids-history.php');
	}
	
	/**
	 * Unset Review Tab Single Page.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */	
	public function uwa_remove_product_reviews_tab( $tabs ) {
		
		global $product;
				
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction') {

			//unset( $tabs['reviews'] );  // Removes the reviews tab	

		}		
		return $tabs;

	}
	
	/**
	 * Add Private message Tab Single Page.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */		
	public function uwa_auction_private_msg_tab( $tabs ) {
				global $product;
				
				if(method_exists( $product, 'get_type') && $product->get_type() == 'auction') {
					
					$tabs['uwa_auction_private_msg_tab'] = array(
						'title' => __('Private message', 'woo_ua'),
						'priority' =>50,
						'callback' => array($this, 'uwa_auction_private_msg_tab_callback'),
						
					);
				}
				
				return $tabs;
	}
	
	/**
	 * Auction call back from Private Message Tab.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 * @return void
	 */
	 
	public function uwa_auction_private_msg_tab_callback($tabs) {
	
		wc_get_template('single-product/tabs/uwa-private-msg.php');
	}
	
	/**
	 * Auction Private Message Send Mail To Admin.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0	
	 * @return json
	 */	
	function send_private_message_process_ajax() {
			
		$firstname = $_POST['firstname'];
		$email_id = $_POST['email'];
		$message = $_POST['message'];
		$product_id = $_POST['product_id'];
		$sending = 1;
		
			if(empty($firstname)){
				$response['status'] = 0;				
				$response['error_name'] = __('Please enter your Name!','woo_ua');
				$sending = 0;
			} 
			if(!is_email($email_id) || empty($email_id)){
				$response['status'] = 0;
				$response['error_email'] = __('Please enter your Email address!','woo_ua');
				$sending = 0;
			}
			if(empty($message)){
				$response['status'] = 0;
				$response['error_message'] = __('Please enter a message!','woo_ua');
				$sending = 0;
			}
			
			if($sending == 1){
				   //Seding private message to admin
				
				  $user_args = array(
					'user_name' => $firstname,
					'user_email' => $email_id,
					'user_message' => $message,
					'product_id' => $product_id,
				  );
			
				 WC()->mailer();							   
				 do_action('uwa_private_msg_email_admin',$user_args);
				
				$response['status'] = 1;
				$response['success_message'] = __('Thank you for Contact.','woo_ua');
				
			}
			
		echo json_encode( $response );
		exit;
	}		
		
	/**
	 * Add Watchlist Button.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0	 
	 */		
	function add_watchlist_button() {
		
			wc_get_template('single-product/uwa-watch.php');
			
	}	
	
	/**
	* Add Watchlist Button.
	*
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0	 
	*/	
	function add_to_watchlist_loop() {

		global $watchlist;

		if (isset($watchlist) && $watchlist == true) {
			
			wc_get_template('single-product/uwa-watch.php');
		}

	}	
			
	/**
	 * Ajax watch list auction
	 *
	 * Function for adding or removing auctions to watchlist
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 *
	 */
	function uwa_ajax_watchlist_auction() {

		if (is_user_logged_in()) {

			global $product;
			global $sitepress;
			
		    $post_id = intval($_GET["post_id"]);
			
			/* For WPML Support - start */
			if (function_exists('icl_object_id') && method_exists($sitepress, 
				'get_default_language')) {
				
				$post_id = icl_object_id($post_id, 'product', false, 
					$sitepress->get_default_language());
			}			
			/* For WPML Support - end */
			
			$user_ID = get_current_user_id();
			$product = wc_get_product($post_id);

			if ($product) {

				if ($product->is_woo_ua_user_watching()) {
						delete_post_meta($post_id, 'woo_ua_auction_watch', $user_ID);
						delete_user_meta($user_ID, 'woo_ua_auction_watch', $post_id);
						do_action('ultimate_woocommerce_auction_delete_from_watchlist',$post_id, $user_ID);
				} else {
						/*add_post_meta($post_id, 'woo_ua_auction_watch', $user_ID);
						add_user_meta($user_ID, 'woo_ua_auction_watch', $post_id);*/
						update_post_meta($post_id, 'woo_ua_auction_watch', $user_ID);
						update_user_meta($user_ID, 'woo_ua_auction_watch', $post_id);
						do_action('ultimate_woocommerce_auction_after_add_to_watchlist', $post_id, $user_ID);
				}
				wc_get_template('single-product/uwa-watch.php');
			}

		} else {

			echo "<p>";
			
			printf(__('<span class="watchlist-error">Please Login/Register in to add auction to watchlist. </span><a href="%s" class="button watchlist-error">Login/Register &rarr;</a>', 'woo_ua'), get_permalink(wc_get_page_id('myaccount')));
			echo "</p>";
		}

		exit;
	}	
	
	/**
	* Ajax function for checking finishing auction
	*		
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0	
	*
	*/
	function uwa_ajax_finish_auction_fun() {
		
		if (isset($_POST["post_id"])) {			 
			
				$product_data = wc_get_product( wc_clean( $_POST["post_id"] ) );
				if ($product_data->is_woo_ua_closed()) {

					if (isset($_POST["ret"]) && $_POST["ret"] != '0') {
                          
						if ($product_data->is_woo_ua_reserved()) {
							if (!$product_data->is_woo_ua_reserve_met()) {
								
								echo "<p class='woo_ua_auction_product_reserve_not_met'>";
								_e("Reserve price has not been met!", 'woo_ua');
								echo "</p>";							
								die();
							}
						}
						
						$current_bidder = $product_data->get_woo_ua_auction_current_bider();						
						if ($current_bidder) {						
							
							printf(__("Winning bid is %s by %s.", 'woo_ua'), wc_price($product_data->get_woo_ua_current_bid()), uwa_user_display_name($current_bidder));
							echo "</p>";
							if (get_current_user_id() == $product_data->get_woo_ua_auction_current_bider()){
								echo '<p><a href="'.apply_filters( 'ultimate_woocommerce_auction_pay_now_button_text',esc_attr(add_query_arg("pay-uwa-auction",$product_data->get_id(), woo_ua_auction_get_checkout_url()))).'" class="button">'.__( 'Pay Now', 'woo_ua' ).'</a></p>';
							}
							
						} else {
							echo "<p>";
							_e("There were no bids for this auction.", 'woo_ua');
							echo "</p>";
							die();
						}

					}

				} else {

					echo "<div>";
					
					printf(__("Please refresh page.", 'woo_ua'));

					echo "</div>";
				}
		}
		die();
	}	

	/**
	 * Based on Setting Modify Product Query.
	 *
	 * @package Ultimate WooCommerce Auction
	 * @author Nitesh Singh 
	 * @since 1.0
	 *
	 */
	function uwa_delete_from_woocommerce_product_query( $q ) {

		// do with main query
		if (!$q->is_main_query()) {
			return;
		}

		if($q === true ){
			return;
		}

		if (!$q->is_post_type_archive('product') && !$q->is_tax(get_object_taxonomies('product'))) {
			return;
		}
		
		//Hide/show Auction product on shop page
		$woo_ua_show_auction_pages_shop = get_option('woo_ua_show_auction_pages_shop');
		
		if ($woo_ua_show_auction_pages_shop != 'yes' && (!isset($q->query_vars['is_auction_archive']) OR $q->query_vars['is_auction_archive'] !== 'true')) {
				$taxquery = $q->get('tax_query');
				if (!is_array($taxquery)) {
					$taxquery = array();
				}
				$taxquery[] =
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => 'auction',
					'operator' => 'NOT IN',
				);
				$q->set('tax_query', $taxquery);
		}
		
		//Hide/show Auction product on category page page				
		$woo_ua_show_auction_pages_cat = get_option('woo_ua_show_auction_pages_cat');
		
		if ($woo_ua_show_auction_pages_cat != 'yes' && is_product_category()) {
			
			$taxquery = $q->get('tax_query');
			if (!is_array($taxquery)) {
				$taxquery = array();
			}
			$taxquery[] =
			array(
				'taxonomy' => 'product_type',
				'field' => 'slug',
				'terms' => 'auction',
				'operator' => 'NOT IN',
			);
			$q->set('tax_query', $taxquery);
		}
		
		//Hide/show Auction product on Tag page page	
		$woo_ua_show_auction_pages_tag = get_option('woo_ua_show_auction_pages_tag');
		
		if ($woo_ua_show_auction_pages_tag != 'yes' && is_product_tag()) {
			$taxquery = $q->get('tax_query');
			if (!is_array($taxquery)) {
				$taxquery = array();
			}
			$taxquery[] =
			array(
				'taxonomy' => 'product_type',
				'field' => 'slug',
				'terms' => 'auction',
				'operator' => 'NOT IN',
			);
			$q->set('tax_query', $taxquery);
		}
		
		//Hide/show Auction product on Search page page
		$woo_ua_show_auction_pages_search = get_option('woo_ua_show_auction_pages_search');

		if (!is_admin() && $q->is_main_query() && $q->is_search()) {

			if (isset($q->query['search_auctions']) && $q->query['search_auctions'] == TRUE) {
				$taxquery = $q->get('tax_query');
				if (!is_array($taxquery)) {
					$taxquery = array();
				}
				$taxquery[] =
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => 'auction',

				);

				$q->set('tax_query', $taxquery);
				$q->query['auction_arhive'] = TRUE;

			} elseif ($woo_ua_show_auction_pages_search == 'yes') {

				$taxquery = $q->get('tax_query');
				if (!is_array($taxquery)) {
					$taxquery = array();
				}
				$taxquery[] =
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => 'auction',
					'operator' => 'NOT IN',
				);

				$q->set('tax_query', $taxquery);
			}

			return;

		}

	}	
	
	/**
	* Update Last Activity.
	*
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*
	*/	
	function update_last_activity_timestamp( $data ){

			$product_id = is_array($data) ? $data['product_id'] : $data;
			$current_time = current_time('timestamp');
			
			update_option('woo_ua_auction_last_activity', $current_time);
			update_post_meta($product_id, 'woo_ua_auction_last_activity', $current_time);

	}
	/**
	* Ajax get Live Status For Auctions
	*
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	* @return json
	*
	*/
	function uwa_get_live_stutus_auction_callback() {		
		$response = null;						 
		if (isset($_POST["last_timestamp"])) {
			
			$last_timestamp = get_option('woo_ua_auction_last_activity','0');

			if(intval($_POST['last_timestamp']) == $last_timestamp){
				wp_send_json(apply_filters('woo_auction_get_price_for_auctions',$response));
				die();
			} else{
				$response['last_timestamp'] = $last_timestamp;
			}	
		 
		 $args = array(
				'post_type' => 'product',
				'posts_per_page' => '-1',
				'meta_query' => array(
					array(
						'key'     => 'woo_ua_auction_last_activity',
						'compare' => '>',
						'value'		=> 	intval($_POST['last_timestamp']),
						'type' => 'NUMERIC'
					),
				),						
				'fields' => 'ids',


			);
			$the_query = new WP_Query($args);
			
			$posts_ids = $the_query->posts;	
			if(is_array($posts_ids)){
				foreach ($posts_ids as $posts_id) {
					$product_data = wc_get_product($posts_id);
					$response[$posts_id]['wua_curent_bid'] = $product_data->get_price_html();
					$response[$posts_id]['wua_current_bider'] = $product_data->get_woo_ua_auction_current_bider();
					$response[$posts_id]['wua_timer'] = $product_data->get_woo_ua_remaining_seconds();
					$response[$posts_id]['wua_activity'] = $product_data->woo_ua_auction_history_last($posts_id);
					$response[$posts_id]['wua_bid_value'] = $product_data->woo_ua_bid_value();
					$response[$posts_id]['wua_bid_value_inc'] = wc_price($product_data->woo_ua_bid_value());
										
					$response[$posts_id]['add_to_cart_text'] = $product_data->add_to_cart_text();
					if ($product_data->is_woo_ua_reserved() === TRUE) {
						if ($product_data->is_woo_ua_reserve_met() === FALSE) {
							$response[$posts_id]['wua_reserve'] = __("Reserve price has not been met.", 'woo_ua');
						} elseif ($product_data->is_woo_ua_reserve_met() === TRUE) {
							$response[$posts_id]['wua_reserve'] =__("Reserve price has been met.", 'woo_ua');
						}

					}
				}
				
				//echo "<pre>";
				//print_r($response);
				//exit;
			}
			
		}
		wp_send_json(apply_filters('woo_auction_get_price_for_auctions',$response));
		die();
		
	}
	
	
		
	/**
	* Modify is_purchasable For Auction Product
	*
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*
	*/	
	function is_purchasable( $is_purchasable, $object ) {

		$object_type = method_exists( $object, 'get_type' ) ? $object->get_type() : $object->product_type;
		if ($object_type == 'auction') {
			
			if (!$object->get_woo_ua_auction_closed() && $object->get_woo_ua_auction_type() == 'normal' && ($object->get_price() < $object->get_woo_ua_current_bid())) {
				return false;
			} 
			
			if (!$object->get_woo_ua_auction_closed() && !$object->get_woo_ua_auction_closed() && $object->get_price() !== '') {
				return TRUE;
			}

			if (!is_user_logged_in()) {
				return false;
			}

			$current_user = wp_get_current_user();
			if ($current_user->ID != $object->get_woo_ua_auction_current_bider()) {
				return false;
			}

			if (!$object->get_woo_ua_auction_closed()) {
				return false;
			}
			if ($object->get_woo_ua_auction_closed() != '2') {
				return false;
			}
			

			return TRUE;
		}
		return $is_purchasable;
	}
	
	/**
	* Redirect Auction page After login
	*
	* Add Custom $_GET parameters in form for redirect to single product page
	* 
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*
	*/	
	public function add_redirect_after_login() {

		global $post;		

		$slug =  $post->post_name; /* default = my-account */
		
		if(isset($_SERVER["HTTP_REFERER"])){

			/* check which is referer page */
			$url = $_SERVER["HTTP_REFERER"];
			$url_parts = explode("/", $url);
			$total = count($url_parts);	
			$refer_slug  = $url_parts[$total - 2];


			if($refer_slug != $slug){
				$auction_url = $_SERVER["HTTP_REFERER"];				
			} 
			else{				
				if(isset($_REQUEST['redirect'])){
					$auction_url = $_REQUEST['redirect'];		
				}
				else{
					$auction_url = $_SERVER["HTTP_REFERER"];
				}
			} 
						
			echo '<input type="hidden" name="redirect" 
					value="'.$auction_url.'" >';


		} /* end of if - http referer */

	}

	/**
	* Redirect Auction page After Registration
	*
	* Add Custom $_GET parameters in form for redirect to single product page
	* 
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*
	*/	
	public function add_redirect_after_register() {

		global $post;		

		$slug =  $post->post_name; /* default = my-account */
		
		if(isset($_SERVER["HTTP_REFERER"])){

			/* check which is referer page */
			$url = $_SERVER["HTTP_REFERER"];
			$url_parts = explode("/", $url);
			$total = count($url_parts);	
			$refer_slug  = $url_parts[$total - 2];


			if($refer_slug != $slug){
				$auction_url = $_SERVER["HTTP_REFERER"];				
			} 
			else{				
				if(isset($_REQUEST['redirect'])){
					$auction_url = $_REQUEST['redirect'];		
				}
				else{
					$auction_url = $_SERVER["HTTP_REFERER"];
				}
			} 
						
			echo '<input type="hidden" name="redirect" 
					value="'.$auction_url.'" >';


		} /* end of if - http referer */

	}

	
			/**
			 * Modify query based on settings
			 *
			 * @access public
			 * @param object
			 * @return object
			 *
			 */
			function pre_get_posts($q) {

				$auction = array();
				$woo_ua_expired_auction_enabled = get_option('woo_ua_expired_auction_enabled');				
				$woo_ua_show_auction_pages_shop = get_option('woo_ua_show_auction_pages_shop');
				$woo_ua_show_auction_pages_cat = get_option('woo_ua_show_auction_pages_cat');
				$woo_ua_show_auction_pages_tag = get_option('woo_ua_show_auction_pages_tag');

				if (

					($woo_ua_expired_auction_enabled != 'yes' && (!isset($q->query['show_expired_auctions']) or !$q->query['show_expired_auctions'])
						OR (isset($q->query['show_expired_auctions']) && $q->query['show_expired_auctions'] == FALSE)
					)
				) {

					$metaquery = $q->get('meta_query');
					if (!is_array($metaquery)) {
						$metaquery = array();
					}

					$metaquery[] =array(

							'key'     => 'woo_ua_auction_closed',
							'compare' => 'NOT EXISTS',
						);

					$q->set('meta_query', $metaquery);

				}

				if ($woo_ua_show_auction_pages_cat != 'yes' && is_product_category()) {
					return;
				}

				if ($woo_ua_show_auction_pages_tag != 'yes' && is_product_tag()) {
					return;
				}

				if (!isset($q->query_vars['auction_arhive'])  && !$q->is_main_query()) {

					if ($woo_ua_show_auction_pages_shop == 'yes') {

						$taxquery = $q->get('tax_query');
						if (!is_array($taxquery)) {
							$taxquery = array();
						}
						$taxquery[] =
						array(
							'taxonomy' => 'product_type',
							'field' => 'slug',
							'terms' => 'auction',
							'operator' => 'NOT IN',
						);

						$q->set('tax_query', $taxquery);
						return;
					}

					return;
				}

			}
         
		
	function uwa_extra_div_class_start($classes) {
	
	global $post,$product;
	
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction') {		
			
			if(($product->is_woo_ua_closed() === FALSE ) and ($product->is_woo_ua_started() === TRUE )) {
				$classes[] .= 'uwa_auction_status_live';
			}
			if($product->is_woo_ua_closed() === True ) {
				$classes[] .= 'uwa_auction_status_expired';
			}
			
			return $classes; 
			
		} else {
				  return $classes;
			  }
		}
		
	
}

UWA_Front::get_instance();