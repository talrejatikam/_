<?php $embk_booking_statuses = SmsAlertEMBooking::em_booking_statuses(); ?>
<!-- accordion -->	
<div class="cvt-accordion">
	<div class="accordion-section">
	<?php 
	 foreach($embk_booking_statuses as $ks => $vs)
	 {
	 	 $ks = $vs;
	 	 $ks = str_replace(' ', '_', $ks);
		  $current_val = (is_array($embk_booking_statuses) && array_key_exists($vs, $embk_booking_statuses)) ? $embk_booking_statuses[$vs] : $vs;
		 ?>		
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_cust_<?php echo $ks; ?>"><input type="checkbox" name="smsalert_embk_general[embk_order_status_<?php echo $vs; ?>]" id="smsalert_embk_general[embk_order_status_<?php echo $vs; ?>]" class="notify_box" 
		<?php echo ((smsalert_get_option( 'embk_order_status_'.$vs, 'smsalert_embk_general', 'on')=='on')?"checked='checked'":''); ?>/><label><?php _e( 'when Order is '.ucwords(str_replace('-', ' ', $vs )), SmsAlertConstants::TEXT_DOMAIN ) ?></label>
		<span class="expand_btn"></span>
		</a>		 
		<div id="accordion_cust_<?php echo $ks; ?>" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
				<td><div class="smsalert_tokens"><?php  echo SmsAlertEMBooking::getEMBookingvariables(); ?></div>
				<textarea name="smsalert_embk_message[embk_sms_body_<?php echo $vs; ?>]" id="smsalert_embk_message[embk_sms_body_<?php
				echo $vs; ?>]" <?php echo(($current_val==$vs)?'' : "readonly='readonly'"); ?>><?php	echo smsalert_get_option('embk_sms_body_'.$vs, 'smsalert_embk_message', sprintf(__('Hello %s, status of your booking %s with %s has been changed to %s.',SmsAlertConstants::TEXT_DOMAIN), '[#_BOOKINGNAME]', '[#_BOOKINGID]', '[#_EVENTNAME]', '[#_BOOKINGSTATUS]')); ?></textarea>
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