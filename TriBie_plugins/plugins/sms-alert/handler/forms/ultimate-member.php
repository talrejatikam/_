<?php
if (! defined( 'ABSPATH' )) exit;
	class UltimateMemberRegistrationForm extends FormInterface
	{		
		private $formSessionVar = FormSessionVars::UM_DEFAULT_REG;
		private $phoneFormID 	= "input[name^='billing_phone']";
		
		function handleForm()
		{
			if (is_plugin_active( 'ultimate-member/ultimate-member.php' )) //>= UM version 2.0.17			
			{
				add_filter( 'um_add_user_frontend_submitted', array($this,'smsalert_um_user_registration'), 1,1);
			}
			else //< UM version 2.0.17 
			{
				add_action( 'um_before_new_user_register'	, array($this,'smsalert_um_user_registration'), 1,1);
			}
			add_action( 'um_submit_form_errors_hook_'	, array($this,'smsalert_um_registration_validation'), 10 );			
			add_action( 'um_registration_complete'		, array($this,'smsalert_um_registration_complete')	, 10, 2 );
		}
		
		public static function my_predefined_fields( $predefined_fields ) 
		{
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
		
		function smsalert_um_registration_validation( $args ) {
			if(smsalert_get_option('allow_multiple_user', 'smsalert_general')!="on" && !SmsAlertUtility::isBlank( $args['billing_phone'] ) ) {
				if(sizeof(get_users(array('meta_key' => 'billing_phone', 'meta_value' => $args['billing_phone'] ))) > 0 ) 
				{
					UM()->form()->add_error( 'billing_phone', 'An account is already registered with this mobile number. Please login.');
				}
			}
		}
		
		function smsalert_um_registration_complete( $user_id, $args ) {
			$user_phone = (!empty($args['billing_phone'])) ? $args['billing_phone'] : '';
			do_action('smsalert_after_update_new_user_phone', $user_id, $user_phone);
		}

		public static function isFormEnabled() 
		{
			return (smsalert_get_option('buyer_signup_otp', 'smsalert_general')=="on") ? true : false;
		}

		function smsalert_um_user_registration($args)
		{
			
			SmsAlertUtility::checkSession();
			$errors = new WP_Error();
			
			if(isset($_SESSION['sa_um_mobile_verified']))
			{
				unset($_SESSION['sa_um_mobile_verified']);
				return $args;
			}
			
			SmsAlertUtility::initialize_transaction($this->formSessionVar);
			
			foreach ($args as $key => $value)
			{
				if($key=="user_login")
					$username = $value;
				elseif ($key=="user_email")
					$email = $value;
				elseif ($key=="user_password")
					$password = $value;
				elseif ($key == 'billing_phone')
					$phone_number = $value;
				else
					$extra_data[$key]=$value;
			}
			
			$this->startOtpTransaction($username,$email,$errors,$phone_number,$password,$extra_data);
			exit();
		}

		function startOtpTransaction($username,$email,$errors,$phone_number,$password,$extra_data)
		{
			smsalert_site_challenge_otp($username,$email,$errors,$phone_number,"phone",$password,$extra_data);
		}

		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			SmsAlertUtility::checkSession();
			if(!isset($_SESSION[$this->formSessionVar])) return;
			smsalert_site_otp_validation_form($user_login,$user_email,$phone_number,SmsAlertUtility::_get_invalid_otp_method(),"phone",FALSE);
		}

		function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
		{
			SmsAlertUtility::checkSession();
			if(!isset($_SESSION[$this->formSessionVar])) return;
			$_SESSION['sa_um_mobile_verified']=true;
		}

		public function unsetOTPSessionVariables()
		{
			unset($_SESSION[$this->txSessionId]);
			unset($_SESSION[$this->formSessionVar]);
		}

		public function is_ajax_form_in_play($isAjax)
		{
			SmsAlertUtility::checkSession();
			return isset($_SESSION[$this->formSessionVar]) ? FALSE : $isAjax;
		}

		public function getPhoneNumberSelector($selector)	
		{
			SmsAlertUtility::checkSession();
			if(self::isFormEnabled()) array_push($selector, $this->phoneFormID); 
			return $selector;
		}

		function handleFormOptions()
	    {
		}
	}
	new UltimateMemberRegistrationForm;