<?php 
		$wc_warrant_status = sa_Return_Warranty::getWarrantStatus();
		
		foreach($wc_warrant_status as $ks => $vs)
		{
				$vs = str_replace(' ', '-', strtolower($vs));			
				$wc_warranty_checkbox = smsalert_get_option('warranty_status_'.$vs, 'smsalert_warranty','');
				$wc_warranty_text = smsalert_get_option('sms_text_'.$vs, 'smsalert_warranty','');
				
?>
<div class="cvt-accordion">
	<div class="accordion-section">
		
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_cust_<?php echo $ks; ?>">
			<input type="checkbox" name="smsalert_warranty[warranty_status_<?php echo $vs; ?>]" id="smsalert_warranty[warranty_status_<?php echo $vs; ?>]" class="notify_box" <?php echo (($wc_warranty_checkbox=='on')?"checked='checked'":''); ?> /><label> <?php _e( 'when Order is '.ucwords(str_replace('-', ' ', $vs )), SmsAlertConstants::TEXT_DOMAIN ) ?></label>
			<span class="expand_btn"></span>
		</a>
		
		
		<div id="accordion_cust_<?php echo $ks; ?>" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
					<td>
						<div class="smsalert_tokens"><a href="#" val="[order_id]">Order Id</a> | <a href="#" val="[rma_number]">RMA Number</a> | <a href="#" val="[rma_status]">RMA status</a> | <a href="#" val="[order_amount]">Order Total</a> | <a href="#" val="[billing_first_name]">First Name</a> | <a href="#" val="[store_name]">Store Name</a> | <a href="#" val="[item_name]">Product Name</a> </div>
						
						<textarea name="smsalert_warranty[sms_text_<?php echo $vs; ?>]" id="smsalert_warranty[sms_text_<?php
						echo $vs; ?>]" <?php echo(($wc_warranty_text==$vs)?'' : "readonly='readonly'"); ?> data-parent_id="smsalert_warranty[warranty_status_<?php echo $vs; ?>]"><?php 	
				
							echo smsalert_get_option('sms_text_'.$vs, 'smsalert_warranty', '') ? smsalert_get_option('sms_text_'.$vs, 'smsalert_warranty', '') : sprintf(__('Hello %s, status of your warranty request no. %s against %s with %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[billing_first_name]', '[rma_number]', '[order_id]', '[store_name]', '[rma_status]'); ?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<?php
		}
?>