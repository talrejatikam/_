<header class="header">
	<input class="menu-btn" type="checkbox" id="menu-btn" />
	<label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>
	<a href="" class="logo">SMS ALERT</a>
	<ul class="menu">
		<li tab_type="logo" onclick="return false;" class="hidemb">
			<img src="https://www.smsalert.co.in/logo/www.smsalert.co.in.png" width="150px;" />
		</li>
		<li tab_type="global" onclick="SMSAlert_change_nav(this, 'SMSAlert_nav_global_box')" class="SMSAlert_active">
			<a href="#general"><span class="dashicons-before dashicons-admin-generic"></span> <?php echo _e( 'General Settings', SmsAlertConstants::TEXT_DOMAIN );?> </a>
		</li>
		<?php
		if ($hasWoocommerce|| $hasWPmembers || $hasUltimate || $hasWPAM)
		{
		?>
		<li tab_type="css" onclick="SMSAlert_change_nav(this, 'SMSAlert_nav_css_box')">
			<a href="#customertemplates"><span class="dashicons-before dashicons-admin-users"></span> <?php echo _e( 'Customer Notifications', SmsAlertConstants::TEXT_DOMAIN );?></a>
		</li>
		<li tab_type="admintemplates" onclick="SMSAlert_change_nav(this, 'SMSAlert_nav_admintemplates_box')" >
			<a href="#admintemplates"><span class="dashicons-before dashicons-list-view"></span> <?php echo _e( 'Admin Notifications', SmsAlertConstants::TEXT_DOMAIN );?></a>
		</li>
		<?php 
		} 
		?>
		<li tab_type="otpsection" onclick="SMSAlert_change_nav(this, 'SMSAlert_nav_otp_section_box')" >
			<a href="#otpsection"><span class="dashicons dashicons-admin-tools"></span> <?php echo _e( 'OTP Settings', SmsAlertConstants::TEXT_DOMAIN );?></a>
		</li>
		
		<?php
			$tabs = apply_filters('sa_addTabs',array());
			foreach($tabs as $tab){
		?>
			<li tab_type="<?php echo $tab['tab_section']; ?>" onclick="SMSAlert_change_nav(this, 'SMSAlert_nav_<?php echo $tab['tab_section']; ?>_box')" >
				<a href="#<?php echo $tab['tab_section']; ?>"><span class="dashicons <?php echo $tab['icon']; ?>"></span> <?php echo _e( $tab['title'], SmsAlertConstants::TEXT_DOMAIN );?></a>
			</li>
		<?php } ?>
		<li tab_type="callbacks" onclick="SMSAlert_change_nav(this, 'SMSAlert_nav_callbacks_box')" >
			<a href="#otp"><span class="dashicons-before dashicons-admin-settings"></span> <?php echo _e( 'Advanced Settings', SmsAlertConstants::TEXT_DOMAIN );?></a>
		</li>
		<li tab_type="credits" onclick="SMSAlert_change_nav(this, 'SMSAlert_nav_credits_box')" class="<?php echo $credit_show?>">
			<a href="#credits"><span class="dashicons-before dashicons-admin-comments"></span> <?php echo _e( 'SMS Credits', SmsAlertConstants::TEXT_DOMAIN );?></a>
		</li>
		<li tab_type="support" onclick="SMSAlert_change_nav(this, 'SMSAlert_nav_support_box')" >
			<a href="#support"><span class="dashicons-before dashicons-editor-help"></span> <?php echo _e( 'Support', SmsAlertConstants::TEXT_DOMAIN );?></a>
		</li>
	</ul>
</header>

<script>
jQuery(document).ready(function (jQuery) {
    /* toggle nav */
    jQuery(".menu-icon").on("click", function () {
        jQuery(this).toggleClass("active");
    });
    jQuery(".menu").on("click", "li", function () {
        jQuery(".menu-icon").click();
    });
});
</script>