<?php
/**
 * Auction Product Bid Area
 *
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

global $woocommerce, $product, $post;
if(!(method_exists( $product, 'get_type') && $product->get_type() == 'auction')){
	return;
}
$curent_bid = $product->get_woo_ua_auction_current_bid();
$current_user = wp_get_current_user();
$product_id =  $product->get_id();
$user_max_bid = $product->get_woo_ua_user_max_bid($product_id ,$current_user->ID );

?>
	<p class="woo_ua_auction_product_condition">
	<strong>
		<?php _e('Item condition:', 'woo_ua'); ?>
	</strong>
	<span class="woo_ua_current_condition"> <?php  _e($product->get_woo_ua_condition(),'woo_ua' )  ?></span>
	</p>
<?php if(($product->is_woo_ua_closed() === FALSE ) and ($product->is_woo_ua_started() === TRUE )) : ?>

	<div class="woo_ua_auction_product_time" id="woo_acution_countdown">
			<strong>
				<?php _e('Time Left:', 'woo_ua'); ?>
			</strong>
			<div class="main-auction-product auction_product_countdown" data-time="<?php echo $product->get_woo_ua_remaining_seconds() ?>" data-auction-id="<?php echo esc_attr( $product_id ); ?>" 
			data-format="<?php echo get_option( 'woo_ua_auctions_countdown_format' ) ?>"></div>
	</div>	

	<div class='woo_ua_auction_product_ajax_change' >
	
		<p class="woo_ua_auction_product_end_time">
			<strong><?php _e('Ending On:', 'woo_ua'); ?></strong>
			<?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $product->get_woo_ua_auctions_end_time() ));  ?>  
			<?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $product->get_woo_ua_auctions_end_time() ));  ?>
				
		</p>
		
		<p class="woo_ua_auction_product_timezone">
			<strong><?php _e('Timezone:', 'woo_ua'); ?></strong>
			<?php echo strtolower(get_option('timezone_string') ? get_option('timezone_string') : __('UTC+','woo_ua').get_option('gmt_offset')); ?>
		</p>
			<div class="checkreserve">
		<?php if(($product->is_woo_ua_reserved() === TRUE) &&( $product->is_woo_ua_reserve_met() === FALSE )  ) { ?>
			<?php $reserve_text = __( "price has not been met.", 'woo_ua' ); ?>
				<p class="woo_ua_auction_product_reserve_not_met">
				<strong><?php printf(__('Reserve %s','woo_ua') , $reserve_text);?></strong>
				</p>	
		<?php } ?>
	
	<?php if(($product->is_woo_ua_reserved() === TRUE) &&( $product->is_woo_ua_reserve_met() === TRUE )  ) { ?>
			<?php $reserve_text = __( "price has been met.", 'woo_ua' ); ?>
			<p class="woo_ua_auction_product_reserve_met">
				<strong><?php printf(__('Reserve %s','woo_ua') , $reserve_text);?></strong>
			</p>
	<?php } ?>
	</div>
	
	<?php do_action('woocommerce_before_woo_ua_bid_form'); ?>
	
	<form class="woo_ua_auction_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $product_id; ?>">
		<?php do_action('woocommerce_before_woo_ua_bid_button'); ?>
		<input type="hidden" name="bid" value="<?php echo esc_attr( $product_id ); ?>" />
		
			<div class="quantity buttons_added">
				<input type="button" value="+" class="plus" />					
				<input type="number" name="wua_bid_value" data-auction-id="<?php echo esc_attr( $product_id ); ?>"
				value="<?php echo $product->woo_ua_bid_value()  ?>"
				min="<?php echo $product->woo_ua_bid_value()  ?>"  
				step="any" size="<?php echo strlen($product->get_woo_ua_current_bid())+2 ?>" title="bid"  class="input-text qty  bid text left">
				<input type="button" value="-" class="minus" />
			</div>	
			
		<button type="submit" class="bid_button button alt">
		<?php echo apply_filters('ultimate_woocommerce_auction_bid_text', __( 'Place Bid', 'woo_ua' ), $product); ?></button>	
		
		<input type="hidden" name="wua-place-bid" value="<?php echo $product_id; ?>" />
		<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
		<?php if ( is_user_logged_in() ) { ?>
			<input type="hidden" name="user_id" value="<?php echo  get_current_user_id(); ?>" />
		<?php  } ?> 
		
	<?php do_action('woocommerce_after_woo_ua_bid_button'); ?>
		
	</form>
	
	<?php do_action('woocommerce_after_woo_ua_bid_form'); ?>
	
	</div>
<?php endif; ?>

	<?php if ($product->get_woo_ua_auction_fail_reason() == '1'){ ?>
		
	<p class="expired">	<?php  _e('Auction Expired because there were no bids', 'woo_ua');?>  </p>
		 
	 <?php } elseif($product->get_woo_ua_auction_fail_reason() == '2'){ ?>
		
	<p class="reserve_not_met"> <?php	_e('Auction expired without reaching reserve price', 'woo_ua'); ?> </p>
		
	 <?php } ?>
</p>