<?php
/**
 * Auction history tab
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $post, $product;
$datetimeformat = get_option('date_format').' '.get_option('time_format');
?>

<h2><?php _e('Total Bids Placed:', 'woo_ua'); ?></h2>

<?php if(($product->is_woo_ua_closed() === TRUE ) and ($product->is_woo_ua_started() === TRUE )) : ?>
    
	<p><?php _e('Auction has expired', 'woo_ua') ?></p>
	<?php if ($product->get_woo_ua_auction_fail_reason() == '1'){
		 _e('Auction Expired because there were no bids', 'woo_ua');
	} elseif($product->get_woo_ua_auction_fail_reason() == '2'){
		_e('Auction expired without reaching reserve price', 'woo_ua');
	}
	
	if($product->get_woo_ua_auction_closed() == '3'){?>
		<p><?php _e('Product sold for buy now price', 'woo_ua') ?>: <span><?php echo wc_price($product->get_regular_price()) ?></span></p>
	<?php }elseif($product->get_woo_ua_auction_current_bider()){ ?>
		<p><?php _e('Highest bidder was', 'woo_ua') ?>: <span><?php echo get_userdata($product->get_woo_ua_auction_current_bider())->display_name ?></span></p>
	<?php } ?>
						
<?php endif; ?>	
<table id="auction-history-table-<?php echo $product->get_id(); ?>" class="auction-history-table">
    <?php 
        
      $woo_ua_auction_history = $product->woo_ua_auction_history();
	
        if ( !empty($woo_ua_auction_history) ): ?>

        <thead>
            <tr>
                <th><?php _e('Bidder Name', 'woo_ua')?></th>
				<th><?php _e('Bidding Time', 'woo_ua')?></th>
                <th><?php _e('Bid', 'woo_ua') ?></th>
              
               
            </tr>
        </thead>
        <tbody>
        <?php 
            foreach ($woo_ua_auction_history as $history_value) { ?>
			<tr>
                <td class="bid_username"><?php echo get_userdata($history_value->userid)->display_name;?></td>
				<td class="bid_date"><?php echo mysql2date($datetimeformat ,$history_value->date)?></td>
				<td class="bid_price"><?php echo wc_price($history_value->bid);?></td>
           </tr>
      <?php } ?> 
        </tbody>

    <?php endif;?>
        
	<tr class="start">
        <?php 
		$start_date = $product->get_woo_ua_auction_start_time(); ?>
		<?php if ($product->is_woo_ua_started() === TRUE) { ?>
		<td class="started"><?php echo __('Auction started', 'woo_ua');?>
		<?php }   else { ?>									
		<td  class="started"><?php echo __('Auction starting', 'woo_ua');?>		
		<?php } ?></td>	
		<td colspan="3"  class="bid_date"><?php echo mysql2date($datetimeformat,$start_date)?></td>
							
	</tr>
</table>