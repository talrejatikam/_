<?php
$smsalert_driver_message 			= smsalert_get_option('driver_notify', 'smsalert_driver_message', sprintf(__('Hi %s %s , The package for order %s Products %s %s has been ready to pickup from %s.',SmsAlertConstants::TEXT_DOMAIN), '[first_name]', '[last_name]', '[order_id]', '[item_name]', '[item_name_qty]', '[store_name]'));
$smsalert_driver_notify 			= smsalert_get_option( 'driver_notify', 'smsalert_driver_general', 'on');	
?>
<div class="cvt-accordion">
	<div class="accordion-section">
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_cust1">
			<input type="checkbox" name="smsalert_driver_general[driver_notify]" id="smsalert_driver_general[driver_notify]" class="notify_box" <?php echo (($smsalert_driver_notify=='on')?"checked='checked'":''); ?> /><label> <?php _e( 'when Order is assigned to driver', SmsAlertConstants::TEXT_DOMAIN );
			?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="accordion_cust1" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td>
						<div class="smsalert_tokens"><div class="smsalert_tokens">
						<a href="#" val="[first_name]"><?php _e( 'First Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[last_name]"><?php _e( 'Last Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[order_id]"><?php _e( 'Order Id', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[item_name]"><?php _e( 'Product Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[item_name_qty]"><?php _e( 'Product Name with Quantity', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[store_name]"><?php _e( 'Store Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a></div></div>
						<textarea name="smsalert_driver_message[driver_notify]" id="smsalert_driver_message[driver_notify]" data-parent_id="smsalert_driver_general[driver_notify]" <?php echo(($smsalert_driver_notify=='on')?'' : "readonly='readonly'"); ?>><?php echo $smsalert_driver_message;?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div class="submit">
<a href="users.php?role=driver" class="button action alignright"><?php _e( 'View Drivers', SmsAlertConstants::TEXT_DOMAIN ) ?></a>
</div>