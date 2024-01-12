
window.onload = function(e) { 

    $(document).ready(function() {

        $('textarea').attr('maxlength','500000');

        function makeTitle(slug) {
            var words = slug.split('-');
            for (var i = 0; i < words.length; i++) {
                var word = words[i];
                words[i] = word.charAt(0).toUpperCase() + word.slice(1);
            }
            return words.join(' ');
        }

        $('#parse').click(function(){

            window.blockBuilderData = '';
            window.customFieldData = '';

            $('#data-parsing').html($('#input').val());

            /* --- Create data for new block type - goes in customSortedListBlocks() in pagebuilder_custom_helper.php --- */

            if($('#data-parsing').find('[cybblock]').length == 0) {

                /* Assign a cyblock value if none exists - uses the first element's class name */

                var assignedCybblockValue = $('#data-parsing').children().first().attr('class').split(' ')[0];

                $('#data-parsing').children().first().attr('cybblock',assignedCybblockValue);

            }

            $('#data-parsing').find('[cybblock]').each(function() {

                var title =  makeTitle($(this).attr('cybblock'));

                var blockSlug = title.replace(/ /g, "-").replace(/[^\w-]+/g, "");
                blockSlug = blockSlug.toLowerCase();

                blockBuilderData += '$items[] = [';
                blockBuilderData += '\n    "title" => "' + title + '",';
                blockBuilderData += '\n    "value" => "' + blockSlug + '",';
                blockBuilderData += '\n    "block" => [';
                blockBuilderData += '\n        "type" => 2,';
                blockBuilderData += '\n        "title" => "' + title + '",';
                blockBuilderData += '\n        "settings" => [';
                blockBuilderData += '\n            ["AMSD Columns", 2],';
                blockBuilderData += '\n            ["AMSD Delete", "false"],';
                blockBuilderData += '\n            ["AMSD Edit", "false"],';
                blockBuilderData += '\n            ["Heading Read Only", "true"],';
                blockBuilderData += '\n            ["Table", "amsd_strings"],';
                blockBuilderData += '\n            ["Template", "' + blockSlug + '"]';
                blockBuilderData += '\n        ],"items" => [';

                $(this).find('[cybdata]').each(function() {

                    if($(this).parents('[cybdata="list"]').length) {

                        /* Skip items inside of a sorted list item, those get handled inside of the list / sorted_list config */
                        return;

                    }

                    var config = $(this).attr('cybdata');
                    var key = $(this).attr('cybkey');
                    if(!key) { key = config.charAt(0).toUpperCase() + config.slice(1); }

                    var itemSlug = key.replace(/ /g, "_").replace(/[^\w-]+/g, "");
                    itemSlug = itemSlug.toLowerCase();

                    if(config == 'buttontext' || config == 'nav' || config == 'logo' || config == 'footerlogo' || config == 'alertbar' || config == 'popup' || config == 'tag') {

                        /* Skip these field types */
                        return;

                    } else if(config == '' || config == 'title' || config == 'txt') {
                        
                        config = '';

                    } else if(config == 'list') {

                        /* --- Create data for custom sorted list fields - goes in field_builder_custom_preset() in amsd_custom_helper.php --- */
                        
                        config = itemSlug;

                        if($(this).find('[cybdata]').length !== 0) {

                            customFieldData += 'case "' + itemSlug + '":';
                            customFieldData += '\n';
                            customFieldData += '\n    if($GRID) {';
                            customFieldData += '\n        $OUTPUT = \'<a class="fg-edit-html-in-strings-table"><span>Click Here to Edit</span></a>\';';
                            customFieldData += '\n    } else {';
                            customFieldData += '\n        $LABEL_MARKUP = false;';
                            customFieldData += '\n        $fields = [';

                            $(this).find('[cybdata]').each(function() {

                                var nestedConfig = $(this).attr('cybdata');
                                var nestedKey = $(this).attr('cybkey');
                                if(!nestedKey) { nestedKey = nestedConfig; }
    
                                if(nestedConfig == 'buttontext' || nestedConfig == 'list' || nestedConfig == 'nav' || nestedConfig == 'logo' || nestedConfig == 'footerlogo' || nestedConfig == 'alertbar' || nestedConfig == 'popup' || nestedConfig == 'tag') {

                                    /* Skip these field types */
                                    return;

                                } else if(nestedConfig == '' || nestedConfig == 'title' || nestedConfig == 'txt') {

                                    nestedConfig = 'text';

                                } else if(nestedConfig == 'img') {

                                    nestedConfig = 'photo';

                                } else if(nestedConfig == 'bg') {

                                    nestedConfig = 'focused_img';

                                }

                                customFieldData += '\n            [';
                                customFieldData += '\n                "key" => "' + nestedConfig + '",';
                                customFieldData += '\n                "label" => "' + makeTitle(nestedKey) + '",';
                                customFieldData += '\n                "config" => [';
                                customFieldData += '\n                    "type" => "' + nestedConfig + '"';
                                customFieldData += '\n                ]';
                                customFieldData += '\n            ],';

                            });

                            customFieldData = customFieldData.slice(0,-1);

                            customFieldData += '\n        ];';
                            customFieldData += '\n        $FIELD_HTML .= $FIELD->special($KEY, [';
                            customFieldData += '\n            "type" => "sorted_list",';
                            customFieldData += '\n            "fields" => $fields';
                            customFieldData += '\n        ]);';
                            customFieldData += '\n        $OUTPUT= \'<div class="field"><div class="field-inner">\' . $FIELD_HTML . \'</div></div>\';';
                            customFieldData += '\n    }';
                            customFieldData += '\n';
                            customFieldData += '\n    break;\n\n';

                        }

                    } else if(config == 'img') {

                        config = 'photo';

                    } else if(config == 'bg') {

                        config = 'focused_img';

                    }
                    
                    blockBuilderData += '\n            ["key" => "' + key + '","config" => "' + config + '"],';
                    
                });

                if(blockBuilderData[blockBuilderData.length -1] == ',') {
                    blockBuilderData = blockBuilderData.slice(0,-1);
                }

                blockBuilderData += '\n        ]';
                blockBuilderData += '\n    ],';
                blockBuilderData += '\n"dev" => true';
                blockBuilderData += '\n];\n\n';

            });

            $('#block-builder-data-output').val(blockBuilderData);
            $('#custom-field-data-output').val(customFieldData);
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

                if(type == 'buttontext') {

                    /* Skip */

                } else if(type == 'nav') {

                    $(this).replaceWith('<? printWebflowMenu(); ?>');

                } else if(type == 'logo') {

                    $(this).replaceWith('<a href="/home" class="logo-home-link w-nav-brand" title="Home"><img src="/image/<?= $DEV_CONFIG[\'Logo\'] ?>/600" alt="<?= $owner->site_title ?>" class="logo-image"><img src="/image/<?= $DEV_CONFIG[\'Logo on Scroll\'] ?>/600" alt="<?= $owner->site_title ?>" class="logo-scrolled"></a>');

                } else if(type == 'footerlogo') {

                    $(this).replaceWith('<a href="/home" class="footer-logo-link-block w-inline-block" title="Home"><img src="/image/<?= $DEV_CONFIG[\'Footer Logo\'] ?>/600" alt="<?= $owner->site_title ?>" class="footer-logo"></a>');

                } else if(type == 'alertbar') {

                    $(this).replaceWith('<? printBlock(ALERT_BAR_BLOCK_ID); ?>');

                } else if(type == 'popup') {

                    $(this).replaceWith('<? printBlock(POPUP_BLOCK_ID); ?>');
                
                } else if(type == 'tag') {

                    $(this).replaceWith('<div class="cybernautic-tag"><? seoCybernauticLogo($cms); ?></div>');

                } else if(type == 'list') {
                    
                    itemLabel = key.toUpperCase().replace(/ /g,"_") + "_ITEM";

                    /* Keep last link intact on footer links (sitemap link is usually last and is should not be controllable in the CMS) */
                    if(key == 'Footer Links') {
                        $(this).children().not(':first').not(':last').remove();
                    } else {
                        $(this).children().not(':first').remove();
                    }

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

            var phpOutput = $('#parsing').html().replace(/<!--\?/g, '<?').replace(/\?-->/g, '?>').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/=-->/g, '=>').replace(/--->/g, '->');

            if($('#common-items').is(':checked')) {
                $('#php-output').val(phpOutput.replace(/\$DATA/g,'$COMMON_ITEMS'));
            } else {
                $('#php-output').val('<? $DATA = strings($block->id); ?>' + phpOutput);
            }
            
            $('#parsing').html('');
            $('#php-output').focus();

        });

        $('.textarea').focus(function() { 
            this.select(); 
        });

    });

}
