<?php
if (! defined( 'ABSPATH' )) exit;

class WCAbandonedCart
{
	public function __construct() {
		add_filter( 'sAlertDefaultSettings',  __CLASS__ .'::addDefaultSetting',1);
		add_action( 'cartbounty_notification_sendout_hook', array( $this, 'smsalert_send_sms' ),1 );
		add_action( 'sa_addTabs', array( $this, 'addTabs' ), 10 );
	}
	
	
	/*add tabs to smsalert settings at backend*/
	public static function addTabs($tabs=array())
	{
		$tabs['cartbounty']['title']		= 'CartBounty';
		$tabs['cartbounty']['tab_section']  = 'cartbountytemplates';
		$tabs['cartbounty']['tabContent']	= 'views/cartbounty_template.php';
		$tabs['cartbounty']['icon']		 	= 'dashicons-cart';
		return $tabs;
	}
	

	/*add default settings to savesetting in setting-options*/
	public function addDefaultSetting($defaults=array())
	{
		$defaults['smsalert_ac_general']['customer_notify']	= 'off';
		$defaults['smsalert_ac_message']['customer_notify']	= '';
		$defaults['smsalert_ac_general']['admin_notify']	= 'off';
		$defaults['smsalert_ac_message']['admin_notify']	= '';
		return $defaults;
	}

	public function smsalert_send_sms()
	{
		global $wpdb;
		$table_name 	= $wpdb->prefix . CARTBOUNTY_TABLE_NAME;
		
		//$user_settings_notification_frequency = get_option('cartbounty_notification_frequency');
		
		$timezone =  wp_timezone_string();
		$datetime =  get_gmt_from_date('UTC'.$timezone);
		
		$time_interval = date('Y-m-d H:i:s',strtotime('-'.CARTBOUNTY_STILL_SHOPPING.' Minutes',strtotime($datetime)));
		
		//send msg to user
		$rows_to_phone 	= $wpdb->get_results(
			"SELECT * FROM ". $table_name ." WHERE mail_sent = 0 AND cart_contents != '' AND time < '". $time_interval."'", ARRAY_A );
		
		if ($rows_to_phone){
			$smsalert_ac_customer_notify 	= smsalert_get_option( 'customer_notify', 'smsalert_ac_general', 'on');
			$smsalert_ac_customer_message 	= smsalert_get_option( 'customer_notify', 'smsalert_ac_message', '' );

			if($smsalert_ac_customer_notify == 'on' && $smsalert_ac_customer_message != ''){
				foreach ( $rows_to_phone as $data ) {
					/* $buyer_sms_data['number']	= $data['phone'];
					$buyer_sms_data['sms_body'] = $this->parse_sms_body($data,$smsalert_ac_customer_message);
					$buyer_response = SmsAlertcURLOTP::sendsms( $buyer_sms_data );
					*/
					do_action('sa_send_sms', $data['phone'], $this->parse_sms_body($data,$smsalert_ac_customer_message));
				}
			}
			
			//send msg to admin
			$sms_admin_phone 			= smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
			if (!empty($sms_admin_phone)){
				$smsalert_ac_admin_notify 	= smsalert_get_option( 'admin_notify', 'smsalert_ac_general', 'on');
				$smsalert_ac_admin_message 	= smsalert_get_option( 'admin_notify', 'smsalert_ac_message', '' );

				if($smsalert_ac_admin_notify == 'on' && $smsalert_ac_admin_message != ''){
					$sms_admin_phone 	= explode(",",$sms_admin_phone);
					foreach($sms_admin_phone as $phone ) {
						/* $admin_sms_data['number']	= $phone;
						$admin_sms_data['sms_body'] = $this->parse_sms_body($data,$smsalert_ac_admin_message);
						$admin_response = SmsAlertcURLOTP::sendsms( $admin_sms_data ); */
						do_action('sa_send_sms', $phone, $this->parse_sms_body($data,$smsalert_ac_admin_message));
					}
				}
			}
		}
	}

	public static function getAbandonCartvariables($onlyvariable=false)
	{
		$variables = array(
			'[name]' 			=> 'Name',
			'[surname]' 		=> 'Surname',
			'[email]'  			=> 'Email',
			'[phone]'			=> 'Phone',
			'[location]' 		=> 'Location',
			'[cart_total]' 		=> 'Cart Total',
			'[currency]' 		=> 'Currency',
			'[time]' 			=> 'Time',
			'[item_name]' 		=> 'Item name',
			'[item_name_qty]' 	=> 'Item with Qty',
			'[store_name]' 		=> 'Store Name',
		);

		if($onlyvariable)
		{
			return $variables;
		}
		else
		{
			$ret_string = '';
			foreach($variables as $vk => $vv)
			{
				$ret_string .= sprintf( "<a href='#' val='%s'>%s</a> | " , $vk , __($vv,SmsAlertConstants::TEXT_DOMAIN));
			}
			return $ret_string;
		}
	}

	public function parse_sms_body($data=array(),$content=null)
	{
		$cart_items 		= (array)unserialize($data['cart_contents']);
		$item_name			= implode(", ",array_map(function($o){return $o['product_title'];},$cart_items));
		$item_name_with_qty	= implode(", ",array_map(function($o){return sprintf("%s [%u]", $o['product_title'], $o['quantity']);},$cart_items));

		$find = array(
            '[item_name]',
            '[item_name_qty]',
            '[store_name]',
        );
		
		$replace = array(
			wp_specialchars_decode($item_name),
			$item_name_with_qty,
			get_bloginfo(),
		);

        $content = str_replace( $find, $replace, $content );

		$order_variables		= self::getAbandonCartvariables(true);
		foreach ($order_variables as $key => $value) {
			foreach ($data as $dkey => $dvalue) {
				if(trim($key,'[]')==$dkey){
					$array_trim_keys[$key] = $dvalue;
				}
			}
		}
		$content = str_replace( array_keys($order_variables), array_values($array_trim_keys), $content );
		
		return $content;
	}
}
new WCAbandonedCart;