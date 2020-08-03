<?php
add_thickbox();

$url = add_query_arg( array(
    'action'    => 'foo_modal_box',
	'TB_iframe' => 'true',
    'width'     => '800',
    'height'    => '500',	
), admin_url( 'admin.php?page=all-order-variable' ) );
?>
</script>
<!-- accordion -->
<div class="cvt-accordion">
	<div class="accordion-section">
		<?php
		foreach($order_statuses as $ks => $vs)
		{
			$prefix = 'wc-';
			$vs = $ks;
			if (substr($vs, 0, strlen($prefix)) == $prefix){
				$vs = substr($vs, strlen($prefix));
			}
			$current_val = (is_array($smsalert_notification_status) && array_key_exists($vs, $smsalert_notification_status)) ? $smsalert_notification_status[$vs] : $vs;
		?>
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_cust_<?php echo $ks; ?>">
			<input type="checkbox" name="smsalert_general[order_status][<?php echo $vs; ?>]" id="smsalert_general[order_status][<?php echo $vs; ?>]" class="notify_box" <?php echo (($current_val==$vs)?"checked='checked'":''); ?> value="<?php echo $vs; ?>"/><label><?php _e( 'when Order is '.ucwords(str_replace('-', ' ', $vs )), SmsAlertConstants::TEXT_DOMAIN ) ?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="accordion_cust_<?php echo $ks; ?>" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td>
						<?php $default_template = SmsAlertMessages::showMessage('DEFAULT_BUYER_SMS_'.str_replace('-', '_', strtoupper($vs)));?>
						<div class="smsalert_tokens"><?php echo $getvariables; ?><a href="<?php echo $url; ?>" class="thickbox search-token-btn">[...More]</a></div>
						<textarea name="smsalert_message[sms_body_<?php echo $vs; ?>]" id="smsalert_message[sms_body_<?php echo $vs; ?>]" data-parent_id="smsalert_general[order_status][<?php echo $vs; ?>]" <?php echo(($current_val==$vs)?'' : "readonly='readonly'"); ?>><?php echo smsalert_get_option('sms_body_'.$vs, 'smsalert_message', (($default_template!='') ? $default_template : SmsAlertMessages::showMessage('DEFAULT_BUYER_SMS_STATUS_CHANGED'))); ?></textarea>
					</td>
				</tr>
			</table>
		</div>
		<?php } ?>

		<?php if ($hasWoocommerce) { ?>
		<!-- accordion --5-->
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_5">
			<input type="checkbox" name="smsalert_general[buyer_notification_notes]" id="smsalert_general[buyer_notification_notes]" class="notify_box" <?php echo (($smsalert_notification_notes=='on')?"checked='checked'":'')?>/>
			<label><?php _e( 'When a new note is added to order', SmsAlertConstants::TEXT_DOMAIN ) ?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="accordion_5" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td>
						<div class="smsalert_tokens"><?php echo $getvariables; ?><a href="#" val="[note]">order note</a> </div>
						<textarea name="smsalert_message[sms_body_new_note]" id="smsalert_message[sms_body_new_note]"><?php echo $sms_body_new_note; ?></textarea>
					</td>
				</tr>
			</table>
		</div>
		<?php } ?>
	</div>
</div>
<!--end accordion-->