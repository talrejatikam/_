<?php
if (! defined( 'ABSPATH' )) exit;

class sa_Return_Warranty
{
	public function __construct() {
		add_action( 'sa_addTabs', array( $this, 'addTabs' ), 10 );
		add_action( 'wc_warranty_settings_tabs', __CLASS__ .'::smsalert_warranty_tab'  );
		add_action( 'wc_warranty_settings_panels', __CLASS__ .'::smsalert_warranty_settings_panels'  );
		
		add_action( 'admin_post_wc_warranty_settings_update', array($this, 'update_wc_warranty_settings'),5 );
		add_action( 'wp_ajax_warranty_update_request_fragment', array($this, 'on_rma_status_update'),0 );
		add_action( 'wc_warranty_created',  array($this, 'on_new_rma_request'),5);
		
		
	}
	
	public static function getWarrantStatus()
	{
		if (!class_exists('WooCommerce_Warranty')) {
			return array();
		}
		
		$wc_warranty = new WooCommerce_Warranty();
		return $wc_warranty->get_default_statuses();
		
	}
	
	function update_wc_warranty_settings($data)
	{
		$options = $_POST;
		if($options['tab'] == 'smsalert_warranty')
		{
			foreach($options as $name => $value)
			{
			   if(is_array($value))
			   {
				   foreach($value as $k => $v)
				   {
					   if(!is_array($v))
					   {
							$value[$k] = stripcslashes($v);
					   }
				   }
			   }
				update_option( $name, $value );
		    }
		}
	}
	
	function send_rma_status_sms($request_id,$status)
	{
		$wc_warranty_checkbox=smsalert_get_option('warranty_status_'.$status, 'smsalert_warranty','');
		$is_sms_enabled = ($wc_warranty_checkbox=='on')  ? true : false;
		if($is_sms_enabled)
		{
			$sms_content	= smsalert_get_option('sms_text_'.$status, 'smsalert_warranty','');
			$order_id 		= get_post_meta( $request_id, '_order_id', true );
			$rma_id 		= get_post_meta( $request_id, '_code', true );
			$order 			= wc_get_order( $order_id );
			global $wpdb;
			$products 		= $items = $wpdb->get_results( $wpdb->prepare(
							"SELECT *
							FROM {$wpdb->prefix}wc_warranty_products
							WHERE request_id = %d",
							$request_id
						), ARRAY_A );
						
			$item_name = '';						
			foreach ( $products as $product ) {

				if ( empty( $product['product_id'] ) && empty( $item['product_name'] ) ) {
					continue;
				}

				if ( $product['product_id'] == 0 ) {
					$item_name .= $item['product_name'].', ';
				} else {
					$item_name .= warranty_get_product_title( $product['product_id'] ).', ';
				}
			}
			$item_name 					= rtrim($item_name, ', ');
			$sms_content 				= str_replace( '[item_name]', $item_name, $sms_content );
			$buyer_sms_data				= array();
			$buyer_mob   				= get_post_meta( $order_id, '_billing_phone', true );
			$message 					= WooCommerceCheckOutForm::pharse_sms_body($sms_content, $status, $order, '', $rma_id);
			do_action('sa_send_sms', $buyer_mob, $message);
		}
	}
	
	function on_new_rma_request($warranty_id)
	{
		$this->send_rma_status_sms($warranty_id,"new");
	}
	
	function on_rma_status_update()
	{
		$request_id = $_POST['request_id'];
		$status 	= $_POST['status'];
		
		$this->send_rma_status_sms($request_id,$status);
	}
	
	
	public static function smsalert_warranty_tab()
	{
		$active_tab=isset($_GET['tab'])?$_GET['tab']:'';
	?>
		<a href="admin.php?page=warranties-settings&tab=smsalert_warranty" class="nav-tab <?php echo ($active_tab == 'smsalert_warranty') ? 'nav-tab-active' : ''; ?>"><?php _e('SMS Alert', 'wc_warranty'); ?></a>
	<?php
	}
	
	public static function smsalert_warranty_settings_panels()
	{
		$active_tab=isset($_GET['tab'])?$_GET['tab']:'';

		if($active_tab == 'smsalert_warranty')
		{
			echo get_smsalert_template('views/return_warranty_template.php',array());
		}
	}
	
	/*add tabs to smsalert settings at backend*/
	public static function addTabs($tabs=array())
	{
		$tabs['return_warranty']['title']		= __("Return & Warranty",SmsAlertConstants::TEXT_DOMAIN);
		$tabs['return_warranty']['tab_section']	= 'return_warranty';
		$tabs['return_warranty']['tabContent']	= 'views/return_warranty_template.php';
		$tabs['return_warranty']['icon']		= 'dashicons-products';		
		return $tabs;
	}
}
new sa_Return_Warranty;
?>