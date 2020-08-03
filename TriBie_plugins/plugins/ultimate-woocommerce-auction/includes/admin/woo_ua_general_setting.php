<?php
/**
 * Ultimate WooCommerce Auction Cron Setting Page 
 *
 * @author   WooThemes
 * @category Admin
 * @package  WooCommerce/Admin
 * @version  2.4.0
 */   
 ?>
 
<div class='wrap'>
	<div id='icon-tools' class='icon32'></br></div>
	<h2><?php _e( 'Ultimate WooCommerce Auction', 'woo_ua' ); ?></h2>
	<h2 class="nav-tab-wrapper">
		<?php
		if( isset( $_GET[ 'tab' ] ) ) {
			$active_tab = $_GET[ 'tab' ];
		} // end if
		else {
			$active_tab = 'wua_auctions_logs';
		}
		?>	
		<a href="?page=woo_ua_auctions_setting&tab=wua_auctions_logs" class="nav-tab <?php echo $active_tab == 'wua_auctions_logs' ? 'nav-tab-active' : ''; ?>">Ultimate Auction Log</a>			
					
		</h2>
		<?php 
		
		if( $active_tab == 'wua_auctions_logs' ) {
			
				include_once( WOO_UA_ADMIN . '/woo_ua_auctions_logs.php');
				woo_ua_list_page_handler_display();
			
	   } ?>	
    </div>