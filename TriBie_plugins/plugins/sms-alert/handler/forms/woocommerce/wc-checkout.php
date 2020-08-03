<?php
if (! defined( 'ABSPATH' )) exit;
class WooCommerceCheckOutForm extends FormInterface
{
	private $guestCheckOutOnly;
	private $showButton;
	private $formSessionVar = FormSessionVars::WC_CHECKOUT;
	private $formSessionVar2 = 'block-checkout';										 
	private $popupEnabled;
	private $paymentMethods;
	private $otp_for_selected_gateways;

	function handleForm()
	{	
		add_action( 'woocommerce_checkout_process', array($this,'my_custom_checkout_field_process'));
		add_action( 'woocommerce_blocks_enqueue_checkout_block_scripts_after', array($this,'showButtonOnBlockPage'));


		$this->paymentMethods 				= maybe_unserialize(smsalert_get_option( 'checkout_payment_plans', 'smsalert_general' ));
		$this->otp_for_selected_gateways 	= (smsalert_get_option('otp_for_selected_gateways', 'smsalert_general')=="on") ? TRUE : FALSE;
		
		$this->popupEnabled					= (smsalert_get_option('checkout_otp_popup', 'smsalert_general')=="on") ? TRUE : FALSE;
		$this->guestCheckOutOnly			= (smsalert_get_option('checkout_show_otp_guest_only', 'smsalert_general')=="on") ? TRUE : FALSE;
		$this->showButton 					= (smsalert_get_option('checkout_show_otp_button', 'smsalert_general')=="on") ? TRUE : FALSE;

		if($this->popupEnabled) 
		{				
			add_action( 'woocommerce_review_order_before_submit' , array($this,'add_custom_popup') 		, 99	);
			add_action( 'woocommerce_review_order_after_submit'  , array($this,'add_custom_button')		, 1, 1	);
		}
		else
		{
			add_action( 'woocommerce_after_checkout_billing_form' , array($this,'my_custom_checkout_field'), 99		);
		}
		
		add_action( 'wp_enqueue_scripts', array($this,'enqueue_script_for_intellinput'));
		
		if($this->otp_for_selected_gateways==TRUE)
			add_action( 'wp_enqueue_scripts', array($this,'enqueue_script_on_page'));
		
		$this->routeData();
	}
	public function showButtonOnBlockPage(){
		$otp_verify_btn_text = smsalert_get_option( 'otp_verify_btn_text', 'smsalert_general', '');
		$otp_resend_timer = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15');
		
		echo '<script>
		jQuery(document).ready(function(){
			
			var button_text = "'.$otp_verify_btn_text.'";
						
			var button=jQuery("<button/>").attr({
				type: "button",
				id: "smsalert_otp_token_block_submit",
				title:"Please Enter a Phone Number to enable this link",
				class:"components-button wc-block-components-button button alt ",

			});
			
			jQuery(".wc-block-components-checkout-place-order-button").next().append(button);
			
			jQuery(button).insertAfter(".wc-block-components-checkout-place-order-button");
			
			jQuery(".wc-block-components-checkout-place-order-button").hide();			
			
			jQuery("#smsalert_otp_token_block_submit").text(button_text);
			
			jQuery("#smsalert_otp_token_block_submit").click(function(){				
				var e = jQuery(".wc-block-components-checkout-form").find("#email").val();
				var m = jQuery(".wc-block-components-checkout-form").find("#phone").val();
				
				saInitBlockOTPProcess(
					this,
					"'.site_url().'/?option=smsalert-woocommerce-block-checkout",
					{user_email:e, user_phone:m},
					'.$otp_resend_timer.',
					function(resp){
						if(resp.result=="success"){$mo(".blockUI").hide()}else{$mo(".blockUI").hide()}
					},
					function(resp){
						
					}
				)
				
			});
			
			jQuery(document).on("click", ".smsalert_otp_validate_submit",function(){				
				var current_form = jQuery(".smsalertModal");
				var action_url = "'.site_url().'/?option=smsalert-woocommerce-validate-otp-form";
				var otp_token = jQuery("#order_verify").val();
				var bil_phone = jQuery(".wc-block-components-checkout-form").find("#phone").val();
				
				var data = {otp_type:"phone",from_both:"",billing_phone:bil_phone,order_verify:otp_token};
				sa_validateBlockOTP(this,action_url,data,function(o){
					console.log(o)
					console.log("validated now do what you want");		
					jQuery(".wc-block-components-checkout-place-order-button").trigger("click");			
				});
				return false;
			});
			
		});
		</script>';
		
		$params=array(
		'otp_input_field_nm'=>'order_verify',
		);
		$otp_template_style =  smsalert_get_option( 'otp_template_style', 'smsalert_general', 'otp-popup-1.php');
		echo get_smsalert_template('template/'.$otp_template_style,$params);
	}									 

	public static function isFormEnabled()
	{
		
		return (is_plugin_active('woocommerce/woocommerce.php') && smsalert_get_option('buyer_checkout_otp', 'smsalert_general')=="on") ? true : false;
	}

	function routeData()
	{
		if(!array_key_exists('option', $_GET)) return;
		if(strcasecmp(trim($_GET['option']),'smsalert-woocommerce-checkout') == 0) $this->handle_woocommere_checkout_form($_POST);
		
		if(strcasecmp(trim($_GET['option']),'smsalert-woocommerce-block-checkout') == 0) $this->handle_woocommere_checkout_form($_POST);
		
		if(strcasecmp(trim($_GET['option']),'smsalert-woocommerce-validate-otp-form') == 0) $this->handle_otp_token_submitted($_POST);
	}
	function handle_woocommere_checkout_form($getdata)
	{
		SmsAlertUtility::checkSession();
		if(!empty($_GET['option']) && $_GET['option']=='smsalert-woocommerce-block-checkout')
		{
			SmsAlertUtility::initialize_transaction($this->formSessionVar2);
		}
		else
		{
			SmsAlertUtility::initialize_transaction($this->formSessionVar);
		}
	
		//$phone_num = preg_replace('/[^0-9]/', '', $getdata['user_phone']);
		$phone_num = SmsAlertcURLOTP::checkPhoneNos($getdata['user_phone']);
		smsalert_site_challenge_otp('test',$getdata['user_email'],null, trim($phone_num),"phone");
	}

