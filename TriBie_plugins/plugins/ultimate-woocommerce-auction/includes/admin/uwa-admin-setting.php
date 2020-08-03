<?php
/**
 * Ultimate WooCommerce Auction Cron Setting Page 
 *
 * @author   WooThemes
 * @category Admin
 * @package  WooCommerce/Admin
 * @version  2.4.0
 */   
 
 if(isset($_POST['uwa-settings-submit']) == 'Save Changes')
	{
		
		// Cron Setting Section
		if (isset($_POST['uwa_cron_status_in'])) {
			update_option('woo_ua_cron_auction_status', $_POST['uwa_cron_status_in']);
		} 
		if (isset($_POST['uwa_cron_status_number'])) {
			update_option('woo_ua_cron_auction_status_number', $_POST['uwa_cron_status_number']);
		}
		// Auction Product Section
		if(isset($_POST['uwa_bid_ajax_enable'])){	
			update_option('woo_ua_auctions_bid_ajax_enable', "yes");
		}else{
			update_option('woo_ua_auctions_bid_ajax_enable', "no");
		}		
		if (isset($_POST['uwa_bid_ajax_interval'])) {
			update_option('woo_ua_auctions_bid_ajax_interval', $_POST['uwa_bid_ajax_interval']);
		
		}
		//shop Page	
		if(isset($_POST['uwa_shop_enabled']) && $_POST['uwa_shop_enabled'] =="1"){
			update_option('woo_ua_show_auction_pages_shop', "yes");
		} else{
			update_option('woo_ua_show_auction_pages_shop', "no");
		}
		if(isset($_POST['uwa_search_enabled']) && $_POST['uwa_search_enabled'] =="1"){
			update_option('woo_ua_show_auction_pages_search', "yes");
		} else{
			update_option('woo_ua_show_auction_pages_search', "no");
		}
		if(isset($_POST['uwa_cat_enabled']) && $_POST['uwa_cat_enabled'] =="1"){
			update_option('woo_ua_show_auction_pages_cat', "yes");
		} else{
			update_option('woo_ua_show_auction_pages_cat', "no");
		}
		if(isset($_POST['uwa_tag_enabled']) && $_POST['uwa_tag_enabled'] =="1"){
			update_option('woo_ua_show_auction_pages_tag', "yes");
		} else{
			update_option('woo_ua_show_auction_pages_tag', "no");
		}
		if(isset($_POST['uwa_expired_enabled']) && $_POST['uwa_expired_enabled'] =="1"){
			update_option('woo_ua_expired_auction_enabled', "yes");
		} else{
			update_option('woo_ua_expired_auction_enabled', "no");
		}
		
		//Auction Detail Page
		if (isset($_POST['uwa_countdown_format'])) {				
			update_option('woo_ua_auctions_countdown_format', $_POST['uwa_countdown_format']);			
		} else {				
			update_option('woo_ua_auctions_countdown_format', 'yowdHMS');
		}	 
		if (isset($_POST['uwa_hide_compact_enable'])) {			
			update_option('uwa_hide_compact_enable', "yes");
		} else {
			update_option('uwa_hide_compact_enable', "no");
		}
				
		if(isset($_POST['uwa_private_message']) && $_POST['uwa_private_message'] =="1"){
			update_option('woo_ua_auctions_private_message', "yes");
		} else{
			update_option('woo_ua_auctions_private_message', "no");
		}		
		if(isset($_POST['uwa_bids_tab']) && $_POST['uwa_bids_tab'] =="1"){
			update_option('woo_ua_auctions_bids_section_tab', "yes");
		} else{
			update_option('woo_ua_auctions_bids_section_tab', "no");
		}			
		if(isset($_POST['uwa_watchlists_tab']) && $_POST['uwa_watchlists_tab'] =="1"){
			update_option('woo_ua_auctions_watchlists', "yes");
		} else{
			update_option('woo_ua_auctions_watchlists', "no");
		}			
		if(isset($_POST['uwa_allow_owner_to_bid'])){	
			update_option('uwa_allow_owner_to_bid', "yes");
		} else{
			update_option('uwa_allow_owner_to_bid', "no");
		}
		
		if(isset($_POST['uwa_allow_admin_to_bid'])){	
			update_option('uwa_allow_admin_to_bid', "yes");
		} else{
			update_option('uwa_allow_admin_to_bid', "no");
		}
			
	}
	
	
	// Cron Setting Section
	$uwa_cron_status_in = get_option('woo_ua_cron_auction_status', '2');
	$uwa_cron_status_number = get_option('woo_ua_cron_auction_status_number', '25');
	
	//Auction Section
	$uwa_bid_ajax_interval = get_option('woo_ua_auctions_bid_ajax_interval', '25');	
	$uwa_ajax_enable = get_option('woo_ua_auctions_bid_ajax_enable');
	$checked_enable="";
	if($uwa_ajax_enable =="yes"){
		   $checked_enable = "checked";
	}
	   
	//Shop Page   
	$expired_enable = get_option('woo_ua_expired_auction_enabled');		
    $expired_checked_enable="";
    if($expired_enable =="yes"){
		   $expired_checked_enable = "checked";
	}
	
	$shop_enable = get_option('woo_ua_show_auction_pages_shop');	
	$shop_checked_enable="";
	if($shop_enable =="yes"){
		$shop_checked_enable = "checked";
	}	
	
	$search_enable = get_option('woo_ua_show_auction_pages_search');
	$search_checked_enable="";
	if($search_enable =="yes"){
		$search_checked_enable = "checked";
	}	
	
	$cat_enable = get_option('woo_ua_show_auction_pages_cat');
	$cat_checked_enable="";
	if($cat_enable =="yes"){
		$cat_checked_enable = "checked";
	}
	$tag_enable = get_option('woo_ua_show_auction_pages_tag');
	$tag_checked_enable="";
	if($tag_enable =="yes"){
		$tag_checked_enable = "checked";
	}
	
	//Auction Detail Page
	$countdown_format = get_option('woo_ua_auctions_countdown_format');	   
	$reviews_tab_enable = get_option('woo_ua_auctions_bids_reviews_tab');
	$reviews_checked_enable="";
	if($reviews_tab_enable =="yes"){
		$reviews_checked_enable = "checked";
	}
	$private_tab_enable = get_option('woo_ua_auctions_private_message');
	$private_checked_enable="";
	if($private_tab_enable =="yes"){
		$private_checked_enable = "checked";
	}
	$bids_tab_enable = get_option('woo_ua_auctions_bids_section_tab');
	$bids_checked_enable="";
	if($bids_tab_enable =="yes"){
		$bids_checked_enable = "checked";
	}
	$watchlists_tab_enable = get_option('woo_ua_auctions_watchlists');
	$watchlists_checked_enable="";
	if($watchlists_tab_enable =="yes"){
		$watchlists_checked_enable = "checked";
	}
	
	$uwa_hide_compact_enable= get_option('uwa_hide_compact_enable');
	$uwa_hide_compact_checked_enable="";
	if($uwa_hide_compact_enable =="yes"){
		$uwa_hide_compact_checked_enable = "checked";
	}
	
	$uwa_allow_owner_to_bid = get_option('uwa_allow_owner_to_bid',"no");
	$uwa_restrict_owner_checked_enable="";
	if($uwa_allow_owner_to_bid =="yes"){
		$uwa_restrict_owner_checked_enable = "checked";
	}
 
	$uwa_allow_admin_to_bid = get_option('uwa_allow_admin_to_bid',"no");
	$uwa_allow_admin_to_bid_checked="";
	if($uwa_allow_admin_to_bid =="yes"){
		$uwa_allow_admin_to_bid_checked = "checked";
	}
	
