
<? $backgroundVideoID = '';
if($block->additional_settings['Background Video Vimeo ID']) { 
	$backgroundVideoID = $block->additional_settings['Background Video Vimeo ID'];
} ?>

<div class="home-banner-section">
    <div data-delay="8000" data-animation="cross" class="slider <? if ($backgroundVideoID != '') { ?>with-video-background<? } ?> w-slider" data-autoplay="true" data-easing="ease" data-hide-arrows="false" data-disable-swipe="false" data-autoplay-limit="0" data-nav-spacing="3" data-duration="800" data-infinite="true">
        <div class="slider-mask w-slider-mask">
            <? foreach($amsd["data"] as $k => $ITEM) { ?>
            <? $bannerImage = json_decode($ITEM->banner_image); ?>
            <div class="slide w-slide" <? if(isset($bannerImage) && $backgroundVideoID == '') { ?>style="background-position: <?= $bannerImage->config->{'background-position'} ?>; background-image: url('/image/<?= $bannerImage->id ?>/2000');"<? } else if ($backgroundVideoID != '') { ?>style="background-image: none;"<? } ?>>
                <div class="home-banner-content-outer-wrapper">
                    <div class="home-banner-content-inner-wrapper">
                        <div class="home-banner-text-large"><?= $ITEM->title ?></div>
                        <div class="home-banner-text-small"><?= nl2br($ITEM->caption); ?></div>
                        <div class="home-banner-buttons-wrapper">
                            <? foreach (json_decode($ITEM->buttons) as $bk => $BUTTON) { ?>
                            <a href="<?= $BUTTON->url ?>" class="cms-btn <? if(($bk+1)%2 == 0) { ?>cms-btn-outlined-white<? } ?> banner-button"><? if($BUTTON->icon) { ?><span class="button-icon <? if(($bk+1)%2 == 0) { ?>white<? } ?>"><i class="<?= $BUTTON->icon ?>"></i></span> <? } ?><?= $BUTTON->title ?></a>
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

	<? if($backgroundVideoID != '') { ?>
		<div class="video-background-wrapper-outer visible">
			<div class="video-background-wrapper-inner">
				<div class="video-background w-embed w-iframe"><iframe class="cms-video-vimeo" src="https://player.vimeo.com/video/<?= $backgroundVideoID ?>?background=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe></div>
			</div>
		</div>
	<? } ?>

</div>