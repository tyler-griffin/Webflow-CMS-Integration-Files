
<div class="contact-info-wrapper">
	<? if($DATA['Address']) { ?>
		<div class="contact-info-item">
			<div class="contact-info-icon"><i class="fas fa-map-marker-alt"></i></div>
			<a href="<?= $DATA['Address URL'] ?>" class="contact-info-link"><?= nl2br($DATA['Address']); ?></a>
		</div>
	<? } ?>
	<? if($DATA['Phone']) { ?>
		<div class="contact-info-item">
			<div class="contact-info-icon"><i class="fas fa-phone"></i></div>
			<a href="tel:+1<?= $DATA['Phone'] ?>" class="contact-info-link">Phone:<br><?= $DATA['Phone'] ?></a>
		</div>
	<? } ?>
	<? if($DATA['Fax']) { ?>
		<div class="contact-info-item">
			<div class="contact-info-icon"><i class="fas fa-fax"></i></div>
			<a href="#" class="contact-info-link">Fax:<br><?= $DATA['Fax'] ?></a>
		</div>
	<? } ?>
</div>
