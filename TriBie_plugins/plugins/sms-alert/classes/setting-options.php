<?php
/**
 * WordPress settings API class
 *
 * @author SMSAlert\Cozyvision Technologies pvt. ltd.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
class smsalert_Setting_Options {
	/**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
		require_once plugin_dir_path( __DIR__ ).'/helper/countrylist.php';

		if ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) )
		{
			require_once plugin_dir_path( __DIR__ ).'/helper/edd.php';
		}

		if ( is_plugin_active( 'learnpress/learnpress.php' ) )
		{
			require_once plugin_dir_path( __DIR__ ).'/helper/learnpress.php';
		}

		if(is_plugin_active('woocommerce-bookings/woocommerce-bookings.php'))
		{
			require_once plugin_dir_path( __DIR__ ).'/helper/woocommerce-booking.php';
		}

		if(is_plugin_active('events-manager/events-manager.php'))
		{
			require_once plugin_dir_path( __DIR__ ).'/helper/events-manager.php';
		}

		if(is_plugin_active('woo-save-abandoned-carts/cartbounty-abandoned-carts.php'))
		{
			require_once plugin_dir_path( __DIR__ ).'/helper/cartbounty.php';
		}
		
		if(is_plugin_active('woocommerce/woocommerce.php'))
		{
			require_once plugin_dir_path( __DIR__ ).'/helper/backinstock.php';
			require_once plugin_dir_path( __DIR__ ).'/helper/wc-low-stock.php';
		}
		
		if(is_plugin_active('delivery-drivers-for-woocommerce/delivery-drivers-for-woocommerce.php'))
		{
			require_once plugin_dir_path( __DIR__ ).'/helper/delivery-drivers-woocommerce.php';
		}
		
		add_action('admin_menu', __CLASS__ . '::sms_alert_wc_submenu');
		
		add_filter( 'um_predefined_fields_hook' , array('UltimateMemberRegistrationForm','my_predefined_fields') , 10, 2 );

		add_action( 'verify_senderid_button', 				__CLASS__ . '::action_woocommerce_admin_field_verify_sms_alert_user' 	);
		add_action( 'admin_post_save_sms_alert_settings',  __CLASS__ . '::save'  													);
		if ( is_plugin_active( 'woocommerce-warranty/woocommerce-warranty.php' ) ){
			require_once plugin_dir_path( __DIR__ ).'/helper/return-warranty.php';
		}

		if(!self::isUserAuthorised()){
			add_action( 'admin_notices',  __CLASS__ . '::show_admin_notice__success' );
		}

		self::smsalert_dashboard_setup();

		if(array_key_exists('option', $_GET) && $_GET['option'])
		{
			switch (trim($_GET['option']))
			{
				case 'smsalert-woocommerce-senderlist':
					echo SmsAlertcURLOTP::get_senderids($_GET['user'],$_GET['pwd']);exit();	break;
				case 'smsalert-woocommerce-creategroup':
					SmsAlertcURLOTP::creategrp();
					echo SmsAlertcURLOTP::group_list();
					break;
				case 'smsalert-woocommerce-logout':
				echo self::logout();	break;
			}
		}
	}

	/*add smsalert phone button in ultimate form*/
	public static function my_predefined_fields( $predefined_fields ) {
		$fields = array('billing_phone' => array(
			'title' => 'Smsalert Phone',
			'metakey' => 'billing_phone',
			'type' => 'text',
			'label' => 'Mobile Number',
			'required' => 0,
			'public' => 1,
			'editable' => 1,
			'validate' => 'billing_phone',
			'icon' => 'um-faicon-mobile',
		));
		$predefined_fields = array_merge($predefined_fields,$fields);
		return $predefined_fields;
	}

	public static function smsalert_dashboard_setup()
	{
		add_action( 'dashboard_glance_items',  __CLASS__ . '::smsalert_add_dashboard_widgets', 10, 1 );
	}
	public static function show_admin_notice__success() {
    ?>
	<div class="notice notice-warning is-dismissible">
		<p><?php echo sprintf( __( '<a href="%s" target="_blank">Login to SMS Alert</a> to configure SMS Notifications', 'smsalert' ), esc_url( 'admin.php?page=sms-alert' ) );	?></p>
	</div>
	<?php
	}

	public static function get_wc_payment_dropdown($checkout_payment_plans)
	{
		if (!is_plugin_active( 'woocommerce/woocommerce.php' ) ) { return array(); } // add on 25/05
		if(!is_array($checkout_payment_plans))
			$checkout_payment_plans = self::get_all_gateways();
		
		$paymentPlans = WC()->payment_gateways->payment_gateways();//add on 11/06

		$str = '<select multiple size="5" name="smsalert_general[checkout_payment_plans][]" id="checkout_payment_plans" class="multiselect chosen-select" data-parent_id="smsalert_general[otp_for_selected_gateways]" data-placeholder="Select Payment Gateways">';
		foreach ($paymentPlans as $paymentPlan) {
			$str .=  '<option ';
			if(in_array($paymentPlan->id, $checkout_payment_plans)) $str.= 'selected';
			$str .= ' value="'.esc_attr( $paymentPlan->id ).'">'.$paymentPlan->title.'</option>';
		}
		$str .= '</select>';
		$str .= '<script>jQuery(function() {jQuery(".chosen-select").chosen({width: "100%"});});</script>';
		return $str;
	}

	public static function get_wc_roles_dropdown($admin_bypass_otp_login)
	{
		global $wp_roles;
		$roles = $wp_roles->roles;
		if(!is_array($admin_bypass_otp_login) && $admin_bypass_otp_login=='on')
			$admin_bypass_otp_login = array('administrator');

		$str = '<select multiple size="5" name="smsalert_general[admin_bypass_otp_login][]" id="admin_bypass_otp_login" data-parent_id="smsalert_general[otp_for_roles]" class="multiselect chosen-select"  data-placeholder="Select Roles OTP For login">';
		foreach ($roles as $role_key => $role) {
			$str .= '<option ';
			if(in_array($role_key, $admin_bypass_otp_login)) $str.='selected';
			$str .= ' value="'.esc_attr( $role_key ).'">'.$role['name'].'</option>';
		}
		$str .= '</select>';

		return $str;
	}

	public static function get_country_code_dropdown()
	{
		$default_country_code = smsalert_get_option( 'default_country_code', 'smsalert_general');
		$content='<select name="smsalert_general[default_country_code]" id="default_country_code" onchange="choseMobPattern(this)">';
		$content.= '<option value="" data-pattern="'.SmsAlertConstants::PATTERN_PHONE.'" '.(($default_country_code=='')? 'selected="selected"':'').'>Global</option>';
		$countries = (SmsAlertCountryList::getCountryCodeList()) ? SmsAlertCountryList::getCountryCodeList() : array();
		foreach($countries as $key => $country)
		{
			$content.= '<option value="'.$country['Country']['c_code'].'"';
			$content.= ($country['Country']['c_code']==$default_country_code) ? 'selected="selected"' : '';
			$content.= ' data-pattern="'.(!empty($country['Country']['pattern'])?$country['Country']['pattern']:SmsAlertConstants::PATTERN_PHONE).'">'.$country['Country']['name'].'</option>';
		}
		$content.= '</select>';
		return $content;
	}

	public static function get_all_gateways()
	{
		if (!is_plugin_active( 'woocommerce/woocommerce.php' ) ) { return array(); } // add on 25/05
		$gateways 		= array();
		$paymentPlans = WC()->payment_gateways->payment_gateways();//add on 11/06
		foreach ($paymentPlans as $paymentPlan) {
			$gateways[] =  $paymentPlan->id;
		}
		return $gateways;
	}
	
	public static function sms_alert_wc_submenu() {
		
		add_submenu_page( 'woocommerce', 'SMS Alert', 'SMS Alert', 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab');

		add_submenu_page( 'edit.php?post_type=download', 	'SMS Alert', 						'SMS Alert', 'manage_options', 'sms-alert', __CLASS__ . '::settings_tab');

		add_submenu_page( 'gf_edit_forms', 					__( 'SMS ALERT', 'gravityforms' ),	__( 'SMS Alert', 'gravityforms' ), 'manage_options', 'sms-alert' , __CLASS__ . '::settings_tab');

		add_submenu_page( 'ultimatemember', 				__( 'SMS ALERT', 'ultimatemember' ), __( 'SMS Alert', 'ultimatemember' ), 'manage_options', 'sms-alert' , __CLASS__ . '::settings_tab');

		add_submenu_page( 'wpcf7', 							__( 'SMS ALERT', 'wpcf7' ), 		__( 'SMS Alert', 'wpcf7' ), 'manage_options', 'sms-alert' , __CLASS__ . '::settings_tab');

		add_submenu_page( 'pie-register', 					__( 'SMS ALERT', 'pie-register' ), 	__( 'SMS Alert', 'pie-register' ), 'manage_options', 'sms-alert' , __CLASS__ . '::settings_tab');

		add_submenu_page( 'wpam-affiliates', 				__( 'SMS ALERT', 'affiliates-manager' ), __( 'SMS Alert', 'affiliates-manager' ), 'manage_options', 'sms-alert' , __CLASS__ . '::settings_tab');

		add_submenu_page( 'learn_press', 					__( 'SMS ALERT', 'learnpress' ), 	__( 'SMS Alert', 'learnpress' ), 'manage_options', 'sms-alert' , __CLASS__ . '::settings_tab');

		add_submenu_page('edit.php?post_type=event', 		__('SMS ALERT','events-manager'),__('SMS Alert','events-manager'), 'manage_options', "sms-alert", __CLASS__ .'::settings_tab');
		
		add_submenu_page('ninja-forms', 		__('SMS ALERT','ninja-forms'),__('SMS Alert','ninja-forms'), 'manage_options', "sms-alert", __CLASS__ .'::settings_tab');
		
	}

	public static function isUserAuthorised()
	{
		$islogged			= false;
		$smsalert_name 		= smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '' );
		$smsalert_password  = smsalert_get_option( 'smsalert_password', 'smsalert_gateway', '' );
		$islogged			= false;
		if($smsalert_name != ''&&$smsalert_password != '')
		{
			$islogged=true;
		}
		return $islogged;
	}

	public static function smsalert_add_dashboard_widgets($items = array())
	{
		if(self::isUserAuthorised())
		{
			$credits = json_decode(SmsAlertcURLOTP::get_credits(),true);
			if(is_array($credits['description']) && array_key_exists('routes', $credits['description']))
			{
				foreach($credits['description']['routes'] as $credit){
					$items[] = sprintf('<a href="%1$s" class="smsalert-credit"><strong>%2$s SMS</strong> : %3$s</a>', admin_url( 'admin.php?page=sms-alert' ), ucwords($credit['route']), $credit['credits']).'<br />';
				}
			}
		}
		return $items;
	}

	public static function logout()
	{
		if(delete_option( 'smsalert_gateway' ))
			return true;
	}

    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
		//woocommerce_admin_fields( self::get_settings() );
		self::get_settings();
    }

	public static function save()
	{
		$_POST = smsalert_sanitize_array($_POST);
		self::save_settings($_POST);
	}

	public static function save_settings($options)
	{
		$order_statuses 		= is_plugin_active('woocommerce/woocommerce.php') ? wc_get_order_statuses() : array();

		if ( empty( $_POST ) ) {
			return false;
		}

		$defaults = array(
			'smsalert_gateway'       => array(
				'smsalert_name' => '',
				'smsalert_password'  => '',
				'smsalert_api'  => '',
			),
			'smsalert_message'       => array(
				'sms_admin_phone' => '',
				'group_auto_sync' => '',
				'sms_body_new_note'  => '',
				'sms_body_registration_msg'  => '',
				'sms_body_registration_admin_msg'  => '',
				'sms_body_admin_low_stock_msg'  => '',
				'sms_body_admin_out_of_stock_msg'  => '',
				'sms_otp_send'  => '',
			),
			'smsalert_general'       => array(
				'buyer_checkout_otp'=>'off',
				'buyer_signup_otp'=>'off',
				'buyer_login_otp'=>'off',
				'buyer_notification_notes'=>'off',
				'allow_multiple_user'=>'off',
				'admin_bypass_otp_login'=>array('administrator'),
				'checkout_show_otp_button'=>'off',
				'checkout_show_otp_guest_only'=>'off',
				'checkout_show_country_code'=>'off',
				'checkout_otp_popup'=>'off',
				'daily_bal_alert'=>'off',
				'enable_short_url'=>'off',
				'auto_sync'=>'off',
				'low_bal_alert'=>'off',
				'alert_email'=>'',
				'otp_template_style'=>'otp-popup-1.php',
				'checkout_payment_plans' => '',
				'otp_for_selected_gateways' => 'off',
				'otp_for_roles' => 'off',
				'otp_resend_timer' => '15',
				'max_otp_resend_allowed' => '4',
				'otp_verify_btn_text' => 'Click here to verify your Phone',
				'default_country_code' => '91',
				'sa_mobile_pattern' => '',
				'login_with_otp' => 'off',
				'login_popup' => 'off',
				'validate_before_send_otp' => 'off',
				'registration_msg' => 'off',
				'admin_registration_msg' => 'off',
				'admin_low_stock_msg' => 'off',
				'admin_out_of_stock_msg' => 'off',
				'reset_password' => 'off',
				'register_otp_popup_enabled' => 'off',
			),
			'smsalert_sync'       => array(
			    'last_sync_userId'=>'0'
			),
			'smsalert_background_task'       => array(
			    'last_updated_lBal_alert'=>'',
			),
			'smsalert_background_dBal_task'       => array(
				'last_updated_dBal_alert'=>'',
			),
			'smsalert_edd_general'=>array(),
		);

		foreach($order_statuses as $ks => $vs)
		{
			$prefix = 'wc-';
			if (substr($ks, 0, strlen($prefix)) == $prefix) {
				$ks = substr($ks, strlen($prefix));
			}
			$defaults['smsalert_general']['admin_notification_'.$ks]= 'off';
			$defaults['smsalert_general']['order_status'][$ks] 		= '';
			$defaults['smsalert_message']['admin_sms_body_'.$ks]	= '';
			$defaults['smsalert_message']['sms_body_'.$ks]			= '';
		}

		$defaults = apply_filters('sAlertDefaultSettings',$defaults);

		$_POST['smsalert_general']['checkout_payment_plans'] = isset($_POST['smsalert_general']['checkout_payment_plans']) ? maybe_serialize($_POST['smsalert_general']['checkout_payment_plans']) : array();
		$options=array_replace_recursive($defaults, array_intersect_key( $_POST, $defaults));

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
		//return true;
		wp_redirect(  admin_url( 'admin.php?page=sms-alert&m=1' ) );
		exit;
	}

	/**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
	public static function get_settings() {

		global $current_user;
		wp_get_current_user();

		$smsalert_name     			    	= smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '' );
		$smsalert_password  			   	= smsalert_get_option( 'smsalert_password', 'smsalert_gateway', '' );
		$smsalert_api   			       	= smsalert_get_option( 'smsalert_api', 'smsalert_gateway', '' );
		$hasWoocommerce 					= is_plugin_active( 'woocommerce/woocommerce.php' );
		$hasWPmembers 						= is_plugin_active( 'wp-members/wp-members.php' );
		$hasUltimate 						= (is_plugin_active( 'ultimate-member/ultimate-member.php' ) || is_plugin_active( 'ultimate-member/index.php' )) ? true : false;
		$hasWoocommerceBookings 			= (is_plugin_active('woocommerce-bookings/woocommerce-bookings.php')) ? true : false;
		$hasEMBookings 						= (is_plugin_active('events-manager/events-manager.php')) ? true : false;
		$hasWPAM 							= (is_plugin_active('affiliates-manager/boot-strap.php' )) ? true : false;
		$hasLearnPress 						= (is_plugin_active('learnpress/learnpress.php' )) ? true : false;
		$hasCartBounty						= (is_plugin_active('woo-save-abandoned-carts/cartbounty-abandoned-carts.php')) ? true : false;
		//$hasDeliveryDriver					= (is_plugin_active('delivery-drivers-for-woocommerce/delivery-drivers-for-woocommerce.php')) ? true : false;
		$hasBookingCalendar					= (is_plugin_active('booking/wpdev-booking.php')) ? true : false;
		$sms_admin_phone 					= smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
		$group_auto_sync 					= smsalert_get_option( 'group_auto_sync', 'smsalert_general', '' );
		$sms_body_on_hold 					= smsalert_get_option( 'sms_body_on-hold', 'smsalert_message', SmsAlertMessages::showMessage('DEFAULT_BUYER_SMS_ON_HOLD'));
		$sms_body_processing 				= smsalert_get_option( 'sms_body_processing', 'smsalert_message', SmsAlertMessages::showMessage('DEFAULT_BUYER_SMS_PROCESSING'));
		$sms_body_completed 				= smsalert_get_option( 'sms_body_completed', 'smsalert_message',  SmsAlertMessages::showMessage('DEFAULT_BUYER_SMS_COMPLETED'));
		$sms_body_cancelled 				= smsalert_get_option( 'sms_body_cancelled', 'smsalert_message', SmsAlertMessages::showMessage('DEFAULT_BUYER_SMS_CANCELLED') );
		$sms_body_new_note 					= smsalert_get_option( 'sms_body_new_note', 'smsalert_message', SmsAlertMessages::showMessage('DEFAULT_BUYER_NOTE') );
		$sms_body_registration_msg 			= smsalert_get_option( 'sms_body_registration_msg', 'smsalert_message', SmsAlertMessages::showMessage('DEFAULT_NEW_USER_REGISTER') );
		$sms_body_registration_admin_msg 	= smsalert_get_option( 'sms_body_registration_admin_msg', 'smsalert_message', SmsAlertMessages::showMessage('DEFAULT_ADMIN_NEW_USER_REGISTER') );
		$sms_body_admin_low_stock_msg 		= smsalert_get_option( 'sms_body_admin_low_stock_msg', 'smsalert_message', SmsAlertMessages::showMessage('DEFAULT_ADMIN_LOW_STOCK_MSG') );
		$sms_body_admin_out_of_stock_msg 	= smsalert_get_option( 'sms_body_admin_out_of_stock_msg', 'smsalert_message', SmsAlertMessages::showMessage('DEFAULT_ADMIN_OUT_OF_STOCK_MSG') );
		$sms_otp_send 						= smsalert_get_option( 'sms_otp_send', 'smsalert_message',  SmsAlertMessages::showMessage('DEFAULT_BUYER_OTP'));
		$smsalert_notification_status 		= smsalert_get_option( 'order_status', 'smsalert_general', '');
		$smsalert_notification_onhold 		= (is_array($smsalert_notification_status) && array_key_exists('on-hold', $smsalert_notification_status)) ? $smsalert_notification_status['on-hold'] : 'on-hold';
		$smsalert_notification_processing 	= (is_array($smsalert_notification_status) && array_key_exists('processing', $smsalert_notification_status)) ? $smsalert_notification_status['processing'] : 'processing';
		$smsalert_notification_completed 	= (is_array($smsalert_notification_status) && array_key_exists('completed', $smsalert_notification_status)) ? $smsalert_notification_status['completed'] : 'completed';
		$smsalert_notification_cancelled 	= (is_array($smsalert_notification_status) && array_key_exists('cancelled', $smsalert_notification_status)) ? $smsalert_notification_status['cancelled'] : 'cancelled';
		$smsalert_notification_checkout_otp = smsalert_get_option( 'buyer_checkout_otp', 'smsalert_general', 'on');
		$smsalert_notification_signup_otp 	= smsalert_get_option( 'buyer_signup_otp', 'smsalert_general', 'on');
		$smsalert_notification_login_otp 	= smsalert_get_option( 'buyer_login_otp', 'smsalert_general', 'on');
		$smsalert_notification_notes 		= smsalert_get_option( 'buyer_notification_notes', 'smsalert_general', 'on');
		$smsalert_notification_reg_msg 		= smsalert_get_option( 'registration_msg', 'smsalert_general', 'on');
		$smsalert_notification_reg_admin_msg = smsalert_get_option( 'admin_registration_msg', 'smsalert_general', 'on');
		$smsalert_notification_low_stock_admin_msg = smsalert_get_option( 'admin_low_stock_msg', 'smsalert_general', 'on');
		$smsalert_notification_out_of_stock_admin_msg = smsalert_get_option( 'admin_out_of_stock_msg', 'smsalert_general', 'on');
		$smsalert_allow_multiple_user 		= smsalert_get_option( 'allow_multiple_user', 'smsalert_general', 'on');
		$admin_bypass_otp_login 			= maybe_unserialize(smsalert_get_option( 'admin_bypass_otp_login', 'smsalert_general', array('administrator')));
		$checkout_show_otp_button 			= smsalert_get_option( 'checkout_show_otp_button', 'smsalert_general', 'on');
		$checkout_show_otp_guest_only 		= smsalert_get_option( 'checkout_show_otp_guest_only', 'smsalert_general', 'on');
		$checkout_show_country_code 		= smsalert_get_option( 'checkout_show_country_code', 'smsalert_general', 'off');
		$enable_reset_password 				= smsalert_get_option( 'reset_password', 'smsalert_general', 'off');
		$register_otp_popup_enabled 		= smsalert_get_option( 'register_otp_popup_enabled', 'smsalert_general', 'off');
		$otp_resend_timer 					= smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15');
		$max_otp_resend_allowed 			= smsalert_get_option( 'max_otp_resend_allowed', 'smsalert_general', '4');
		$otp_verify_btn_text 				= smsalert_get_option( 'otp_verify_btn_text', 'smsalert_general', 'Click here to verify your Phone');
		$default_country_code 				= smsalert_get_option( 'default_country_code', 'smsalert_general', '');
		$sa_mobile_pattern 					= smsalert_get_option( 'sa_mobile_pattern', 'smsalert_general', '');
		$checkout_otp_popup 				= smsalert_get_option( 'checkout_otp_popup', 'smsalert_general', 'on');
		$login_with_otp 					= smsalert_get_option( 'login_with_otp', 'smsalert_general', 'off');
		$login_popup	 					= smsalert_get_option( 'login_popup', 'smsalert_general', 'off');
		$daily_bal_alert 					= smsalert_get_option( 'daily_bal_alert', 'smsalert_general', 'on');
		$enable_short_url 					= smsalert_get_option( 'enable_short_url', 'smsalert_general', 'off');
		$auto_sync 							= smsalert_get_option( 'auto_sync', 'smsalert_general', 'off');
		$low_bal_alert 						= smsalert_get_option( 'low_bal_alert', 'smsalert_general', 'on');
		$low_bal_val 						= smsalert_get_option( 'low_bal_val', 'smsalert_general', '1000');
		$alert_email 						= smsalert_get_option( 'alert_email', 'smsalert_general', $current_user->user_email);
		$validate_before_send_otp			= smsalert_get_option( 'validate_before_send_otp', 'smsalert_general', 'off');
		$checkout_payment_plans 			= maybe_unserialize(smsalert_get_option( 'checkout_payment_plans', 'smsalert_general', NULL));
		$otp_for_selected_gateways 			= smsalert_get_option( 'otp_for_selected_gateways', 'smsalert_general', 'off');
		$otp_for_roles 						= smsalert_get_option( 'otp_for_roles', 'smsalert_general', 'on');
		$islogged 							= false;
		$hidden								= '';
		$credit_show						= 'hidden';
		if($smsalert_name != ''&& $smsalert_password != '')
		{
			$credits = json_decode(SmsAlertcURLOTP::get_credits(),true);
			if($credits['status']=='success' || (is_array($credits['description']) && $credits['description']['desc']=='no senderid available for your account'))
			{
				$islogged = true;
				$hidden='hidden';
				$credit_show='';
			}
		}

		$smsalert_helper = (!$islogged) ? sprintf( __( 'Please enter below your <a href="%s" target="_blank">www.smsalert.co.in</a> login details to link it with '.get_bloginfo(), SmsAlertConstants::TEXT_DOMAIN ), esc_url( 'http://www.smsalert.co.in' ) ) : '';
		?>
		<form method="post" id="smsalert_form" action="<?php echo admin_url('admin-post.php'); ?>">
			<div class="SMSAlert_box SMSAlert_settings_box">
				<div class="SMSAlert_nav_tabs">
				<?php
					$params=array(
						'hasWoocommerce'=>$hasWoocommerce,
						'hasWPmembers'=>$hasWPmembers,
						'hasUltimate'=>$hasUltimate,
						'hasWPAM'=>$hasWPAM,
						'credit_show'=>$credit_show,
						'hasCartBounty'=>$hasCartBounty,
						//'hasDeliveryDriver'=>$hasDeliveryDriver,
						'hasBookingCalendar'=>$hasBookingCalendar,
					);
					echo get_smsalert_template('views/smsalert_nav_tabs.php',$params);
				?>
				</div>
				<div>
					<div class="SMSAlert_nav_box SMSAlert_nav_global_box SMSAlert_active general">
					<!--general tab-->
					<?php
					$params=array(
						'smsalert_helper'=>$smsalert_helper,
						'smsalert_name'=>$smsalert_name,
						'smsalert_password'=>$smsalert_password,
						'hidden'=>$hidden,
						'smsalert_api'=>$smsalert_api,
						'islogged'=>$islogged,
						'sms_admin_phone'=>$sms_admin_phone,
						'hasWoocommerce'=>$hasWoocommerce,
						'hasWPAM'=>$hasWPAM,
						'hasEMBookings'=>$hasEMBookings,
					);
					echo get_smsalert_template('views/smsalert_general_tab.php',$params);
					?>
					</div><!--/-general tab-->

					<div class="SMSAlert_nav_box SMSAlert_nav_css_box customertemplates"><!--customertemplates tab-->
					<?php
						$order_statuses = is_plugin_active('woocommerce/woocommerce.php') ? wc_get_order_statuses() : array();
					?>
					<?php
					$params=array(
						'order_statuses'=>$order_statuses,
						'smsalert_notification_status'=>$smsalert_notification_status,
						'getvariables'=>WooCommerceCheckOutForm::getvariables(),
						'hasWoocommerce'=>$hasWoocommerce,
						'smsalert_notification_notes'=>$smsalert_notification_notes,
						'smsalert_notification_reg_msg'=>$smsalert_notification_reg_msg,
						'sms_body_new_note'=>$sms_body_new_note,
						'sms_body_registration_msg'=>$sms_body_registration_msg,
						'sms_body_registration_admin_msg'=>$sms_body_registration_admin_msg,
					);
					echo get_smsalert_template('views/wc-customer-template.php',$params);
					?>
					</div><!--/-customertemplates tab-->

					<div class="SMSAlert_nav_box SMSAlert_nav_admintemplates_box admintemplates"><!--admintemplates tab-->
					<?php
					$params=array(
						'order_statuses'=>$order_statuses,
						'hasWoocommerce'=>$hasWoocommerce,
						'smsalert_notification_reg_admin_msg'=>$smsalert_notification_reg_admin_msg,
						'smsalert_notification_low_stock_admin_msg'=>$smsalert_notification_low_stock_admin_msg,
						'smsalert_notification_out_of_stock_admin_msg'=>$smsalert_notification_out_of_stock_admin_msg,
						'sms_body_registration_admin_msg'=>$sms_body_registration_admin_msg,
						'sms_body_admin_out_of_stock_msg'=>$sms_body_admin_out_of_stock_msg,
						'sms_body_admin_low_stock_msg'=>$sms_body_admin_low_stock_msg,
						'getvariables'=>WooCommerceCheckOutForm::getvariables(),
						'hasWPAM'=>$hasWPAM,
						'hasEMBookings'=>$hasEMBookings,
					);
					echo get_smsalert_template('views/wc-admin-template.php',$params);
					?>
					</div><!--/-admintemplates tab-->
					
					<?php 
						$tabs = apply_filters('sa_addTabs',array());					
						foreach($tabs as $tab)
						{
					?>
							<div class="SMSAlert_nav_box SMSAlert_nav_<?php echo $tab['tab_section']; ?>_box <?php echo $tab['tab_section'];?>">
							<?php							
								echo get_smsalert_template($tab['tabContent'],array());
							?>
							</div>
					<?php } ?>	

					<div class="SMSAlert_nav_box SMSAlert_nav_otp_section_box otpsection"><!--otp_section tab-->
					<?php
					$params=array(
						'smsalert_notification_checkout_otp'=>$smsalert_notification_checkout_otp,
						'smsalert_notification_signup_otp'=>$smsalert_notification_signup_otp,
						'smsalert_notification_login_otp'=>$smsalert_notification_login_otp,
						'hasWPmembers'=>$hasWPmembers,
						'hasWoocommerce'=>$hasWoocommerce,
						'hasUltimate'=>$hasUltimate,
						'hasWPAM'=>$hasWPAM,
						'sms_otp_send'=>$sms_otp_send,
						'login_with_otp'=>$login_with_otp,
						'login_popup'=>$login_popup,
						'enable_reset_password'=>$enable_reset_password,
						'hasLearnPress'=>$hasLearnPress,
						'otp_for_selected_gateways' => $otp_for_selected_gateways,
						'checkout_otp_popup'=>$checkout_otp_popup,
						'register_otp_popup_enabled' => $register_otp_popup_enabled,
						'checkout_show_otp_button'=>$checkout_show_otp_button,
						'checkout_show_otp_guest_only'=>$checkout_show_otp_guest_only,
						'checkout_show_country_code'=>$checkout_show_country_code,
						'max_otp_resend_allowed' => $max_otp_resend_allowed,
						'otp_verify_btn_text' => $otp_verify_btn_text,
						'validate_before_send_otp' => $validate_before_send_otp,
						'show_payment_gateways'=>self::get_wc_payment_dropdown($checkout_payment_plans),
						'show_wc_roles_dropdown'=>self::get_wc_roles_dropdown($admin_bypass_otp_login),
						'otp_resend_timer'=>$otp_resend_timer,
						'smsalert_allow_multiple_user'=>$smsalert_allow_multiple_user,
						'otp_for_roles'=>$otp_for_roles,
					);

					echo get_smsalert_template('views/otp-section-template.php',$params);
					?>
					</div><!--/-otp_section tab-->

					<div class="SMSAlert_nav_box SMSAlert_nav_callbacks_box otp "><!--otp tab-->
						<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;">
						<style>.top-border{border-top:1px dashed #b4b9be;}</style>
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php _e( 'Default Country', SmsAlertConstants::TEXT_DOMAIN ) ?>
								</th>
								<td>
									<?php echo self::get_country_code_dropdown(); ?>
									<span class="tooltip" data-title="Default Country for mobile number format validation"><span class="dashicons dashicons-info"></span></span>
									<input type="hidden" name="smsalert_general[sa_mobile_pattern]" id="sa_mobile_pattern" value="<?php echo $sa_mobile_pattern;?>"/>
								</td>
							</tr>
							
							<tr valign="top">
							<th scope="row"></th>
							<td>
								<?php if ($hasWoocommerce) {?>
								<input type="checkbox" name="smsalert_general[checkout_show_country_code]" id="smsalert_general[checkout_show_country_code]" class="notify_box" data-parent_id="smsalert_general[buyer_checkout_otp]" <?php echo (($checkout_show_country_code=='on')?"checked='checked'":'')?>/><?php _e( 'Enable Country Code Selection', SmsAlertConstants::TEXT_DOMAIN ) ?>
								<span class="tooltip" data-title="Enable Country Code & Flag at Billing Phone Field"><span class="dashicons dashicons-info"></span></span>
								<?php } ?>
							</td>
							</tr>
							
							
							<style>
							.otp .tags-input-wrapper {float:left;}
							</style>
							<tr valign="top" class="top-border">
								<th scope="row"><?php _e( 'Alerts', SmsAlertConstants::TEXT_DOMAIN ) ?>
								</th>
								<td>
									<input type="text" name="smsalert_general[alert_email]" class="admin_email " id="smsalert_general[alert_email]" value="<?php echo $alert_email; ?>" style="width: 40%;">

									<span class="tooltip" data-title="Send Alerts for low balance & daily balance etc."><span class="dashicons dashicons-info"></span></span>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"> </th>
								<td>
									<input type="checkbox" name="smsalert_general[low_bal_alert]" id="smsalert_general[low_bal_alert]" class="SMSAlert_box notify_box" <?php echo (($low_bal_alert=='on')?"checked='checked'":'');?> />
									<?php _e( 'Low Balance Alert', SmsAlertConstants::TEXT_DOMAIN ) ?> <input type="number" min="500" name="smsalert_general[low_bal_val]" id="smsalert_general[low_bal_val]" data-parent_id="smsalert_general[low_bal_alert]" value="<?php echo $low_bal_val; ?>" >
									<span class="tooltip" data-title="Set Low Balance Alert"><span class="dashicons dashicons-info"></span></span>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"> </th>
								<td>
									<input type="checkbox" name="smsalert_general[daily_bal_alert]" id="smsalert_general[daily_bal_alert]" class="notify_box" <?php echo (($daily_bal_alert=='on')?"checked='checked'":''); ?> />
									<?php _e( 'Daily Balance Alert', SmsAlertConstants::TEXT_DOMAIN ) ?>
									<span class="tooltip" data-title="Set Daily Balance Alert"><span class="dashicons dashicons-info"></span></span>
								</td>
							</tr>
							<!--enable shorturl-->
							<tr valign="top" class="top-border">
								<th scope="row"> </th>
								<td>
									<input type="checkbox" name="smsalert_general[enable_short_url]" id="smsalert_general[enable_short_url]" class="notify_box" <?php echo (($enable_short_url=='on')?"checked='checked'":''); ?> />
										<?php _e( 'Enable Short Url', SmsAlertConstants::TEXT_DOMAIN ) ?>
									<span class="tooltip" data-title="Enable Short Url"><span class="dashicons dashicons-info"></span></span>
								</td>
							</tr>
							<!--/-enable shorturl-->
							<?php if(is_plugin_active('woocommerce/woocommerce.php')) { ?>
							<tr valign="top">
								<th scope="row"> </th>
								<td>
									<input type="checkbox" name="smsalert_general[auto_sync]" id="smsalert_general[auto_sync]" class="SMSAlert_box sync_group" <?php echo (($auto_sync=='on')?"checked='checked'":''); ?> /> <?php _e( 'Sync To Group', SmsAlertConstants::TEXT_DOMAIN ) ?>
									<?php $groups = json_decode(SmsAlertcURLOTP::group_list(),true); ?>

									<select name="smsalert_general[group_auto_sync]" data-parent_id="smsalert_general[auto_sync]" id="group_auto_sync">
									<?php
									if(!is_array($groups['description']) || array_key_exists('desc', $groups['description']))
									{
										?>
										<option value="0"><?php _e( 'SELECT', SmsAlertConstants::TEXT_DOMAIN ) ?></option>
										<?php
									}
									else
									{
										foreach($groups['description'] as $group)
										{
										?>
										<option value="<?php echo $group['Group']['name']; ?>" <?php echo (trim($group_auto_sync) == $group['Group']['name']) ? 'selected="selected"' : ''; ?>><?php echo $group['Group']['name']; ?></option>
										<?php
										}
									}
									?>
									</select>
									<?php
									if(!empty($groups) && (!is_array($groups['description']) || array_key_exists('desc', $groups['description'])) && $islogged==true)
									{
									?>
										<a href="javascript:void(0)" onclick="create_group(this);" id="create_group" data-parent_id="smsalert_general[auto_sync]" style="text-decoration: none;"><?php _e( 'Create Group', SmsAlertConstants::TEXT_DOMAIN ) ?></a>
									<?php
									}
									elseif($auto_sync=='on' && $group_auto_sync!='0')
									{
									?>
										<input type="button" id="smsalert_sync_btn" data-parent_id="smsalert_general[auto_sync]" onclick="doSASyncNow(this)" class="button button-primary" value="Sync Now" disabled>
									<?php
									}
									?>
									<span class="tooltip" data-title="<?php __( 'Sync users to a Group in smsalert.co.in', SmsAlertConstants::TEXT_DOMAIN ) ?>"><span class="dashicons dashicons-info"></span></span>
									<span id="sync_status" style="opacity:0;margin-left: 20px;"><?php echo sprintf(__('%s contacts synced',SmsAlertConstants::TEXT_DOMAIN), '0')?></span>
									<div id="sa_progressbar"></div>
								</td>
							</tr>
							<?php } ?>
						</table>
						</div>
					</div><!--/-otp tab-->
					<div class="SMSAlert_nav_box SMSAlert_nav_credits_box credits <?php echo $credit_show?>">		<!--credit tab-->
						<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;">
							<table class="form-table">
								<tr valign="top">
									<td>
									<?php
									if($islogged)
									{
										echo '<h2><strong>SMS Credits</strong></h2>';
										foreach($credits['description']['routes'] as $credit){
									?>
										<div class="col-lg-12 creditlist" >
											<div class="col-lg-8 route">
												<h3><span class="dashicons dashicons-email"></span> <?php echo ucwords($credit['route']);?></h3>
											</div>
											<div class="col-lg-4 credit">
												<h3><?php echo $credit['credits'];?> <?php _e( 'Credits', SmsAlertConstants::TEXT_DOMAIN ) ?></h3>
											</div>
										</div>
									<?php } } ?>
									</td>
								</tr>
								<tr valign="top">
									<td>
										<p><b><?php _e( 'Need More credits?', SmsAlertConstants::TEXT_DOMAIN ) ?></b> 
										
										<?php echo sprintf( __( '<a href="%s" target="_blank">Click Here</a> to purchase. ', SmsAlertConstants::TEXT_DOMAIN ), esc_url( 'http://www.smsalert.co.in/#pricebox' ) ); ?></p>
									</td>
								</tr>
							</table>
						</div>
					</div><!--/-credit tab-->
					<div class="SMSAlert_nav_box SMSAlert_nav_support_box support"><!--support tab-->
						<?php echo get_smsalert_template('views/support.php',array());?>
					</div><!--/-support tab-->
					<script>
					/*tagged input start*/
					// Email Alerts
					var adminemail = "<?php echo $alert_email; ?>";
					var tagInput2 = new TagsInput({
						selector: 'smsalert_general[alert_email]',
						duplicate : false,
						max : 10,
					});
					
					var email = (adminemail!='') ? adminemail.split(",") : [];
					if(email.length >= 1){
						tagInput2.addData(email);
					}
					
					//Send Admin SMS To
					<?php if($islogged==true){?>
					var adminnumber = "<?php echo $sms_admin_phone;?>";
					
						var tagInput1 = new TagsInput({
							selector: 'smsalert_message[sms_admin_phone]',
							duplicate : false,
							max : 10,
						});
						var number = (adminnumber!='') ? adminnumber.split(",") : [];
						if(number.length > 0){
							tagInput1.addData(number);
						}
					<?php }?>
									
					/*tagged input end*/

					// on checkbox enable-disable select
					function choseMobPattern(obj){
						var pattern = jQuery('option:selected', obj).attr('data-pattern');
						jQuery('#sa_mobile_pattern').val(pattern);
					}
					//geo ip to country code
					<?php
					if(!$islogged){?>
					try
					{
						jQuery.get("https://ipapi.co/json/", function(data, status){
							if(status=='success')
								calling_code = data.country_calling_code.replace(/\+/g,'');
							else{
								calling_code = 91;
							}
							jQuery('#default_country_code').val(calling_code);
						}).fail(function() {
							console.log("ip check url is not working");
							jQuery('#default_country_code').val(91);
						});
					}
					catch(e){jQuery('#default_country_code').val(91);}
					<?php } ?>
					//geo ip to country code ends
					jQuery('#default_country_code').trigger('change');
					</script>
				</div>
			</div>
			<?php //submit_button(); ?>
			<p class="submit"><input type="submit" id="smsalert_bckendform_btn" class="button button-primary" value="Save Changes" /></p>
		</form>
		<script>
		var isSubmitting = false;

		jQuery('#smsalert_bckendform_btn').click(function(){
			if(jQuery('[name="smsalert_gateway[smsalert_api]"]').val()=='SELECT' || jQuery('[name="smsalert_gateway[smsalert_api]"]').val()=='')
			{
				alert('Please choose your senderid.');
				return false;
			}
			if(jQuery('[name="smsalert_message[sms_otp_send]"]').val() =='' || jQuery('[name="smsalert_message[sms_otp_send]"]').val().match(/\[otp.*?\]/i)==null)
			{
				alert('Please add OTP tag in OTP Template.');
				return false;
			}
			isSubmitting = true;			
			if(jQuery('[name="smsalert_general[alert_email]"]').val() != '')
			{
				var inputText = jQuery('[name="smsalert_general[alert_email]"]').val();				
				var email = inputText.split(',');
				
				for (i = 0; i < email.length; i++) 
				{
					var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
					if(!email[i].match(mailformat))
					{
						alert("You have entered an invalid email address in Advanced Settings option!");
						jQuery('[tab_type=callbacks]').trigger('click');
						window.location.hash = '#otp';
						
						return false;
					}
				}				
			}
			if(jQuery('#smsalert_form')[0].checkValidity()){
				var url = jQuery("#smsalert_form").attr('action');
				var hash = window.location.hash;
				jQuery('#smsalert_form').attr('action', url+hash);
				jQuery('#smsalert_form').submit();
			}			
		});

		//check before leave page
		jQuery('form').data('initial-state', jQuery('form').serialize());

		jQuery(window).on('beforeunload', function() {
			if (!isSubmitting && jQuery('form').serialize() != jQuery('form').data('initial-state')){
				return 'You have unsaved changes which will not be saved.';
			}
		});
		</script>
		<script>
		//add token variable on admin and customer template 21/07/2020		
		window.addEventListener('message', receiveMessage, false); 
		function receiveMessage(evt) { 
			var txtbox_id =  jQuery('.cvt-accordion-body-content.open').find('textarea').attr('id');
			insertAtCaret(evt.data, txtbox_id); 
			tb_remove();
		}
		</script>
		<?php
		return apply_filters('wc_sms_alert_setting',array());
		}

		public static function action_woocommerce_admin_field_verify_sms_alert_user($value)
		{
			global $current_user;
			wp_get_current_user();
			$smsalert_name         = smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '' );
			$smsalert_password     = smsalert_get_option( 'smsalert_password', 'smsalert_gateway', '' );
			$hidden='';
			if($smsalert_name != ''&&$smsalert_password != '')
			{
				$credits = json_decode(SmsAlertcURLOTP::get_credits(),true);
				if($credits['status']=='success' || (is_array($credits['description']) && $credits['description']['desc']=='no senderid available for your account'))
				{
					$hidden='hidden';
				}
			}
			?>
			<tr valign="top" class="<?php echo $hidden?>">
				<th>&nbsp;</th>
				<td >
					<a href="#" class="button-primary woocommerce-save-button" onclick="verifyUser(this); return false;"><?php _e( 'verify and continue', SmsAlertConstants::TEXT_DOMAIN ); ?></a>
					<?php
					$link = "http://www.smsalert.co.in/?name=".urlencode($current_user->user_firstname.' '.$current_user->user_lastname)."&email=".urlencode($current_user->user_email)."&phone=&username=".preg_replace('/\s+/', '_', strtolower(trim(get_bloginfo())))."#register";
					echo sprintf( __( 'Don\'t have an account on SMS Alert? <a href="%s" target="_blank">Signup Here for FREE</a> ', SmsAlertConstants::TEXT_DOMAIN ), esc_url( $link ) ); ?>
					<div id="verify_status"></div>
				</td>
			</tr>
			<?php
		}
	}
smsalert_Setting_Options::init();