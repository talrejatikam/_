<?php
if (! defined( 'ABSPATH' )) exit;
class SmsAlertMessages
{
	function __construct()
	{
		//created an array instead of messages instead of constant variables for Translation reasons.
		define("SALRT_MESSAGES", serialize( array(
			//General Messages
			//"VALID_PHONE_TXT" => __("Validate your Phone Number",SmsAlertConstants::TEXT_DOMAIN),
			//"ERROR_OTP_PHONE" => __("There was an error in sending the OTP to the given Phone Number. Please Try Again or contact site Admin.",SmsAlertConstants::TEXT_DOMAIN),
			"OTP_RANGE" => __("Only digits within range 4-8 are allowed.",SmsAlertConstants::TEXT_DOMAIN),
			//"GO_BACK" => __("Go Back",SmsAlertConstants::TEXT_DOMAIN),
			//"ENTER_PHONE_FORMAT"  => __("Enter a number in the following format : 9xxxxxxxxx",SmsAlertConstants::TEXT_DOMAIN),
			//"VERIFY_CODE_TXT"  => __("Verify Code ",SmsAlertConstants::TEXT_DOMAIN),
			"SEND_OTP"  => __("Send OTP",SmsAlertConstants::TEXT_DOMAIN),
			"RESEND_OTP"  => __("Resend OTP",SmsAlertConstants::TEXT_DOMAIN),
			"VALIDATE_OTP"  => __("Validate OTP",SmsAlertConstants::TEXT_DOMAIN),
			"RESEND"  => __("Resend",SmsAlertConstants::TEXT_DOMAIN),
			//"Enter_Verify_Code"  => __("Enter Verification Code",SmsAlertConstants::TEXT_DOMAIN),
			//"ENABLE_LINK"  => __("Please Enter a Phone Number to enable this link",SmsAlertConstants::TEXT_DOMAIN),
			"Phone"  => __("Phone",SmsAlertConstants::TEXT_DOMAIN),
			//"ENTER_MOB_NO"  => __("Please enter your mobile number",SmsAlertConstants::TEXT_DOMAIN),
			"INVALID_OTP"  => __("Invalid one time passcode. Please enter a valid passcode.",SmsAlertConstants::TEXT_DOMAIN),
			"ENTER_PHONE_CODE"  => __("Please enter the verification code sent to your phone.",SmsAlertConstants::TEXT_DOMAIN),
			//"ENTER_VERIFY_CODE"  => __("Verify Code is a required field",SmsAlertConstants::TEXT_DOMAIN),
			
			
			
			//one time use message start			
			
			"DEFAULT_BUYER_SMS_PENDING" 				=> sprintf(__('Hello %s, you are just one step away from placing your order, please complete your payment, to proceed.',SmsAlertConstants::TEXT_DOMAIN), '[billing_first_name]'),
			"DEFAULT_ADMIN_SMS_CANCELLED" 	=> sprintf(__('%s Your order %s Rs. %s. is Cancelled.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[order_id]', '[order_amount]'),
			"DEFAULT_ADMIN_SMS_PENDING" 	=> sprintf(__('%s Hello, %s is trying to place order %s value Rs. %s',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '[billing_first_name]', '#[order_id]', '[order_amount]'),			
			"DEFAULT_ADMIN_SMS_ON_HOLD" 	=> sprintf(__('%s Your order %s Rs. %s. is On Hold Now.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[order_id]', '[order_amount]'),
			"DEFAULT_ADMIN_SMS_COMPLETED" 	=> sprintf(__('%s Your order %s Rs. %s. is completed.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[order_id]', '[order_amount]'),
			"DEFAULT_ADMIN_SMS_PROCESSING" 	=> sprintf(__('%s You have a new order %s for order value Rs. %s. Please check your admin dashboard for complete details.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[order_id]', '[order_amount]'),
			"DEFAULT_BUYER_SMS_PROCESSING"  	=> sprintf(__('Hello %s, thank you for placing your order %s with %s.',SmsAlertConstants::TEXT_DOMAIN), '[billing_first_name]', '#[order_id]', '[store_name]'),
			"DEFAULT_BUYER_SMS_COMPLETED" 		=> sprintf(__('Hello %s, your order %s with %s has been dispatched and shall deliver to you shortly.',SmsAlertConstants::TEXT_DOMAIN), '[billing_first_name]', '#[order_id]', '[store_name]'),			
			"DEFAULT_BUYER_SMS_ON_HOLD" 				=> sprintf(__('Hello %s, your order %s with %s has been put on hold, our team will contact you shortly with more details.',SmsAlertConstants::TEXT_DOMAIN), '[billing_first_name]', '#[order_id]', '[store_name]'),			
			"DEFAULT_BUYER_SMS_CANCELLED" 				=> sprintf(__('Hello %s, your order %s with %s has been cancelled due to some un-avoidable conditions. Sorry for the inconvenience caused.',SmsAlertConstants::TEXT_DOMAIN), '[billing_first_name]', '#[order_id]', '[store_name]'),
			
			//"DEFAULT_WARRANTY_STATUS_CHANGED" 	=> sprintf(__('Hello %s, status of your warranty request no. %s against %s with %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[billing_first_name]', '[rma_number]', '[order_id]', '[store_name]', '[rma_status]'),			
			//"DEFAULT_EDD_BUYER_SMS_STATUS_CHANGED" 	=> sprintf(__('EDD:Hello %s, status of your %s with %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[first_name]', '[order_id]', '[store_name]', '[order_status]'),
			//"DEFAULT_EDD_ADMIN_SMS_STATUS_CHANGED" 	=> sprintf(__('%s status of order %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[order_id]', '[order_status]'),			
			//"DEFAULT_WCBK_BUYER_SMS_STATUS_CHANGED" 	=> sprintf(__('Hello %s, status of your booking %s with %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[first_name]', '[booking_id]', '[store_name]', '[booking_status]'),
			//"DEFAULT_WCBK_ADMIN_SMS_STATUS_CHANGED" 	=> sprintf(__('%s status of order %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[booking_id]', '[booking_status]'),			
			//"DEFAULT_EMBK_BUYER_SMS_STATUS_CHANGED" 	=> sprintf(__('Hello %s, status of your booking %s with %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[first_name]', '[booking_id]', '[store_name]', '[booking_status]'),			
			//"DEFAULT_EMBK_ADMIN_SMS_STATUS_CHANGED" 	=> sprintf(__('%s status of order %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[booking_id]', '[booking_status]'),			
			
			"DEFAULT_ADMIN_OUT_OF_STOCK_MSG" 	=> sprintf(__('%s Out Of Stock Alert For Product %s, current stock %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '[item_name]', '[item_qty]'),
			"DEFAULT_ADMIN_LOW_STOCK_MSG" 	=> sprintf(__('%s Low Stock Alert For Product %s, current stock %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '[item_name]', '[item_qty]'),
			
			//"DEFAULT_BIS_CUSTOMER_MESSAGE" 	=> sprintf(__('Hello, %s is now available, you can order it on %s.',SmsAlertConstants::TEXT_DOMAIN), '[item_name]', '[shop_url]'),
			//"DEFAULT_BIS_SUBSCRIBED_MESSAGE" 	=> sprintf(__('We have noted your request and we will notify you as soon as %s is available for order with us.',SmsAlertConstants::TEXT_DOMAIN), '[item_name]'),
		
			"DEFAULT_AC_ADMIN_MESSAGE" 	=> sprintf(__('%s Product %s is left in cart by %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '[item_name]', '[name]', '[affiliate_id]', '#[order_id]'),			
			"DEFAULT_AC_CUSTOMER_MESSAGE" 	=> sprintf(__('Hello %s, Your Product %s is left in cart.',SmsAlertConstants::TEXT_DOMAIN), '[name]', '[item_name]'),
			
			"DEFAULT_ADMIN_SMS_STATUS_CHANGED" 	=> sprintf(__('%s status of order %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[order_id]', '[order_status]'),
			//"ERROR_PHONE_FORMAT" 			=> sprintf(__('%sphone%s is not a valid phone number. Please enter a valid Phone Number',SmsAlertConstants::TEXT_DOMAIN), '##', '##'),			
			//"PHONE_EXISTS" 			=> __('Phone Number is already in use. Please use another number.',SmsAlertConstants::TEXT_DOMAIN),
			//"REGISTER_PHONE_LOGIN" 	=> __('A new security system has been enabled for you. Please register your phone to continue.',SmsAlertConstants::TEXT_DOMAIN),
			
			//one time use message end	
			
			
			//not in use start
			
			"OTP_INVALID_NO" 				=> sprintf(__('your verification code is %s. Only valid for %s min.',SmsAlertConstants::TEXT_DOMAIN), '[otp]', '15'),
			"OTP_ADMIN_MESSAGE" 			=> sprintf(__('You have a new Order%sThe %s is now %s',SmsAlertConstants::TEXT_DOMAIN), PHP_EOL, '[order_id]', '[order_status]'.PHP_EOL),
			"OTP_BUYER_MESSAGE" 			=> sprintf(__('Thanks for purchasing%sYour %s is now %sThank you',SmsAlertConstants::TEXT_DOMAIN), PHP_EOL, '[order_id]', '[order_status]'.PHP_EOL),			
			//"DEFAULT_ADMIN_SMS_DRIVER_ASSIGNED" => __('Driver is assigned for this order.',SmsAlertConstants::TEXT_DOMAIN),
			//"DEFAULT_ADMIN_SMS_OUT_FOR_DELIVERY" 	=> __('Driver is out for delivery with this order.',SmsAlertConstants::TEXT_DOMAIN),
			//"DEFAULT_ADMIN_SMS_ORDER_RETURNED" 	=> __('Driver is out for return order.',SmsAlertConstants::TEXT_DOMAIN),
			//not in use end
			
			//two time and three time start
			
			//"DEFAULT_WPAM_BUYER_SMS_STATUS_CHANGED" 		=> sprintf(__('Hello %s, status of your affiliate account %s with %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[first_name]', '[affiliate_id]', '[store_name]', '[affiliate_status]'),			
			"DEFAULT_BUYER_SMS_STATUS_CHANGED" 				=> sprintf(__('Hello %s, status of your order %s with %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[billing_first_name]', '#[order_id]', '[store_name]', '[order_status]'),
			"DEFAULT_BUYER_NOTE" 							=> sprintf(__('Hello %s, a new note has been added to your order %s %s',SmsAlertConstants::TEXT_DOMAIN), '[billing_first_name]', '#[order_id]:', '[note]'),
			"DEFAULT_BUYER_OTP" 							=> sprintf(__('Your verification code is %s',SmsAlertConstants::TEXT_DOMAIN), '[otp]'),
			"OTP_SENT_PHONE" 								=> sprintf(__('A OTP (One Time Passcode) has been sent to %sphone%s . Please enter the OTP in the field below to verify your phone.',SmsAlertConstants::TEXT_DOMAIN), '##', '##'),			
			"DEFAULT_WPAM_ADMIN_SMS_STATUS_CHANGED" 		=> sprintf(__('%s status of order %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[affiliate_id]', '[affiliate_status]'),
			"DEFAULT_WPAM_BUYER_SMS_TRANS_STATUS_CHANGED" 	=> sprintf(__('Hello %s,commission has been %s for %s to your affiliate account %s against order %s.',SmsAlertConstants::TEXT_DOMAIN), '[first_name]', '[transaction_type]', '[commission_amt]', '[affiliate_id]', '#[order_id]'),
			"DEFAULT_WPAM_ADMIN_SMS_TRANS_STATUS_CHANGED" 	=> sprintf(__('%s commission has been %s for %s to affiliate account %s against order %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '[transaction_type]', '[commission_amt]', '[affiliate_id]', '#[order_id]'),			
			"DEFAULT_ADMIN_NEW_USER_REGISTER"     			=> sprintf(__('%s New user signup.%sName: %sEmail: %sPhone: %s',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', PHP_EOL, '[username]'.PHP_EOL, '[email]'.PHP_EOL, '[billing_phone]'),
			"PHONE_NOT_FOUND" 								=> __('Sorry, but you do not have a registered phone number.',SmsAlertConstants::TEXT_DOMAIN),			
			"PHONE_MISMATCH" 								=> __('The phone number OTP was sent to and the phone number in contact submission do not match.',SmsAlertConstants::TEXT_DOMAIN),
			//two time and three time end
		
		
			"DEFAULT_USER_COURSE_ENROLL" 	=> sprintf(__('Congratulation %s, you have enrolled course - %s',SmsAlertConstants::TEXT_DOMAIN), '[username]', '[course_name]'),
			//"DEFAULT_LPRESS_ADMIN_SMS_STATUS_CHANGED" 	=> sprintf(__('%s status of order %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[order_id]', '[order_status]'),
			//"DEFAULT_LPRESS_BUYER_SMS_STATUS_CHANGED" 	=> sprintf(__('Hello %s, status of your %s with %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[username]', '[order_id]', '[store_name]', '[order_status]'),			
			"DEFAULT_NEW_USER_REGISTER" 	=> sprintf(__('Hello %s, Thank you for registering with %s.',SmsAlertConstants::TEXT_DOMAIN), '[username]', '[store_name]'),			
			"DEFAULT_ADMIN_COURSE_FINISHED" 	=> sprintf(__('Hi Admin %s has finished course - %s',SmsAlertConstants::TEXT_DOMAIN), '[username]', '[course_name]'),			
			"DEFAULT_USER_COURSE_FINISHED" 	=> sprintf(__('Congratulation you have finished course - %s',SmsAlertConstants::TEXT_DOMAIN), '[course_name]'),			
			"DEFAULT_ADMIN_NEW_TEACHER_REGISTER" 	=> sprintf(__('Hi admin, an instructor %s has been joined.',SmsAlertConstants::TEXT_DOMAIN), '[username]'),			
			"DEFAULT_ADMIN_COURSE_ENROLL" 	=> sprintf(__('Hi Admin %s has enrolled course - %s',SmsAlertConstants::TEXT_DOMAIN), '[username]', '[course_name]'),
			"DEFAULT_NEW_TEACHER_REGISTER" 	=> sprintf(__('Congratulation %s, You have become an instructor.',SmsAlertConstants::TEXT_DOMAIN), '[username]'),	

			/*translation required*/
		)));
	}

	public static function showMessage($message , $data=array())
	{
		$displayMessage = "";
		$messages = explode(" ",$message);
		$msg = unserialize(SALRT_MESSAGES);
		//return __($msg[$message],SmsAlertConstants::TEXT_DOMAIN);
		return (!empty($msg[$message]) ? $msg[$message] : '');
		/* foreach ($messages as $message)
		{
			if(!SmsAlertUtility::isBlank($message))
			{
				//$formatMessage = constant( "self::".$message );
				$formatMessage = $msg[$message];
			    foreach($data as $key => $value)
			    {
			        $formatMessage = str_replace("{{" . $key . "}}", $value ,$formatMessage);
			    }
			    $displayMessage.=$formatMessage;
			}
		}
	    return $displayMessage; */
	}
}
new SmsAlertMessages;