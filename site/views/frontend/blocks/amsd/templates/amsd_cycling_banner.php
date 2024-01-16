
<div class="home-banner-section">
	<div data-delay="8000" data-animation="cross" class="slider w-slider" data-autoplay="true" data-easing="ease" data-hide-arrows="false" data-disable-swipe="false" data-autoplay-limit="0" data-nav-spacing="3" data-duration="800" data-infinite="true">
		<div class="slider-mask w-slider-mask">
			<? foreach($amsd["data"] as $k => $ITEM) { ?>
				<? $bannerImage = json_decode($ITEM->focused_img); ?>
				<div class="slide w-slide" <? if(isset($bannerImage)) { ?>style="background-position: <?= $bannerImage->config->{'background-position'} ?>; background-image: url('/image/<?= $bannerImage->id ?>/2000');"<? } ?>>
					<div class="home-banner-content-outer-wrapper">
						<div class="home-banner-content-inner-wrapper">
							<div class="home-banner-text-large"><?= $ITEM->title ?></div>
							<div class="home-banner-text-small"><?= nl2br($ITEM->caption); ?></div>
							<div class="home-banner-buttons-wrapper">

								<? $buttonOne = json_decode($ITEM->button_one); ?>
								<? if($buttonOne->text) { ?>
									<a href="<?= $buttonOne->url ?>" class="cms-btn banner-button w-button"><? if($ITEM->button_one_icon) { ?><span class="button-icon"><i class="<?= $ITEM->button_one_icon ?>"></i></span> <? } ?><?= $buttonOne->text ?></a>
								<? } ?>

								<? $buttonTwo = json_decode($ITEM->button_two); ?>
								<? if($buttonTwo->text) { ?>
									<a href="<?= $buttonTwo->url ?>" class="cms-btn cms-btn-secondary banner-button w-button"><? if($ITEM->button_two_icon) { ?><span class="button-icon"><i class="<?= $ITEM->button_two_icon ?>"></i></span> <? } ?><?= $buttonTwo->text ?></a>
								<? } ?>

							</div>
						</div>
					</div>
				</div>
			<? } ?>
		</div>
		<? if(sizeof($amsd["data"]) > 1) { ?>
			<div class="slider-arrow w-slider-arrow-left">
				<div class="slider-arrow-icon w-icon-slider-left"></div>
			</div>
			<div class="slider-arrow w-slider-arrow-right">
				<div class="slider-arrow-icon w-icon-slider-right"></div>
			</div>
			<div class="slide-nav w-slider-nav w-round"></div>
		<? } ?>
	</div>
</div>
