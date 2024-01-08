
/* https://cybflow.webflow.io/ */

window.onload = function(e) { 

    $(document).ready(function() {

        $('#parse').click(function(){

            $('#parsing').html($('#input').val());

            $('#parsing').find('[cybdata]').each(function() {

                var type = $(this).attr('cybdata');
                var key = $(this).attr('cybkey');
                var prefix = "$DATA['";
                var suffix = "']";
                var itemLabel = "LIST_ITEM";

                if($('#common-items').checked) {
                    prefix = "$COMMON_ITEMS['";
                }

                if($('#common-items').checked) {
                    prefix = "$DEV_CONFIG['";
                }

                if($(this).parents('[cybdata="list"]').length) {
                    if($(this).parents('[cybdata="list"]').attr('cybkey')) {
                        itemLabel = $(this).parents('[cybdata="list"]').attr('cybkey').toUpperCase().replace(/ /g,"_") + "_ITEM";
                    }
                    prefix = "$" + itemLabel+ "->";
                    suffix = ""; 
                    if(key) {
                        key = key.replace(/ /g,"_");
                    } else {
                        key = type;
                    }
                }

                if(!key) { key = type.charAt(0).toUpperCase() + type.slice(1); }

                /* --- DATA TYPES --- */

                if(type == 'list') {
                    
                    itemLabel = key.toUpperCase().replace(/ /g,"_") + "_ITEM";
                    $(this).children().not(':first').remove();
                    $(this).children().first().before("<? foreach (json_decode(" + prefix + key + suffix + ") as $k => $" + itemLabel + ") { ?>\n").after("\n<? } ?>");

                } else if(type == 'textarea') {

                    $(this).html("<?= nl2br(" + prefix + key + suffix + "); ?>");

                } else if(type == 'img') {

                    $(this).attr("src","/image/<?= " + prefix + key + suffix + " ?>/1000");

                } else if(type == 'bg') {

                    $(this).attr("style","background-position: <?= json_decode(" + prefix + key + suffix + ")->config->{'background-position'} ?>; background-image: url('/image/<?= json_decode(" + prefix + key + suffix + ")->id ?>/2000');");
                
                } else if(type == 'phone') {

                    $(this).attr("href","tel:+1<?= " + prefix + key + suffix + " ?>");
                    $(this).html("<?= " + prefix + key + suffix + " ?>");

                } else if(type == 'email') {

                    $(this).attr("href","mailto:+<?= " + prefix + key + suffix + " ?>");
                    $(this).html("<?= " + prefix + key + suffix + " ?>");

                } else if(type == 'link') {

                    $(this).attr("href","<?= " + prefix + "url" + suffix + " ?>");
                    $(this).html("<?= " + prefix + "title" + suffix + " ?>");

                } else if(type == 'buttontext') {

                    /* Handled inside of button below, skipping item so it doesn't get treated as a standard text data type */

                } else if(type == 'button') {

                    if($(this).parents('[cybdata="list"]').length) {

                        $(this).attr("href","<?= json_decode(" + prefix + key + suffix + ")->url ?>");

                        if($(this).find('[cybdata="buttontext"]').length) {
                            $(this).find('[cybdata="buttontext"]').html("<?= json_decode(" + prefix + key + suffix + ")->text ?>");
                        } else {
                            $(this).html("<?= json_decode(" + prefix + key + suffix + ")->text ?>");
                        }
                            
                    } else {

                        $(this).attr("href","<?= " + prefix + key + suffix + "['url'] ?>");

                        if($(this).find('[cybdata="buttontext"]').length) {
                            $(this).find('[cybdata="buttontext"]').html("<?= " + prefix + key + suffix + "['text'] ?>");
                        } else {
                            $(this).html("<?= " + prefix + key + suffix + "['text'] ?>");
                        }

                    }

                } else if(type == 'icon') {

                    $(this).html('<i class="<?= ' + prefix + key + suffix + ' ?>">');

                } else if(type == 'url') {
                    
                    $(this).attr("href","<?= " + prefix + key + suffix + " ?>");

                 } else if(type == 'date') {

                    $(this).html('<?= date("F j, Y", strtotime(' + prefix + key + suffix + ')); ?>');     

                 } else if(type == 'time') {

                    $(this).html('<?= date("g:ia", strtotime(' + prefix + key + suffix + ')); ?>');
                    
                } else {

                    $(this).html("<?= " + prefix + key + suffix + " ?>");

                }

            });
 
            $('#parsing').find('[cybdata]').removeAttr('cybdata');
            $('#parsing').find('[cybkey]').removeAttr('cybkey');

            var cleanedUp = $('#parsing').html().replace(/<!--\?/g, '<?').replace(/\?-->/g, '?>').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/=-->/g, '=>').replace(/--->/g, '->');

            $('#output').val(cleanedUp);
            $('#parsing').html('');
            $('.textarea').focus(function() { 
                this.select(); 
            });
            $('#output').focus();

        });

    });

}