?>		
	
<div class="wrap" id="uwa_auction_setID">
	<div id='icon-tools' class='icon32'></br></div>
	
	<h2 class="uwa_main_h2"><?php _e( 'Ultimate WooCommerce Auction', 'woo_ua' ); ?><span class="uwa_version_text"><?php _e( 'Version :', 'woo_ua' ); ?> <?php echo WOO_UA_VERSION; ?></span></h2>
	
	<div class="get_uwa_pro">

                 <a href="https://auctionplugin.net?utm_source=woo plugin&utm_medium=horizontal banner&utm_campaign=learn-more-button" target="_blank"> <img src="<?php echo WOO_UA_ASSETS_URL;?>/images/UWCA_row.jpg" alt="" /> </a>
                
    	<div class="clear"></div>
    </div>

 	
	<div id="uwa-auction-banner-text">	
	<?php _e('If you like <a href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post" target="_blank"> our plugin working </a> with WooCommerce, please leave us a <a href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post" target="_blank">★★★★★</a> rating. A huge thanks in advance!', 'woo_ua' ); ?>	 
    </div>
	 <div class="uwa_setting_right">
		
			<div class="box_like_plugin">
				<div class="like_plugin">
						<h2 class="title_uwa_setting">Like this plugin?</h2>
					<div class="text_uwa_setting">
						<div class="star_rating">
							<form class="rating">
							  <label>
							    <input type="radio" name="stars" value="1" />
							    <span class="icon">★</span>
							  </label>
							  <label>
							    <input type="radio" name="stars" value="2" />
							    <span class="icon">★</span>
							    <span class="icon">★</span>
							  </label>
							  <label>
							    <input type="radio" name="stars" value="3" />
							    <span class="icon">★</span>
							    <span class="icon">★</span>
							    <span class="icon">★</span>   
							  </label>
							  <label>
							    <input type="radio" name="stars" value="4" />
							    <span class="icon">★</span>
							    <span class="icon">★</span>
							    <span class="icon">★</span>
							    <span class="icon">★</span>
							  </label>
							  <label>
							    <input type="radio" name="stars" value="5" />
							    <span class="icon">★</span>
							    <span class="icon">★</span>
							    <span class="icon">★</span>
							    <span class="icon">★</span>
							    <span class="icon">★</span>
							  </label>
							</form>
						</div>

						<div class="happy_img"> 
							<a target="_blank" href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post"> <img src="<?php echo WOO_UA_ASSETS_URL;?>/images/we_just_need_love.png" alt="" /></a>
						</div>
					</div>
				</div>	
			</div>
			
				<div class="box_get_premium">
					<a href="https://auctionplugin.net?utm_source=woo plugin&utm_medium=vertical banner&utm_campaign=learn-more-button" target="_blank"> <img src="<?php echo WOO_UA_ASSETS_URL;?>/images/UWCA_col.jpg" alt="" /> </a>
			</div>
			
		
		</div>
	<div class="uwa_setting_left">
	
		<form  method='post' class='uwa_auction_setting_style'>

			<!-- beginning of the left meta box section -->
			<div id="wps-deals-misc" class="post-box-container">
				<div class="metabox-holder">	
				<div class="meta-box-sortables ui-sortable">
				<div id="general">					
					<div class="inside ">					
					<table class="form-table">
						<tbody>
						<tr>
						<th scope="row">
						<h2><?php _e( 'Auction Settings', 'woo_ua' ); ?></h2>
								
						</th>
						
						</tr>
						
							<tr>
								<tr>
								<th scope="row">
									<label for="uwa_cron_status_in"><?php _e( 'Check Auction Status:', 'woo_ua' ); ?></label>
								</th>
								<td>
									<?php _e( 'In every', 'woo_ua' ); ?>
									<input type="number" name="uwa_cron_status_in" class="regular-number" min="1" id="uwa_cron_status_in" value="<?php echo $uwa_cron_status_in; ?>"><?php _e( 'Minutes.', 'woo_ua' ); ?>
									</br>
									<div class="uwa-auction-settings-tip">
									<?php _e('A scheduler runs on an interval specified in this field in recurring manner.It checks, if some live auctions product can be expired and accordingly update their status.', 'woo_ua');
                                     ?>	
									</div>									 
								</td>
							   </tr>
							 
							   <tr>
								<th scope="row">
									<label for="uwa_cron_status_number"><?php _e( 'Auctions Processed Simultaneously:', 'woo_ua' ); ?></label>
								</th>
								
									<td>
									<?php _e( 'Process ', 'woo_ua' ); ?>
									<input type="number" name="uwa_cron_status_number" class="regular-number" min="1"
									id="uwa_cron_status_number" value="<?php echo $uwa_cron_status_number; ?>"><?php _e( 'auctions per request.', 'woo_ua' ); ?>
									</br>
									<div class="uwa-auction-settings-tip">
									<?php _e('Number of auctions products Process per request.The scheduler processes the specified no. auctions whenever a schedule occurs.', 'woo_ua');
                                     ?>									
									
									<a href="" class="uwa_fields_tooltip" onclick="return false"><strong>?</strong>
		<span style="width: 500px; margin-left: -375px;">* <strong><?php _e( 'Note :', 'woo_ua' ); ?><strong> <ol>
										<li><?php _e( 'It is recommended to fill the above values in a balanced manner based upon the traffic, no. of auction products and no. of users on your site.', 'woo_ua' ); ?>
										</li>
										<li><?php _e( 'The less is the no. of auctions per request (fields 2 and 4 from above), the processing will be more optimized. If you are allowing so many auctions to be processed in each request, it can affect your site performance.', 'woo_ua' ); ?>
										</li>
										<li><?php _e( 'Similarly, you should also not set a very few no. of auction products since there may be delayed in expiry of some auction products and/or email notifications.', 'woo_ua' ); ?>
										</li>
										<li><?php _e( 'It is recommended not to keep on changing these values frequently as your auction products will be rescheduled every time you update the values.', 'woo_ua' ); ?>
										</li>										
									</ol>
		</span></a>	</div>
								</td>
							   </tr>
							   
							  
							  <tr>
								<tr>
								<th scope="row">
									<label for="uwa_bid_ajax_enable"><?php _e( 'Bidding Information:', 'woo_ua' ); ?></label>
								</th>
								<td>									
									<input type="checkbox" <?php echo $checked_enable;?> name="uwa_bid_ajax_enable" class="regular-number" id="uwa_bid_ajax_enable" value="1"><?php _e( 'Enable Ajax update for latest bidding.', 'woo_ua' ); ?>
									</br>
									<div class="uwa-auction-settings-tip">
									<?php _e('Enables/disables ajax current bid checker (refresher) for auction - updates current bid value without refreshing page (increases server load, disable for best performance)', 'woo_ua');
                                     ?>	
								</div>									 
								</td>
							   </tr>
							 
							   <tr>
								<th scope="row">
									<label for="uwa_bid_ajax_interval"><?php _e( 'Check Bidding Info:', 'woo_ua' ); ?></label>
								</th>
								
									<td>
									<?php _e( 'In every', 'woo_ua' ); ?>
									<input type="number" name="uwa_bid_ajax_interval" class="regular-number" min="1" 
									id="uwa_bid_ajax_interval" value="<?php echo $uwa_bid_ajax_interval; ?>"><?php _e( 'Second.', 'woo_ua' ); ?>
									</br>
									<div class="uwa-auction-settings-tip">
									<?php _e('Time interval between two ajax requests in seconds (bigger intervals means less load for server)', 'woo_ua');
                                     ?>									
									</div>							
								</td>
							   </tr>
							   
							  
							   
							   <tr>
					<th scope="row">
									<label for="uwa_bid_ajax_interval"><?php _e( 'Bidding Restriction:', 'woo_ua' ); ?></label>
								</th>
					<td class="uwaforminp">						
						<input type="checkbox" <?php echo $uwa_allow_admin_to_bid_checked;?> name="uwa_allow_admin_to_bid"  id="uwa_allow_admin_to_bid" value="1">
						<?php _e('Allow Administrator to bid on their own auction.', 'woo_ua');  ?>
					</td>
				</tr>
				
				<tr>
					<th></th>
					<td class="uwaforminp">						
						<input type="checkbox" <?php echo $uwa_restrict_owner_checked_enable;?> name="uwa_allow_owner_to_bid"  id="uwa_allow_owner_to_bid" value="1">
					<?php _e('Allow Auction Owner (Seller/Vendor) to bid on their own auction.', 'woo_ua');  ?>								 
					</td>
				</tr>
							   
							   
							   
							   
							<tr >
							<td colspan="2">
							<h2 class="uwa_section_tr"><?php _e( 'Shop Page', 'woo_ua' ); ?></h2>						
							<span style='vertical-align: top;'><?php _e( 'The following options affect on frontend Shop Page.', 'woo_ua' ); ?></span>  
							</td>

							</tr>
							   
							 <tr>
							 <th scope="row"><?php _e( 'Auctions Display:', 'woo_ua' ); ?></th>
							 
							 <td>
							 <input <?php echo $expired_checked_enable; ?> value="1" name="uwa_expired_enabled" type="checkbox">
							  <?php _e( 'Show Expired Auctions.', 'woo_ua' ); ?>
							 </td>
							 </tr>
							   
							 
							<tr>
							 <th scope="row"><?php _e( 'Show Auctions on:', 'woo_ua' ); ?></th>							 
							 <td>
							 <input <?php echo $shop_checked_enable; ?> value="1" name="uwa_shop_enabled" type="checkbox">
							  <?php _e( 'On Shop Page.', 'woo_ua' ); ?>
							 </td>
							 </tr>
							<tr>
							 <th scope="row"></th>							 
							 <td>
							 <input <?php echo $search_checked_enable; ?> value="1" name="uwa_search_enabled" type="checkbox">
							 <?php _e( 'On Product Search Page.', 'woo_ua' ); ?>
							 </td>
							 </tr> 
							 
							<tr>
							 <th scope="row"></th>							 
							 <td>
							 <input <?php echo $cat_checked_enable; ?> value="1" name="uwa_cat_enabled" type="checkbox">
							  <?php _e( 'On Product Category Page.', 'woo_ua' ); ?>
							 </td>
							 </tr>  
	
							<tr>
							 <th scope="row"></th>							 
							 <td>
							 <input <?php echo $tag_checked_enable; ?> value="1" name="uwa_tag_enabled" type="checkbox"> <?php _e( 'On Product Tag Page.', 'woo_ua' ); ?>
							 </td>
							 </tr> 	
							<tr >
							
							   <td colspan="2">
							   <h2 class="uwa_section_tr"><?php _e( 'Auction Detail Page', 'woo_ua' ); ?></h2>
							   
								  
						<span style='vertical-align: top;'><?php _e( 'The following options affect on frontend Auction Detail page.', 'woo_ua' ); ?></span>  </td>
				  
							   </tr>    


