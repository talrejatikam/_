<?php
if ( ! defined( 'ABSPATH' ) ) {	exit;}

$user_id  = get_current_user_id();$my_auctions = get_woo_ua_auction_by_user($user_id);if ( count($my_auctions ) > 0 ) {
?><table class="shop_table shop_table_responsive">    <tr>        <th class="toptable"><?php echo __( 'Image', 'woo_ua' ); ?></td>        <th class="toptable"><?php echo __( 'Product', 'woo_ua' ); ?></td>        <th class="toptable"><?php echo __( 'Your bid', 'woo_ua' ); ?></td>        <th class="toptable"><?php echo __( 'Current bid', 'woo_ua' ); ?></td>        <th class="toptable"><?php echo __( 'Status', 'woo_ua' ); ?></td>    </tr>    <?php
	    foreach ( $my_auctions as $my_auction ) {
		global $product;
		global $sitepress;
		
		$product_id =  $my_auction->auction_id;
		
		/* For WPML Support - start */		
		if (function_exists('icl_object_id') && method_exists($sitepress, 
			'get_current_language')) {				
			
			$product_id = icl_object_id($product_id	, 'product', false, 
				$sitepress->get_current_language());
		}
		/* For WPML Support - end */
		        $product = wc_get_product( $product_id );
		
		if (  method_exists( $product, 'get_type') && $product->get_type() == 'auction' ) {
		        $product_name = get_the_title( $product_id );        $product_url  = get_the_permalink( $product_id );        $a            = $product->get_image( 'thumbnail' );        ?>        <tr>            
            <td><?php echo $a ;?></td>            <td><a href="<?php echo $product_url; ?>"><?php echo $product_name ?></a></td>            <td><?php echo wc_price( $my_auction->max_bid ) ?></td>            <td><?php echo $product->get_price_html(); ?></td>            <?php			if (($user_id == $product->get_woo_ua_auction_current_bider() && $product->get_woo_ua_auction_closed() == '2' && !$product->get_woo_ua_auction_payed() )) {             ?>				<td><a href="<?php echo apply_filters('ultimate_woocommerce_auction_pay_now_button_text',esc_attr(add_query_arg("pay-uwa-auction",$product->get_id(), woo_ua_auction_get_checkout_url()))); ?>" class="button alt"><?php _e('Pay Now', 'woo_ua') ?></a></td>            <?php  } elseif ( $product->is_woo_ua_closed() ){ ?> 							<td><?php echo __( 'Closed', 'woo_ua' ); ?></td>   				<?php } else { ?>                <td><?php echo __( 'Started', 'woo_ua' ); ?></td>                <?php            } ?>        <tr>  
    <?php }
   } 
	}
   else {
	$shop_page_id = wc_get_page_id( 'shop' );   
	$shop_page_url = $shop_page_id ? get_permalink( $shop_page_id ) : '';
	?>  
   <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">		
		  <a class="woocommerce-Button button" href="<?php echo $shop_page_url;?>">
			<?php _e( 'Go shop' , 'woocommerce' ) ?>		</a> <?php _e( 'No auctions available yet.' , 'woo_ua' ) ?>
		   </div>
                 
   <?php } ?>
  
</table>