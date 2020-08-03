<?php

/**
 * Admin notification when auction won by user. (HTML)
 *
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

?>
<?php do_action('woocommerce_email_header', $email_heading, $email); ?>
<?php
$product_id = $email->object['product_id']; 
$product = wc_get_product($product_id);
$auction_url = $email->object['url_product'];
$user_name = $email->object['user_name'];
$auction_title = $product->get_title();
$auction_bid_value = wc_price($product->get_woo_ua_current_bid());
$thumb_image = $product->get_image( 'thumbnail' );
$userlink = add_query_arg( 'user_id', $email->object['user_id'] , admin_url( 'user-edit.php' ) );
?>

<p><?php printf( __( "Hi Admin,", 'woo_ua' )); ?></p>

<p><?php printf( __( "The auction has expired and won by user. Auction url <a href='%s'>%s</a>.", 'woo_ua' ),$auction_url,$auction_title); ?></p>
<p><?php printf( __( "Here are the details : ", 'woo_ua' )); ?></p>
<table>
     <tr>	 
	 <td><?php echo __( 'Image', 'woo_ua' ); ?></td>
	 <td><?php echo __( 'Product', 'woo_ua' ); ?></td>
	 <td><?php echo __( 'Winning bid', 'woo_ua' ); ?></td>	
	 <td><?php echo __( 'Winner', 'woo_ua' ); ?></td>	
	 </tr>
    <tr>
      <td><?php echo $thumb_image;?></td>
	  <td><a href="<?php echo $auction_url ;?>"><?php echo $auction_title; ?></a></td>
      <td><?php echo $auction_bid_value;  ?></td>
      <td><a href="<?php echo $userlink;?>"><?php echo $user_name;  ?></a></td>
    </tr>
</table>

<?php do_action('woocommerce_email_footer', $email);?>