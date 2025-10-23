
<? /*

	* Replace WF_SITE and WF_PAGE in <html data-wf-site="WF_SITE" data-wf-page="WF_PAGE"> with the corresponding from the webflow export


	* Additional configuration is necessary in:

		/assets/css/cms.css
		footer.php


	* Double check the files below to make sure common variables are correct such as ALERT_BAR_BLOCK_ID, $DEV_CONFIG, $COMMON_ITEMS

		/config/globals.php
		/config/constants.php

*/ ?>

<? define("M", _mobileCheck()); ?>

<!DOCTYPE html>

<html class="w-mod-js" data-wf-site="WF_SITE" data-wf-page="WF_PAGE" lang="en-US">

<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="generator" content="Webflow">

	<? seoMeta($cms); ?>

	<link rel="stylesheet" href="<?= assets("css/cms-frontend-framework.css") ?>" type="text/css" />

	<link rel="stylesheet" type="text/css" href="<?= bustCache("scss/base.scss") ?>">

	<script type="text/javascript" src="<?= assets("js/jquery/3.4.1/jquery.min.js") ?>"></script>

	<script type="text/javascript" src="<?= assets("js/helpers/head.min.js?v=3") ?>"></script>
	<script type="text/javascript" src="<?= assets("js/helpers/maskedInput.js") ?>"></script>

	<!--[if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif]-->

	<script type="text/javascript" src="<?= assets("js/cms-frontend-framework.js") ?>"></script>
	<script type="text/javascript" src="<?= bustCache("js/cms.js") ?>"></script>

</head>

<body class="cms-frontend" data-page-id="<?= $page->id ?>">

	<div class="header-section">

		<div data-collapse="medium" data-duration="400" role="banner" class="navbar w-nav">
			<div class="nav-container w-container">
				<a href="/home" class="logo-home-link w-nav-brand" title="Home">
					<img src="/image/<?= $DEV_CONFIG['Logo'] ?>/600" alt="<?= $owner->site_title ?>" class="logo-image">
					<img src="/image/<?= $DEV_CONFIG['Logo on Scroll'] ?>/600" alt="<?= $owner->site_title ?>" class="logo-scrolled">
				</a>
				<div class="menu-button w-nav-button">
					<div class="w-icon-nav-menu"></div>
				</div>
				<? printWebflowMenu(); ?>
			</div>
		</div>
	</div>

