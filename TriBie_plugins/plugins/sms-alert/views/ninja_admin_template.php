<?php 
$ninja_forms = SmsAlertNinjaForms::get_ninja_forms();
if(!empty($ninja_forms)){
?>
<div class="cvt-accordion">
	<div class="accordion-section">			      
	<?php foreach($ninja_forms as $ks => $vs){ ?>		
		<a class="cvt-accordion-body-title" href="javascript:void(0)" data-href="#accordion_<?php echo $ks; ?>">
			<input type="checkbox" name="smsalert_ninja_general[ninja_admin_notification_<?php echo $vs; ?>]" id="smsalert_ninja_general[ninja_admin_notification_<?php echo $vs; ?>]" class="notify_box" <?php echo ((smsalert_get_option( 'ninja_admin_notification_'.$vs, 'smsalert_ninja_general', 'on')=='on')?"checked='checked'":''); ?>/><label><?php _e(ucwords(str_replace('-', ' ', $vs )), SmsAlertConstants::TEXT_DOMAIN ) ?></label>
			<span class="expand_btn"></span>
		</a>		 
		<div id="accordion_<?php echo $ks; ?>" class="cvt-accordion-body-content">
			<table class="form-table">
				<tr valign="top">
				<td><div class="smsalert_tokens"><?php echo SmsAlertNinjaForms::getNinjavariables($ks); ?></div>
				<textarea data-parent_id="smsalert_ninja_general[ninja_admin_notification_<?php echo $vs; ?>]" name="smsalert_ninja_message[ninja_admin_sms_body_<?php echo $vs; ?>]" id="smsalert_ninja_message[ninja_admin_sms_body_<?php echo $vs; ?>]" <?php echo((smsalert_get_option( 'ninja_admin_notification_'.$vs, 'smsalert_ninja_general', 'on')=='on')?'' : "readonly='readonly'"); ?>><?php echo smsalert_get_option('ninja_admin_sms_body_'.$vs, 'smsalert_ninja_message', sprintf(__('Dear admin, %s has submitted a form.',SmsAlertConstants::TEXT_DOMAIN), '[name]'));?></textarea>
				</td>
				</tr>
			</table>
		</div>
	<?php } ?>
	</div>	
</div>
<?php 
}else{
	echo "<h3>No Form publish</h3>";
}
?>	