	function checkIfVerificationNotStarted()
	{
		SmsAlertUtility::checkSession();
		if(!isset($_SESSION[$this->formSessionVar])){
			wc_add_notice(__("Verify Code is a required field",SmsAlertConstants::TEXT_DOMAIN), 'error' );
			return TRUE;
		}
		return FALSE;
	}

	function checkIfVerificationCodeNotEntered()
	{
		if(array_key_exists('order_verify', $_POST) && isset($_POST['order_verify'])) return FALSE;

		wc_add_notice( SmsAlertMessages::showMessage('ENTER_PHONE_CODE'), 'error' );
		return TRUE;
	}

	function add_custom_button($order_id)
	{
		if($this->guestCheckOutOnly && is_user_logged_in())  return;
		$this->show_validation_button_or_text(TRUE);
		$this->common_button_or_link_enable_disable_script();
		$otp_resend_timer = smsalert_get_option( 'otp_resend_timer', 'smsalert_general', '15');
		$validate_before_send_otp = smsalert_get_option( 'validate_before_send_otp', 'smsalert_general', 'off');
		
		echo ',counterRunning=false, $mo(".woocommerce-error").length>0&&$mo("html, body").animate({scrollTop:$mo("div.woocommerce").offset().top-50},1e3),';
		
		echo '$mo("#smsalert_otp_token_submit").click(function(o){ if(counterRunning){$mo("#myModal").show();return false;}';
			if($validate_before_send_otp=='on')
			{
				echo '$mo(".validate-required").find(":input,select").trigger("change");
				var error = $mo(".woocommerce-billing-fields .validate-required").not(".woocommerce-validated").find("input:not(:hidden)").length;
				
				error=error + parseInt($mo(".woocommerce-account-fields .validate-required").not(".woocommerce-validated").find("input:not(:hidden)").length);
				
				if($mo(".validate-required #terms").length> 0 && $mo(".validate-required #terms").prop("checked")==false)
				{
					error=error + 1;
				}
				if($mo("#ship-to-different-address-checkbox").prop("checked")==true)
				{
					error=error + parseInt($mo(".woocommerce-shipping-fields .validate-required").not(".woocommerce-validated").find("input:not(:hidden)").length);
				}
				if(error>0){
					$mo(".validate-required").not(".woocommerce-validated").find(":input,select").eq(0).focus();return false;
				}';
			}
			
			echo 'var e=$mo("input[name=billing_email]").val(),';
			
			
			if(is_checkout() && smsalert_get_option('checkout_show_country_code', 'smsalert_general')=="on" )
			{
			echo 'm=$mo(this).parents("form").find("input[name=billing_phone]").intlTelInput("getNumber"),';
			}
			else
			{
				echo 'm=$mo(this).parents("form").find("input[name=billing_phone]").val(),';
			}
			
			echo 'a=$mo("div.woocommerce");a.addClass("processing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),
			
			
				saInitOTPProcess(
					this,
					"'.site_url().'/?option=smsalert-woocommerce-checkout",
					{user_email:e, user_phone:m},
					'.$otp_resend_timer.',
					function(resp){
						if(resp.result=="success"){$mo(".blockUI").hide()}else{$mo(".blockUI").hide()}
					},
					function(resp){
						
					}
				)
			
		}),';
		
		echo '$mo("form.woocommerce-checkout .smsalert_otp_validate_submit").click(function(o){
			counterRunning=false,clearInterval(interval),$mo(".smsalertModal").hide(),$mo(".sa-message").removeClass("woocommerce-message"),$mo(".smsalertModal .smsalert_validate_field").hide(),$mo(".woocommerce-checkout").submit()});});';
		
		echo ($this->otp_for_selected_gateways && $this->popupEnabled) ? '' : 'jQuery("input[name=woocommerce_checkout_place_order], button[name=woocommerce_checkout_place_order]").hide();';
		
		echo '</script>';
	}

	function add_custom_popup()
	{
		if($this->guestCheckOutOnly && is_user_logged_in())  return;
		$params=array(
			'otp_input_field_nm'=>'order_verify',
		);
		$otp_template_style =  smsalert_get_option( 'otp_template_style', 'smsalert_general', 'otp-popup-1.php');
		echo get_smsalert_template('template/'.$otp_template_style,$params);
	}

	function my_custom_checkout_field( $checkout )
	{
		if($this->guestCheckOutOnly && is_user_logged_in())  return;


		echo '<div id="mo_message" style="display:none"></div>';
		$this->show_validation_button_or_text();	
		woocommerce_form_field( 'order_verify', array(
		'type'          => 'text',
		'class'         => array('form-row-wide'),
		'label'         => __("Verify Code ",SmsAlertConstants::TEXT_DOMAIN),
		'required'  	=> true,
		'placeholder'   => __("Enter Verification Code",SmsAlertConstants::TEXT_DOMAIN),
		), $checkout->get_value( 'order_verify' ));
		
		$this->common_button_or_link_enable_disable_script();

		echo ',$mo(".woocommerce-error").length>0&&$mo("html, body").animate({scrollTop:$mo("div.woocommerce").offset().top-50},1e3),$mo("#smsalert_otp_token_submit").click(function(o){var e=$mo("input[name=billing_email]").val(),';
		
		if(is_checkout() && smsalert_get_option('checkout_show_country_code', 'smsalert_general')=="on" )
		{
		echo 'n=$mo(this).parents("form").find("input[name=billing_phone]").intlTelInput("getNumber"),';
		}
		else
		{
			echo 'n=$mo(this).parents("form").find("input[name=billing_phone]").val(),';
		}
		
		echo 'a=$mo("div.woocommerce");a.addClass("processing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),$mo.ajax({url:"'.site_url().'/?option=smsalert-woocommerce-checkout",type:"POST",data:{user_email:e, user_phone:n},crossDomain:!0,dataType:"json",success:function(o){ if(o.result=="success"){$mo(".blockUI").hide(),$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").addClass("woocommerce-message"),$mo("#mo_message").show(),$mo("#order_verify").focus()}else{$mo(".blockUI").hide(),$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").addClass("woocommerce-error"),$mo("#mo_message").show();} ;},error:function(o,e,n){}}),o.preventDefault()});});</script>';
	}

	function show_validation_button_or_text($popup=FALSE)
	{
		if(!$this->showButton) $this->showTextLinkOnPage();
		if($this->showButton) $this->showButtonOnPage();
	}

	function showTextLinkOnPage()
	{
		$otp_verify_btn_text = smsalert_get_option( 'otp_verify_btn_text', 'smsalert_general', '');
		echo '<div title="'.__("Please Enter a Phone Number to enable this link",SmsAlertConstants::TEXT_DOMAIN).'"><a href="#" style="text-align:center;color:grey;pointer-events:none;" id="smsalert_otp_token_submit" class="" >'.$otp_verify_btn_text.'</a></div>';
	}

	function showButtonOnPage()
	{
		$otp_verify_btn_text = smsalert_get_option( 'otp_verify_btn_text', 'smsalert_general', '');
		echo '<button type="button" class="button alt sa-otp-btn-init" id="smsalert_otp_token_submit" disabled title="'
			.__("Please Enter a Phone Number to enable this link",SmsAlertConstants::TEXT_DOMAIN).'" value="'
			.$otp_verify_btn_text.'" >'.$otp_verify_btn_text.'</button>';
	}

	function common_button_or_link_enable_disable_script()
	{
		echo '<script> jQuery(document).ready(function() {$mo = jQuery,';
		echo '$mo(".woocommerce-message").length>0&&($mo("#order_verify").focus(),$mo("#mo_message").addClass("woocommerce-message"),$mo("#mo_message").show());';
		if(!$this->showButton) $this->enabledDisableScriptForTextOnPage();
		if($this->showButton) $this->enableDisableScriptForButtonOnPage();
	}

	function enabledDisableScriptForTextOnPage()
	{
		echo '""!=$mo("input[name=billing_phone]").val()&&$mo("#smsalert_otp_token_submit").removeAttr("style"); $mo("input[name=billing_phone]").keyup(function(){
			var phone = $mo(this).val();				
			if(phone.replace(/\s+/g, "").match('.SmsAlertConstants::getPhonePattern().')) { $mo("#smsalert_otp_token_submit").removeAttr("style");} else{$mo("#smsalert_otp_token_submit").css({"color":"grey","pointer-events":"none"}); }
		})';
	}

	function enableDisableScriptForButtonOnPage()
	{
		echo '""!=$mo("input[name=billing_phone]").val()&&$mo("#smsalert_otp_token_submit").prop( "disabled", false );$mo("input[name=billing_phone]").keyup(function() {
			var phone = $mo(this).val();				
			if(phone.replace(/\s+/g, "").match('.SmsAlertConstants::getPhonePattern().')) {$mo("#smsalert_otp_token_submit").prop( "disabled", false );} else { $mo("#smsalert_otp_token_submit").prop( "disabled", true ); }})';
	}

	function my_custom_checkout_field_process()
	{
		if($this->guestCheckOutOnly && is_user_logged_in()) return; 
		if(!$this->isPaymentVerificationNeeded()) return;
		if($this->checkIfVerificationNotStarted()) return;
		if($this->checkIfVerificationCodeNotEntered()) return;
		$this->handle_otp_token_submitted(FALSE);		
	}

	function handle_otp_token_submitted($error)
	{
		$error = $this->processPhoneNumber();
		if(!$error) $this->processOTPEntered();
	}

	function isPaymentVerificationNeeded()
	{
		if(!$this->otp_for_selected_gateways)
			return true;
		
		$payment_method = $_POST['payment_method'];
		return in_array($payment_method,$this->paymentMethods);
	}

	function processPhoneNumber()
	{
		SmsAlertUtility::checkSession();
		//$phone_no = (smsalert_get_option('checkout_show_country_code', 'smsalert_general')=="on") ? SmsAlertcURLOTP::checkPhoneNos($_POST['billing_phone']) : $_POST['billing_phone'];
		$phone_no = SmsAlertcURLOTP::checkPhoneNos($_POST['billing_phone']);
		if(array_key_exists('phone_number_mo', $_SESSION) 
				&& strcasecmp($_SESSION['phone_number_mo'], $phone_no)!=0)
		{
			wc_add_notice(  SmsAlertMessages::showMessage('PHONE_MISMATCH'), 'error' );
			return TRUE;
		}
	}

	function handle_failed_verification($user_login,$user_email,$phone_number)
	{
		SmsAlertUtility::checkSession();
		if(!isset($_SESSION[$this->formSessionVar]) && !isset($_SESSION[$this->formSessionVar2])) return;
		//wc_add_notice( SmsAlertUtility::_get_invalid_otp_method(), 'error' );
		
		if(isset($_SESSION[$this->formSessionVar2]))
		{
			wp_send_json( SmsAlertUtility::_create_json_response(SmsAlertMessages::showMessage('INVALID_OTP'),'error'));
		}
		else
		{
			wc_add_notice( SmsAlertUtility::_get_invalid_otp_method(), 'error' );
		}		
																	   
	}

	function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
	{
		SmsAlertUtility::checkSession();
		if(!isset($_SESSION[$this->formSessionVar]) && !isset($_SESSION[$this->formSessionVar2])) return;
		
		if(isset($_SESSION[$this->formSessionVar2]))
		{
			wp_send_json( SmsAlertUtility::_create_json_response("OTP Validated Successfully.",'success'));
			$this->unsetOTPSessionVariables();
			exit();
		}
		else
		{
			$this->unsetOTPSessionVariables();
		}
	}
	
	//for number validator
	function enqueue_script_for_intellinput(){
		
		if(is_checkout() && smsalert_get_option('checkout_show_country_code', 'smsalert_general')=="on" )
		{
			wp_enqueue_script('sa_pv_intl-phones-lib',SA_MOV_URL .'js/intlTelInput-jquery.min.js' , array('jquery') ,SmsAlertConstants::SA_VERSION,true);
			wp_enqueue_script('wccheckout_utils',SA_MOV_URL .'js/utils.js',array('jquery') ,SmsAlertConstants::SA_VERSION,true);
			wp_enqueue_script('wccheckout_default',SA_MOV_URL .'js/phone-number-validate.js',array('sa_pv_intl-phones-lib'),SmsAlertConstants::SA_VERSION, true);
			wp_enqueue_style('wpv_telinputcss_style',SA_MOV_URL .'css/intlTelInput.min.css',array(),SmsAlertConstants::SA_VERSION, false);
		}		
	}

	function enqueue_script_on_page()
	{		
		if(is_checkout())
		{
			wp_register_script( 'wccheckout', SA_MOV_URL . 'js/wccheckout.min.js' , array('jquery') ,SmsAlertConstants::SA_VERSION,true);
			
			wp_localize_script( 'wccheckout', 'otp_for_selected_gateways', array(
				'paymentMethods' => $this->paymentMethods,
				'ask_otp' => ($this->guestCheckOutOnly && is_user_logged_in() ? false : true),
			));
			
			wp_enqueue_script('wccheckout');
			wp_register_script( 'smsalert-auth', SA_MOV_URL . 'js/otp-sms.min.js', array('jquery'), SmsAlertConstants::SA_VERSION, true );
			wp_enqueue_script('smsalert-auth');
		}		
	}

	function processOTPEntered()
	{
		$this->validateOTPRequest();	
	}

	function validateOTPRequest()
	{
		do_action('smsalert_validate_otp','order_verify');
	}

	public function unsetOTPSessionVariables()
	{
		unset($_SESSION[$this->txSessionId]);
		unset($_SESSION[$this->formSessionVar]);
		unset($_SESSION[$this->formSessionVar2]);
	}

	public function is_ajax_form_in_play($isAjax)
	{
		SmsAlertUtility::checkSession();
		return (isset($_SESSION[$this->formSessionVar]) || isset($_SESSION[$this->formSessionVar2])) ? TRUE : $isAjax;
	}
	

	function handleFormOptions()
	{
		//add on 12/05/2020
		add_action( 'add_meta_boxes', array($this, 'add_send_sms_meta_box') );
		add_action( 'wp_ajax_wc_sms_alert_sms_send_order_sms', array( $this,'send_custom_sms'));
		add_action( 'woocommerce_new_customer_note', array($this, 'trigger_new_customer_note'), 10 );
		update_option('mo_customer_validation_wc_checkout_enable',
			isset( $_POST['mo_customer_validation_wc_checkout_enable']) ? $_POST['mo_customer_validation_wc_checkout_enable'] : 0);
		update_option('mo_customer_validation_wc_checkout_type',
			isset(  $_POST['mo_customer_validation_wc_checkout_type']) ? $_POST['mo_customer_validation_wc_checkout_type'] : '');
		update_option('mo_customer_validation_wc_checkout_guest',
			isset(  $_POST['mo_customer_validation_wc_checkout_guest']) ? $_POST['mo_customer_validation_wc_checkout_guest'] : '');
		update_option('mo_customer_validation_wc_checkout_button',
			isset(  $_POST['mo_customer_validation_wc_checkout_button']) ? $_POST['mo_customer_validation_wc_checkout_button'] : '');
		update_option('mo_customer_validation_wc_checkout_popup',
			isset(  $_POST['mo_customer_validation_wc_checkout_popup']) ? $_POST['mo_customer_validation_wc_checkout_popup'] : '');
	}
	
	//add on 12/05/2020
	public static function pharse_sms_body( $content, $order_status, $order, $order_note, $rma_id = '' ) {
	
		$order_id			= is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
		$order_variables	= get_post_custom($order_id); 
		$order_items 		= $order->get_items();
		
		if(strpos($content,'orderitem')!==false)
		{
			$content = self::sa_parse_orderItem_data($order_items,$content);
		}
		
		
		$item_name			= implode(", ",array_map(function($o){return $o['name'];},$order_items));
		$item_name_with_qty	= implode(", ",array_map(function($o){return sprintf("%s [%u]", $o['name'], $o['qty']);},$order_items));
		$store_name 		= get_bloginfo();
		$tracking_number 	= '';
		$tracking_provider 	= '';
		$tracking_link 		= '';
		$aftrShp_tracking_number 	= '';
		$aftrShp_tracking_provider_name 	= '';
		$delivery_dt_stamp 	= '';
		if(
			(strpos($content, '[tracking_number]') 		!== false) || 
			(strpos($content, '[tracking_provider]') 	!== false) || 
			(strpos($content, '[tracking_link]') 		!== false)
		)//fetch from database only if tracking plugin is installed
		{			
			if(is_plugin_active( 'woocommerce-shipment-tracking/woocommerce-shipment-tracking.php')) 
			{
				$tracking_info = get_post_meta( $order_id, '_wc_shipment_tracking_items', true );
				if(sizeof($tracking_info) > 0)
				{
					$t_info = array_shift($tracking_info);
					$tracking_number 	= $t_info['tracking_number'];
					$tracking_provider 	= ($t_info['tracking_provider'] != '') ? $t_info['tracking_provider'] : $t_info['custom_tracking_provider'];
					$tracking_link 		= $t_info['custom_tracking_link'];
				}
			}
			elseif( is_plugin_active( 'woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php' ))
			{
				$ast = new WC_Advanced_Shipment_Tracking_Actions;
				$tracking_items = $ast->get_tracking_items( $order_id,true );
				if (count($tracking_items)>0)
				{
					$t_info = array_shift($tracking_items);
					$tracking_number = $t_info['tracking_number'];
					$tracking_provider = $t_info['formatted_tracking_provider'];
					$tracking_link = $t_info['formatted_tracking_link'];
				}
			}
		}
		
		if(
			(strpos($content, '[aftership_tracking_number]') 		!== false) || 
			(strpos($content, '[aftership_tracking_provider_name]') 	!== false) 
		)//fetch from database only if tracking plugin is installed
		{	if(is_plugin_active( 'aftership-woocommerce-tracking/aftership.php')) 
			{		
				$aftrShp_tracking_number = get_post_meta( $order_id, '_aftership_tracking_number', true );
				$aftrShp_tracking_provider_name = get_post_meta( $order_id, '_aftership_tracking_provider_name', true );
			}
		}
		
		if((strpos($content, '[orddd_lite_timestamp]')!== false))
		{ 
			if(is_plugin_active('order-delivery-date-for-woocommerce/order_delivery_date.php'))
			{
				$delivery_dt_stamp = Orddd_Lite_Common::orddd_lite_get_order_delivery_date($order_id);
			}
		}
		
		$date_format = 'F j, Y';
		$date_tag = '[order_date]';
		
		if(preg_match_all('/\[order_date.*?\]/',$content,$matched))
		{
			$date_tag = $matched[0][0];
			$date_params = SmsAlertUtility::parseAttributesFromTag($date_tag);
			$date_format = array_key_exists('format', $date_params) ? $date_params['format'] : "F j, Y";
		}
		
		$find = array(
            '[order_id]',
			$date_tag,
            '[order_status]',
            '[rma_status]',
            '[first_name]',
            '[item_name]',
            '[item_name_qty]',
            '[order_amount]',
            '[note]',
            '[rma_number]',
            '[store_name]',
            '[tracking_number]',
            '[tracking_provider]',
            '[tracking_link]',
			'[aftership_tracking_number]',
            '[aftership_tracking_provider_name]',
            '[pdf_invoice_link]',
			'[orddd_lite_timestamp]',
        );
        $replace = array(
            $order->get_order_number(),
            $order->get_date_created()->date($date_format),
            $order_status,
            $order_status,
            '[billing_first_name]',
            wp_specialchars_decode($item_name),
			wp_specialchars_decode($item_name_with_qty),
			$order->get_total(),
			$order_note,
			$rma_id,
			$store_name,
			$tracking_number,
			$tracking_provider,
			$tracking_link,
			$aftrShp_tracking_number,
			$aftrShp_tracking_provider_name,
			admin_url( "admin-ajax.php?action=generate_wpo_wcpdf&document_type=invoice&order_ids=" . $order_id."&order_key=".$order->get_order_key() ),
			$delivery_dt_stamp
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
		
		$content = apply_filters('sa_wc_order_sms_before_send', $content, $order_id);//added on 05-05-2020
        return $content;
    }
	//add on 12/05/2020
	public	function send_custom_sms($data) 
	{
		$order 							= new WC_Order($_POST['order_id']);
		$sms_body 						= $_POST['sms_body'];
		$buyer_sms_data 				= array();
		$buyer_sms_data['number']   	= get_post_meta( $_POST['order_id'], '_billing_phone', true );
		$buyer_sms_data['sms_body'] 	= self::pharse_sms_body($sms_body, $order->get_status(), $order, '');
		$buyer_response 				= SmsAlertcURLOTP::sendsms( $buyer_sms_data );
		echo $buyer_response;
		exit();
	}
	//add on 12/05/2020
	function trigger_new_customer_note( $data ) {
		
		if(smsalert_get_option('buyer_notification_notes', 'smsalert_general')=="on")
		{
			$order_id					= $data['order_id'];
			$order						= new WC_Order( $order_id ); 
			$buyer_sms_body         	= smsalert_get_option( 'sms_body_new_note', 'smsalert_message', SmsAlertMessages::showMessage('DEFAULT_BUYER_NOTE') );
			$buyer_sms_data 			= array();
			$buyer_sms_data['number']   = get_post_meta( $data['order_id'], '_billing_phone', true );
			$buyer_sms_data['sms_body'] = self::pharse_sms_body( $buyer_sms_body, $order->get_status(), $order, $data['customer_note']);
			$buyer_response 			= SmsAlertcURLOTP::sendsms( $buyer_sms_data );
			$response					= json_decode($buyer_response,true);
			
			if( $response['status']	== 'success' ) {
				$order->add_order_note( __( 'Order note SMS Sent to buyer', 'smsalert' ) );
			} else {
				$order->add_order_note( __($response['description']['desc'], 'smsalert' ) );
			}
		}
	}
	//add on 12/05/2020
	function add_send_sms_meta_box(){
		add_meta_box(
			'wc_sms_alert_send_sms_meta_box',
			'SMS Alert (Custom SMS)',
			array($this, 'display_send_sms_meta_box'),
			'shop_order',
			'side',
			'default'
		);
	}
	//add on 12/05/2020
	function display_send_sms_meta_box($data){
		global $woocommerce, $post;
		$order = new WC_Order($post->ID);
		$order_id = $post->ID;
		
		$username 	= smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
		$password 	= smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
		$result 	= SmsAlertcURLOTP::get_templates($username, $password);
		$templates 	= json_decode($result, true);
		?>
		<select name="smsalert_templates" id="smsalert_templates" style="width:87%;" onchange="return selecttemplate(this, '#wc_sms_alert_sms_order_message');">
		<option value=""><?php  _e( 'Select Template', SmsAlertConstants::TEXT_DOMAIN ) ?></option>
		<?php
		if(array_key_exists('description', $templates) && (!array_key_exists('desc', $templates['description']))) {
		foreach($templates['description'] as $template) {
		?>
		<option value="<?php echo $template['Smstemplate']['template'] ?>"><?php echo $template['Smstemplate']['title'] ?></option>
		<?php } } ?>
		</select>
		<span class="woocommerce-help-tip" data-tip="You can add templates from your www.smsalert.co.in Dashboard"></span>
		<p><textarea type="text" name="wc_sms_alert_sms_order_message" id="wc_sms_alert_sms_order_message" class="input-text" style="width: 100%;" rows="4" value=""></textarea></p>
		<input type="hidden" class="wc_sms_alert_order_id" id="wc_sms_alert_order_id" value="<?php echo $order_id;?>" >
		<p><a class="button tips" id="wc_sms_alert_sms_order_send_message" data-tip="<?php __( 'Send an SMS to the billing phone number for this order.', SmsAlertConstants::TEXT_DOMAIN ) ?>"><?php _e( 'Send SMS', SmsAlertConstants::TEXT_DOMAIN ) ?></a>
		<span id="wc_sms_alert_sms_order_message_char_count" style="color: green; float: right; font-size: 16px;">0</span></p>
		<?php
	}
	
	/*
		sa_parse_orderItem_data
		attributes can be used : order_id,name,product_id,variation_id,quantity,tax_class,subtotal,subtotal_tax,total,total_tax
		properties : list="2" , format="%s,$d"
		[orderitem list='2' name product_id quantity subtotal]
	*/
	public static function sa_parse_orderItem_data($orderItems,$content)
	{
		
		$attr_delimiter = ' ';
		$pattern = get_shortcode_regex();
		preg_match_all('/\[orderitem(.*?)\]/', $content, $matches );
		$shortcode_tags = $matches[0];
		$parsed_codes=array();
		foreach($shortcode_tags as $tag)
		{
			$r_tag = preg_replace( "/\[|\]+/", '', $tag );
			$parsed_codes[$tag] = shortcode_parse_atts($r_tag);
		}
		$r_text = '';
		$replaced_arr=array();
		foreach($parsed_codes as $token => &$parsed_code)
		{
			$replace_text 	= '';
			
			$item_iterate 	= (!empty($parsed_code['list']) && $parsed_code['list']>0) ? $parsed_code['list'] : 0;
			$format		  	= (!empty($parsed_code['format'])) ? $parsed_code['format'] : '';
			$current_iterate = 1;
			
			foreach ( $orderItems as $item_id => $item ) 
			{
					if($item_iterate > 0  && $item_iterate<$current_iterate)
					{
						break;
					}
				
					$prop=array();
					foreach($parsed_code as $kcode => $code)
					{
						if(!in_array($kcode,array('list','format')))
						{
							$parts = array();
							if(strpos($code,".")!==FALSE)
							{
								$parts = explode(".",$code);
								$code  = array_shift($parts);
							}
							
							
							$attr_val = (!empty($item[$code])) ? $item[$code] : wc_get_order_item_meta($item_id,$code,true);
							
							if(!empty($parts))
							{
								$attr_val = self::getRecursiveVal($parts,$attr_val);
								$attr_val = is_array($attr_val) ? 'Array' : $attr_val;
							}
							
							if(!empty($format)){
								$prop[]=  $attr_val;
							}
							else
							{
								$replace_text .= $attr_val.$attr_delimiter;
							}
							
						}
					}
					
					if(!empty($format))
					{
						$replace_text .= vsprintf($format,$prop);
					}
					$replace_text = $replace_text.",";
					$current_iterate++;
			}
			$replaced_arr[$token] = substr($replace_text,0,-1);
		}
		
		return str_replace(array_keys($replaced_arr),array_values($replaced_arr),$content);
	}
	
	public static function getRecursiveVal($array , $attr)
	{
		foreach($array as $part)
		{
			if(is_array($part))
			{
				$attr = self::getRecursiveVal($part , $attr);
			}
			else
			{
				$attr = (!empty($attr[$part])) ? $attr[$part] : '';
			}
		}
		
		return $attr;
	}
	
	//add on 25/05/2020
	public static function trigger_after_order_place( $order_id, $old_status, $new_status ) {	
		
		$order = new WC_Order( $order_id );
		
        if( !$order_id ) {
            return;
        }
        $admin_sms_data 		= $buyer_sms_data = array();

        $order_status_settings  = smsalert_get_option( 'order_status', 'smsalert_general', array() );
        $admin_phone_number     = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
		$admin_phone_number 	= str_replace('postauthor','post_author',$admin_phone_number);
        if( count( $order_status_settings ) < 0 ) {
            return;
        }
		
        if( in_array( $new_status, $order_status_settings ) && $order->parent_id == 0 ) 
		{
			$default_buyer_sms 			=  defined('SmsAlertMessages::DEFAULT_BUYER_SMS_'.str_replace(" ","_",strtoupper($new_status))) ? constant('SmsAlertMessages::DEFAULT_BUYER_SMS_'.str_replace(" ","_",strtoupper($new_status))) : SmsAlertMessages::showMessage('DEFAULT_BUYER_SMS_STATUS_CHANGED');
			
			$buyer_sms_body 			= smsalert_get_option( 'sms_body_'.$new_status, 'smsalert_message', $default_buyer_sms);
			$buyer_sms_data['number'] 	= get_post_meta( $order_id, '_billing_phone', true );
			$buyer_sms_data['sms_body'] = self::pharse_sms_body( $buyer_sms_body, $new_status, $order, '');

			$buyer_response 			= SmsAlertcURLOTP::sendsms( $buyer_sms_data );			
			
			$response					= json_decode($buyer_response, true);
			
			if( $response['status']=='success' ) {
				$order->add_order_note( __('SMS Send to buyer Successfully.', 'smsalert' ) );
			} else {
				if(isset($response['description']) && is_array($response['description']) && array_key_exists('desc', $response['description']))
				{
					$order->add_order_note( __($response['description']['desc'], 'smsalert' ) );
				}
				else
				{
					$order->add_order_note( __($response['description'], 'smsalert' ) );
				}
			}
		}
		
		if(smsalert_get_option( 'admin_notification_'.$new_status, 'smsalert_general', 'on' ) == 'on' && $admin_phone_number!='')
		{	
			//send sms to post author
			$has_sub_order 			= metadata_exists('post',$order_id,'has_sub_order');
			if(
				(strpos($admin_phone_number,'post_author') !== false) && 
				($order->parent_id != 0 || ($order->parent_id == 0 && $has_sub_order == '')))
			{
				$order_items 		= $order->get_items();
				$first_item 		= current($order_items);		
				$prod_id 			= $first_item['product_id'];
				$product 			= wc_get_product( $prod_id );
				$author_no 			= get_the_author_meta('billing_phone', get_post($prod_id)->post_author);
				
				if($order->parent_id == 0) {
					$admin_phone_number = str_replace('post_author', $author_no, $admin_phone_number);
				}
				else {
					$admin_phone_number = $author_no;
				}
			}			
			
			$default_template 			= SmsAlertMessages::showMessage('DEFAULT_ADMIN_SMS_'.str_replace('-', '_', strtoupper($new_status)));		
			
			$default_admin_sms 			= (($default_template!='') ? $default_template : sprintf(__('%s status of order %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[store_name]:', '#[order_id]', '[order_status]'));
			
			$admin_sms_body  			= smsalert_get_option( 'admin_sms_body_'.$new_status, 'smsalert_message', $default_admin_sms );	
			$admin_sms_data['number']   = $admin_phone_number;
			$admin_sms_data['sms_body'] = self::pharse_sms_body( $admin_sms_body, $new_status, $order, '');
			$admin_response             = SmsAlertcURLOTP::sendsms( $admin_sms_data );
			$response=json_decode($admin_response,true);
			if( $response['status']=='success' ) {
				$order->add_order_note( __( 'SMS Sent Successfully.', 'smsalert' ) );
			} else {
				if(is_array($response['description']) && array_key_exists('desc', $response['description']))
				{
					$order->add_order_note( __($response['description']['desc'], 'smsalert' ) );
				}
				else {
					$order->add_order_note( __($response['description'], 'smsalert' ) );
				}
			}
		}
    }

	public static function getvariables()
	{
		$variables = array(
			'[order_id]' 				=> 'Order Id',
			'[order_status]' 			=> 'Order Status',
			'[order_amount]' 			=> 'Order Amount',
			'[order_date]' 				=> 'Order Date',
			'[store_name]' 				=> 'Store Name',
			'[item_name]' 				=> 'Product Name',
			'[item_name_qty]' 			=> 'Product Name with Quantity',
			'[billing_first_name]' 		=> 'Billing First Name',
			'[billing_last_name]' 		=> 'Billing Last Name',
			'[billing_company]' 		=> 'Billing Company',
			'[billing_address_1]' 		=> 'Billing Address 1',
			'[billing_address_2]' 		=> 'Billing Address 2',
			'[billing_city]' 			=> 'Billing City',
			'[billing_state]' 			=> 'Billing State',
			'[billing_postcode]' 		=> 'Billing Postcode',
			'[billing_country]' 		=> 'Billing Country',
			'[billing_email]' 			=> 'Billing Email',
			'[billing_phone]' 			=> 'Billing Phone',

			'[shipping_first_name]'		=> 'Shipping First Name',
			'[shipping_last_name]' 		=> 'Shipping Last Name',
			'[shipping_company]' 		=> 'Shipping Company',
			'[shipping_address_1]' 		=> 'Shipping Address 1',
			'[shipping_address_2]' 		=> 'Shipping Address 2',
			'[shipping_city]' 			=> 'Shipping City',
			'[shipping_state]' 			=> 'Shipping State',
			'[shipping_postcode]' 		=> 'Shipping Postcode',
			'[shipping_country]' 		=> 'Shipping Country',

			'[order_currency]' 			=> 'Order Currency',
			'[payment_method]' 			=> 'Payment Method',
			'[payment_method_title]' 	=> 'Payment Method Title',
			'[shipping_method]' 		=> 'Shipping Method',
		);

		if ( is_plugin_active( 'woocommerce-shipment-tracking/woocommerce-shipment-tracking.php' ) ||
			is_plugin_active( 'woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php')
		)
		{
			$wc_shipment_variables = array(
				'[tracking_number]' 		=> 'tracking number',
				'[tracking_provider]' 		=> 'tracking provider',
				'[tracking_link]' 			=> 'tracking link',
			);
			$variables = array_merge($variables, $wc_shipment_variables);
		}

		if ( is_plugin_active( 'aftership-woocommerce-tracking/aftership.php' ) )
		{
			$wc_shipment_variables = array(
				'[aftership_tracking_number]' 		=> 'afshp tracking number',
				'[aftership_tracking_provider_name]' 		=> 'afshp tracking provider',
				//'[tracking_link]' 			=> 'tracking link',
			);
			$variables = array_merge($variables, $wc_shipment_variables);
		}

		if ( is_plugin_active( 'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php' ) )
		{
			$wc_pdf_invoice = array(
				'[pdf_invoice_link]' 		=> 'pdf invoice link',
			);
			$variables = array_merge($variables, $wc_pdf_invoice);
		}

		if ( is_plugin_active( 'claim-gst/claim-gst.php' ) )
		{
			$variables = array_merge($variables,  array(
			'[gstin]' => 'GST Number',
			'[gstin_holder_name]' => 'GST Holder Name',
			'[gstin_holder_address]' => 'GST Holder Address',
			));
		}

		if ( is_plugin_active( 'order-delivery-date-for-woocommerce/order_delivery_date.php' ) )
		{
			$variables = array_merge($variables,  array(
				'[orddd_lite_timestamp]' => 'Delivery Date',
			));
		}
		
		$variables = apply_filters('sa_wc_variables',$variables);//added on 05-05-2020

		$ret_string = '';
		foreach($variables as $vk => $vv)
		{
			$ret_string .= sprintf( "<a href='#' val='%s'>%s</a> | " , $vk , __($vv,SmsAlertConstants::TEXT_DOMAIN) );
		}
		return $ret_string;
	}	
	
}
new WooCommerceCheckOutForm;
?>
<?php    
class sa_all_order_variable
{
	public function __construct() {
		add_action( 'woocommerce_after_register_post_type', array($this, 'routeData'), 10, 1 );	
	}

	public function routeData()
	{
		if(!empty($_REQUEST['option']) && $_REQUEST['option']=='fetch-order-variable' && !empty($_REQUEST['order_id']))
		{			
			$order_id = $_REQUEST['order_id'];
			
			$tokens=array();
			
			global $woocommerce, $post;
			
			$order = new WC_Order($order_id);
			
			//Order Detail
			$order_variables	= get_post_custom($order_id);
			
			$variables=array();
			foreach ($order_variables as $meta_key => &$value) {
				$temp = maybe_unserialize($value[0]);
				
				if(is_array($temp))
				{
					$variables[$meta_key] = $temp;
				}
				else 
				{
					$variables[$meta_key] = $value[0];
				}
			}
			$variables['order_status'] 	= $order->get_status();
			$variables['order_date'] 	= $order->get_date_created();;
			
			$tokens['Order details'] = $variables;
			
			//OrderItem & OrderItemMeta
			$sno=1;
			foreach ($order->get_items() as $item_key => $item ){
				$item_sno = "Item ".$sno;
				$item_data  = $item->get_data();
				foreach ($item_data as $i_key => $i_val ){
					
				
					if($i_key=='meta_data'){
						$item_meta_data = $item->get_meta_data();
						foreach ($item_meta_data as $mkey => $meta ){
							
							$meta_value = $meta->get_data();
							
							$temp = unserialize($meta_value['value']);
							if(is_array($temp))
							{
								$item_variables[$item_sno]['Order ItemMeta']["orderitem ".$meta_value['key']] = $temp;
							}
							else
							{
								$item_variables[$item_sno]['Order ItemMeta']["orderitem ".$meta_value['key']] =$meta_value['value'];
							}
						}
					}
					else
					{
						$item_variables[$item_sno]["orderitem ".$i_key] = $i_val;
					}
				}
				
				$sno++;
			}			
			$tokens['Order details']['Order Items'] = $item_variables;
			
			wp_send_json($tokens);
			exit();
		}
	}
}
new sa_all_order_variable;
?>
<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class All_Order_List extends WP_List_Table {

	function __construct()
	{
		 parent::__construct(array(
					'singular' => 'allordervaribale',
					'plural' => 'allordervariables',
		 ));
	}

	/*get all subscriber info*/	
	public static function get_all_order() {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}posts  WHERE post_type = 'shop_order' && post_status != 'auto-draft' ORDER BY post_date desc LIMIT 5";

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );		

		return $result;
	}

	public function no_items() {
	  _e( 'No Order.', 'smsalert' );
	}

	function column_default($item, $column_name)
	{
		return $item[$column_name];
	}
	
	function column_cb($item)
	{
		return sprintf(
			'<input type="checkbox" name="ID[]" value="%s" />',
			$item['ID']
		);
	}
	
	function column_post_status($item)
	{
		$post_status = sprintf('<button class="button-primary"/>%s</a>',trim($item['post_status'],'wc-'));
		return $post_status;
	}
	
	function column_post_date($item)
	{
		$date 	= date("d-m-Y", strtotime($item['post_date']));;
		return $date;
	}
	
	function get_columns() {
	  $columns = [
		'ID' => __( 'Order'),
		'post_date' => __( 'Date'),
		'post_status'    => __( 'Status'),
	  ];

	  return $columns;
	}
	
	public function prepare_items() {
		
		$columns = $this->get_columns();
		$this->items = self::get_all_order();
	
		// here we configure table headers, defined in our methods
		$this->_column_headers = array($columns);
	  
		return $this->items;
	}	
}