<tr>
								<th scope="row">
									<label for="uwa_countdown_format"><?php _e( 'Countdown Format', 'woo_ua' ); ?></label>
								</th>
								
									<td>									
									<input type="text" name="uwa_countdown_format" class="regular-number" id="uwa_countdown_format" value="<?php echo $countdown_format; ?>"><?php _e( 'The format for the countdown display. Default is yowdHMS', 'woo_ua' ); ?>
									<a href="" class="uwa_fields_tooltip" onclick="return false"><strong>?</strong>
		<span style="width: 500px; margin-left: -375px;"><?php _e("Use the following characters (in order) to indicate which periods you want to display: 'Y' for years, 'O' for months, 'W' for weeks, 'D' for days, 'H' for hours, 'M' for minutes, 'S' for seconds.	Use upper-case characters for mandatory periods, or the corresponding lower-case characters for optional periods, i.e. only display if non-zero. Once one optional period is shown, all the ones after that are also shown.", 'woo_ua');
                                     ?>	
		</span></a>	</div>
									</br>
															
								</td>
							   </tr>
						<tr>				
						<th scope="row">
							<label for="uwa_hide_compact_enable"><?php _e( 'Hide compact countdown', 'woo_ua' ); ?></label>
						</th>
						<td>
							<input <?php echo $uwa_hide_compact_checked_enable; ?> value="1" name="uwa_hide_compact_enable" type="checkbox">
							<?php _e('Hide compact countdown format and display simple format.','woo_ua');?>	
					    </td>
					</tr>	 
							 
							 <tr>
							 <th scope="row"><?php _e( 'Enable Specific Sections:', 'woo_ua' ); ?></th>
							 
							 <td>
							<input <?php echo $private_checked_enable; ?> value="1" name="uwa_private_message" type="checkbox">
								<?php _e('Enable Send Private message.','woo_ua');?>
							 </td>
							 </tr>							 
							 
							 <tr>
							 <th scope="row"></th>
							 
							 <td>
							 <input <?php echo $bids_checked_enable; ?> value="1" name="uwa_bids_tab" type="checkbox">
								<?php _e('Enable Bids section.','woo_ua');?>						  
							
							
							 </td>
							 </tr> 
							 
							<tr>
							 <th scope="row"></th>
							 
							 <td>
							 <input <?php echo $watchlists_checked_enable; ?> value="1" name="uwa_watchlists_tab" type="checkbox"><?php _e( 'Enable Watchlists.', 'woo_ua' ); ?>
								
							 </td>
							 </tr> 							   

						</tbody>						
					</table>
					

						<tfoot>
							<tr>
								<td colspan="2" valign="top" scope="row">								
									<input type="submit" id="uwa-settings-submit" name="uwa-settings-submit" class="button-primary" value="<?php _e('Save Changes','woo_ua');?>" />
								</td>
							</tr>
						</tfoot>
					</table>
					
					
				</div><!-- /.inside -->
			</div></div></div></div>
			<!-- bend of the meta box section -->
        </form>
		
		</div>
		
		
    </div>
	