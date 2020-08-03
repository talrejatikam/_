<style>
.section{
	margin-left: -20px;
	margin-right: -20px;
	font-family: "Raleway",san-serif;
	overflow-x: hidden;
}
.section h1{
	text-align: center;
	text-transform: uppercase;
	color: #808a97;
	font-size: 35px;
	font-weight: 700;
	line-height: normal;
	display: inline-block;
	width: 100%;
	margin: 50px 0 0;
}
.section ul{
	list-style-type: disc;
	padding-left: 15px;
}
.section:nth-child(even){
	background-color: #f1f1f1;
}
.section:nth-child(odd){
	background-color: #fff;
}
.section .section-title img{
	display: table-cell;
	vertical-align: middle;
	width: auto;
	margin-right: 15px;
}
.section h2,
.section h3 {
	display: inline-block;
	vertical-align: middle;
	padding: 0;
	font-size: 24px;
	font-weight: 700;
	color: #808a97;
	text-transform: uppercase;
}

.section .section-title h2{
	display: table-cell;
	vertical-align: middle;
	line-height: 25px;
}

.section-title{
	display: table;
}

.section h3 {
	font-size: 14px;
	line-height: 28px;
	margin-bottom: 0;
	display: block;
}

.section p{
	font-size: 13px;
	margin: 25px 0;
}
.section ul li{
	margin-bottom: 4px;
}
.landing-container{
	max-width: 750px;
	margin-left: auto;
	margin-right: auto;
	padding: 50px 0 30px;
}
.landing-container:after{
	display: block;
	clear: both;
	content: '';
}
.landing-container .col-1,
.landing-container .col-2{
	float: left;
	box-sizing: border-box;
	padding: 0 15px;
}
.landing-container .col-1 img{
	width: 100%;
}
.landing-container .col-1{
	width: 55%;
}
.landing-container .col-2{
	width: 45%;
}

.landing-container h2 {
	border: 0;
	background: transparent;
}
.premium-cta{
	background-color: #808a97;
	color: #fff;
	border-radius: 6px;
	padding: 20px 15px;
}
.premium-cta:after{
	content: '';
	display: block;
	clear: both;
}
.premium-cta p{
	margin: 7px 0;
	font-size: 14px;
	font-weight: 500;
	display: inline-block;
	width: 60%;
}
.premium-cta a.button{
	border-radius: 6px;
	height: 60px;
	float: right;
	background: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/upgrade.png) #ff643f no-repeat 13px 13px;
	border-color: #ff643f;
	box-shadow: none;
	outline: none;
	color: #fff;
	position: relative;
	padding: 9px 50px 9px 70px;
}
.premium-cta a.button:hover,
.premium-cta a.button:active,
.premium-cta a.button:focus{
	color: #fff;
	background: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/upgrade.png) #971d00 no-repeat 13px 13px;
	border-color: #971d00;
	box-shadow: none;
	outline: none;
}
.premium-cta a.button:focus{
	top: 1px;
}
.premium-cta a.button span{
	line-height: 13px;
}
.premium-cta a.button .highlight{
	display: block;
	font-size: 20px;
	font-weight: 700;
	line-height: 20px;
}
.premium-cta .highlight{
	text-transform: uppercase;
	background: none;
	font-weight: 800;
	color: #fff;
}

