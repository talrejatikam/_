<?php if ($hasWoocommerce || $hasWPmembers || $hasUltimate || $hasWPAM || $hasLearnPress) {?>
<div class="cvt-accordion">
	<div class="accordion-section">
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_6"> <input type="checkbox" name="smsalert_general[buyer_checkout_otp]" id="smsalert_general[buyer_checkout_otp]" class="notify_box" <?php echo (($smsalert_notification_checkout_otp=='on')?"checked='checked'":'')?>/><?php _e( 'OTP for Checkout', SmsAlertConstants::TEXT_DOMAIN ) ?><span class="expand_btn"></span>
		</a>

		<div id="accordion_6" class="cvt-accordion-body-content">
			<table class="form-table">
				<?php if($hasWoocommerce){ ?>
				<tr valign="top">
					<th scope="row">
						<input type="checkbox" name="smsalert_general[otp_for_selected_gateways]" id="smsalert_general[otp_for_selected_gateways]" class=" notify_box" data-parent_id="smsalert_general[buyer_checkout_otp]"  <?php echo (($otp_for_selected_gateways=='on')?"checked='checked'":'')?> /><?php  _e( 'Enable OTP only for Selected Payment Options', SmsAlertConstants::TEXT_DOMAIN ) ?>
						<?php ?>
						<span class="tooltip" data-title="Please select payment gateway for which you wish to enable OTP Verification"><span class="dashicons dashicons-info"></span></span><br /><br />
					</th>
					<td>
					<?php
					if($hasWoocommerce){
						echo $show_payment_gateways;						
					} ?>
					</td>
				</tr>
				<?php } ?>
				<tr valign="top" class="top-border">
					<?php if ($hasWoocommerce) {?>
					<th scope="row">
						<input type="checkbox" name="smsalert_general[checkout_otp_popup]" id="smsalert_general[checkout_otp_popup]" class="notify_box" data-parent_id="smsalert_general[buyer_checkout_otp]" <?php echo (($checkout_otp_popup=='on')?"checked='checked'":'')?>/><?php _e( 'Verify OTP in Popup', SmsAlertConstants::TEXT_DOMAIN ) ?>
						<span class="tooltip" data-title="Verify OTP in Popup"><span class="dashicons dashicons-info"></span></span>
					</th>
					<th scope="row">
						<input type="checkbox" name="smsalert_general[checkout_show_otp_button]" id="smsalert_general[checkout_show_otp_button]" class="notify_box" data-parent_id="smsalert_general[buyer_checkout_otp]" <?php echo (($checkout_show_otp_button=='on')?"checked='checked'":'')?>/><?php _e( 'Show Verify Button at Checkout', SmsAlertConstants::TEXT_DOMAIN ) ?>
						<span class="tooltip" data-title="Show verify button in-place of link at checkout"><span class="dashicons dashicons-info"></span></span>
					</th>
					<?php } ?>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php if ($hasWoocommerce) {?>
						<input type="checkbox" name="smsalert_general[checkout_show_otp_guest_only]" id="smsalert_general[checkout_show_otp_guest_only]" class="notify_box" data-parent_id="smsalert_general[buyer_checkout_otp]" <?php echo (($checkout_show_otp_guest_only=='on')?"checked='checked'":'')?>/><?php _e( 'Verify only Guest Checkout', SmsAlertConstants::TEXT_DOMAIN ) ?>
						<span class="tooltip" data-title="OTP verification only for guest checkout"><span class="dashicons dashicons-info"></span></span>
						<?php } ?>
					</th>
					<th scope="row">
						<?php if($hasWoocommerce || $hasUltimate || $hasWPAM) { ?>
						<!--Validate Before Sending OTP-->
						<input type="checkbox" name="smsalert_general[validate_before_send_otp]" id="smsalert_general[validate_before_send_otp]" class="notify_box" data-parent_id="smsalert_general[buyer_checkout_otp]" <?php echo (($validate_before_send_otp=='on')?"checked='checked'":'')?>/><?php _e( 'Validate Form Before Sending OTP At Checkout', SmsAlertConstants::TEXT_DOMAIN ) ?>
						<span class="tooltip" data-title="Validate Before Sending OTP"><span class="dashicons dashicons-info"></span></span>
						<!--/-Validate Before Sending OTP-->
						<?php } ?>
					</th>
				</tr>
			</table>
		</div>


		<?php if ($hasWoocommerce || $hasWPmembers || $hasUltimate || $hasWPAM || $hasLearnPress) {?>
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_7"> <input type="checkbox" name="smsalert_general[buyer_signup_otp]" id="smsalert_general[buyer_signup_otp]" class="notify_box" <?php echo (($smsalert_notification_signup_otp=='on')?"checked='checked'":'')?> > <label><?php _e( 'OTP for Registration', SmsAlertConstants::TEXT_DOMAIN ) ?></label>
		<span class="expand_btn"></span>
		</a>
		<div id="accordion_7" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<?php if ($hasWoocommerce) { ?>
						<input type="checkbox" name="smsalert_general[register_otp_popup_enabled]" id="smsalert_general[register_otp_popup_enabled]" class="notify_box" data-parent_id="smsalert_general[buyer_signup_otp]" <?php echo (($register_otp_popup_enabled=='on')?"checked='checked'":'')?>/><?php _e( 'Register OTP in Popup', SmsAlertConstants::TEXT_DOMAIN ) ?>
						<span class="tooltip" data-title="Register OTP in Popup"><span class="dashicons dashicons-info"></span></span>
						<?php } ?>

						<?php if(is_plugin_active('woocommerce/woocommerce.php')){ ?>
						<th scope="row">
							<input type="checkbox" name="smsalert_general[allow_multiple_user]" id="smsalert_general[allow_multiple_user]" class="notify_box" data-parent_id="smsalert_general[buyer_signup_otp]" <?php echo (($smsalert_allow_multiple_user=='on')?"checked='checked'":'')?>/><?php _e( 'Allow multiple accounts with same mobile number', SmsAlertConstants::TEXT_DOMAIN ) ?>
							<span class="tooltip" data-title="OTP at registration should be active"><span class="dashicons dashicons-info"></span></span>
						</th>
						<?php } ?>
					</th>
				</tr>
			</table>
		</div>
		<?php }?>

		<?php if ($hasWoocommerce || $hasWPAM) {?>
		<a class="cvt-accordion-body-title " href="javascript:void(0)" data-href="#accordion_8"> <input type="checkbox" name="smsalert_general[buyer_login_otp]" id="smsalert_general[buyer_login_otp]" class="notify_box" <?php echo (($smsalert_notification_login_otp=='on')?"checked='checked'":'')?>> <label><?php _e( 'OTP for Login', SmsAlertConstants::TEXT_DOMAIN ) ?></label>
		<span class="expand_btn"></span>
		</a>
		<div id="accordion_8" class="cvt-accordion-body-content">
			<table class="form-table">
				<?php if($hasWoocommerce){ ?>
				<tr valign="top">
					<th scope="row" class="login-width">
						<input type="checkbox" name="smsalert_general[otp_for_roles]" id="smsalert_general[otp_for_roles]" class=" notify_box" data-parent_id="smsalert_general[buyer_login_otp]"  <?php echo (($otp_for_roles=='on')?"checked='checked'":'')?> />
						<?php _e( 'Exclude Role from LOGIN OTP', SmsAlertConstants::TEXT_DOMAIN ) ?>
						<span class="tooltip" data-title="Exclude Role from LOGIN OTP"><span class="dashicons dashicons-info"></span></span><br /><br />
					</th>
					<td>
					<?php echo $show_wc_roles_dropdown;?>
					</td>
				</tr>
				<?php } ?>
				<tr valign="top">
					<th scope="row">
						<!--Login with OTP-->
						<?php if ($hasWoocommerce || $hasWPAM) {?>
							<input type="checkbox" name="smsalert_general[login_with_otp]" id="smsalert_general[login_with_otp]" class="notify_box" data-parent_id="smsalert_general[buyer_login_otp]" <?php echo (($login_with_otp=='on')?"checked='checked'":'')?>/><?php _e( 'Login With OTP', SmsAlertConstants::TEXT_DOMAIN ) ?>
							<span class="tooltip" data-title="Login using Mobile Number"><span class="dashicons dashicons-info"></span></span>
						<?php } ?>
						<!--/-Login with OTP-->
					</th>
					<th scope="row">
						<!--Login with popup-->
						<?php if ($hasWoocommerce || $hasWPAM) {?>
							<input type="checkbox" name="smsalert_general[login_popup]" id="smsalert_general[login_popup]" class="notify_box" data-parent_id="smsalert_general[buyer_login_otp]" <?php echo (($login_popup=='on')?"checked='checked'":'')?>/><?php _e( 'Login OTP in Popup', SmsAlertConstants::TEXT_DOMAIN ) ?>
							<span class="tooltip" data-title="Login via Username & Pwd, OTP will be asked in Popup Modal"><span class="dashicons dashicons-info"></span></span>
						<?php } ?>
						<!--/-Login with popup-->
					</th>
				</tr>
			</table>
		</div>
		<?php }?>
	</div>
</div>
<br>
<?php } ?>
<!--end accordion-->

