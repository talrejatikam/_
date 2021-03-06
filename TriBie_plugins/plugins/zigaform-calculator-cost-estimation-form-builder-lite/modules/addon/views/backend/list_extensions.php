<?php
/**
 * Intranet
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2015 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://wordpress-cost-estimator.zigaform.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );}
?>

<div class="sfdclauncher sfdc-block1-container"  >
	<div class="space20"></div>
	<div class="sfdc-row">
		<div class="col-lg-12">

 <div class="widget widget-padding span12">
				<div class="widget-header">
					<i class="fa fa-plug"></i>
					<h5>
						<?php echo __( 'Extensions', 'FRocket_admin' ); ?>
					</h5>
				   
				</div>  
				<div class="widget-body">
					 <ul>
						<?php foreach ( $query as $key => $value ) { ?>
							<li>
									<div data-name="<?php echo $value->add_name; ?>"  class="zgfm-ext-block effect8">
									<div class="zgfm-ext-title"><?php echo $value->add_title; ?></div>
												<div class="zgfm-ext-info"><?php echo urldecode( $value->add_info ); ?></div>
									<div class="zgfm-ext-buttons">
													<?php if ( UIFORM_DEMO === 0 ) { ?>
													
														<?php if ( ZIGAFORM_C_LITE === 0 ) { ?>
													
															<?php if ( intval( $value->flag_status ) === 0 ) { ?>
															<!-- Indicates a successful or positive action -->
																	<button data-status='1' onclick="javascript:zgfm_back_addon.listaddon_changeStatus(this);" type="button" class="btn btn-success"><?php echo __( 'Enable', 'FRocket_admin' ); ?></button>
														<?php } else { ?>       
																	<!-- Indicates caution should be taken with this action -->
																	<button data-status='0' onclick="javascript:zgfm_back_addon.listaddon_changeStatus(this);" type="button" class="btn btn-warning"><?php echo __( 'Disable', 'FRocket_admin' ); ?></button>
														<?php } ?>   
													
															<?php
														} else {
															if ( strval( $value->add_name ) === 'woocommerce' ) {
																?>
																<?php if ( intval( $value->flag_status ) === 0 ) { ?>
															<!-- Indicates a successful or positive action -->
																	<button data-status='1' onclick="javascript:zgfm_back_addon.listaddon_changeStatus(this);" type="button" class="btn btn-success"><?php echo __( 'Enable', 'FRocket_admin' ); ?></button>
															<?php } else { ?>       
																		<!-- Indicates caution should be taken with this action -->
																		<button data-status='0' onclick="javascript:zgfm_back_addon.listaddon_changeStatus(this);" type="button" class="btn btn-warning"><?php echo __( 'Disable', 'FRocket_admin' ); ?></button>
															<?php } ?>    
																<?php
															} else {
																?>
																		<button data-blocked-feature="This extension" onclick="javascript:rocketform.showFeatureLocked(this);;" type="button" class="btn btn-danger"><?php echo __( 'locked', 'FRocket_admin' ); ?> <i class="fa fa-lock"></i> </button>
																		
																<?php
															}
														}
														?>
																  
																	
													<?php } ?>            
									</div>
								</div>

							</li>	
						<?php } ?>
					</ul>
				</div>	
</div>

		</div>
	 </div>
</div>        	
