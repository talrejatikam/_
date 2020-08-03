<?php
if (! defined( 'ABSPATH' )) exit;

class BookingCalendar
{
	public function __construct() {
		//add_action( 'cartbounty_notification_sendout_hook', array( $this, 'smsalert_send_sms' ),1 );
		//add_action( 'wpbc_booking_approved', array( $this, 'sendsms_approved_pending' ),99,2 );
		add_action( 'init', array( $this, 'update_visual_form_structure' ),99);
		
		//update_bk_option( 'booking_form_visual',  $visual_form_structure  );  
		//add_action( 'wpbc_booking_delete', $approved_id_str ); //check it is possible to get reason in this hook
		//add_action( 'wpbc_booking_trash', $booking_id, $is_trash ); //check it is possible to get reason in this hook
		//self::update_visual_form_structure();
	}
	
	public function update_visual_form_structure(){
		
		$obj = get_option('booking_form_visual');
		
		 $obj[] = array(
                                          'type'     => 'text'
                                        , 'name'     => 'smsalert_bk_phone'
                                        , 'obligatory' => 'On'
                                        , 'active'   => 'On'
                                        , 'required' => 'On'
                                        , 'label'    => 'Phone'            
                                    );
									
		update_option('booking_form_visual',$obj);							
		//print_r($obj);exit();
		/* $visual_form_structure = import_old_booking_form();
		echo "in--------------";
		print_r($visual_form_structure);
		exit(); */
	}

	public function sendsms_approved_pending($booking_id,$is_approve_or_pending)
	{
		if (function_exists( 'wpbc_api_get_booking_by_id' ) )
		{
			$buyer_sms_data = array();
			$booking = wpbc_api_get_booking_by_id($booking_id);
			$buyer_sms_data['number']   = $booking['formdata']['phone1'];
			print_r($booking);
			exit();
			$buyer_sms_data['sms_body'] = "message".$booking['formdata']['phone1'];
			
			
			do_action('sa_send_sms', $buyer_sms_data['number'], $buyer_sms_data['sms_body']);
			
			
			// send msg to admin
			$sms_admin_phone 			= smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
			if (!empty($sms_admin_phone)){
				$smsalert_ac_admin_notify 	= smsalert_get_option( 'admin_notify', 'smsalert_ac_general', 'on');
				$smsalert_ac_admin_message 	= smsalert_get_option( 'admin_notify', 'smsalert_ac_message', '' );

				if($smsalert_ac_admin_notify == 'on' && $smsalert_ac_admin_message != ''){
					$sms_admin_phone 	= explode(",",$sms_admin_phone);
					foreach($sms_admin_phone as $phone ) {
						do_action('sa_send_sms', $phone, $this->parse_sms_body($data,$smsalert_ac_admin_message));
					}
				}
			}
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
			$item_name,
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
new BookingCalendar;