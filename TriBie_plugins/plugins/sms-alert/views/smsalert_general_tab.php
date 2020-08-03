<div class="smsalert_wrapper cvt-accordion" style="padding: 5px 10px 10px 10px;">
	<strong><?php _e( $smsalert_helper, SmsAlertConstants::TEXT_DOMAIN ); ?></strong>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('SMS Alert Username',SmsAlertConstants::TEXT_DOMAIN); ?>
				<span class="tooltip" data-title="Enter SMSAlert Username"><span class="dashicons dashicons-info"></span></span>
			</th>
			<td style="vertical-align: top;">
				<?php if($islogged){echo $smsalert_name;}?>
				<input type="text" name="smsalert_gateway[smsalert_name]" id="smsalert_gateway[smsalert_name]" value="<?php echo $smsalert_name; ?>" data-id="smsalert_name" class="<?php echo $hidden?>">
				<input type="hidden" name="action" value="save_sms_alert_settings" />
				<span class="<?php echo $hidden?>"><?php _e( 'your SMS Alert user name', SmsAlertConstants::TEXT_DOMAIN ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'SMS Alert Password', SmsAlertConstants::TEXT_DOMAIN ) ?>
				<span class="tooltip" data-title="Enter SMSAlert Password"><span class="dashicons dashicons-info"></span></span>
			</th>
			<td>
				<?php if($islogged){echo '*****';}?>
				<input type="text" name="smsalert_gateway[smsalert_password]" id="smsalert_gateway[smsalert_password]" value="<?php echo $smsalert_password; ?>" data-id="smsalert_password" class="<?php echo $hidden?>">
				<span class="<?php echo $hidden?>"><?php _e( 'your SMS Alert password', SmsAlertConstants::TEXT_DOMAIN ); ?></span>
			</td>
		</tr>
		<?php do_action('verify_senderid_button')?>
		<tr valign="top">
			<th scope="row">
				<?php _e( 'SMS Alert Sender Id', SmsAlertConstants::TEXT_DOMAIN ) ?>
				<span class="tooltip" data-title="Only available for transactional route"><span class="dashicons dashicons-info"></span></span>
			</th>
			<td>
				<?php if($islogged){?>
					<?php echo $smsalert_api;?>
					<input type="hidden" value="<?php echo $smsalert_api;?>" name="smsalert_gateway[smsalert_api]" id="smsalert_gateway[smsalert_api]">
				<?php }else{?>
				<select name="smsalert_gateway[smsalert_api]" id="smsalert_gateway[smsalert_api]" disabled>
					<option value="SELECT"><?php _e( 'SELECT', SmsAlertConstants::TEXT_DOMAIN ); ?></option>
				</select>
				<span class="<?php echo $hidden?>"><?php _e( 'display name for SMS\'s to be sent', SmsAlertConstants::TEXT_DOMAIN ); ?></span>
				<?php } ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
			</th>
			<td>
				<?php if($islogged){?>
				<a href="#" class="button-primary" onclick="logout(); return false;"><?php echo _e( 'Logout', SmsAlertConstants::TEXT_DOMAIN );?></a>
				<?php }?>
			</td>
		</tr>
	</table>
</div>
<br>
<?php if($islogged){  ?>
<?php if($hasWoocommerce || $hasWPAM || $hasEMBookings){?>
<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;">
	<table class="form-table">
		<?php if($hasWoocommerce || $hasWPAM || $hasEMBookings){?>
		<tr valign="top">
			<th scope="row"><?php _e( 'Send Admin SMS To', SmsAlertConstants::TEXT_DOMAIN ) ?>
				<span class="tooltip" data-title="Please make sure that the number must be without country code (e.g.: 8010551055)"><span class="dashicons dashicons-info"></span></span>
			</th>
			<td>
				<select id="send_admin_sms_to" onchange="toggle_send_admin_alert(this);">
					<option value=""><?php _e( 'Custom', SmsAlertConstants::TEXT_DOMAIN ) ?></option>
					<option value="post_author" <?php echo (trim($sms_admin_phone) == 'post_author') ? 'selected="selected"' : ''; ?>><?php _e( 'Post Author', SmsAlertConstants::TEXT_DOMAIN ) ?></option>
				</select>
				<script>
				function toggle_send_admin_alert(obj)
				{
					if(obj.value == "post_author")
					{
						tagInput1.addTag(obj.value);
					}
				}
				</script>
				<input type="text" name="smsalert_message[sms_admin_phone]" class="admin_no" id="smsalert_message[sms_admin_phone]" <?php echo (trim($sms_admin_phone) == 'post_author') ? 'readonly="readonly"' : ''; ?> value="<?php echo $sms_admin_phone; ?>"><br /><br />
				<span><?php _e( 'Admin order sms notifications will be send in this number.', SmsAlertConstants::TEXT_DOMAIN ); ?></span>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>
<?php } } ?>