<?php
// $options = get_option( 'cartbounty_notification_frequency' );
// if($options['hours'] == 0) {
?>
<!--<div class="cvt-accordion" style="padding: 0px 10px 10px 10px;">
	<table class="form-table">
		<tbody>
		<tr valign="top">
			<td>
				<p><span class="dashicons dashicons-info"></span> <b>Please enable Email Notification at Cart Bounty Setting page.</b> <a href="<?php echo admin_url().'admin.php?page=cartbounty&tab=settings'?>">Click Here</a></p>
			</td>
		</tr>
	</tbody></table>
</div>-->
<?php
// }
// else
// {
?>
<div class="cvt-accordion">
	<div class="accordion-section">
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_cust1">
			<input type="checkbox" name="smsalert_ac_general[customer_notify]" id="smsalert_ac_general[customer_notify]" class="notify_box" <?php echo (($smsalert_ac_customer_notify=='on')?"checked='checked'":''); ?> /><label><?php _e( 'Send msg to customer when product is left in cart', SmsAlertConstants::TEXT_DOMAIN ) ?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="accordion_cust1" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td>
						<div class="smsalert_tokens"><?php echo $getAbandonCartvariables; ?></div>
						<textarea name="smsalert_ac_message[customer_notify]" id="smsalert_ac_message[customer_notify]" data-parent_id="smsalert_ac_general[customer_notify]" <?php echo(($smsalert_ac_customer_notify=='on')?'' : "readonly='readonly'"); ?>><?php echo $smsalert_ac_customer_message;?></textarea>
					</td>
				</tr>
			</table>
		</div>
		
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_admin1">
			<input type="checkbox" name="smsalert_ac_general[admin_notify]" id="smsalert_ac_general[admin_notify]" class="notify_box" <?php echo (($smsalert_ac_admin_notify=='on')?"checked='checked'":''); ?> /><label><?php _e( 'Send msg to admin when product is left in cart', SmsAlertConstants::TEXT_DOMAIN ) ?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="accordion_admin1" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td>
						<div class="smsalert_tokens"><?php echo $getAbandonCartvariables; ?></div>
						<textarea name="smsalert_ac_message[admin_notify]" id="smsalert_ac_message[admin_notify]" data-parent_id="smsalert_ac_general[admin_notify]" <?php echo(($smsalert_ac_admin_notify=='on')?'' : "readonly='readonly'"); ?>><?php echo $smsalert_ac_admin_message;?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<?php// }?>