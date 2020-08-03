<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package    YITH
 */

/**
 * Text Plugin Admin View
 *
 * @package    YITH
 * @author     Emanuela Castorina <emanuela.castorina@yithemes.it>
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$id   = $this->_panel->get_id_field( $option['id'] );
$name = $this->_panel->get_name_field( $option['id'] );

$points = ( isset( $db_value['points'] ) ) ? $db_value['points'] : 1;
$money  = ( isset( $db_value['money'] ) ) ? $db_value['money'] : 1;
?>
<div id="<?php echo esc_attr( $id ); ?>-container"
					<?php
					if ( isset( $option['deps'] ) ) :
						?>
	data-field="<?php echo esc_attr( $id ); ?>" data-dep="<?php echo esc_attr( $this->get_id_field( $option['deps']['ids'] ) ); ?>" data-value="<?php echo esc_attr( $option['deps']['values'] ); ?>" <?php endif ?> class="yit_options rm_option rm_input rm_text">
	<div class="option">
		<input type="text" name="<?php echo esc_attr( $name ); ?>[points]" id="<?php echo esc_attr( $id ); ?>-points" value="<?php echo esc_attr( $points ); ?>"/> <?php esc_html_e( 'Points', 'yith-woocommerce-points-and-rewards' ); ?> <input type="text" name="<?php echo esc_attr( $name ); ?>[money]" id="<?php echo esc_attr( $id ); ?>-money" value="<?php echo esc_attr( $money ); ?>"/> <?php echo esc_html( get_woocommerce_currency_symbol() ); ?>
	</div>
	<span class="description"><?php echo wp_kses_post( $option['desc'] ); ?></span>

	<div class="clear"></div>
</div>

