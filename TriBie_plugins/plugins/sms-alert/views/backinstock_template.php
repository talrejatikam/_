<?php
$smsalert_bis_cust_message 			= smsalert_get_option('customer_bis_notify', 'smsalert_bis_message', sprintf(__('Hello, %s is now available, you can order it on %s.',SmsAlertConstants::TEXT_DOMAIN), '[item_name]', '[shop_url]'));
$smsalert_bis_cust_notify 			= smsalert_get_option( 'customer_bis_notify', 'smsalert_bis_general', 'on');
$smsalert_bis_subscribed_message 	= smsalert_get_option('subscribed_bis_notify', 'smsalert_bis_message', sprintf(__('We have noted your request and we will notify you as soon as %s is available for order with us.',SmsAlertConstants::TEXT_DOMAIN), '[item_name]'));
$smsalert_bis_subscribed_notify 	= smsalert_get_option( 'subscribed_bis_notify', 'smsalert_bis_general', 'on');	
?>
<div class="cvt-accordion">
	<div class="accordion-section">
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_cust1">
			<input type="checkbox" name="smsalert_bis_general[customer_bis_notify]" id="smsalert_bis_general[customer_bis_notify]" class="notify_box" <?php echo (($smsalert_bis_cust_notify=='on')?"checked='checked'":''); ?> /><label> <?php _e( 'Send msg to customer when product is back in stock', SmsAlertConstants::TEXT_DOMAIN );
			?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="accordion_cust1" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td>
						<div class="smsalert_tokens"><div class="smsalert_tokens"><a href="#" val="[item_name]"><?php _e( 'Product Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[name]"><?php _e( 'Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[subscribed_date]"><?php _e( 'Date', SmsAlertConstants::TEXT_DOMAIN ) ?> </a></div></div>
						<textarea name="smsalert_bis_message[customer_bis_notify]" id="smsalert_bis_message[customer_bis_notify]" data-parent_id="smsalert_bis_general[customer_bis_notify]" <?php echo(($smsalert_bis_cust_notify=='on')?'' : "readonly='readonly'"); ?>><?php echo $smsalert_bis_cust_message;?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<div class="cvt-accordion">
	<div class="accordion-section">
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_cust3">
			<input type="checkbox" name="smsalert_bis_general[subscribed_bis_notify]" id="smsalert_bis_general[subscribed_bis_notify]" class="notify_box" <?php echo (($smsalert_bis_subscribed_notify=='on')?"checked='checked'":''); ?> /><label> <?php _e( 'Send msg to customer when product is subscribed', SmsAlertConstants::TEXT_DOMAIN ) ?></label>
			<span class="expand_btn"></span>
		</a>
		<div id="accordion_cust3" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td>
						<div class="smsalert_tokens"><div class="smsalert_tokens"><a href="#" val="[item_name]"><?php _e( 'Product Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[name]"><?php _e( 'Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[subscribed_date]"><?php _e( 'Date', SmsAlertConstants::TEXT_DOMAIN ) ?> </a></div></div>
						<textarea name="smsalert_bis_message[subscribed_bis_notify]" id="smsalert_bis_message[subscribed_bis_notify]" data-parent_id="smsalert_bis_general[subscribed_bis_notify]" <?php echo(($smsalert_bis_subscribed_notify=='on')?'' : "readonly='readonly'"); ?>><?php echo $smsalert_bis_subscribed_message;?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<div class="submit">
<a href="admin.php?page=all-subscriber" class="button action alignright"><?php _e( 'View Subscriber', SmsAlertConstants::TEXT_DOMAIN ) ?></a>
</div>