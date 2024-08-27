
<div class="amsd-select-form-wrapper">

    <? foreach ($page->blocks as $BLOCK) { ?>
                
        <? if($BLOCK->settings['Category Table Block']) { ?>

            <? $categoriesBlock = getBlock($BLOCK->settings['Category Table Block']); ?>

            <? if(sizeof($categoriesBlock["data"]) > 0) { ?>

                <form class="amsd-select-form">
                    <label for="category-select" class="amsd-select-form-label">Filter by Category</label>
                    <div class="form-input select-input-wrapper">
                        <select id="category-select-<?= $BLOCK->id ?>" name="category-select" class="input-field select-input no-floating-label category-filter w-select">
                            <option value="/<?= $page->uri ?>">All Categories</option>
                            <? foreach($categoriesBlock["data"] as $k => $CATEGORY) { ?>
                                <? $categoryPath = '/' . $page->uri . '/category/' . $CATEGORY->slug; ?>
                                <option value="<?= $categoryPath ?>" <? if($_SERVER['REQUEST_URI'] == $categoryPath) { ?>selected<? } ?>><?= $CATEGORY->title ?></option>
                            <? } ?>
                        </select>
                    </div>
                </form>

                <script>
                    $("#category-select-<?= $BLOCK->id ?>").on('change', function() {
                        window.location.href = $(this).val();
                    });
                </script>

            <? } ?>

        <? } ?>

    <? } ?>

    <? $ARCHIVE = newsArchive(); ?>

    <? if(sizeof($ARCHIVE["dates"]) > 0) { ?>
       
        <form class="amsd-select-form">
            <label for="news-select" class="amsd-select-form-label">Filter by Month</label>
            <div class="form-input select-input-wrapper">
                <select id="news-select" class="input-field select-input no-floating-label category-filter w-select">
                    <option value="">
                    <? if($ARCHIVE["selected"]) { ?><?= $ARCHIVE["selected"] ?><? } ?><? if(!$ARCHIVE["selected"]) { ?>Recent Posts<? } ?></option>
                    <? if($ARCHIVE["selected"]) { ?><option value="/<?= $page->uri ?>/">Recent Posts</option><? } ?>
                    <? foreach($ARCHIVE["dates"] as $date) { ?>
                        <? $MONTH = "$date->text $date->year" ?>
                        <? if($ARCHIVE["selected"] != $MONTH) { ?>
                            <option value="/<?= $page->uri ?>/archive/<?= $date->year ?>/<?= $date->month ?>"><?= $MONTH ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>
        </form>

        <script>
            $(function(){
                $('#news-select').on('change', function () {
                    window.location.href = $(this).val();
                });
            });
        </script>

    <? } ?>

</div>