
<?

/*

	* Add webflow.js file from webflow export to /assets/js/ folder

	* Make sure webflow.js is added in the FOOTER, after all content excpet <? seoAnalytics($cms); ?>

	* Add custom scripts from webflow export to /assets/js/scripts.js (custom scripts can be found before the closing </body> tag in the index.html file from the export)

*/

?>

	<div class="cybernautic-tag">
		<? seoCybernauticLogo($cms); ?>
	</div>

	© <?= date("Y"); ?> <?= $owner->site_title ?>

	

	<? printBlock(ALERT_BAR_BLOCK_ID); ?>

	<script type="text/javascript" src="<?= bustCache("js/scripts.js") ?>"></script>
	<script type="text/javascript" src="<?= bustCache("js/webflow.js") ?>"></script>

	<? seoAnalytics($cms); ?>

</body>
</html>