.section.one{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/01-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.two{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/02-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.three{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/03-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.four{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/04-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.five{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/05-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.six{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/06-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.seven{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/07-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.eight{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/08-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.nine{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/09-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.ten{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/10-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.eleven{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/11-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.twelve{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/12-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.thirteen{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/13-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.fourteen{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/14-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.fifteen{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/15-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.sixteen{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/16-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.seventeen{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/17-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.eighteen{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/18-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.nineteen{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/19-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.twenty{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/20-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.twenty-one{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/21-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}
.section.twenty-two{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/22-bg.png); background-repeat: no-repeat; background-position: 15% 100%
}
.section.twenty-three{
	background-image: url(<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/23-bg.png); background-repeat: no-repeat; background-position: 85% 75%
}

@media (max-width: 768px) {
	.section{margin: 0}
	.premium-cta p{
		width: 100%;
	}
	.premium-cta{
		text-align: center;
	}
	.premium-cta a.button{
		float: none;
	}
}

@media (max-width: 480px){
	.wrap{
		margin-right: 0;
	}
	.section{
		margin: 0;
	}
	.landing-container .col-1,
	.landing-container .col-2{
		width: 100%;
		padding: 0 15px;
	}
	.section-odd .col-1 {
		float: left;
		margin-right: -100%;
	}
	.section-odd .col-2 {
		float: right;
		margin-top: 65%;
	}
}

@media (max-width: 320px){
	.premium-cta a.button{
		padding: 9px 20px 9px 70px;
	}

	.section .section-title img{
		display: none;
	}
}
</style>
<div class="landing">
	<div class="section section-cta section-odd">
		<div class="landing-container">
			<div class="premium-cta">
				<p>
					<?php echo sprintf( __( 'Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Points and Rewards%2$s to benefit from all features!', 'yith-woocommerce-points-and-rewards' ), '<span class="highlight">', '</span>' ); ?>
				</p>
				<a href="<?php echo $this->get_premium_landing_uri(); ?>" target="_blank" class="premium-cta-button button btn">
					<span class="highlight"><?php _e( 'UPGRADE', 'yith-woocommerce-points-and-rewards' ); ?></span>
					<span><?php _e( 'to the premium version', 'yith-woocommerce-points-and-rewards' ); ?></span>
				</a>
			</div>
		</div>
	</div>
	<div class="one section section-even clear">
		<h1><?php _e( 'Premium Features', 'yith-woocommerce-points-and-rewards' ); ?></h1>
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/01.png" alt="<?php _e( 'Get points on every order', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/01-icon.png" alt="icon 01"/>
					<h2><?php _e( 'Get points on every order', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'Reward loyal customers by giving them the possibility to benefit of %1$sspecial discounts%2$s thanks to the points collected with their shopping.%3$sAnother reason to encourage your users to prefer your e-commerce store instead of another one.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>', '<br>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="two section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/02-icon.png" alt="icon 02" />
					<h2><?php _e( 'Restrictions on collected points', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'If you are afraid to become a victim of your loyalty rewards system, just set a %1$smaximum amount%2$s for discounts that users can benefit from. This limit will be calculated according to the number of products in cart. Moreover, you can either set global restrictions or more specific ones on %1$scategory or product level%2$s.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/02.png" alt="<?php _e( 'Limit for collected points', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>
	<div class="three section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/03.png" alt="<?php _e( 'User role', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/03-icon.png" alt="icon 03" />
					<h2><?php _e( 'User role', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'The criteria for awarding of points can vary according to the plugin configuration: among the many available settings, you can assign a different number of points to users based on their %1$sWordPress role%2$s. You can also have different settings by user role for rewards.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="four section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/04-icon.png" alt="icon 04" />
					<h2><?php _e( 'Expiry date ', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'Setting an expiry date for points collected can help you %1$sincrease the sales volume%2$s of your store, because this actually pushes your users to keep purchasing so they do not lose points collected so far and the discount they can get.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/04.png" alt="<?php _e( 'Expiry date', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>
	<div class="five section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/05.png" alt="<?php _e( 'Automatic emails', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/05-icon.png" alt="icon 05" />
					<h2><?php _e( 'Automatic emails', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'Keep your users always updated by enabling %1$sautomatic emails%2$s for every time their points balance is updated. They will be able to track their points-awarding actions and be always updated about their points balance.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
				<p>
					<?php echo sprintf( __( 'And that\'s not all. If you have set an %1$sexpiry date for points%2$s of your shop, enable also automatic emails that will be sent a certain number of days before the expiration, so they will not forget about them nor lose their bonus.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="six section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/06-icon.png" alt="icon 06" />
					<h2><?php _e( 'Point removal', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'Sometimes it happens that you have to %1$scancel%2$s or %1$srefund%2$s an order, one that maybe has already credited points to the user. In case you want remove these points from their balance, you\'ll just have to enable the option and the system will automatically update it.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/06.png" alt="<?php _e( 'Point removal', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>
	<div class="seven section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/07.png" alt="<?php _e( 'Extra points', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/07-icon.png" alt="icon 07" />
					<h2><?php _e( 'Extra points', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'Beyond usual points credited at each purchase, %1$sthe plugin allows you to gift extra points%2$s when certain conditions occur: either after registration in the shop or to thank them for reviewing one product, when a certain spend threshold has been reached or a certain number of points or orders is collected.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="eight section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/08-icon.png" alt="icon 08" />
					<h2><?php _e( 'Label customisation', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'All labels displayed to users can be %1$scustomised%2$s from plugin settings panel. In few and quick moves you can change your texts as you prefer.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/08.png" alt="<?php _e( 'Labels', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>
	<div class="nine section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/09.png" alt="<?php _e( 'Product exclusion', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/09-icon.png" alt="icon 09" />
					<h2><?php _e( 'Pre-activation purchases', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'The premium version of %1$sYITH WooCommerce Points and Rewards%2$s allows you to include in your point system also users who have purchased before its activation.%3$sIn this case, it will be up to you to inform them about the great news reserved to them and tell them that their point credit is already up-to-date and availble in "My Account" page.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>', '<br>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="ten section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/10-icon.png" alt="icon 10" />
					<h2><?php _e( 'User messages', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'Encourage your users to purchase from your shop by showing in product detail page the %1$snumber of points%2$s due for that purchase. But that\'s not the only benefit: enable message displaying in cart page to inform users both about points due for that purchase and the possible %1$sdiscount%2$s they can benefit with points collected sofar.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/10.png" alt="<?php _e( 'User messages', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>
	<div class="eleven section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/11.png" alt="<?php _e( '"My Account" summary', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/11-icon.png" alt="icon 11" />
					<h2><?php _e( '"My Account" summary', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'Any user of your shop must be able to see their own point credit any time they want. This is the reason why the plugin adds a dedicated section into %1$s"My account"%2$s page where users can see the total credit available and all actions that have given them points.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="twelve section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/12-icon.png" alt="icon 12" />
					<h2><?php _e( 'Widgets', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( '3 widgets of different type. The first one to show logged-in users their %1$scurrent points balance%2$s and two more widgets for the admin only, who will be able to immediately see users that %1$shave collected more points and redeemed the highest rewards%2$s in the shop.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/12.png" alt="<?php _e( 'Bulk actions', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>
	<div class="thirteen section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/13.png" alt="<?php _e( 'Points history', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/13-icon.png" alt="icon 13" />
					<h2><?php _e( 'Points history', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'Thanks to %1$sPoint List%2$s shortcode, your users can consult their points history at any time and check action details of orders in the shop.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="fourteen section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/14-icon.png" alt="icon 14" />
					<h2><?php _e( 'Fixed or percentage discount', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'Choose the discount typology to apply when %1$susers redeem their points%2$s, from fixed or percentage, in relation to the price of products in cart', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/14.png" alt="<?php _e( 'Fixed or percentage discount', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>
	<div class="fifteen section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/15.png" alt="<?php _e( 'Reset points', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/15-icon.png" alt="icon 13" />
					<h2><?php _e( 'Reset points', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'You can reset your customers\' points at any time. You can either do that for all customers or for just some of them on every single customer\'s points page or with bulk actions.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="sixteen section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/16-icon.png" alt="icon 16" />
					<h2><?php _e( 'Points management for user roles', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php _e( 'Hot news! Now you can limit the points earning or redeeming only to those users with a specified role on your site', 'yith-woocommerce-points-and-rewards' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/16.png" alt="<?php _e( '', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>
	<div class="seventeen section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/17.png" alt="<?php _e( '', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/17-icon.png" alt="icon 17" />
					<h2><?php _e( 'Shop manager', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'Do you wish your %1$sshop manager%2$s to take care of the loyalty rewards programme without giving them %1$sadmin capabilities%2$s in your shop?%3$s No worries, we have the right solution. ', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>', '<br>' ); ?>
				</p>
				<p>
					<?php echo sprintf( __( 'You only need to enable the option to give the shop manager the %1$sbenefits to editing%2$s the points balance of each user. ', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="eighteen section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/18-icon.png" alt="icon 18" />
					<h2><?php _e( 'Points per product', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php _e( 'Give your users a complete view of the points assigned to each product so that they can immediately notice the opportunity made available. ', 'yith-woocommerce-points-and-rewards' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/18.png" alt="<?php _e( '', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>
	<div class="nineteen section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/19.png" alt="<?php _e( '', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/19-icon.png" alt="icon 19" />
					<h2><?php _e( 'Free shipping', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'From now on, also those purchasing by redeeming their points can %1$sbenefit of free shipping%2$s.%3$s Decide whether offering this opportunity or not. ', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>', '<br>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="twenty section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/20-icon.png" alt="icon 20" />
					<h2><?php _e( 'Link to "My points"', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php _e( 'A dedicated link in the "My Account" page, where users have direct access to an oversight of their points. They will be able to see the date, status, amount of points and the related order. ', 'yith-woocommerce-points-and-rewards' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/20.png" alt="<?php _e( '', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>

	<div class="twenty-one section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/21.png" alt="<?php _e( 'Import and export', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/21-icon.png" alt="icon 21" />
					<h2><?php _e( 'Import and Export', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'There may be thousands reasons why you have to import or export your customers\' points. Thanks to the dedicated tab, you can do that in few simple steps and your customers will never lose a point!', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>', '<br>' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="twenty-two section section-odd clear">
		<div class="landing-container">
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/22-icon.png" alt="icon 22" />
					<h2><?php _e( 'Everything at the right moment', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'You may want to assign points to your users immediately after the order is placed, or make sure, first, that the order is paid.%3%s No worries. You can choose yourself on which order status the points will be assigned to your customers: %1$sOrder completed%2$s, %1$sOrder processing%2$s, %1$s Payment completed%2$s. You will never lose control!', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>', '<br>' ); ?>
				</p>
			</div>
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/22.png" alt="<?php _e( '', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
		</div>
	</div>

	<div class="twenty-three section section-even clear">
		<div class="landing-container">
			<div class="col-1">
				<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/23.png" alt="<?php _e( 'Multi-currency support', 'yith-woocommerce-points-and-rewards' ); ?>" />
			</div>
			<div class="col-2">
				<div class="section-title">
					<img src="<?php echo YITH_YWPAR_ASSETS_URL; ?>/images/23-icon.png" alt="icon 19" />
					<h2><?php _e( 'Multi-currency support', 'yith-woocommerce-points-and-rewards' ); ?></h2>
				</div>
				<p>
					<?php echo sprintf( __( 'If your shop is running on multiple currencies and you want to set up specific points conversion rules for each currency, you can! You will be able to do that both for awarding and for redeeming points, and for each customer user role that you set up.', 'yith-woocommerce-points-and-rewards' ), '<b>', '</b>', '<br>' ); ?>
				</p>
			</div>
		</div>
	</div>

	<div class="section section-cta section-odd">
		<div class="landing-container">
			<div class="premium-cta">
				<p>
					<?php echo sprintf( __( 'Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Points and Rewards%2$s to benefit from all features!', 'yith-woocommerce-points-and-rewards' ), '<span class="highlight">', '</span>' ); ?>
				</p>
				<a href="<?php echo $this->get_premium_landing_uri(); ?>" target="_blank" class="premium-cta-button button btn">
					<span class="highlight"><?php _e( 'UPGRADE', 'yith-woocommerce-points-and-rewards' ); ?></span>
					<span><?php _e( 'to the premium version', 'yith-woocommerce-points-and-rewards' ); ?></span>
				</a>
			</div>
		</div>
	</div>
</div>
