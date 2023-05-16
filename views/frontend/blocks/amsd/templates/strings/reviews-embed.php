
<? $DATA = strings($block->id); ?>

<? if($DATA['Reviewability ID']) { ?>
    <div class="text-block">
        <div data-bid="<?= $DATA['Reviewability ID'] ?>" data-url="https://cybernautic.reviewability.com" ><script src="https://widget.reviewability.com/js/widgetAdv.min.js" async></script></div><script class="json-ld-content" type="application/ld+json"></script>
    </div>
<? } ?>
