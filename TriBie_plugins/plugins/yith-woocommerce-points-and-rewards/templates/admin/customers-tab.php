<?php
/**
 * Admin Customer Tab.
 *
 * @package YITH
 *
 * @var int    $user_id
 * @var string $type
 */

?>
<div class="wrap par-wrap">
	<h2><?php esc_html_e( 'Customer\'s Points', 'yith-woocommerce-points-and-rewards' ); ?>
		<?php
		if ( isset( $_GET['action'] ) && isset( $link ) ) :
			?>
			<a href="<?php echo esc_url( $link ); ?>"
			class="add-new-h2"><?php esc_html_e( 'Back to list', 'yith-woocommerce-points-and-rewards' ); ?></a><?php endif ?>
	</h2>

	<?php
	if ( 'customer' === $type ) :

		$user = new WC_Customer( $user_id );
		$arg  = remove_query_arg( array( 'paged', 'orderby', 'order' ) );
		$name = '';
		if ( $user->get_first_name() || $user->get_last_name() ) {
			$name = sprintf( __( '#%1$d - %2$s %3$s', 'yith-woocommerce-points-and-rewards' ), $user->get_id(), $user->get_first_name(), $user->get_last_name() );
		} else {
			$name = sprintf( __( '#%1$d - %2$s', 'yith-woocommerce-points-and-rewards' ), $user->get_id(), $user->get_display_name() );
		}
		$points = get_user_meta( $user_id, '_ywpar_user_total_points', true );
		?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="ywpar_user_info_wrapper">
						<div class="ywpar_user_info">
							<h2><?php echo esc_html( $name ); ?> </h2>
							<p><?php wp_kses_post( printf( wp_kses_post( __( 'Current points: <strong>%d</strong> ', 'yith-woocommerce-points-and-rewards' ) ), esc_html( $points ) ) ); ?></p>
						</div>
						<div class="ywpar_update_point">
							<form method="post" class="ywpar_update_point_form">
								<h2><?php esc_html_e( 'Update user points', 'yith-woocommerce-points-and-rewards' ); ?></h2>
								<p><?php esc_html_e( 'You can either add or remove points. Insert positive numbers to add points to the customer\'s credit or negative values to remove points.', 'yith-woocommerce-points-and-rewards' ); ?></p>
								<div class="ywpar-input-wrapper">
									<input type="number" value="" name="user_points" placeholder="0"/>
									<input type="hidden" name="action" value="save"/>
									<input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>"/>
									<?php wp_nonce_field( 'update_points', 'ywpar_update_points' ); ?>
									<input type="submit" class="ywpar_update_points button button-primary action"
										value="<?php esc_attr_e( 'Update Points', 'yith-woocommerce-points-and-rewards' ); ?>"/>
								</div>

							</form>
						</div>
					</div>
					<div class="history-table">
						<div class="meta-box-sortables ui-sortable">
							<h2><?php esc_html_e( 'Point history', 'yith-woocommerce-points-and-rewards' ); ?></h2>

							<?php
							$this->cpt_obj->prepare_items();
							$this->cpt_obj->display();
							?>

						</div>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	<?php else : ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<?php $this->cpt_obj->search_box( 'search', 'search_id' ); ?>
						<form method="post">
							<?php
							$this->cpt_obj->prepare_items();
							$this->cpt_obj->display();
							?>
						</form>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	<?php endif; ?>
</div>
