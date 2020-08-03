<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Class
 *
 * Handles generic Admin functionality and AJAX requests.
 *
 * @package Ultimate WooCommerce Auction
 * @author Nitesh Singh 
 * @since 1.0
 */
class Woo_Ua_Admin {
	
	
	private static $instance;	
	public $woo_ua_auction_item_condition;
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
		
		//Admin Menu Page init
		add_action('admin_menu', array($this, 'woo_ua_auction_log_page'));
		
		//Create new Product Type - Auction
		add_filter( 'product_type_selector', array( $this, 'woo_ua_add_auction_product' ) );
		
		//Create Auction Product Tab
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'woo_ua_custom_product_tabs' ) );
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'woo_ua_hide_attributes_data_panel' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'woo_ua_options_product_tab_content' ) );
		
		//Save Auction Product Data
		add_action( 'woocommerce_process_product_meta_auction', array( $this, 'woo_ua_save_auction_option_field' )  );		
		
		//Auction Product Metabox - Bid History
		add_action( 'add_meta_boxes_product', array( $this, 'woo_ua_add_auction_metabox') );	
		
		//Add Auction Setting Page on woocoomrce Setting page
		add_filter('woocommerce_get_settings_pages', array($this, 'woo_ua_auction_settings_class_fun'));
		
		//Auction Product Condition
		$this->woo_ua_auction_item_condition =  array('new' => __('New', 'woo_ua'), 'used' => __('Used', 'woo_ua'));		
		
		//Emails For Admin 
     	 add_filter('woocommerce_email_classes', array($this, 'woo_ua_register_email_classes'));
		 
		//Emails for html/plain
        add_filter( 'woocommerce_locate_core_template', array( $this, 'woo_ua_locate_core_template' ), 10, 3 );	
		
		//Filter On Admin product List page For Auction Product Type
		add_action('restrict_manage_posts', array($this, 'admin_woo_ua_filter_restrict_manage_posts'));		
		add_filter('parse_query', array($this, 'admin_woo_ua_filter'));
		
		//processing auction  product  item with woocoomrce order.
		add_action('woocommerce_order_status_processing', array($this, 'woo_ua_auction_payed'), 10, 1);
		add_action('woocommerce_order_status_completed', array($this, 'woo_ua_auction_payed'), 10, 1);
		add_action('woocommerce_order_status_cancelled', array($this, 'woo_ua_auction_order_canceled'), 10, 1);
		add_action('woocommerce_order_status_refunded', array($this, 'woo_ua_auction_order_canceled'), 10, 1);
		add_action('woocommerce_checkout_update_order_meta', array($this, 'woo_ua_auction_order'), 10, 2);
		
		//Bid Cancel By Admin
		add_action("wp_ajax_admin_cancel_bid", array($this, "wp_ajax_admin_cancel_bid"));
		
		// End Auction by Admin 
		add_action("wp_ajax_admin_end_auction_now", array($this, "wp_ajax_admin_end_auction_now"));
		
		// Delete Auction product Meta While duplicating Products
		add_action("woocommerce_duplicate_product", array($this, "woocommerce_duplicate_product"));
		
		//custom js
		add_action( 'admin_footer', array( $this, 'woo_ua_auction_custom_js' ) );
		
		//new auction status in admin side in product list page		
		add_filter( 'manage_edit-product_columns',array( $this, 'woo_ua_auctions_status_columns'), 20 );
		
		add_action( 'manage_product_posts_custom_column', array( $this, 'woo_ua_auctions_status_columns_status' ),10, 2  );

	}
	/**
	* Add Page In Admin Menu.
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*/
	public function woo_ua_auction_log_page(  ){

		$hook = add_submenu_page('woocommerce', 'Auctions', 'Auctions', 'manage_woocommerce', 'woo_ua_auctions_setting', array($this, 'woo_ua_auctions_setting'));

	}
	/**
	* Auction Setting Callback Function.
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*/	
	public function woo_ua_auctions_setting() {

		if ( ! empty( $_GET['_wp_http_referer'] ) ) {			
			wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );	
			exit;
		
		}
		include_once( WOO_UA_ADMIN . '/woo_ua_general_setting.php');
	}	
	/**
	* Add to product type drop down.
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*/
	public function woo_ua_add_auction_product( $types ){
		// Key should be exactly the same as in the class
		$types[ 'auction' ] = __( 'Auction Product', 'woo_ua' );
		return $types;
	}
	/**
	* Add a custom product tab.
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*/
	public  function woo_ua_custom_product_tabs( $product_data_tabs) {	  
	    $auction_tab = array(
						'auction_tab' => array(
									'label'  => __('Auction', 'woo_ua'),
									'target' => 'auction_options',
									'class'  => array('show_if_auction' , 'hide_if_grouped', 'hide_if_external','hide_if_variable','hide_if_simple' ),
								),
					);

				return $auction_tab + $product_data_tabs;
    }
	/**
	* Hide Attributes data panel.
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*/
	public  function woo_ua_hide_attributes_data_panel( $tabs) {
        
        return $tabs;
    }
	/**
	* Contents of the Auction  Product options product tab.
	* 
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*/
     public function woo_ua_options_product_tab_content() {
        global $post;
			$timezone = get_option('timezone_string') ? get_option('timezone_string') : "UTC";
			date_default_timezone_set($timezone);
			$product = wc_get_product($post->ID);?>
				<div id='auction_options' class='panel woocommerce_options_panel'>
					<div class='options_group'>
						<?php		
						woocommerce_wp_select( array(
							'id' => 'woo_ua_product_condition', 
							'label' => __('Product Condition', 'woo_ua'),
							'options' => apply_filters('woo_ua_auction_item_condition' ,$this->woo_ua_auction_item_condition))
						);		
						woocommerce_wp_text_input( array(
							'id'			=> 'woo_ua_opening_price',
							'label'			=> __( 'Opening Price', 'woo_ua' ). ' (' . get_woocommerce_currency_symbol() . ')',
							'desc_tip'		=> 'true',
							'description'	=> __( 'Set the price where the price of the product will start from.', 'woo_ua' ),
							'type' 			=> 'number',
						) );		
						  
						woocommerce_wp_text_input( array(
							'id'			=> 'woo_ua_lowest_price',            
							'label'			=>  __('Lowest Price to Accept', 'woo_ua') . ' (' . get_woocommerce_currency_symbol() . ')',
							'desc_tip'		=> 'true',
							'data_type' => 'price',
							'description'	=> __( 'Set Reserve price for your auction.', 'woo_ua' ),
							'type' 			=> 'number',
						) );
						
						woocommerce_wp_text_input( array(
							'id'			=> 'woo_ua_bid_increment',
							'label'			=> __( 'Bid Increment', 'woo_ua' ) . ' (' . get_woocommerce_currency_symbol() . ')',
							'desc_tip'		=> 'true',
							'data_type' => 'price',
							'description'	=> __( 'Set an amount from which next bid should start.', 'woo_ua' ),
							'type' 			=> 'number',
						) );
						 
						woocommerce_wp_text_input( array(
							'id'			=> '_regular_price',
							'label'			=> __( 'Buy now price', 'woo_ua' ). ' (' . get_woocommerce_currency_symbol() . ')',
							'desc_tip'		=> 'true',
							'data_type' => 'price',
							'description'	=> __( 'Visitors can buy your auction by making payments via Available payment method.', 'woo_ua' ),
							'type' 			=> 'number',
						) );
						 
						 $nowdate = date('Y-m-d H:i:s', strtotime("+1 day"));
						 $end_date = get_post_meta($post->ID, 'woo_ua_auction_end_date', true) ?  : $nowdate;	 
						woocommerce_wp_text_input( array(
							'id'			=> 'woo_ua_auction_end_date',
							'label'			=> __( 'Ending Date', 'woo_ua' ),
							'desc_tip'		=> 'true',
							'description'	=> __( 'Set the end date of Auction Product.', 'woo_ua' ),
							'type' 			=> 'text',			
							'class'         => 'datetimepicker',
							'value'         => $end_date
						) );
						?>
							<div class="woo_admin_current_time">
								<?php
								date_default_timezone_set($timezone);
								printf(__('Current Blog Time is %s', 'woo_ua'), '<strong>'.date('Y-m-d H:i:s', time()).'</strong> ');
								echo __('Timezone:', 'woo_ua').' <strong>'.$timezone.'</strong>';
								echo __('<a href="'.admin_url('options-general.php?#timezone_string').'" target="_blank">'.' '.__('Change', 'woo_ua').'</a>');?>								
							</div>							
					</div>
				</div>
	<?php	 
    }
	/**
	* Save Auction Product Data.
	* 
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*/
    function woo_ua_save_auction_option_field( $post_id ) {
		global $wpdb, $woocommerce, $woocommerce_errors;
		$product_type = empty($_POST['product-type']) ? 'simple' : sanitize_title(stripslashes($_POST['product-type']));

		$timezone = get_option('timezone_string') ? get_option('timezone_string') : __('UTC+','woo_ua').get_option('gmt_offset');
		date_default_timezone_set($timezone);
	
		if ( $product_type == 'auction' ) {
			
			update_post_meta($post_id, '_manage_stock', 'yes');
			update_post_meta($post_id, '_stock', '1');
			update_post_meta($post_id, '_backorders', 'no');
			update_post_meta($post_id, '_sold_individually', 'yes');
			
			
			if ( !isset( $_POST['_regular_price'] ) || !$_POST['_regular_price'] ) {
				$_POST['_regular_price'] = 0;
				update_post_meta( $post_id, '_price', $_POST['_regular_price'] );
				update_post_meta( $post_id, '_regular_price', $_POST['_regular_price'] );
			}
			
			if( isset( $_POST['woo_ua_product_condition']) ) {
				update_post_meta( $post_id, 'woo_ua_product_condition', esc_attr( $_POST['woo_ua_product_condition'] ) );
			}
			
			if( isset( $_POST['woo_ua_opening_price'] ) && is_numeric( $_POST['woo_ua_opening_price'] ) ) {
				update_post_meta( $post_id, 'woo_ua_opening_price', $_POST['woo_ua_opening_price'] );
			}
			
			if( isset( $_POST['woo_ua_lowest_price'] ) && is_numeric( $_POST['woo_ua_lowest_price'] ) ) {
				update_post_meta( $post_id, 'woo_ua_lowest_price', $_POST['woo_ua_lowest_price'] );
			}
			
			if( isset( $_POST['woo_ua_bid_increment']) && is_numeric( $_POST['woo_ua_opening_price'] ) ) {
				update_post_meta( $post_id, 'woo_ua_bid_increment', (int)$_POST['woo_ua_bid_increment'] );
			}
			 $start_date = date("Y-m-d H:i:s", time());
			if( isset( $_POST['woo_ua_auction_end_date']) ) {
				update_post_meta( $post_id, 'woo_ua_auction_end_date', stripslashes( $_POST['woo_ua_auction_end_date'] ) );
			   
			}
			
			if( isset( $_POST['_regular_price']) && is_numeric( $_POST['_regular_price'] ) ) {
				 update_post_meta( $post_id, '_price', $_POST['_regular_price'] );
				update_post_meta( $post_id, '_regular_price', (int)$_POST['_regular_price'] );
			}
			
			 update_post_meta( $post_id, 'woo_ua_auction_start_date', stripslashes( $start_date ) );
			 $auction_type = "normal";
			 update_post_meta( $post_id, 'woo_ua_auction_type', esc_attr( $auction_type ) );
		}
	
    }
	
	/**
	* Add Metabox for Auction Log/History Section
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*/
	public function woo_ua_add_auction_metabox( $product ) {

		$woo_pf = new WC_Product_Factory();
		$woo_prd = $woo_pf->get_product($product->ID);
		if( $woo_prd->get_type() !== 'auction' ) return;

		add_meta_box('woo-ua-auction-log',
					__( 'Bids History', 'woo_ua' ),
					 array( $this, 'render_woo_ua_auction_log' ),
					'product',
					'normal',
					'default'
		);
	}	
	/**
	 *  Callback for adding a meta box to the product editing screen used in render_woo_ua_auction_log
	 *
	 * @access public
	 *
	 */
	function render_woo_ua_auction_log() {
		global $woocommerce, $post;
			$product_data = wc_get_product($post->ID); ?>
			
		<?php if (($product_data->is_woo_ua_closed() === TRUE) and ($product_data->is_woo_ua_started() === TRUE)): ?>
				
				<p><?php _e('Auction has expired', 'woo_ua')?></p>
				
				<?php if ($product_data->get_woo_ua_auction_fail_reason() == '1') { ?>
				
							<p><?php _e('Auction Expired without any bids.', 'woo_ua')?></p>
					
				<?php } elseif ($product_data->get_woo_ua_auction_fail_reason() == '2') { ?>
				
							<p><?php _e('Auction Expired without reserve price met', 'woo_ua')?></p>
							
							<!--<a class="removereserve" href="#" data-postid="<?php echo $post->ID;?>">
							<?php _e('Remove Reserve Price', 'woo_ua'); ?> </a>	-->
					
				<?php }
				
				if ($product_data->get_woo_ua_auction_closed() == '3') {?>
				
					<p><?php _e('This Auction Product has been sold for buy now price', 'woo_ua')?>: <span><?php echo wc_price($product_data->get_regular_price()) ?></span></p>
				
				<?php } elseif ($product_data->get_woo_ua_auction_current_bider()) {?>

					<p><?php _e('Highest bidder was', 'woo_ua')?>: <span class="maxbider"><a href='<?php echo get_edit_user_link($product_data->get_woo_ua_auction_current_bider())?>'><?php echo get_userdata($product_data->get_woo_ua_auction_current_bider())->display_name ?></a></span></p>
					
					<p><?php _e('Highest bid was', 'woo_ua')?>: <span class="maxbid" ><?php echo wc_price($product_data->get_woo_ua_current_bid()) ?></span></p>

				<?php if ($product_data->get_woo_ua_auction_payed()) {?>
				
					<p><?php _e('Order has been paid, order ID is', 'woo_ua')?>: <span><a href='post.php?&action=edit&post=<?php echo $product_data->get_woo_ua_order_id() ?>'><?php echo $product_data->get_woo_ua_order_id() ?></a></span></p>
					
				<?php } elseif ($product_data->get_woo_ua_order_id()) {
					
					$order = wc_get_order( $product_data->get_woo_ua_order_id() );
					if ( $order ){
						$order_status = $order->get_status() ? $order->get_status() : __('unknown', 'woo_ua');?>
						<p><?php _e('Order has been made, order status is', 'woo_ua')?>: <a href='post.php?&action=edit&post=<?php echo $product_data->get_woo_ua_order_id() ?>'><?php echo $order_status ?></a><span>
					<?php }
				}?>
				
				<?php }?>


		<?php endif;?>
		
		<?php if (($product_data->is_woo_ua_closed() === FALSE) and ($product_data->is_woo_ua_started() === TRUE)): ?>
		
		<?php endif;?>
		
		<h2><?php _e('Total Bids Placed:', 'woo_ua'); ?></h2>

			<div class="woo_ua" id="woo_ua_auction_history" v-cloak>
				<div class="woo_ua-table-responsive">
						<table class="woo_ua-table woo_ua-table-bordered">
						<?php
						$datetimeformat = get_option('date_format').' '.get_option('time_format');
						$woo_ua_auction_history = $product_data->woo_ua_auction_history();

						if ( !empty($woo_ua_auction_history)  ): ?>
						
							<tr>
								<th><?php _e('Bidder Name', 'woo_ua')?></th>
								<th><?php _e('Bidding Time', 'woo_ua')?></th>
								<th><?php _e('Bid', 'woo_ua')?></th>								
								<th class="actions"><?php _e('Actions', 'woo_ua')?></th>
							</tr>
							<?php foreach ($woo_ua_auction_history as $history_value) { ?>

							<tr>
								<td class="bid_username"><a href="<?php echo get_edit_user_link($history_value->userid);?>">
								<?php echo get_userdata($history_value->userid)->display_name;?></a></td>
								<td class="bid_date"><?php echo mysql2date($datetimeformat ,$history_value->date)?></td>
								<td class="bid_price"><?php echo get_woocommerce_currency_symbol().$history_value->bid;?></td>
								<td class="bid_action">
								<a href='#' data-id=<?php echo $history_value->id;?> 
								data-postid=<?php echo $post->ID;?>  ><?php echo __('Delete', 'woo_ua');?></a>
								</td>
							</tr>
							<?php } ?>	
						<?php endif;?>
							<tr class="start">
									<?php 
									$start_date = $product_data->get_woo_ua_auction_start_time(); ?>
									<?php if ($product_data->is_woo_ua_started() === TRUE) { ?>
								<td class="started"><?php echo __('Auction started', 'woo_ua');?>
									<?php }   else { ?>									
								<td  class="started"><?php echo __('Auction starting', 'woo_ua');?>		
									<?php } ?></td>	
								<td colspan="3"  class="bid_date"><?php echo mysql2date($datetimeformat,$start_date)?></td>
							</tr>
						</table>
				</div>
			</div>		
	<?php }	
	/**
	 * Auction Setting page on WooCommerce Setting page
	 *	
	 */	
	function woo_ua_auction_settings_class_fun($settings) {
				$settings[] = include WOO_UA_ADMIN.'/class-woo-ua-admin-setting.php';
				return $settings;
	}
	/**
	* Add New  Email Setting On WooCommerce Email Setting page
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*/
	public function woo_ua_register_email_classes( $email_classes ) {
           
		   //User Emails
            $email_classes['WOO_UA_Email_Place_Bid'] = include(WOO_UA_ADMIN . '/email/class-woo-ua-auction-email-place-bid.php');	
			$email_classes['WOO_UA_Email_Auction_Bid_Overbidded'] = include(WOO_UA_ADMIN . '/email/class-woo-ua-auction-email-bid-overbidded.php');
			
			$email_classes['WOO_UA_Email_Auction_Winner'] = include(WOO_UA_ADMIN . '/email/class-woo-auction-email-auction-winner.php');
			
		   //Admin	
			$email_classes['WOO_UA_Email_Auction_Bid_Intimation_Admin'] = include(WOO_UA_ADMIN . '/email/class-woo-ua-auction-email-bidding-intimation.php');
			$email_classes['WOO_UA_Email_Auction_Bid_Overbidded_Admin'] = include(WOO_UA_ADMIN . '/email/class-woo-ua-auction-email-bid-overbidded-admin.php');
			
			//Admin	Private Message
			$email_classes['WOO_UA_Email_Auction_Private_Msg_Admin'] = include(WOO_UA_ADMIN . '/email/class-woo-ua-auction-email-private-msg-admin.php');
			
			
			$email_classes['WOO_UA_Email_Auction_Winner_Admin'] = include(WOO_UA_ADMIN . '/email/class-woo-ua-auction-email-winner-admin.php');
			
             return $email_classes;
    }
	/**
	* Create local Email Template for email setting
	* @package Ultimate WooCommerce Auction
	* @author Nitesh Singh 
	* @since 1.0
	*/		
	 public function woo_ua_locate_core_template( $core_file, $template, $template_base ) {
            $custom_template = array(
                
				//HTML Email  Bidder(User)
                'emails/placed-bid.php',               
                'emails/bid-outbided.php', 
				'emails/auction-winner.php',
				
				//HTML Email  Admin(Administrator)
				'emails/placed-bid-admin.php',
				'emails/bid-outbided-admin.php',
				'emails/auction-winner-admin.php',
				'emails/auction-private-msg.php',
				
				
                // Plain Email Bidder(User)
                'emails/plain/placed-bid.php',
                'emails/plain/bid-outbided.php',
                'emails/plain/auction-winner.php',
				
				// Plain Email Admin(Administrator)
				'emails/plain/placed-bid-admin.php',             
                'emails/plain/bid-outbided-admin.php',
                'emails/plain/auction-winner-admin.php',
                

            );

            if ( in_array( $template, $custom_template ) ) {
                $core_file = WOO_UA_WC_TEMPLATE . $template;
            }

            return $core_file;
    }	
	/**
	* Auction Filter On Product list Page
	*	 
	*/
	function admin_woo_ua_filter_restrict_manage_posts() {
				// Drop down list for auction 
				if (isset($_GET['post_type']) && $_GET['post_type'] == 'product') {
					$filter_values = array(
						'Live Auction' => 'live',
						'Expired Auction' => 'expired',
						'Fail Auction' => 'fail',
						'Sold Auction' => 'sold',
						'Paid Auction' => 'payed',
					);
					?>
			        <select name="woo_ua_filter">
			        <option value=""><?php _e('Auction filter By ', 'woo_ua');?></option>
			        <?php
                        $current_filter = isset($_GET['woo_ua_filter']) ? $_GET['woo_ua_filter'] : '';
                        foreach ($filter_values as $label => $value) {
                            printf ( '<option value="%s"%s>%s</option>',$value, $value == $current_filter ? ' selected="selected"' : '', $label );
                        }
                    ?>
			        </select>
			        <?php
                }
	}			
	/**
	* If submitted filter by post meta
	*
	* make sure to change META_KEY to the actual meta key
	* and POST_TYPE to the name of your custom post type
	*
	* @access public
	* @param  (wp_query object) $query
	* @return void
	*/
	function admin_woo_ua_filter($query) {				
		global $pagenow;	
		
		if (isset($_GET['post_type']) && $_GET['post_type'] == 'product' && is_admin() && $pagenow == 'edit.php' && isset($_GET['woo_ua_filter']) && $_GET['woo_ua_filter'] != '') {

			$taxquery = $query->get('tax_query');
			if (!is_array($taxquery)) {
				$taxquery = array();
			}

			$taxquery[] =
			array(
				'taxonomy' => 'product_type',
				'field' => 'slug',
				'terms' => 'auction',

			);

			$query->set('tax_query', $taxquery);

			
			switch ($_GET['woo_ua_filter']) {
			case 'live':
				$query->query_vars['meta_query'] = array(

					array(
							'key'     => 'woo_ua_auction_closed',
							'compare' => 'NOT EXISTS',
					)

				);

				break;
			case 'expired':
				$query->query_vars['meta_query'] = array(
					array(
						'key' => 'woo_ua_auction_closed',
						'value' => array('1','2','3','4'),
						'compare' => 'IN',
					),
				);

				break;
			case 'fail':
				$query->query_vars['meta_key'] = 'woo_ua_auction_closed';
				$query->query_vars['meta_value'] = '1';

				break;
			case 'sold':
				$query->query_vars['meta_query'] = array(
					array(
						'key' => 'woo_ua_auction_closed',
						'value' => '3',
					),

					array(
						'key'     => 'woo_ua_auction_payed',
						'compare' => 'NOT EXISTS',
					)

				);

				break;
			case 'payed':
				$query->query_vars['meta_key'] = 'woo_ua_auction_payed';
				$query->query_vars['meta_value'] = '1';
				break;
			}
			
			
			
		}
	}
	/**
	* Auction Product  paid for
	*			 
	*
	*/
	function woo_ua_auction_payed($order_id) {

			$order = wc_get_order($order_id);

			if ($order) {
				$order_items = $order->get_items();

				if ($order_items) {
					foreach ($order_items as $item_id => $item) {
						$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
						$product_data = wc_get_product($item_meta["_product_id"][0]);
						if (method_exists( $product_data, 'get_type') && $product_data->get_type() == 'auction') {
								update_post_meta($item_meta["_product_id"][0], 'woo_ua_auction_payed', 1, true);
								update_post_meta($item_meta["_product_id"][0], 'woo_ua_order_id', $order_id, true);                                       
						}
					}
				}
			}

	}
	/**
	* Function When Order Cancel by user
	*		 
	*/
	function woo_ua_auction_order_canceled($order_id) {
			$order = wc_get_order($order_id);

			if ($order) {
				$order_items = $order->get_items();

				if ($order_items) {

					foreach ($order_items as $item_id => $item) {

						$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
						$product_data = wc_get_product($item_meta["_product_id"][0]);
						if (method_exists( $product_data, 'get_type') && $product_data->get_type() == 'auction') {
								delete_post_meta($item_meta["_product_id"][0], 'woo_ua_auction_payed');
																																					}
					}
				}
			} 

	}

	/**
	 * Auction Product Order
	 *			 
	 */
	function woo_ua_auction_order($order_id, $posteddata) {

		$order = wc_get_order($order_id);

		if ($order) {

			$order_items = $order->get_items();

			if ($order_items) {

				foreach ($order_items as $item_id => $item) {
					$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					$product_data = wc_get_product($item_meta["_product_id"][0]);
					if (method_exists( $product_data, 'get_type') && $product_data->get_type() == 'auction') {
					update_post_meta($order_id, '_auction', '1');
					update_post_meta($item_meta["_product_id"][0], '_order_id', $order_id, true);
					if (!$product_data->is_woo_ua_finished()) {
							update_post_meta($item_meta["_product_id"][0], 'woo_ua_auction_closed', '3');
							update_post_meta($item_meta["_product_id"][0], 'woo_ua_buy_now', '1');
							update_post_meta($item_meta["_product_id"][0], 'woo_ua_auction_end_date', date('Y-m-h h:s'));
							
						}
					}
				}
			}
		}
	}
	/**
	 * Ajax delete bid
	 *
	 * Function for deleting bid in wp admin
	 *
	 * @access public
	 * @param  array
	 * @return string
	 *
	 */
	function wp_ajax_admin_cancel_bid() {
				global $wpdb;
				if (!current_user_can('edit_product', $_POST["postid"])) {
						die();
				}
				if ($_POST["postid"] && $_POST["logid"]) {
						$product_data = wc_get_product($_POST["postid"]);						
						$log = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "woo_ua_auction_log WHERE id=%d", $_POST["logid"]));
				
				if (!is_null($log)) {
					if ($product_data->get_woo_ua_auction_type() == 'normal') {
						
					if (($log->bid == $product_data->get_woo_ua_auction_current_bid()) && ($log->userid == $product_data->get_woo_ua_auction_current_bider())) {

						$newbid = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "woo_ua_auction_log WHERE auction_id =%d ". $time." ORDER BY  `date` desc , `bid`  desc LIMIT 1, 1 ", $_POST["postid"]));
						if (!is_null($newbid)) {
								update_post_meta($_POST["postid"], 'woo_ua_auction_current_bid', $newbid->bid);
								update_post_meta($_POST["postid"], 'woo_ua_auction_current_bider', $newbid->userid);
								delete_post_meta($_POST["postid"], 'woo_ua_auction_max_bid');
								delete_post_meta($_POST["postid"], 'woo_ua_auction_max_current_bider');
								$new_max_bider_id =  $newbid->userid;
						} else {
								delete_post_meta($_POST["postid"], 'woo_ua_auction_current_bid');
								delete_post_meta($_POST["postid"], 'woo_ua_auction_current_bider');
								delete_post_meta($_POST["postid"], 'woo_ua_auction_max_bid');
								delete_post_meta($_POST["postid"], 'woo_ua_auction_max_current_bider');
								$new_max_bider_id = false;
						}
						$wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "woo_ua_auction_log WHERE id= %d", $_POST["logid"]));
						update_post_meta($_POST["postid"], 'woo_ua_auction_bid_count', intval($product_data->get_woo_ua_auction_bid_count() - 1));
						do_action('woo_ua_auctions_delete_bid', array('product_id' => $_POST["postid"], 'delete_user_id' => $log->userid, 'new_max_bider_id ' => $new_max_bider_id ) );
						$response['status'] = 1;
						$response['success_message'] = __('Bid Deleted Successfully','woo_ua');
						

					} else {
					$wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "woo_ua_auction_log WHERE id= %d", $_POST["logid"]));
					update_post_meta($_POST["postid"], 'woo_ua_auction_bid_count', intval($product_data->get_woo_ua_auction_bid_count() - 1));
					$wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "woo_ua_auction_log WHERE id= %d", $_POST["logid"]));
					$response['status'] = 1;
					$response['success_message'] = __('Bid Deleted Successfully','woo_ua');

							}
						
					}
					
					}

				} else {
					
					$response['status'] = 0;
					$response['error_message'] = __('Bid Not Deleted','woo_ua');	
				}
				
				echo json_encode( $response );
	        exit;
				
    }		
	/**
	 * Ajax End Auction
	 *
	 * Function for deleting bid in wp admin
	 *
	 * @access public
	 * @param  array
	 * @return string
	 *
	 */	
	function wp_ajax_admin_end_auction_now () {
		 global $wpdb;
		$timezone = get_option('timezone_string') ? get_option('timezone_string') : __('UTC+','woo_ua').get_option('gmt_offset');
		date_default_timezone_set($timezone);
		if (!current_user_can('edit_product', $_POST["postid"])) {
				die();
		}					
		if (!empty($_POST["postid"])) {	
		 $product_id = $_POST["postid"];
		 $product_data = wc_get_product( wc_clean( $product_id ) );					
		 $end_time = date('Y-m-d H:i:s', time());					 
		 $closed_auction = $product_data->get_woo_ua_auction_closed();
		 if (!empty($closed_auction)){
				die();//Auction Already Ended

			}else {							
				$started_auction = $product_data->is_woo_ua_started();
			   echo  $finished_auction = $product_data->is_woo_ua_finished();
				$current_bider = $product_data->get_woo_ua_auction_current_bider();
				$current_bid = $product_data->get_woo_ua_auction_current_bid();
				
				if ($started_auction ){
					
				if ( !$current_bider && !$current_bid){
					
				update_post_meta( $product_id, 'woo_ua_auction_closed', '1');
				update_post_meta( $product_id, 'woo_ua_auction_fail_reason', '1');	
					}
					
				if ( $product_data->is_woo_ua_reserve_met() == FALSE){
					update_post_meta( $product_id, 'woo_ua_auction_closed', '1');
					update_post_meta( $product_id, 'woo_ua_auction_fail_reason', '2');
							
				}
				update_post_meta($product_id, 'woo_ua_auction_closed', '2');
				add_user_meta($current_bider, 'woo_ua_auction_win', $product_id);
				
				if($current_bider){
					 WC()->mailer();
					 do_action('woo_ua_auctions_won_email_bidder', $product_id ,$current_bider);
					 do_action('woo_ua_auctions_won_email_admin', $product_id , $current_bider );
				  }
				
				$response['status'] = 1;
				$response['success_message'] = __('Auction ended successfully.','woo_ua');
				}
				
				else {

					$response['status'] = 0;
					$response['error_message'] = __('Sorry, this auction cannot be ended.','woo_ua');

				}
								
				
			}
		 
		}
		echo json_encode( $response );
		exit;
		
	}	
	/**
	* Duplicate post
	*
	* Clear metadata when copy auction
	*
	* @access public
	* @param  array
	* @return string
	*
	*/
	function woocommerce_duplicate_product($postid) {

		$product = wc_get_product($postid);

			if (!$product) {
				return FALSE;
			}

			if (!(method_exists( $product, 'get_type') && $product->get_type() == 'auction') ) {
				return FALSE;
			}
			delete_post_meta($postid, 'woo_ua_auction_end_date');
			delete_post_meta($postid, 'woo_ua_auction_start_date');
			delete_post_meta($postid, 'woo_ua_auction_current_bid');
			delete_post_meta($postid, 'woo_ua_auction_current_bider');
			delete_post_meta($postid, 'woo_ua_auction_bid_count');			
			delete_post_meta($postid, 'woo_ua_winner_mail_sent');
			delete_post_meta($postid, 'woo_ua_auction_has_started');
			delete_post_meta($postid, 'woo_ua_auction_closed');
			delete_post_meta($postid, 'woo_ua_auction_started');			
			delete_post_meta($postid, 'woo_ua_auction_max_bid');			
			delete_post_meta($postid, 'woo_ua_auction_max_current_bider');
			delete_post_meta($postid, 'woo_ua_auction_fail_reason');
			delete_post_meta($postid, 'woo_ua_auction_payed');
			delete_post_meta($postid, 'woo_ua_order_id');	
			delete_post_meta($postid, '_stock_status');
			update_post_meta($postid, '_stock_status', 'instock');
			update_post_meta($postid, '_stock', '1');

			return TRUE;
	}	
	
	
		
		function woo_ua_auctions_status_columns( $columns_array ) {
		 
			// I want to display Brand column just after the product name column
			$auction_status_columns = __('Auction Status','woo_ua');
			return array_slice( $columns_array, 0, 5, true )
			+ array( 'admin_auction_status' => $auction_status_columns )
			+ array_slice( $columns_array, 5, NULL, true );
		 
		 
		}
 
	
	function woo_ua_auctions_status_columns_status( $column, $postid ) {
			global $woocommerce, $post;
			if( $column  == 'admin_auction_status' ) {				
				$product_data = wc_get_product($postid);				
				if( $product_data->get_type() == 'auction' ) {
				$closed = $product_data->is_woo_ua_closed();
				$failed = $product_data->get_woo_ua_auction_fail_reason();
				if($closed === TRUE){ ?>				
					<span style="color:red;font-size:20px"><?php _e('Expired', 'woo_ua')?></span>
				<?php } else { ?>				
					<span style="color:green;font-size:20px"><?php _e('Live', 'woo_ua')?></span>
				<?php }
				
				}
			}
	}
			
			
	/**
	* Show pricing fields for Action product.
	*/
	function woo_ua_auction_custom_js() {

		if ( 'product' != get_post_type() ) :
			return;
		endif;

		?><script type='text/javascript'>
			jQuery( document ).ready( function() {
				jQuery( '.inventory_tab' ).addClass( 'show_if_auction' ).show();
				
			});

		</script><?php

	}

}
Woo_Ua_Admin::get_instance();