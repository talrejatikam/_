<?php

/**
 * WooCommerce Product Settings
 *
 * @author   WooThemes
 * @category Admin
 * @package  WooCommerce/Admin
 * @version  2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if (  class_exists( 'WC_Settings_Page' ) ) :

 /**
     * WC_Settings_Products.
     */
    class WC_Settings_Woo_Ua_Auction extends WC_Settings_Page {

        /**
         * Constructor.
         */
        public function __construct() {
			
        $this->id    = 'woo_ua_auctions';
		$this->label = __( 'Auctions', 'woo_ua' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
        }

        /**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

		return apply_filters( 'woocommerce_' . $this->id . '_settings', array(

		
			array(	
				'title' => __( 'Options for Ultimate WooCommerce Auction:', 'woo_ua' ),
				'type' => 'title',
				'desc' => 'If you like <a href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post" target="_blank"> our plugin working </a> with WooCommerce, please leave us a <a href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post" target="_blank">★★★★★</a> rating. A huge thanks in advance!', 
				'id' => 'woo_ua_auction_options' ),		  
				  
				array(
					'title' => __( 'Auction Settings', 'woo_ua' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'woo_ua_scheduler_settings',
				),  
				  
				array(
						'title' 			=> __( 'Check Auction Status:', 'woo_ua' ),
						'desc' 				=> __( 'Minutes. Default is 2 Minutes.', 'woo_ua'),	
						'desc_tip' 			=> __( "A scheduler runs on an interval specified in this field in recurring manner.It checks, if some live auctions product can be expired and accordingly update their status.", 'woo_ua' ),						
						'type' 				=> 'text',
						'id'				=> 'woo_ua_cron_auction_status',
						'default' 			=> '2',
						'css'               => 'width:150px;',						
						'type'              => 'number',
						'custom_attributes' => array(
							'min'  => 1,
							'step' => 1,
						),
					  ),
					  
				array(
						'title' 			=> __( 'Auctions Processed Simultaneously:', 'woo_ua' ),
						'desc' 				=> __( 'Process. Default is 25 Process.						
						<ol><li> It is recommended to fill the above values in a balanced manner based upon the traffic, no. of auction products and no. of users on your site.</li><li>The less is the no. of auctions per request (fields 2 and 4 from above), the processing will be more optimized. If you are allowing so many auctions to be processed in each request, it can affect your site performance.
						</li><li>Similarly, you should also not set a very few no. of auction products since there may be delayed in expiry of some auction products and/or email notifications.</li><li>It is recommended not to keep on changing these values frequently as your auction products will be rescheduled every time you update the values.</li></ol>', 'woo_ua'),	
						'desc_tip' 			=> __( "Number of auctions products Process per request.The scheduler processes the specified no. auctions whenever a schedule occurs.", 'woo_ua' ),						
						'type' 				=> 'text',
						'id'				=> 'woo_ua_cron_auction_status_number',
						'default' 			=> '25',
						'css'               => 'width:150px;',						
						'type'              => 'number',
						'custom_attributes' => array(
							'min'  => 10,
							'step' => 1,
						),
					  ),


					array(
						'title' 		=> __( "Bidding Information:", 'woo_ua' ),
						'desc' 			=> __( 'Enable Ajax update for latest bidding.', 'woo_ua' ),
						'desc_tip' 			=> __( "Enables/disables ajax current bid checker (refresher) for auction - updates current bid value without refreshing page (increases server load, disable for best performance)", 'woo_ua' ),		
						'type' 				=> 'checkbox',
						'id'			=> 'woo_ua_auctions_bid_ajax_enable',
						'default' 		=> 'no'
					),
					
					array(
						'title' 		=> __( "Check Bidding Info:", 'woo_ua' ),
						'desc' 			=> __( 'Time interval between two ajax requests in seconds (bigger intervals means less load for server)', 'woo_ua' ),
						'type' 				=> 'text',
						'id'			=> 'woo_ua_auctions_bid_ajax_interval',
						'default' 		=> '1',
						'css'               => 'width:150px;',						
						'type'              => 'number',
						'custom_attributes' => array(
							'min'  => 1,
							'step' => 1,
						),
					),
					
				array(
					'type' => 'sectionend',
					'id'   => 'general_options',
				),
					
				//Shop Page Setting
				array(
					'title' => __( 'Shop Page', 'woo_ua' ),
					'type'  => 'title',
					'desc'  => 'The following options affect on frontend Shop Page.',
					'id'    => 'woo_ua_shop_page_settings',
				),  								
				
				array(
					'title' 		=> __( "Auctions Display:", 'woo_ua' ),
					'desc' 			=> __( 'Show Expired Auctions', 'woo_ua' ),
					'type' 				=> 'checkbox',
					'id'			=> 'woo_ua_expired_auction_enabled',
					'default' 		=> 'no',
					'checkboxgroup'   => 'start',
					),	
				
				array(
					'title' 			=> __( 'Show Auctions on:', 'woo_ua' ),
					'desc' 				=> __( 'On Shop Page', 'woo_ua' ),
					'checkboxgroup'   => 'start',
					'type' 				=> 'checkbox',
					'id'				=> 'woo_ua_show_auction_pages_shop',
					'default' 			=> 'yes'
				  ),
					  
				  array(						
					'desc' 				=> __( 'On product search page', 'woo_ua' ),
					'type' 				=> 'checkbox',
					'checkboxgroup'   => '',
					'id'				=> 'woo_ua_show_auction_pages_search',
					'default' 			=> 'yes'
				  ),
					  
				  array(						
					'desc' 				=> __( 'On product category page', 'woo_ua' ),
					'type' 				=> 'checkbox',
					'checkboxgroup'   => '',
					'id'				=> 'woo_ua_show_auction_pages_cat',
					'default' 			=> 'yes'
				  ),
					  
				  array(						
					'desc' 				=> __( 'On product tag page', 'woo_ua' ),
					'type' 				=> 'checkbox',
					'checkboxgroup'   => 'end',
					'id'				=> 'woo_ua_show_auction_pages_tag',
					'default' 			=> 'yes'
				  ),
				
			array(
					'type' => 'sectionend',
					'id'   => 'general_options',
				),
					
			//Shop Page Setting
			array(
				'title' => __( 'Auction Detail Page', 'woo_ua' ),
				'type'  => 'title',
				'desc'  => 'The following options affect on frontend Auction Detail page.',
				'id'    => 'woo_ua_detail_page_settings',
			), 
			
			array(
			'title' 			=> __( "Countdown Format", 'woo_ua' ),
			'desc'				=> __( "The format for the countdown display. Default is yowdHMS", 'woo_ua' ),
			'desc_tip' 			=> __( "Use the following characters (in order) to indicate which periods you want to display: 'Y' for years, 'O' for months, 'W' for weeks, 'D' for days, 'H' for hours, 'M' for minutes, 'S' for seconds.	Use upper-case characters for mandatory periods, or the corresponding lower-case characters for optional periods, i.e. only display if non-zero. Once one optional period is shown, all the ones after that are also shown.", 'woo_ua' ),
			'type' 				=> 'text',
			'id'				=> 'woo_ua_auctions_countdown_format',
			'default' 			=> 'yowdHMS',
			'css'               => 'width:150px;',
			),					
			
			array(
				'title' 		=> __( "Enable Specific Sections:", 'woo_ua' ),
				'desc' 			=> __( 'Enable Comments section', 'woo_ua' ),
				'type' 				=> 'checkbox',
				'id'			=> 'woo_ua_auctions_bids_reviews_tab',
				'default' 		=> 'yes',
				'checkboxgroup'   => 'start',
			),
					
			array(
				'title' 		=> __( "Enable Send Private message", 'woo_ua' ),
				'desc' 			=> __( 'Enable Send Private message', 'woo_ua' ),
				'type' 				=> 'checkbox',
				'checkboxgroup'   => '',
				'id'			=> 'woo_ua_auctions_private_message',
				'default' 		=> 'yes'
			),
			
			array(
				'title' 		=> __( "Enable Bids section", 'woo_ua' ),
				'desc' 			=> __( 'Enable Bids section', 'woo_ua' ),
				'type' 				=> 'checkbox',
				'checkboxgroup'   => '',
				'id'			=> 'woo_ua_auctions_bids_section_tab',
				'default' 		=> 'yes'
			),
			
			array(
				'title' 		=> __( "Enable Watchlists", 'woo_ua' ),
				'desc' 			=> __( 'Enable Watchlists', 'woo_ua' ),
				'type' 				=> 'checkbox',
				'checkboxgroup'   => 'end',
				'id'			=> 'woo_ua_auctions_watchlists',
				'default' 		=> 'yes'
			),	
				
			array( 'type' => 'sectionend', 'id' => 'woo_ua_auction_options'),

		)); // End pages settings
		
		
	   }
	   
}

endif;

return new WC_Settings_Woo_Ua_Auction();