<?php
if (! defined( 'ABSPATH' )) exit;
class SmsAlertConstants
{
	const SUCCESS				= "SUCCESS";
	const FAILURE				= "FAILURE";
	const TEXT_DOMAIN 			= "sms-alert";
	const PATTERN_PHONE			= '/^(\+)?(country_code)?0?\d+$/'; //'/^\d{10}$/';//'/\d{10}$/';
	const ERROR_JSON_TYPE 		= 'error';
	const SUCCESS_JSON_TYPE 	= 'success';
	const USERPRO_AJAX_CHECK	= "mo_phone_validation";
	const USERPRO_VER_FIELD_META= "verification_form";
	const SA_VERSION = "3.3.5";	
	
	function __construct()
	{
		$this->define_global();
	}
	
	public static function getPhonePattern()
	{
		$country_code = smsalert_get_option( 'default_country_code', 'smsalert_general' );
		$sa_mobile_pattern = smsalert_get_option( 'sa_mobile_pattern', 'smsalert_general','/^(\+)?(country_code)?0?\d{10}$/' );
		$pattern = ($sa_mobile_pattern!='') ? $sa_mobile_pattern:self::PATTERN_PHONE;
		$country_code = str_replace('+', '', $country_code);
		$pattern_phone = str_replace("country_code",$country_code,$pattern);
		return $pattern_phone;
	}	
	
	function define_global()
	{
		global $phoneLogic;
		$phoneLogic = new PhoneLogic();
		define('SA_MOV_DIR', plugin_dir_path(dirname(__FILE__)));
		define('SA_MOV_URL', plugin_dir_url(dirname(__FILE__)));
		define('SA_MOV_CSS_URL', SA_MOV_URL . 'css/sms_alert_customer_validation_style.css?v=3.3.1');
		define('SA_MOV_LOADER_URL', SA_MOV_URL . 'images/ajax-loader.gif');
	}
}
new SmsAlertConstants;