
<?

/*

	* Replace WF_SITE and WF_PAGE in <html data-wf-site="WF_SITE" data-wf-page="WF_PAGE"> with the corresponding from the webflow export


	* Additional configuration is necessary in:

		/assets/css/cms.css
		footer.php


	* The variables below can be moved to /config/globals.php

		$DEV_CONFIG = strings(1);
		$COMMON_ITEMS = strings(2);
		define("M", _mobileCheck());

*/


?>

<!DOCTYPE html>

<html data-wf-site="WF_SITE" data-wf-page="WF_PAGE">

<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="generator" content="Webflow">

	<? seoMeta($cms); ?>

	<link rel="stylesheet" href="<?= assets("css/cms-frontend-framework.css") ?>" type="text/css" />

	<link rel="stylesheet" type="text/css" href="<?= bustCache("css/cms.css") ?>">

	<script type="text/javascript" src="<?= assets("js/jquery/3.4.1/jquery.min.js") ?>"></script>

	<script type="text/javascript" src="<?= assets("js/helpers/head.min.js?v=2") ?>"></script>
	<script type="text/javascript" src="<?= assets("js/helpers/maskedInput.js") ?>"></script>

	<!--[if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif]-->

	<script type="text/javascript" src="<?= assets("js/cms-frontend-framework.js") ?>"></script>
	<script type="text/javascript" src="<?= bustCache("js/cms.js") ?>"></script>

</head>

<body class="cms-frontend" data-page-id="<?= $page->id ?>">

<? /*

Example header section, change as needed

	<div class="header-section">

		<div data-collapse="medium" data-duration="400" role="banner" class="navbar w-nav">
			<div class="nav-container w-container">
				<a href="/home" class="logo-home-link w-nav-brand" title="Home">
					<img src="/image/<?= $DEV_CONFIG['Logo'] ?>/600" width="300" height="93" alt="<?= $owner->site_title ?>" class="logo-image">
				</a>
				<div class="menu-button w-nav-button">
					<div class="w-icon-nav-menu"></div>
				</div>
				<? // printWebflowMenu() exists in /helpers/custom_helper.php
				printWebflowMenu(); ?>
			</div>
		</div>
	</div>

*/
?>
