<?php
add_thickbox();
$url = add_query_arg( array(
    'action'    => 'foo_modal_box',
	'TB_iframe' => 'true',
    'width'     => '800',
    'height'    => '500',	
), admin_url( 'admin.php?page=all-order-variable' ) );

function foo_render_action_page() {
    define( 'IFRAME_REQUEST', true );
    iframe_header();

    // ... your content here ...
    iframe_footer();
    exit;
}
add_action( 'admin_action_foo_modal_box', 'foo_render_action_page' );
?>
<!-- Admin-accordion -->
<div class="cvt-accordion"><!-- cvt-accordion -->
	<div class="accordion-section">
		<?php
		foreach($order_statuses as $ks => $vs)
		{
			$prefix = 'wc-';
			$vs 	= $ks;
			if (substr($vs, 0, strlen($prefix)) == $prefix){
				$vs = substr($vs, strlen($prefix));
			}
		?>
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_<?php echo $ks; ?>"><input type="checkbox" name="smsalert_general[admin_notification_<?php echo $vs; ?>]" id="smsalert_general[admin_notification_<?php echo $vs; ?>]" class="notify_box" <?php echo ((smsalert_get_option( 'admin_notification_'.$vs, 'smsalert_general', 'on')=='on')?"checked='checked'":''); ?>/><label><?php _e('when Order is '.ucwords(str_replace('-', ' ', $vs )), SmsAlertConstants::TEXT_DOMAIN ) ?></label>
		<span class="expand_btn"></span>
		</a>
		<div id="accordion_<?php echo $ks; ?>" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td>
						<?php $default_template = SmsAlertMessages::showMessage('DEFAULT_ADMIN_SMS_'.str_replace('-', '_', strtoupper($vs)));?>
						<div class="smsalert_tokens"><?php echo $getvariables; ?><a href="<?php echo $url; ?>" class="thickbox search-token-btn">[...More]</a></div>
						<textarea name="smsalert_message[admin_sms_body_<?php echo $vs; ?>]" id="smsalert_message[admin_sms_body_<?php echo $vs; ?>]" <?php echo((smsalert_get_option( 'admin_notification_'.$vs, 'smsalert_general', 'on')=='on')?'' : "readonly='readonly'"); ?>><?php echo smsalert_get_option('admin_sms_body_'.$vs, 'smsalert_message', (($default_template!='') ? $default_template : SmsAlertMessages::showMessage('DEFAULT_ADMIN_SMS_STATUS_CHANGED'))); ?></textarea>					
					</td>
				</tr>
			</table>
		</div>
		<?php } ?>

		<?php if ($hasWoocommerce){?>
			<!--user registration-->
			<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_7">
				<input type="checkbox" name="smsalert_general[admin_registration_msg]" id="smsalert_general[admin_registration_msg]" class="notify_box" <?php echo (($smsalert_notification_reg_admin_msg=='on')?"checked='checked'":'')?>/>
				<label><?php _e( 'When a new user is registered', SmsAlertConstants::TEXT_DOMAIN ) ?></label>
				<span class="expand_btn"></span>
			</a>
			<div id="accordion_7" class="cvt-accordion-body-content">
				<table class="form-table">
					<tr valign="top">
						<td>
							<div class="smsalert_tokens"><a href="#" val="[username]"><?php _e( 'Username', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[store_name]"><?php _e( 'Store Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[email]"><?php _e( 'Email', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[billing_phone]"><?php _e( 'Billing Phone', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[role]"><?php _e( 'Role', SmsAlertConstants::TEXT_DOMAIN ) ?></a></div>
							<textarea name="smsalert_message[sms_body_registration_admin_msg]" id="smsalert_message[sms_body_registration_admin_msg]" <?php echo((smsalert_get_option( 'admin_registration_msg', 'smsalert_general', 'on')=='on')?'' : "readonly='readonly'"); ?>><?php echo $sms_body_registration_admin_msg; ?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<!--/user registration-->
			<!--Low Stock Woocommerce-->
			<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_8">
				<input type="checkbox" name="smsalert_general[admin_low_stock_msg]" id="smsalert_general[admin_low_stock_msg]" class="notify_box" <?php echo (($smsalert_notification_low_stock_admin_msg=='on')?"checked='checked'":'')?>/>
				<label><?php _e( 'When Product is in low stock', SmsAlertConstants::TEXT_DOMAIN ) ?></label>
				<span class="expand_btn"></span>
			</a>
			<div id="accordion_8" class="cvt-accordion-body-content">
				<table class="form-table">
					<tr valign="top">
						<td>
							<div class="smsalert_tokens"><a href="#" val="[item_name]"><?php _e( 'Product Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[store_name]"><?php _e( 'Store Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[item_qty]"><?php _e( 'Quantity', SmsAlertConstants::TEXT_DOMAIN ) ?> </a></div>
							<textarea name="smsalert_message[sms_body_admin_low_stock_msg]" id="smsalert_message[sms_body_admin_low_stock_msg]" <?php echo((smsalert_get_option( 'admin_low_stock_msg', 'smsalert_general', 'on')=='on')?'' : "readonly='readonly'"); ?>><?php echo $sms_body_admin_low_stock_msg; ?></textarea>
						</td>
					</tr>
				</table>
			</div>			
			<!--/Low Stock Woocommerce-->
			<!--Out of Stock Woocommerce-->
			<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_9">
				<input type="checkbox" name="smsalert_general[admin_out_of_stock_msg]" id="smsalert_general[admin_out_of_stock_msg]" class="notify_box" <?php echo (($smsalert_notification_out_of_stock_admin_msg=='on')?"checked='checked'":'')?>/>
				<label><?php _e( 'When Product is out of stock', SmsAlertConstants::TEXT_DOMAIN ) ?></label>
				<span class="expand_btn"></span>
			</a>
			<div id="accordion_9" class="cvt-accordion-body-content">
				<table class="form-table">
					<tr valign="top">
						<td>
							<div class="smsalert_tokens"><a href="#" val="[item_name]"><?php _e( 'Product Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[store_name]"><?php _e( 'Store Name', SmsAlertConstants::TEXT_DOMAIN ) ?></a> | <a href="#" val="[item_qty]"><?php _e( 'Quantity', SmsAlertConstants::TEXT_DOMAIN ) ?> </a></div>
							<textarea name="smsalert_message[sms_body_admin_out_of_stock_msg]" id="smsalert_message[sms_body_admin_out_of_stock_msg]" <?php echo((smsalert_get_option( 'admin_out_of_stock_msg', 'smsalert_general', 'on')=='on')?'' : "readonly='readonly'"); ?>><?php echo $sms_body_admin_out_of_stock_msg; ?></textarea>
						</td>
					</tr>
				</table>
			</div>			
			<!--/Out of Stock Woocommerce-->
		<?php }?>	
	</div>
</div><!-- /-cvt-accordion -->