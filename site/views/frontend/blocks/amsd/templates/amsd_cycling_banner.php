
<?

if($block->additional_settings['Background Video URL'] && $block->additional_settings['Display Background Video']) { 
    $video = parseVideo($block->additional_settings['Background Video URL']);
}

$bannerDelay = '8000';
if($block->additional_settings['Time Between Banners (Milliseconds)']) {
    $bannerDelay = $block->additional_settings['Time Between Banners (Milliseconds)'];
}

$bannerTransition = '800';
if($block->additional_settings['Transition Time During Cycling (Milliseconds)']) {
    $bannerTransition = $block->additional_settings['Transition Time During Cycling (Milliseconds)'];
}

?>

<div class="home-banner-section">
    <div data-delay="<?= $bannerDelay ?>" data-animation="cross" class="slider <? if (isset($video['id'])) { ?>with-video-background<? } ?> w-slider" data-autoplay="true" data-easing="ease" data-hide-arrows="false" data-disable-swipe="false" data-autoplay-limit="0" data-nav-spacing="3" data-duration="<?= $bannerTransition ?>" data-infinite="true">
        <div class="slider-mask w-slider-mask">
            <? foreach($amsd["data"] as $k => $ITEM) { ?>
            <? $bannerImage = json_decode($ITEM->focused_img); ?>
            <div class="slide w-slide" <? if(isset($bannerImage) && !isset($video['id'])) { ?>style="background-position: <?= $bannerImage->config->{'background-position'} ?>; background-image: url('/image/<?= $bannerImage->id ?>/2000');"<? } else if (isset($video['id'])) { ?>style="background-image: none;"<? } ?>>
                <div class="home-banner-content-outer-wrapper">
                    <div class="home-banner-content-inner-wrapper">
                        <div class="home-banner-text-large"><?= $ITEM->title ?></div>
                        <div class="home-banner-text-small"><?= nl2br($ITEM->caption); ?></div>
                        <div class="home-banner-buttons-wrapper">
                            <? foreach (json_decode($ITEM->buttons) as $bk => $BUTTON) { ?>
                            <a href="<?= $BUTTON->url ?>" class="cms-btn <? if(($bk+1)%2 == 0) { ?>cms-btn-outlined-white<? } ?>"><? if($BUTTON->icon) { ?><span class="button-icon <? if(($bk+1)%2 == 0) { ?>white<? } ?>"><i class="<?= $BUTTON->icon ?>"></i></span> <? } ?><?= $BUTTON->title ?></a>
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

	<? if(isset($video['id']) && $block->additional_settings['Display Background Video']) { ?>

        <? if($video["host"] == 'youtube') {
            $backgroundVideoSrc = "https://www.youtube.com/embed/" . $video['id'] . "?autoplay=1&amp;controls=0&amp;rel=0&amp;mute=1&amp;loop=1&amp;playlist=" . $video['id'];
        } else if($video["host"] == 'vimeo') {
            $backgroundVideoSrc = "https://player.vimeo.com/video/" . $video['id'] . "?background=1";
        } ?>

        <div class="video-background-wrapper-outer visible">
            <div class="video-background-wrapper-inner">
                <div class="video-background">
                    <iframe class="cms-video-vimeo" src="<?= $backgroundVideoSrc ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>
                </div>
            </div>
        </div>
        
	<? } ?>

</div>