function all_order_variable_admin_menu()
{	
	add_submenu_page( null, 'All Order Variable','All Order Variable', 'manage_options', 'all-order-variable', 'all_order_variable_page_handler');
}

add_action('admin_menu', 'all_order_variable_admin_menu');

function all_order_variable_page_handler()
{
	global $wpdb;

    $table_data = new All_Order_List();
	$data 		= $table_data->prepare_items();
?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2 class="title">Order List</h2>
	<form id="order-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table_data->display() ?>
    </form>
	<div id="sa_order_variable" class="sa_variables" style="display:none">
		<h3 class="h3-background">Select your variable <span id="order_id" class="alignright"><?php echo $order_id; ?></span></h3>
		<ul id="order_list"></ul>		
	</div>
</div>
<script>
jQuery(document).ready(function(){
    jQuery("tbody tr").addClass("order_click");
	
	jQuery(".order_click").click(function(){
		var id = jQuery(this).find(".ID").text().replace(/\D/g,'');		
		jQuery("#order-table, .title").hide();
		jQuery("#sa_order_variable").show();		
		jQuery("#order_id").html('Order Id: '+id);
		
		if(id != ''){
			jQuery.ajax({
				url         : "<?php echo admin_url();?>?option=fetch-order-variable",
				data        : {order_id:id},	
				dataType	: 'json',
				success: function(data)
				{
					var arr1	= data;				
					var content1 = parseVariables(arr1);
					
					jQuery('ul#order_list').html(content1);
					
					jQuery("ul").prev("a").addClass("nested");
					
					jQuery('ul#order_list, ul#order_item_list').css('textTransform', 'capitalize');
					
					jQuery(".nested").parent("li").css({"list-style":"none"});
					
					jQuery("ul#order_list li ul:first").show();
					jQuery("ul#order_list").show();
					jQuery("ul#order_list li a:first").addClass('nested-close');
					
					toggleSubMenu();
					addToken();
				},
				error:function (e,o){
					//console.log('error'+o);
				}
			});
		}
		
	});
		
	function parseVariables(data,text='')
	{
		jQuery.each(data,function(i,item){
			if(typeof item == 'object')
			{
				var nested_key = i.replace(/_/g," ").replace(/orderitem/g,"");
				var key = i.replace(/_/i,"");
				
				if(i != ''){
					text+='<li><a href="#" value="['+key+']">'+nested_key+'<ul style="display:none">';					
					text += parseVariables(item);
					text+="</li></ul>";
				}
			}
			else
			{
				var j = i;
				var key 	= i.replace(/_/g," ").replace(/orderitem/g,"");
				var title 	= item;
				var value 	= j.replace(/_/i,"");

				text+='<li><a href="#" value="['+value+']" title="'+title+'">'+key+'</a></li>';
			}
	   });
	   return text;
	}
	
	function toggleSubMenu(){
		jQuery("a.nested").click(function(){
			jQuery(this).parent('li').find('ul:first').toggle();
			if(jQuery(this).hasClass("nested-close")){
				jQuery(this).removeClass("nested-close");
			}else{
				jQuery(this).addClass("nested-close");
			}
			return false;
		});	
	}
	
	function addToken(){		
		jQuery('.sa_variables a').click( function() {
			if(jQuery(this).hasClass("nested")){
				return false;
			}			
			var token = jQuery(this).attr('value');
			window.parent.postMessage(token, '*');
		});
	}
	return false;	
});
</script>
<?php } ?>