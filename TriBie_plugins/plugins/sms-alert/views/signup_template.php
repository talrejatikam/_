<?php $wc_user_roles = WooCommerceRegistrationForm::get_user_roles();
?>
<!-- accordion -->	
<div class="cvt-accordion">
	<div class="accordion-section">
	<?php 
	 foreach($wc_user_roles as $role_key => $role)
	 {
		  $current_val = (is_array($wc_user_roles) && array_key_exists($role_key, $wc_user_roles)) ? $wc_user_roles[$role_key] : $role_key;
		 ?>		
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_cust_<?php echo $role_key; ?>"><input type="checkbox" name="smsalert_signup_general[wc_user_roles_<?php echo $role_key; ?>]" id="smsalert_signup_general[wc_user_roles_<?php echo $role_key; ?>]" class="notify_box" 
		<?php echo ((smsalert_get_option( 'wc_user_roles_'.$role_key, 'smsalert_signup_general', 'on')=='on')?"checked='checked'":''); ?>/><label><?php _e( 'when '.ucwords(str_replace('-', ' ', $role['name'] ).' is registered'), SmsAlertConstants::TEXT_DOMAIN ) ?></label>
		<span class="expand_btn"></span>
		</a>		 
		<div id="accordion_cust_<?php echo $role_key; ?>" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
				<td><div class="smsalert_tokens"><a href="#" val="[username]"><?php _e( 'Username', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[store_name]"><?php _e( 'Store Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[email]"><?php _e( 'Email', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[billing_phone]"><?php _e( 'Billing Phone', SmsAlertConstants::TEXT_DOMAIN ) ?></a></div>
				<textarea name="smsalert_signup_message[signup_sms_body_<?php echo $role_key; ?>]" id="smsalert_signup_message[signup_sms_body_<?php
				echo $role_key; ?>]" <?php echo(($current_val==$role_key)?'' : "readonly='readonly'"); ?> data-parent_id="smsalert_signup_general[wc_user_roles_<?php echo $role_key; ?>]"><?php echo smsalert_get_option('signup_sms_body_'.$role_key, 'smsalert_signup_message', sprintf(__('Hello %s, Thank you for registering with %s.',SmsAlertConstants::TEXT_DOMAIN), '[username]', '[store_name]')); ?></textarea>
				</td>
				</tr>
			</table>
		</div>
		 <?php
	 }
	 ?>	
	</div>
</div>
<!--end accordion-->	