<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;">
	<table class="form-table">
		<?php if ($hasWoocommerce || $hasWPAM) {?>
		<tr valign="top">
			<th scope="row">
			<!--OTP FOR Reset Password-->
				<input type="checkbox" name="smsalert_general[reset_password]" id="smsalert_general[reset_password]" class="notify_box" <?php echo (($enable_reset_password=='on')?"checked='checked'":'')?>/><?php _e( 'OTP For Reset Password', SmsAlertConstants::TEXT_DOMAIN ) ?>
			
			<!--/-OTP FOR Reset Password-->
			</th>
		</tr>
		<?php }?>
		<tr valign="top" class="top-border">
			<th scope="row"><?php _e( 'OTP Template Style', SmsAlertConstants::TEXT_DOMAIN ) ?>
			</th>
			<td>
				<?php 
				$otp_template_style	= smsalert_get_option( 'otp_template_style', 'smsalert_general', 'otp-popup-1.php');
				?>
				<select name="smsalert_general[otp_template_style]" id="smsalert_general[otp_template_style]">
					<option value="otp-popup-1.php" <?php echo ($otp_template_style=="otp-popup-1.php") ? 'selected="selected"':''; ?>><?php _e( 'Template 1', SmsAlertConstants::TEXT_DOMAIN ) ?></option>
					<option value="otp-popup-2.php" <?php echo ($otp_template_style=="otp-popup-2.php") ? 'selected="selected"':''; ?>><?php _e( 'Template 2', SmsAlertConstants::TEXT_DOMAIN ) ?></option>
				</select>
				<span class="tooltip" data-title="Select OTP Template Style"><span class="dashicons dashicons-info"></span></span>
			</td>
		</tr>
		<tr valign="top" class="top-border">
			<th scope="row"><?php _e( 'OTP Re-send Timer', SmsAlertConstants::TEXT_DOMAIN ) ?> </th>
			<td>
				<input type="number" name="smsalert_general[otp_resend_timer]" id="smsalert_general[otp_resend_timer]" class="notify_box" value="<?php echo $otp_resend_timer;?>" min="15" max="300"/> <?php _e( 'Seconds', SmsAlertConstants::TEXT_DOMAIN ) ?>
				<span class="tooltip" data-title="Set OTP Re-send Timer"><span class="dashicons dashicons-info"></span></span>
			</td>

			<th scope="row"><?php _e( 'Max OTP Re-send Allowed', SmsAlertConstants::TEXT_DOMAIN ) ?></th>
			<td>
				<input type="number" name="smsalert_general[max_otp_resend_allowed]" id="smsalert_general[max_otp_resend_allowed]" class="notify_box" value="<?php echo $max_otp_resend_allowed;?>" min="1" max="10"/> <?php _e( 'Times', SmsAlertConstants::TEXT_DOMAIN ) ?>
				<span class="tooltip" data-title="Set MAX OTP Re-send Allowed"><span class="dashicons dashicons-info"></span></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'OTP Verify Button Text', SmsAlertConstants::TEXT_DOMAIN ) ?> </th>
			<td>
				<input type="text" name="smsalert_general[otp_verify_btn_text]" id="smsalert_general[otp_verify_btn_text]" class="notify_box" value="<?php echo $otp_verify_btn_text;?>" style="width:90%" required/>
				<span class="tooltip" data-title="Set OTP Verify Button Text"><span class="dashicons dashicons-info"></span></span>
			</td>
		</tr>
		<tr valign="top" class="top-border">
			<th scope="row"><?php _e( 'OTP Template', SmsAlertConstants::TEXT_DOMAIN ) ?></th>
			<td colspan="3" style="margin-top:20px">
			<div class="smsalert_tokens"><a href="#" val="[otp]" style="margin-top:20px">OTP</a> </div>
			<textarea name="smsalert_message[sms_otp_send]" id="smsalert_message[sms_otp_send]"><?php echo $sms_otp_send; ?></textarea>
			<span><?php _e( 'Template to be used for sending OTP', SmsAlertConstants::TEXT_DOMAIN ) ?><hr />
				<?php echo sprintf( __( 'It is mandatory to include %s tag in template content.', SmsAlertConstants::TEXT_DOMAIN ), '[otp]' ); ?>
				<br /><br /><b><?php _e( 'Optional Attributes', SmsAlertConstants::TEXT_DOMAIN ) ?></b><br />
			<ul>
				<li><b>length</b> &nbsp; - <?php _e( 'length of OTP, default is 4,', SmsAlertConstants::TEXT_DOMAIN ) ?></li>
				<li><b>retry</b> &nbsp;&nbsp;&nbsp;&nbsp; - <?php _e( 'set how many times otp message can be sent in specific time default is 5,', SmsAlertConstants::TEXT_DOMAIN ) ?></li>
				<li><b>validity</b> &nbsp;- <?php _e( 'set validity of the OTP default is 15 minutes', SmsAlertConstants::TEXT_DOMAIN ) ?></li>
			</ul>
				<b>eg</b> : <code>[otp length="6" retry="2" validity="10"]</code></span>
			</td>
		</tr>
	</table>
</div>