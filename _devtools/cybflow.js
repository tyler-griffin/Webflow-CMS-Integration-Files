
/* https://cybflow.webflow.io/ */

window.onload = function(e) { 

    $(document).ready(function() {

        function makeTitle(slug) {
            var words = slug.split('-');
            for (var i = 0; i < words.length; i++) {
                var word = words[i];
                words[i] = word.charAt(0).toUpperCase() + word.slice(1);
            }
            return words.join(' ');
        }

        $('#parse').click(function(){

            window.builderData = '';

            $('#data-parsing').html($('#input').val());


            /* --- Create data for customSortedListBlocks() in pagebuilder_custom_helper.php --- */

            $('#data-parsing').find('[cybdata="list"]').each(function() {
                $(this).children().remove();
            });

            $('#data-parsing').find('[cybdata="buttontext"]').each(function() {
                $(this).removeAttr('cybdata');
            });

            $('#data-parsing').find('[cybdata="block"]').each(function() {

                var title = $(this).attr('cybkey');
                if(!title) { 
                    title = makeTitle($(this).attr('class'));
                }
                var slug = title.replace(/ /g, "-").replace(/[^\w-]+/g, "");
                slug = slug.toLowerCase();

                builderData += '$items[] = [';
                    builderData += '"title" => "' + title + '",';
                    builderData += '"value" => "' + slug + '",';
                    builderData += '"block" => [';
                        builderData += '"type" => 2,';
                        builderData += '"title" => "' + title + '",';
                        builderData += '"settings" => [';
                            builderData += '["AMSD Columns", 2],';
                            builderData += '["AMSD Delete", "false"],';
                            builderData += '["AMSD Edit", "false"],';
                            builderData += '["Heading Read Only", "true"],';
                            builderData += '["Table", "amsd_strings"],';
                            builderData += '["Template", "' + slug + '"]';
                        builderData += '],"items" => [';

                            $(this).find('[cybdata]').each(function() {

                                var config = $(this).attr('cybdata');
                                var key = $(this).attr('cybkey');
                                if(!key) { key = config.charAt(0).toUpperCase() + config.slice(1); }

                                if(config == 'list') {
                                    
                                    config = 'sorted_list';

                                } else if(config == 'img') {

                                    config = 'photo';

                                } else if(config == 'bg') {

                                    config = 'focused_img';

                                } else if(config == 'icon') {

                                    config = 'font_awesome';
                                    
                                } 
                                
                                if(config == 'link') {
                                    builderData += '["key" => "' + key + ' Text","config" => ""],';
                                    builderData += '["key" => "' + key + ' URL","config" => "url"],';
                                } else {
                                    builderData += '["key" => "' + key + '","config" => "' + config + '"],';
                                }
                                
                            });

                        builderData += ']';
                    builderData += '],"dev" => true';
                builderData += '];';
            });

            $('#data-output').val(builderData);
            $('#data-parsing').html('');

            /* --- CREATE PHP VIEW FOR TEMPLATE FILE --- */

            $('#parsing').html($('#input').val());

            $('#parsing').find('[cybdata]').each(function() {

                var type = $(this).attr('cybdata');
                var key = $(this).attr('cybkey');
                var prefix = "$DATA['";
                var suffix = "']";
                var itemLabel = "LIST_ITEM";

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

                if(type == 'block' || type == 'buttontext') {

                    /* Skip */

                } else if(type == 'list') {
                    
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

            $('#output').val('<? $DATA = strings($block->id); ?>' + cleanedUp);
            $('#parsing').html('');
            $('#output').focus();

        });

        $('.textarea').focus(function() { 
            this.select(); 
        });

    });

}
