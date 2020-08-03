<?php
if (! defined( 'ABSPATH' )) exit;

class smsalert_delivery_drivers_woocommerce
{
	public function __construct() {
		add_filter('sAlertDefaultSettings',  __CLASS__ .'::addDefaultSetting',1);
		add_filter('sa_wc_variables',  __CLASS__ .'::addTemplateVariable',1);
		
		$smsalert_driver_notify  = smsalert_get_option( 'driver_notify', 'smsalert_driver_general', 'on');
		
		if($smsalert_driver_notify == 'on'){
			add_action( 'woocommerce_order_status_changed', array( $this, 'trigger_onchange_order_status' ), 10, 3 );
		}
		add_action( 'sa_addTabs', array( $this, 'addTabs' ), 10 );
		add_filter('sa_wc_order_sms_before_send', __CLASS__ .'::modifySMSTextByOrderId',1,2);
	}
	
	/*add tabs to smsalert settings at backend*/
	public static function addTabs($tabs=array())
	{
		$tabs['delivery']['title']		 = 'Delivery Drivers';
		$tabs['delivery']['tab_section'] = 'deliverydriverstemplates';
		$tabs['delivery']['tabContent']	 = 'views/delivery-drivers-template.php';
		$tabs['delivery']['icon']		 = 'dashicons-location-alt';		
		return $tabs;
	}
	
	public static function modifySMSTextByOrderId($content,$order_id)
	{    
		$order 				= new WC_Order( $order_id );
		$order_items 		= $order->get_items();
		$first_item 		= current($order_items);		
		$post_id 			= $first_item['order_id'];				
		$driver_id 			= get_post_meta( $post_id, 'ddwc_driver_id', true);
		$order_variables 	= get_user_meta($driver_id);
		$find = array(
			'[delivery_first_name]',
			'[delivery_last_name]',
			'[delivery_boy_number]',
		);

		$replace = array(
			'[first_name]',
			'[last_name]',
			'[billing_phone]',
		);

		$content = str_replace( $find, $replace, $content );
		foreach ($order_variables as &$value) {
			$value = $value[0];
		}
		unset($value);
		
		$order_variables = array_combine(
			array_map(function($key){ return '['.ltrim($key, '_').']'; }, array_keys($order_variables)),
			$order_variables
		);
        $content = str_replace( array_keys($order_variables), array_values($order_variables), $content );

		return $content;   
	}

	
	public static function addTemplateVariable($variables=array())
	{
		$variables = array_merge($variables,  array(
			'[delivery_first_name]' => 'Delivery Boy First Name',
			'[delivery_last_name]' 	=> 'Delivery Boy Last Name',
			'[delivery_boy_number]' => 'Delivery Boy Number',
		));
		return $variables;
	}
	
	
	
	/*add default settings to savesetting in setting-options*/
	public function addDefaultSetting($defaults=array())
	{
		$defaults['smsalert_driver_general']['driver_notify']	= 'off';
		$defaults['smsalert_driver_message']['driver_notify']	= '';
		return $defaults;
	}
	
	public  function trigger_onchange_order_status( $order_id, $old_status, $new_status ) {	
	
		if($new_status == 'driver-assigned') 
		{				
			$order 			= new WC_Order( $order_id );
			
			$driver_message = smsalert_get_option('driver_notify', 'smsalert_driver_message','');
			
			$order_items 	= $order->get_items();
			$first_item 	= current($order_items);		
			$post_id 		= $first_item['order_id'];
		
			$driver_id 		= get_post_meta( $post_id, 'ddwc_driver_id', true);
			$driver_no 		= get_the_author_meta('billing_phone', $driver_id);
			do_action('sa_send_sms', $driver_no, $this->parse_sms_body($order, $driver_message, $driver_id));	
		}
	}
	
	public function parse_sms_body($order, $message, $driver_id){
		
		$order_items 	= $order->get_items();
		$item 			= current($order_items);
		$item_name 		= $item['name'];
		$order_id 		= $item['order_id'];
		$quantity 		= $item['quantity'];
		$first_name 	= get_the_author_meta('first_name', $driver_id);
		$last_name 		= get_the_author_meta('last_name', $driver_id);		
		
		$find = array(
			'[first_name]',
			'[last_name]',
			'[item_name]',
			'[order_id]',
			'[item_name_qty]',
			'[store_name]',
		);

		$replace = array(
			$first_name,
			$last_name,
			$item_name,
			$order_id,
			$item_name.' '.$quantity,
			get_bloginfo(),
		);

		$message 	= str_replace($find, $replace, $message);		
		return $message;
	}
}
new smsalert_delivery_drivers_woocommerce;	
?>