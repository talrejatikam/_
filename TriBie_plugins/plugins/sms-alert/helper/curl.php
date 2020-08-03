<?php
if (! defined( 'ABSPATH' )) exit;
class SmsAlertcURLOTP
{	
	public static function sendtemplatemismatchemail($template)
	{
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '');
		$To_mail=smsalert_get_option( 'alert_email', 'smsalert_general', '');
		
		//Email template with content
		$params = array(
                'template' => nl2br($template),
                'username' => $username,
                'server_name' => $_SERVER['SERVER_NAME'],
                'admin_url' => admin_url(),
        );
		$emailcontent = get_smsalert_template('template/emails/mismatch_template.php',$params);
		wp_mail( $To_mail, '❗ ✱ SMS Alert ✱ Template Mismatch', $emailcontent,'content-type:text/html');
	}
	
	public static function checkPhoneNos($nos=NULL,$force_prefix=true)
	{
		$country_code = smsalert_get_option( 'default_country_code', 'smsalert_general' );
		
		$nos = explode(',',$nos);
		$valid_no=array();
		if(is_array($nos))
		{			
			foreach($nos as $no){
				$no = ltrim(ltrim($no, '+'),'0'); //remove leading + and 0
				if(!$force_prefix)
				{
					$no = (substr($no,0,strlen($country_code))==$country_code) ? substr($no,strlen($country_code)) : $no;
				}
				else
				{
					$no = (substr($no,0,strlen($country_code))!=$country_code) ? $country_code.$no : $no;
				}
				
				
				
				
				
				$match = preg_match(SmsAlertConstants::getPhonePattern(),$no);
				if($match)
				{
					$valid_no[] = $no;
				}
			}
		}
		
		if(sizeof($valid_no)>0)
		{
			return implode(',',$valid_no);
		}
		else
		{
			return false;
		}
	}

	public static function sendsms($sms_data) 
	{
        $response = false;
        $username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
        $password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
        $senderid = smsalert_get_option( 'smsalert_api', 'smsalert_gateway' );
		$enable_short_url = smsalert_get_option( 'enable_short_url', 'smsalert_general');
		
        $phone = self::checkPhoneNos($sms_data['number']);
		if($phone===false)
		{
			$data=array();
			$data['status']= "error";
			$data['description']= "phone number not valid";
			return json_encode($data);
		}
        $text = htmlspecialchars_decode($sms_data['sms_body']);
        //bail out if nothing provided
        if ( empty( $username ) || empty( $password ) || empty( $senderid ) || empty( $text ) ) {
            return $response;
        }

		$url = base64_decode("aHR0cDovL3d3dy5zbXNhbGVydC5jby5pbi9hcGkvcHVzaC5qc29u");
		$fields = array('user'=>$username, 'pwd'=>$password, 'mobileno'=>$phone, 'sender'=>$senderid, 'text'=>$text);
		
		if($enable_short_url=='on'){$fields['shortenurl']=1;}
		$json 			= json_encode($fields);		
		$fields 		= apply_filters('sa_before_send_sms', $fields);
		$response 		= self::callAPI($url, $fields, null);
		$response_arr	= json_decode($response,true);
		
		apply_filters('sa_after_send_sms', $response_arr);
		
		if($response_arr['status']=='error') {
			$error = (is_array($response_arr['description'])) ? $response_arr['description']['desc'] : $response_arr['description'];
			if($error == "Invalid Template Match")
			{
				self::sendtemplatemismatchemail($text);
			}
		}
        return $response;
    }
	
	public static function smsalert_send_otp_token($form, $email='', $phone='')
	{
		$phone = self::checkPhoneNos($phone);
		$cookie_value = get_smsalert_cookie($phone);
		$max_otp_resend_allowed = smsalert_get_option( 'max_otp_resend_allowed', 'smsalert_general');
		if(get_smsalert_cookie($phone)>$max_otp_resend_allowed)
		{
			$data=array();
			$data['status']= "error";
			$data['description']['desc']= SmsAlertMessages::showMessage('MAX_OTP_LIMIT');
			return json_encode($data);
		}
		
		
		
		$response = false;
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
        $password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
        $senderid = smsalert_get_option( 'smsalert_api', 'smsalert_gateway' );
		$template = smsalert_get_option( 'sms_otp_send', 'smsalert_message', SmsAlertMessages::showMessage('DEFAULT_BUYER_OTP'));
		if($phone===false)
		{
			$data=array();
			$data['status']= "error";
			$data['description']['desc']= "phone number not valid";
			return json_encode($data);
		}
		
		
        if ( empty( $username ) || empty( $password ) || empty( $senderid ) ) {
            return $response;
        }
		
		$url = base64_decode("aHR0cDovL3d3dy5zbXNhbGVydC5jby5pbi9hcGkvbXZlcmlmeS5qc29u==");

		$fields = array('user'=>$username, 'pwd'=>$password, 'mobileno'=>$phone, 'sender'=>$senderid, 'template'=>$template);
		$json = json_encode($fields);
		$response = self::callAPI($url, $fields, null);	
		$response_arr = (array)json_decode($response,true);
		if(array_key_exists('status',$response_arr) && $response_arr['status']=='error') {
			$error = (is_array($response_arr['description'])) ? $response_arr['description']['desc'] : $response_arr['description'];
			if($error == "Invalid Template Match")
			{
				self::sendtemplatemismatchemail($template);
			}
		}
		else
		{
			create_smsalert_cookie($phone,$cookie_value+1);
		}
		
		return $response;
	}
	
	public static function validate_otp_token($mobileno,$otpToken)
	{
        $response = false;
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
        $password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
        $senderid = smsalert_get_option( 'smsalert_api', 'smsalert_gateway' );
		$mobileno = self::checkPhoneNos($mobileno);
		if($mobileno===false)
		{
			$data=array();
			$data['status']= "error";
			$data['description']= "phone number not valid";
			return json_encode($data);
		}
		
        if ( empty( $username ) || empty( $password ) || empty( $senderid ) ) {
            return $response;
        }
		$url = base64_decode("aHR0cDovL3d3dy5zbXNhbGVydC5jby5pbi9hcGkvbXZlcmlmeS5qc29u");

		$fields = array('user'=>$username, 'pwd'=>$password, 'mobileno'=>$mobileno, 'code'=>$otpToken);
		
		$response    = self::callAPI($url, $fields, null);
		$content = json_decode($response,true);
		if(isset($content['description']['desc']) && strcasecmp($content['description']['desc'], 'Code Matched successfully.') == 0) {
			clear_smsalert_cookie($mobileno);
		}
		
		
		return $response;
	}
	
	public static function get_senderids( $username=NULL, $password = NULL)
    {
	   if ( empty( $username ) || empty( $password ) ) {
			return '';
       }
               
       $url = base64_decode("aHR0cDovL3d3dy5zbXNhbGVydC5jby5pbi9hcGkvc2VuZGVybGlzdC5qc29u");

		$fields = array('user'=>$username, 'pwd'=>$password);

		$response = self::callAPI($url, $fields, null);
		return $response;
    }
	
	public static function get_templates( $username=NULL, $password = NULL)
    {
	   if ( empty( $username ) || empty( $password ) ) {
			return '';
       }
       $url = base64_decode("aHR0cDovL3d3dy5zbXNhbGVydC5jby5pbi9hcGkvdGVtcGxhdGVsaXN0Lmpzb24=");

		$fields = array('user'=>$username, 'pwd'=>$password);

		$response = self::callAPI($url, $fields, null);
		return $response;
    }
	
	public static function get_credits()
    {
       $response = false;
	   $username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
       $password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
	   
	   if ( empty( $username ) || empty( $password ) ) {
			return $response;
       }
               
       $url = base64_decode("aHR0cDovL3d3dy5zbXNhbGVydC5jby5pbi9hcGkvY3JlZGl0c3RhdHVzLmpzb24=");

		$fields = array('user'=>$username, 'pwd'=>$password);
		$response    = self::callAPI($url, $fields, null);
		return $response;
	} 
	
	public static function group_list()
    {
       $username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
       $password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
	   
	   if ( empty( $username ) || empty( $password ) ) {
			return '';
       }
               
       $url = base64_decode("aHR0cDovL3d3dy5zbXNhbGVydC5jby5pbi9hcGkvZ3JvdXBsaXN0Lmpzb24=");

		$fields = array('user'=>$username, 'pwd'=>$password);

		$response    = self::callAPI($url, $fields, null);
		return $response;
    }

	public static function country_list()
    {
        $url = base64_decode("aHR0cDovL3d3dy5zbXNhbGVydC5jby5pbi9hcGkvY291bnRyeWxpc3QuanNvbg==");
		$response    = self::callAPI($url, null, null);
		return $response;
    }	
		
	public static function creategrp()
    {
       $username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
       $password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
	   
	   if ( empty( $username ) || empty( $password ) ) {
			return '';
       }
               
       $url = base64_decode("aHR0cDovL3d3dy5zbXNhbGVydC5jby5pbi9hcGkvY3JlYXRlZ3JvdXAuanNvbg==");

		$fields = array('user'=>$username, 'pwd'=>$password, 'name'=>$_SERVER['SERVER_NAME']);

		$response    = self::callAPI($url, $fields, null);
		return $response;
    } 	
	
	public static function create_contact($sms_datas=null,$group_name,$extra_fields=array())
	{
	
		if(is_array($sms_datas) && sizeof($sms_datas) == 0)
			return false;
		
		$username 		= smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
        $password 		= smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
		
		//$group_name 	= smsalert_get_option( 'group_auto_sync', 'smsalert_general', '');
		$xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<group>
</group>
XML;

		$msg = new SimpleXMLElement($xmlstr);
		$user = $msg->addChild('user');
		$user->addAttribute('username', $username);
		$user->addAttribute('password', $password);
		$user->addAttribute('grp_name', $group_name);
		$members = $msg->addChild('members');
		foreach($sms_datas as $sms_data)
		{
			$member = $members->addChild('member');
			$member->addAttribute('name', $sms_data['person_name']);
			$member->addAttribute('number', $sms_data['number']);
			
			if(!empty($extra_fields))
			{
				$memb = $member->addChild('meta-data');
				foreach($extra_fields as $key=>$value)
				{
				  $memb->addAttribute($key, $value);
				}
			}
			
		}	
		$xmldata = $msg->asXML();
		$url = base64_decode("aHR0cHM6Ly93d3cuc21zYWxlcnQuY28uaW4vYXBpL2NyZWF0ZWNvbnRhY3R4bWwuanNvbg==");
		$fields = array('data'=>$xmldata);
		$response   = self::callAPI($url, $fields, null);
		return $response;
	}
	
	public  function send_sms_xml($sms_datas)
	{		
		if(is_array($sms_datas) && sizeof($sms_datas) == 0)
			return false;
		
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
        $password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
        $senderid = smsalert_get_option( 'smsalert_api', 'smsalert_gateway' );
		
		$xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<message>
</message>
XML;
		$msg = new SimpleXMLElement($xmlstr);
		$user = $msg->addChild('user');
		$user->addAttribute('username', $username);
		$user->addAttribute('password', $password);
		
		foreach($sms_datas as $sms_data){
			$phone = self::checkPhoneNos($sms_data['number']);
			if($phone!==false)
			{
				$sms = $msg->addChild('sms');
				$sms->addAttribute('text', $sms_data["sms_body"]);
				
				$address = $sms->addChild('address');
				$address->addAttribute('from', $senderid);
				$address->addAttribute('to', $phone);
			}	
		}

		if($msg->count() <= 1)
			return false;
		
		$xmldata = $msg->asXML();
		$url = base64_decode("aHR0cDovL3d3dy5zbXNhbGVydC5jby5pbi9hcGkveG1scHVzaC5qc29uPw==");
			
		$fields 	= array('data'=>$xmldata);		
		$response   = self::callAPI($url, $fields, null);
		return $response;
	}
	
	public static function callAPI($url, $params, $headers = array("Content-Type: application/json"))
	{
		$extra_params 	= array('plugin'=>'woocommerce', 'website'=>$_SERVER['SERVER_NAME']);
		$params 		= (!is_null($params)) ? array_merge($params, $extra_params) : $extra_params;			
		$args			= array('body'=>$params, 'timeout'=>15);
		$request 		= wp_remote_post($url,$args);
		
		if (is_wp_error($request))
		{
			$data					= array();
			$data['status'] 		= "error";
			$data['description']	= $request->get_error_message();
			return json_encode($data);
		}
		
		return wp_remote_retrieve_body( $request );
	}
}