
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
            window.amsdTableSQL = '';
            window.viewFileNames = '';

            $('#data-parsing').html($('#input').val());

            $('#data-parsing').find('[cybkey]').each(function() {
                // Give a blank cybdata attribute to anything that has a cybkey attribute but no cybdata attribute
                if(!$(this).attr('cybdata')) {
                    $(this).attr('cybdata','');
                }
            });

            /* --- Create data for custom sorted list fields - goes in field_builder_custom_preset() in amsd_custom_helper.php --- */
            $('#data-parsing').find('[cybdata="list"]').each(function() {

                var config = $(this).attr('cybdata');
                var key = $(this).attr('cybkey');
                if(!key) { key = config.charAt(0).toUpperCase() + config.slice(1); }

                var itemSlug = key.replace(/ /g, "_").replace(/[^\w-]+/g, "");
                itemSlug = itemSlug.toLowerCase();
                        

                if($(this).find('[cybdata]').length !== 0) {

                    customFieldData += '        case "' + itemSlug + '":';
                    customFieldData += '\n';
                    customFieldData += '\n            if($GRID) {';
                    customFieldData += '\n                $OUTPUT = \'<a class="fg-edit-html-in-strings-table"><span>Click Here to Edit</span></a>\';';
                    customFieldData += '\n            } else {';
                    customFieldData += '\n                $LABEL_MARKUP = false;';
                    customFieldData += '\n                $fields = [';

                    $(this).find('[cybdata]').each(function() {

                        var nestedConfig = $(this).attr('cybdata');
                        var nestedKey = $(this).attr('cybkey');
                        if(!nestedKey) { nestedKey = nestedConfig; }
                        var nestedSlug = nestedKey.replace(/ /g, "_").replace(/[^\w-]+/g, "");
                        nestedSlug = nestedSlug.toLowerCase();

                        if(nestedConfig == 'amsd' || nestedConfig == 'profile' || nestedConfig == 'common' || nestedConfig == 'strings' || nestedConfig == 'buttontext' || nestedConfig == 'list' || nestedConfig == 'nav' || nestedConfig == 'logo' || nestedConfig == 'footerlogo' || nestedConfig == 'alertbar' || nestedConfig == 'popup' || nestedConfig == 'tag') {

                            /* Skip these field types */
                            return;

                        } else if(nestedConfig == '' || nestedConfig == 'title' || nestedConfig == 'txt') {

                            nestedConfig = 'text';

                        } else if(nestedConfig == 'icon') {

                            nestedConfig = 'font_awesome';

                        } else if(nestedConfig.substring(0,3) == 'img') {

                            nestedConfig = 'photo';

                        } else if(nestedConfig.substring(0,2) == 'bg') {

                            nestedConfig = 'focused_img';

                        }

                        customFieldData += '\n                    [';
                        customFieldData += '\n                        "key" => "' + nestedSlug + '",';
                        customFieldData += '\n                        "label" => "' + makeTitle(nestedKey) + '",';
                        customFieldData += '\n                        "config" => [';
                        customFieldData += '\n                            "type" => "' + nestedConfig + '"';
                        customFieldData += '\n                        ]';
                        customFieldData += '\n                    ],';

                    });

                    customFieldData = customFieldData.slice(0,-1);

                    customFieldData += '\n                ];';
                    customFieldData += '\n                $FIELD_HTML .= $FIELD->special($KEY, [';
                    customFieldData += '\n                    "type" => "sorted_list",';
                    customFieldData += '\n                    "fields" => $fields';
                    customFieldData += '\n                ]);';
                    customFieldData += '\n                $OUTPUT= \'<div class="field"><div class="field-inner">\' . $FIELD_HTML . \'</div></div>\';';
                    customFieldData += '\n            }';
                    customFieldData += '\n';
                    customFieldData += '\n            break;\n\n';

                }
            });

            /* --- Create data for STRINGS block type - goes in customSortedListBlocks() in pagebuilder_custom_helper.php --- */
            $('#data-parsing').find('[cybdata="strings"]').each(function() {

                var title =  '';
                if($(this).attr('cybkey')) {
                    title = makeTitle($(this).attr('cybkey'));
                } else {
                    if($(this).attr('class')) {
                        title = makeTitle($(this).attr('class').split(' ')[0]);
                    }  
                }
                if(title == '') {
                    title = 'Custom Strings Block';
                }

                var blockSlug = title.replace(/ /g, "-").replace(/[^\w-]+/g, "");
                blockSlug = blockSlug.toLowerCase();

                viewFileNames += '/strings/' + blockSlug + '.php<br>';

                blockBuilderData += '    $items[] = [';
                blockBuilderData += '\n        "title" => "' + title + '",';
                blockBuilderData += '\n        "value" => "' + blockSlug + '",';
                blockBuilderData += '\n        "block" => [';
                blockBuilderData += '\n            "type" => 2,';
                blockBuilderData += '\n            "title" => "' + title + '",';
                blockBuilderData += '\n            "settings" => [';
                blockBuilderData += '\n                ["AMSD Columns", 2],';
                blockBuilderData += '\n                ["AMSD Delete", "false"],';
                blockBuilderData += '\n                ["AMSD Edit", "false"],';
                blockBuilderData += '\n                ["Heading Read Only", "true"],';
                blockBuilderData += '\n                ["Table", "amsd_strings"],';
                blockBuilderData += '\n                ["Template", "' + blockSlug + '"]';
                blockBuilderData += '\n            ],"items" => [';

                $(this).find('[cybdata]').each(function() {

                    if($(this).parents('[cybdata="list"]').length) {

                        /* Skip items inside of a sorted list item */
                        return;

                    }

                    var config = $(this).attr('cybdata');
                    var key = $(this).attr('cybkey');
                    if(!key) { key = config.charAt(0).toUpperCase() + config.slice(1); }

                    var itemSlug = key.replace(/ /g, "_").replace(/[^\w-]+/g, "");
                    itemSlug = itemSlug.toLowerCase();

                    if(config == 'amsd' || config == 'profile' || config == 'common' || config == 'strings' || config == 'buttontext' || config == 'nav' || config == 'logo' || config == 'footerlogo' || config == 'alertbar' || config == 'popup' || config == 'tag') {

                        /* Skip these field types */
                        return;

                    } else if(config == '' || config == 'title' || config == 'txt') {
                        
                        config = '';

                    } else if(config == 'list') {

                        config = itemSlug;
                        
                    } else if(config.substring(0,3) == 'img') {

                        config = 'photo';

                    } else if(config.substring(0,2) == 'bg') {

                        config = 'focused_img';

                    }
                    
                    blockBuilderData += '\n                ["key" => "' + key + '","config" => "' + config + '"],';
                    
                });

                if(blockBuilderData[blockBuilderData.length -1] == ',') {
                    blockBuilderData = blockBuilderData.slice(0,-1);
                }

                blockBuilderData += '\n            ]';
                blockBuilderData += '\n        ],';
                blockBuilderData += '\n        "dev" => true';
                blockBuilderData += '\n    ];\n\n';

            });

            /* --- Create data for AMSD block type - goes in customSortedListBlocks() in pagebuilder_custom_helper.php --- */
            $('#data-parsing').find('[cybdata="amsd"]').each(function() {

                var title =  '';
                if($(this).attr('cybkey')) {
                    title = makeTitle($(this).attr('cybkey'));
                } else {
                    if($(this).attr('class')) {
                        title = makeTitle($(this).attr('class').split(' ')[0]);
                    }  
                }
                if(title == '') {
                    title = 'Custom Sorted List Block';
                }

                var blockSlug = title.replace(/ /g, "-").replace(/[^\w-]+/g, "");
                blockSlug = blockSlug.toLowerCase();
                var amsdSlug = 'amsd_' + blockSlug.replace(/-/g, "_");

                viewFileNames += amsdSlug + '.php<br>';

                blockBuilderData += '    $items[] = [';
                blockBuilderData += '\n        "title" => "' + title + '",';
                blockBuilderData += '\n        "value" => "' + blockSlug + '",';
                blockBuilderData += '\n        "block" => [';
                blockBuilderData += '\n            "type" => 2,';
                blockBuilderData += '\n            "title" => "' + title + '",';
                blockBuilderData += '\n            "settings" => [';
                blockBuilderData += '\n                ["Heading Read Only", "true"],';
                blockBuilderData += '\n                ["Table", "' + amsdSlug + '"]';
                blockBuilderData += '\n            ]';
                blockBuilderData += '\n        ],';
                blockBuilderData += '\n        "dev" => true';
                blockBuilderData += '\n    ];\n\n';

                amsdTableSQL += 'CREATE TABLE IF NOT EXISTS `' + amsdSlug + '` (';
                amsdTableSQL += '\n    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,';
                amsdTableSQL += '\n    `block` int(11) unsigned NOT NULL DEFAULT 0,';

                $(this).find('[cybdata]').each(function() {

                    if($(this).parents('[cybdata="list"]').length) {

                        /* Skip items inside of a sorted list item */
                        return;

                    }

                    var config = $(this).attr('cybdata');
                    var key = $(this).attr('cybkey');
                    if(!key) { key = config.charAt(0).toUpperCase() + config.slice(1); }

                    var itemSlug = key.replace(/ /g, "_").replace(/[^\w-]+/g, "");
                    itemSlug = itemSlug.toLowerCase();

                    var name = config;
                    var dataType = 'varchar(255)';

                    if(config == 'profile' || config == 'common' || config == 'strings' || config == 'buttontext' || config == 'nav' || config == 'logo' || config == 'footerlogo' || config == 'alertbar' || config == 'popup' || config == 'tag') {

                        /* Skip these field types */
                        return;

                    } else if(config == 'title' || itemSlug == 'title') {

                        name = 'title';

                    } else if(config == 'date' || config == 'time' || config == 'datetime') {

                        name = itemSlug;
                        dataType = config;

                    } else if(config == 'list' || config == 'button' || config == 'textarea' || config == 'html' || config == 'url') {

                        name = itemSlug;
                        dataType = 'text';

                    } else if(config.substring(0,3) == 'img') {

                        name = 'img';
                        dataType = 'int(11) unsigned';

                    } else if(config.substring(0,2) == 'bg') {

                        name = 'focused_img';
                        dataType = 'text';

                    }

                    if(name == '') {
                        name = itemSlug;
                    }

                    if(config != name && config != '') {
                        customFieldData += '        case "' + itemSlug + '":';
                        customFieldData += '\n';
                        customFieldData += '\n            $OUTPUT = $FIELD->build($KEY, [';
                        customFieldData += '\n                "type" => "' + config + '"';
                        customFieldData += '\n';
                        customFieldData += '\n            break;\n\n';
                    }

                    amsdTableSQL += '\n    `' + name + '` ' + dataType + ' DEFAULT NULL,';

                    if(name == 'title') {
                        amsdTableSQL += '\n    `slug` varchar(255) DEFAULT NULL,';
                    }
                    
                });

                amsdTableSQL += '\n    `pos` int(11) unsigned NOT NULL DEFAULT 0,';
                amsdTableSQL += '\n    PRIMARY KEY (`id`)';
                amsdTableSQL += '\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;\n\n';

            });

            if(viewFileNames != '') {
                viewFileNames = 'Create files in: /blocks/amsd/templates/<br>' + viewFileNames;
                /* Remove last line break */
                viewFileNames = viewFileNames.slice(0,-4);
            }
            $('#view-files-output').html(viewFileNames);

            $('#block-builder-data-output').val(blockBuilderData);
            $('#amsd-table-sql-output').val(amsdTableSQL);
            $('#custom-field-data-output').val(customFieldData);
            $('#data-parsing').html('');

            /* --- CREATE PHP VIEW FOR TEMPLATE FILE --- */

            $('#parsing').html($('#input').val());

            $('#parsing').find('[cybkey]').each(function() {
                // Give a blank cybdata attribute to anything that has a cybkey attribute but no cybdata attribute
                if(!$(this).attr('cybdata')) {
                    $(this).attr('cybdata','');
                }
            });

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
                        key = key.toLowerCase().replace(/ /g,"_");
                    } else {
                        key = type;
                    }
                }

                if($(this).parents('[cybdata="amsd"]').length || $(this).parents('[cybdata="profile"]').length) {
                    if($(this).parents('[cybdata="list"]').length == 0) {
                        prefix = "$ITEM->";
                        suffix = "";
                        if(key) {
                            key = key.toLowerCase().replace(/ /g,"_");
                        } else {
                            key = type;
                        }
                    }
                }

                if(!key) { key = type.charAt(0).toUpperCase() + type.slice(1); }

                var blockSlug = key.replace(/ /g, "-").replace(/[^\w-]+/g, "");
                blockSlug = blockSlug.toLowerCase();
                var amsdSlug = 'amsd_' + blockSlug.replace(/-/g, "_");

                /* --- DATA TYPES --- */

                if(type == 'buttontext') {

                    /* Skip */

                } else if(type == 'common') {

                    $(this).before('\n\n<?/* Common Items Area */?>\n');
                    $(this).after('\n<?/* End Common Items Area */?>\n\n');

                } else if(type == 'strings') {

                    $(this).before('\n\n<?/* Strings Block Template for /blocks/amsd/templates/strings/' + blockSlug + '.php */?>\n<? $DATA = strings($block->id); ?>\n');
                    $(this).after('\n<?/* End of String Block Template */?>\n\n');

                } else if(type == 'profile') {

                    $(this).children().first().before('\n\n<?/* AMSD Profile */?>\n<? $ITEM = $profile; ?>\n');
                    $(this).children().last().after('\n<?/* End of AMSD Profile */?>\n\n');

                } else if(type == 'nav') {

                    $(this).replaceWith('<? printWebflowMenu(); ?>');

                } else if(type == 'logo') {

                    $(this).replaceWith('<a href="/home" class="logo-home-link w-nav-brand" title="Home"><img src="/image/<?= $DEV_CONFIG[\'Logo\'] ?>/600" alt="<?= $owner->site_title ?>" class="logo-image"><img src="/image/<?= $DEV_CONFIG[\'Logo on Scroll\'] ?>/600" alt="<?= $owner->site_title ?>" class="logo-scrolled"></a>');

                } else if(type == 'footerlogo') {

                    $(this).replaceWith('<a href="/home" class="footer-logo-link-block w-inline-block" title="Home"><img src="/image/<?= $DEV_CONFIG[\'Footer Logo\'] ?>/600" alt="<?= $owner->site_title ?>" class="footer-logo"></a>');

                } else if(type == 'alertbar') {

                    $(this).replaceWith('<? printBlock(ALERT_BAR_BLOCK_ID); ?>\n');

                } else if(type == 'popup') {

                    $(this).replaceWith('\n<? printBlock(POPUP_BLOCK_ID); ?>\n');
                
                } else if(type == 'tag') {

                    $(this).replaceWith('<div class="cybernautic-tag"><? seoCybernauticLogo($cms); ?></div>');

                } else if(type == 'list' || type == 'amsd') {

                    itemLabel = key.toUpperCase().replace(/ /g,"_") + "_ITEM";

                    /* Keep last link intact on footer links (sitemap link is usually last and is should not be controllable in the CMS) */
                    if(key == 'Footer Links') {
                        $(this).children().not(':first').not(':last').remove();
                    } else {
                        $(this).children().not(':first').remove();
                    }

                    if(type == 'amsd') {
                        $(this).children().first().before('\n\n<?/* AMSD Loop for /blocks/amsd/templates/' + amsdSlug + '.php */?>\n');
                        $(this).children().first().before('<? foreach($amsd["data"] as $k => $ITEM) { ?>\n').after("\n<? } ?>\n");
                    } else {
                        $(this).children().first().before("<? foreach (json_decode(" + prefix + key + suffix + ") as $k => $" + itemLabel + ") { ?>\n").after("\n<? } ?>");
                    }

                } else if(type == 'textarea') {

                    $(this).html("<?= nl2br(" + prefix + key + suffix + "); ?>");

                } else if(type.substring(0,3) == 'img') {

                    var imageSize = type.slice(3);
                    if(imageSize == '') {
                        imageSize = '1000';
                    }

                    $(this).attr("src","/image/<?= " + prefix + key + suffix + " ?>/" + imageSize);

                } else if(type.substring(0,2) == 'bg') {

                    var imageSize = type.slice(2);
                    if(imageSize == '') {
                        imageSize = '2000';
                    }

                    $(this).attr("style","background-position: <?= json_decode(" + prefix + key + suffix + ")->config->{'background-position'} ?>; background-image: url('/image/<?= json_decode(" + prefix + key + suffix + ")->id ?>/" + imageSize + "');");
                
                } else if(type == 'phone') {

                    $(this).attr("href","tel:+1<?= " + prefix + key + suffix + " ?>");
                    $(this).html("<?= " + prefix + key + suffix + " ?>");

                } else if(type == 'email') {

                    $(this).attr("href","mailto:+<?= " + prefix + key + suffix + " ?>");
                    $(this).html("<?= " + prefix + key + suffix + " ?>");

                } else if(type == 'button') {

                    if($(this).parents('[cybdata="list"]').length || $(this).parents('[cybdata="amsd"]').length) {

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

                    $(this).removeClass('w-embed');
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

            $('#parsing').find('[cybdata="common"]').each(function() {
                $(this).find('[cybdata]').each(function() {
                    $(this).html($(this).html().replace(/\$DATA/g,'$COMMON_ITEMS'));
                });
            });
 
            $('#parsing').find('[cybdata]').removeAttr('cybdata');
            $('#parsing').find('[cybkey]').removeAttr('cybkey');

            var phpOutput = $('#parsing').html().replace(/<!--\?/g, '<?').replace(/\?-->/g, '?>').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/=-->/g, '=>').replace(/--->/g, '->');

            $('#php-output').val(phpOutput);

            $('#parsing').html('');
            $('#php-output').focus();

        });

        $('.textarea').focus(function() { 
            this.select(); 
        });

    });

}
