
window.onload = function(e) {

    $(document).ready(function() {

        titleCase = (s) => s.replace(/\b\w/g, c => c.toUpperCase());

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
            window.blocksCount = 0;
            window.customFieldCount = 0;

            $('#data-parsing').html($('#input').val());

            $('#data-parsing').find('[cybkey]').each(function() {
                // Give a blank cybdata attribute to anything that has a cybkey attribute but no cybdata attribute
                if(!$(this).attr('cybdata')) {
                    $(this).attr('cybdata','');
                }
            });

            /* --- Create data for custom sorted list fields - goes in field_builder_custom_preset() in amsd_custom_helper.php --- */
            $('#data-parsing').find('[cybdata="list"]').each(function() {

                /*--- Items within a sorted list field are defined within the first child element, so we're dropping all other elements --*/
                $(this).children().not(':first').remove();

                var config = $(this).attr('cybdata');

                var key = $(this).attr('cybkey');
                if(!key) { key = config.charAt(0).toUpperCase() + config.slice(1); }

                var itemSlug = key.replace(/ /g, "_").replace(/[^\w-]+/g, "");
                itemSlug = itemSlug.toLowerCase();
                        

                if($(this).find('[cybdata]').length !== 0) {
                    
                    customFieldCount ++;
                    if(customFieldCount != 1) { customFieldData += '        '; }
                    customFieldData += 'case "' + itemSlug + '":';
                    customFieldData += '\n';
                    customFieldData += '\n            if($GRID) {';
                    customFieldData += '\n                $OUTPUT = \'<a class="fg-edit-html-in-strings-table"><span>Click Here to Edit</span></a>\';';
                    customFieldData += '\n            } else {';
                    customFieldData += '\n                $fields = [';

                    var existingSortedListFields = new Array();
                    $(this).find('[cybdata]').each(function() {

                        var nestedConfig = $(this).attr('cybdata');
                        var nestedKey = $(this).attr('cybkey');

                        if(nestedConfig == 'profileurl') {
                            nestedConfig = 'url';
                        }

                        var nestedLabel = '';

                        if(nestedConfig == 'amsd' || nestedConfig == 'profile' || nestedConfig == 'common' || nestedConfig == 'block' || nestedConfig == 'buttontext' || nestedConfig == 'list' || nestedConfig == 'nav' || nestedConfig == 'logo' || nestedConfig == 'footerlogo' || nestedConfig == 'alertbar' || nestedConfig == 'popup' || nestedConfig == 'tag' || nestedConfig == 'ifisset') {

                            /* Skip these field types */
                            return;

                        } else if(nestedConfig == '' || nestedConfig == 'title' || nestedConfig == 'text' || nestedConfig == 'txt' || nestedConfig == 'videobg') {

                            nestedConfig = 'text';

                        } else if(nestedConfig == 'icon') {

                            nestedConfig = 'icon';
                            if(!nestedKey) {
                                nestedLabel = 'Icon';
                            }

                        } else if(nestedConfig.substring(0,11) == 'previewtext') {

                            nestedConfig = 'textarea';
                            if(!nestedKey) {
                                nestedKey = 'Preview Text';
                            }

                        } else if(nestedConfig.substring(0,3) == 'img') {

                            nestedConfig = 'photo';
                            if(!nestedKey) {
                                nestedLabel = 'Image';
                            }

                        } else if(nestedConfig.substring(0,2) == 'bg') {

                            nestedConfig = 'focused_img';
                            if(!nestedKey) {
                                nestedLabel = 'Focused Image';
                            }

                        }

                        if(!nestedKey) { nestedKey = nestedConfig; }
                        var nestedSlug = nestedKey.replace(/ /g, "_").replace(/[^\w-]+/g, "");
                        nestedSlug = nestedSlug.toLowerCase();

                        if(nestedLabel == '') {
                            nestedLabel = makeTitle(nestedKey);
                        }

                        /* Skip items if an item with the same key has already been added */
                        if ($.inArray(nestedSlug, existingSortedListFields) != -1) {
                            return;
                        }
                        existingSortedListFields.push(nestedSlug);

                        customFieldData += '\n                    [';
                        customFieldData += '\n                        "key" => "' + nestedSlug + '",';
                        customFieldData += '\n                        "label" => "' + nestedLabel + '",';
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
                    customFieldData += '\n                $OUTPUT = $FIELD_HTML;';
                    customFieldData += '\n            }';
                    customFieldData += '\n';
                    customFieldData += '\n            break;\n\n';

                }
            });

            /* --- Create data for STRINGS block type - goes in customSortedListBlocks() in pagebuilder_custom_helper.php --- */
            $('#data-parsing').find('[cybdata="block"]').each(function() {

                /* Skip block if there's an AMSD loop inside - it is now considered an AMSD block and any items will be added as additional settings to the AMSD block. */
                if($(this).find('[cybdata="amsd"]').length !== 0) {
                    return;
                }

                blocksCount++;

                var title =  '';
                if($(this).attr('cybkey')) {
                    title = makeTitle($(this).attr('cybkey'));
                } else {
                    if($(this).attr('class')) {
                        title = makeTitle($(this).attr('class').split(' ')[0]);
                    }  
                }
                if(title == '') {
                    title = 'Custom Block';
                }

                var blockSlug = title.replace(/ /g, "-").replace(/[^\w-]+/g, "");
                blockSlug = blockSlug.toLowerCase();

                viewFileNames += '/strings/' + blockSlug + '.php<br>';

                if(blocksCount != 1) { blockBuilderData += '    '; }
                blockBuilderData += '$items[] = [';
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
                blockBuilderData += '\n            ],';
                blockBuilderData += '\n            "items" => [';

                var existingBlockBuilderFields = new Array();
                $(this).find('[cybdata]').each(function() {

                    if($(this).parents('[cybdata="list"]').length) {

                        /* Skip items inside of a sorted list item */
                        return;

                    }

                    var config = $(this).attr('cybdata');

                    if(config == 'profileurl') {
                        config = 'url';
                    }

                    var key = $(this).attr('cybkey');
                    if(!key) { key = config.charAt(0).toUpperCase() + config.slice(1); }

                    /* Skip items if an item with the same key has already been added */
                    if ($.inArray(key, existingBlockBuilderFields) != -1) {
                        return;
                    }
                    existingBlockBuilderFields.push(key);

                    var itemSlug = key.replace(/ /g, "_").replace(/[^\w-]+/g, "");
                    itemSlug = itemSlug.toLowerCase();

                    if(config == 'amsd' || config == 'profile' || config == 'common' || config == 'block' || config == 'buttontext' || config == 'nav' || config == 'logo' || config == 'footerlogo' || config == 'alertbar' || config == 'popup' || config == 'tag' || config == 'ifisset') {

                        /* Skip these field types */
                        return;

                    } else if(config == '' || config == 'title' || config == 'text' || config == 'txt' || config == 'videobg') {
                        
                        config = '';

                    } else if(config == 'list') {

                        config = itemSlug;

                    } else if(config.substring(0,11) == 'previewtext') {

                        if(itemSlug = config) {
                            itemSlug = 'preview_text';
                        }
                        config = 'textarea';
                        
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

                /* Items within an AMSD loop are defined within the first child element, so we're dropping all other elements */
                $(this).children().not(':first').remove();

                /* If the AMSD loop is inside a block, any other cybdata items inside the block get pulled into the amsd block as additional settings */
                var hasAdditionalSettings = false;
                var additionalSettingsData = '';
                if($(this).parents('[cybdata="block"]').length) {

                    var existingAdditionalSettingsFields = new Array();

                    $(this).parents('[cybdata="block"]').find('[cybdata]').each(function() {

                        if($(this).attr('cybdata') == 'amsd' || $(this).parents('[cybdata="amsd"]').length) {
                            return;
                        }

                        hasAdditionalSettings = true;

                        var additionalSettingsItemConfig = $(this).attr('cybdata');

                        if(additionalSettingsItemConfig == 'profileurl') {
                            additionalSettingsItemConfig = 'url';
                        }

                        var additionalSettingsItemKey = $(this).attr('cybkey');
                        if(!additionalSettingsItemKey) { additionalSettingsItemKey = additionalSettingsItemConfig.charAt(0).toUpperCase() + additionalSettingsItemConfig.slice(1); }

                        /* Skip items if an item with the same key has already been added */
                        if ($.inArray(additionalSettingsItemKey, existingAdditionalSettingsFields) != -1) {
                            return;
                        }
                        existingAdditionalSettingsFields.push(additionalSettingsItemKey);

                        var additionalSettingsItemSlug = additionalSettingsItemKey.replace(/ /g, "_").replace(/[^\w-]+/g, "");
                        additionalSettingsItemSlug = additionalSettingsItemSlug.toLowerCase();

                        if(additionalSettingsItemConfig == 'amsd' || additionalSettingsItemConfig == 'profile' || additionalSettingsItemConfig == 'common' || additionalSettingsItemConfig == 'block' || additionalSettingsItemConfig == 'buttontext' || additionalSettingsItemConfig == 'nav' || additionalSettingsItemConfig == 'logo' || additionalSettingsItemConfig == 'footerlogo' || additionalSettingsItemConfig == 'alertbar' || additionalSettingsItemConfig == 'popup' || additionalSettingsItemConfig == 'tag' || additionalSettingsItemConfig == 'ifisset') {

                            /* Skip these field types */
                            return;

                        } else if(additionalSettingsItemConfig == '' || additionalSettingsItemConfig == 'title' || additionalSettingsItemConfig == 'text' || additionalSettingsItemConfig == 'txt' || additionalSettingsItemConfig == 'vimeobg' || additionalSettingsItemConfig == 'youtubebg' || additionalSettingsItemConfig == 'videobg') {
                            
                            additionalSettingsItemConfig = 'NULL';

                        } else if(additionalSettingsItemConfig == 'list') {

                            additionalSettingsItemConfig = additionalSettingsItemSlug;

                        } else if(additionalSettingsItemConfig.substring(0,11) == 'previewtext') {

                            additionalSettingsItemConfig = 'textarea';
                            
                        } else if(additionalSettingsItemConfig.substring(0,3) == 'img') {

                            additionalSettingsItemConfig = 'photo';

                        } else if(additionalSettingsItemConfig.substring(0,2) == 'bg') {

                            additionalSettingsItemConfig = 'focused_img';

                        }

                        additionalSettingsData += '\n                [';
                        additionalSettingsData += '\n                    "key" => "' + additionalSettingsItemKey + '",';
                        additionalSettingsData += '\n                    "value" => NULL,';
                        additionalSettingsData += '\n                    "config" => ' + additionalSettingsItemConfig + '';
                        additionalSettingsData += '\n                ],';

                    });
                }

                blocksCount++;

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

                if(blocksCount != 1) { blockBuilderData += '    '; }
                blockBuilderData += '$items[] = [';
                blockBuilderData += '\n        "title" => "' + title + '",';
                blockBuilderData += '\n        "value" => "' + blockSlug + '",';
                blockBuilderData += '\n        "block" => [';
                blockBuilderData += '\n            "type" => 2,';
                blockBuilderData += '\n            "title" => "' + title + '",';
                blockBuilderData += '\n            "settings" => [';
                blockBuilderData += '\n                ["Heading Read Only", "true"],';
                blockBuilderData += '\n                ["Table", "' + amsdSlug + '"]';

                if(hasAdditionalSettings) {
                    blockBuilderData += ',\n                ["Additional Settings", "true"]';
                }

                blockBuilderData += '\n            ]';

                if(hasAdditionalSettings) {

                    /* Remove comma from last item in additional settings data */
                    additionalSettingsData = additionalSettingsData.slice(0,-1);

                    blockBuilderData += ',\n            "additional_settings" => [';
                    blockBuilderData += additionalSettingsData;
                    blockBuilderData += '\n            ]';
                }

                blockBuilderData += '\n        ],';

                blockBuilderData += '\n        "dev" => true';
                blockBuilderData += '\n    ];\n\n';

                amsdTableSQL += 'CREATE TABLE IF NOT EXISTS `' + amsdSlug + '` (';
                amsdTableSQL += '\n    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,';
                amsdTableSQL += '\n    `block` int(11) unsigned NOT NULL DEFAULT 0,';

                var existingAMSDFields = new Array();
                var addHtmlField = false;
                $(this).find('[cybdata]').each(function() {

                    if($(this).parents('[cybdata="list"]').length) {

                        /* Skip items inside of a sorted list item */
                        return;

                    }

                    var config = $(this).attr('cybdata');

                    if(config == 'profileurl') {
                        config = 'url';
                    }

                    var key = $(this).attr('cybkey');
                    if(!key) { key = config.charAt(0).toUpperCase() + config.slice(1); }

                    /* Skip items if an item with the same key has already been added */
                    if ($.inArray(key, existingAMSDFields) != -1 || config == 'ifisset') {
                        return;
                    }
                    existingAMSDFields.push(key);

                    var itemSlug = key.replace(/ /g, "_").replace(/[^\w-]+/g, "");
                    itemSlug = itemSlug.toLowerCase();

                    var name = config;
                    var dataType = 'varchar(255)';

                    if(config == 'profile' || config == 'common' || config == 'block' || config == 'buttontext' || config == 'nav' || config == 'logo' || config == 'footerlogo' || config == 'alertbar' || config == 'popup' || config == 'tag' || config == 'ifisset') {

                        /* Skip these field types */
                        return;

                    } else if(config == 'title' || itemSlug == 'title') {

                        name = 'title';

                    } else if(config == 'date' || config == 'time' || config == 'datetime') {

                        name = itemSlug;
                        dataType = config;

                    } else if(config == 'list') {

                        name = itemSlug;
                        dataType = 'text';

                    } else if(config == 'button' || config == 'textarea' || config == 'html' || config == 'url') {
                        
                        dataType = 'text';

                    
                    } else if(config.substring(0,11) == 'previewtext') {

                        name = 'textarea';
                        dataType = 'text';

                    } else if(config.substring(0,3) == 'img') {

                        if(itemSlug == config) {
                            itemSlug = 'img';
                        }

                        name = 'img';
                        dataType = 'int(11) unsigned';

                    } else if(config.substring(0,2) == 'bg') {

                        if(itemSlug == config) {
                            itemSlug = 'focused_img';
                        }

                        name = 'focused_img';
                        dataType = 'text';

                    }

                    if(itemSlug == 'profile_url' || itemSlug == 'profileurl'){
                        itemSlug = 'url';
                    }

                    if(name == '') {
                        name = itemSlug;
                    }

                    if(itemSlug != name) {

                        if(config != name && config != '') {

                            if(config == 'list') {

                                /* Skip */

                            } else if(config == 'textarea' && itemSlug == 'caption') {

                                /* Skip */

                            } else {
                                customFieldCount ++;
                                if(customFieldCount != 1) { customFieldData += '        '; }
                                customFieldData += 'case "' + itemSlug + '":';
                                customFieldData += '\n';
                                customFieldData += '\n            $OUTPUT = $FIELD->build($KEY, [';
                                customFieldData += '\n                "type" => "' + name + '"';
                                customFieldData += '\n            ], false);';
                                customFieldData += '\n';
                                customFieldData += '\n            break;\n\n';
                            }

                        }

                }

                    amsdTableSQL += '\n    `' + itemSlug + '` ' + dataType + ' DEFAULT NULL,';

                    if(name == 'title') {
                        amsdTableSQL += '\n    `slug` varchar(255) DEFAULT NULL,';
                    }

                    /* Add html field automatically if previewtext or profileurl are used */
                    if(config.substring(0,11) == 'previewtext' || $(this).attr('cybdata') == 'profileurl') {
                        addHtmlField = true;
                    }
                    
                });

                if(addHtmlField) {
                    if ($.inArray('Html', existingAMSDFields) == -1 && $.inArray('HTML', existingAMSDFields) == -1 && $.inArray('html', existingAMSDFields) == -1) {
                        amsdTableSQL += '\n    `html` text DEFAULT NULL,';
                    }
                }

                amsdTableSQL += '\n    `pos` int(11) unsigned NOT NULL DEFAULT 0,';
                amsdTableSQL += '\n    PRIMARY KEY (`id`)';
                amsdTableSQL += '\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;\n\n';

            });

            if(viewFileNames != '') {
                viewFileNames = 'Create block template files in: /blocks/amsd/templates/<br>' + viewFileNames;
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

                if(type == 'profileurl') {
                    type = 'url';
                }

                if(type == 'block') {
                    /* If there's an AMSD loop inside a block, it becomes an AMSD block and gets a new key from the AMSD loop */
                    if($(this).find('[cybdata="amsd"]').length !== 0) {
                        $(this).find('[cybdata="amsd"]').each(function() {
                            key = $(this).attr('cybkey');
                        });
                    }
                }

                if($(this).parents('[cybdata="list"]').length) {
                    if($(this).parents('[cybdata="list"]').attr('cybkey')) {
                        itemLabel = $(this).parents('[cybdata="list"]').attr('cybkey').toUpperCase().replace(/ /g,"_") + "_ITEM";
                    }
                    prefix = "$" + itemLabel + "->";
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

                var variableSlug = key.replace(/-/g, " ").replace(/_/g, " ");
                variableSlug = titleCase(variableSlug).replace(/ /g, "").replace(/[^\w-]+/g, "");
                var blockSlug = key.replace(/ /g, "-").replace(/[^\w-]+/g, "");
                blockSlug = blockSlug.toLowerCase();
                var amsdSlug = 'amsd_' + blockSlug.replace(/-/g, "_");

                /* --- DATA TYPES --- */

                if(type == 'buttontext') {

                    /* Skip */

                } else if(type == 'common') {

                    $(this).before('\n\n<?/* Common Items Area */?>\n');
                    $(this).after('\n<?/* End of Common Items Area */?>\n\n');

                } else if(type == 'block') {

                    if($(this).find('[cybdata="amsd"]').length !== 0) {

                        /* If there's an amsd loop inside the block, it becomes an amsd block */
                        $(this).before('\n\n<?/* AMSD Block Template for /blocks/amsd/templates/' + amsdSlug + '.php */?>\n<? $DATA = strings($block->id); ?>\n');
                        $(this).after('\n<?/* End of AMSD Block Template */?>\n\n');
                    
                    } else {

                        $(this).before('\n\n<?/* Strings Block Template for /blocks/amsd/templates/strings/' + blockSlug + '.php */?>\n<? $DATA = strings($block->id); ?>\n');
                        $(this).after('\n<?/* End of Strings Block Template */?>\n\n');
                    
                    }

                } else if(type == 'profile') {

                    $(this).children().first().before('\n\n<?/* AMSD Profile */?>\n<? $ITEM = $profile; ?>\n');
                    $(this).children().last().after('\n<?/* End of AMSD Profile */?>\n\n');

                } else if(type == 'nav') {

                    $(this).replaceWith('<? printWebflowMenu(); ?>');

                } else if(type == 'logo') {

                    var logoWidth = '';
                    if($(this).find('.logo-image').attr('width')) {
                        var logoWidth = ' width="' + $(this).find('.logo-image').attr('width') + '"';
                    }
                    var logoHeight = '';
                    if($(this).find('.logo-image').attr('height')) {
                        var logoHeight = ' height="' + $(this).find('.logo-image').attr('height') + '"';
                    }

                    var logoScrolledWidth = '';
                    if($(this).find('.logo-scrolled').attr('width')) {
                        var logoScrolledWidth = ' width="' + $(this).find('.logo-scrolled').attr('width') + '"';
                    }
                    var logoScrolledHeight = '';
                    if($(this).find('.logo-scrolled').attr('height')) {
                        var logoScrolledHeight = ' height="' + $(this).find('.logo-scrolled').attr('height') + '"';
                    }

                    $(this).replaceWith('<a href="/home" class="logo-home-link w-nav-brand" title="Home"><img src="/image/<?= $DEV_CONFIG[\'Logo\'] ?>/600"' + logoWidth + logoHeight + ' alt="<?= $owner->site_title ?>" class="logo-image"><img src="/image/<?= $DEV_CONFIG[\'Logo on Scroll\'] ?>/600"' + logoScrolledWidth + logoScrolledHeight + ' alt="<?= $owner->site_title ?>" class="logo-scrolled"></a>');

                } else if(type == 'footerlogo') {

                    var logoFooterWidth = '';
                    if($(this).find('.footer-logo').attr('width')) {
                        var logoFooterWidth = ' width="' + $(this).find('.footer-logo').attr('width') + '"';
                    }
                    var logoFooterHeight = '';
                    if($(this).find('.footer-logo').attr('height')) {
                        var logoFooterHeight = ' height="' + $(this).find('.footer-logo').attr('height') + '"';
                    }

                    $(this).replaceWith('<a href="/home" class="footer-logo-link-block w-inline-block" title="Home"><img src="/image/<?= $DEV_CONFIG[\'Logo in Footer\'] ?>/600"' + logoFooterWidth + logoFooterHeight + ' alt="<?= $owner->site_title ?>" class="footer-logo"></a>');

                } else if(type == 'alertbar') {

                    $(this).replaceWith('<? printBlock(ALERT_BAR_BLOCK_ID); ?>\n');

                } else if(type == 'popup') {

                    $(this).replaceWith('\n<? printBlock(POPUP_BLOCK_ID); ?>\n');
                
                } else if(type == 'tag') {

                    $(this).replaceWith('<div class="cybernautic-tag"><? seoCybernauticLogo($cms); ?></div>');

                } else if(type == 'ifisset') {

                     $(this).before('\n<? if(isset(' + prefix + key + suffix + ')) { ?>');

                     $(this).after('\n<? } ?>');

                } else if(type == 'list' || type == 'amsd') {

                    itemLabel = key.toUpperCase().replace(/ /g,"_") + "_ITEM";

                    /* Keep last link intact on footer links (sitemap link is usually last and is should not be controllable in the CMS) */
                    if(key == 'Footer Links') {
                        $(this).children().not(':first').not(':last').remove();
                    } else {
                        $(this).children().not(':first').remove();
                    }

                    if(type == 'amsd') {
                        $(this).children().first().before('\n<?/* AMSD Loop for /blocks/amsd/templates/' + amsdSlug + '.php */?>\n');
                        $(this).children().first().before('<? foreach($amsd["data"] as $k => $ITEM) { ?>\n').after("\n<? } ?>\n\n<?/* End of AMSD Loop */?>\n");
                    } else {
                        $(this).children().first().before("<? foreach (json_decode(" + prefix + key + suffix + ") as $k => $" + itemLabel + ") { ?>\n").after("\n<? } ?>");
                    }

                } else if(type == 'textarea') {

                    $(this).html("<?= nl2br(" + prefix + key + suffix + "); ?>");

                } else if(type.substring(0,11) == 'previewtext') {

                    var htmlCharLimit = type.slice(11);
                    if(htmlCharLimit == '') {
                        htmlCharLimit = '200';
                    }

                    $(this).html('<?= isset(' + prefix + key + suffix + ') ? nl2br(' + prefix + key + suffix + ') : character_limiter(strip_tags(' + prefix + 'html' + suffix + '), ' + htmlCharLimit + '); ?>');


                } else if(type == 'videobg') {

                    $(this).replaceWith('<? $video = parseVideo(' + prefix + key + suffix + ');if($video["host"] == "youtube") { $backgroundVideoSrc = "https://www.youtube.com/embed/" . $video["id"] . "?autoplay=1&amp;controls=0&amp;rel=0&amp;mute=1&amp;loop=1&amp;playlist=" . $video["id"]; } else if($video["host"] == "vimeo") { $backgroundVideoSrc = "https://player.vimeo.com/video/" . $video["id"] . "?background=1"; } ?>\n<? if($video["id"]) { ?>\n<div class="video-background-wrapper-outer visible">\n<div class="video-background-wrapper-inner">\n<div class="video-background">\n<iframe class="cms-video-vimeo" src="<?= $backgroundVideoSrc ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>\n</div>\n</div>\n</div>\n<? } ?>');

                } else if(type.substring(0,3) == 'img') {

                    if(key == type) {
                        key = 'img';
                    }

                    var imageSize = type.slice(3);
                    if(imageSize == '') {
                        imageSize = '1000';
                    }

                    $(this).attr("src","/image/<?= " + prefix + key + suffix + " ?>/" + imageSize);

                } else if(type.substring(0,2) == 'bg') {

                    if(key == type) {
                        key = 'focused_img';
                    }

                    var imageSize = type.slice(2);
                    if(imageSize == '') {
                        imageSize = '2000';
                    }

                    $(this).attr("style","background-position: <?= json_decode(" + prefix + key + suffix + ")->config->{'background-position'} ?>; background-image: url('/image/<?= json_decode(" + prefix + key + suffix + ")->id ?>/" + imageSize + "');");
                
                } else if(type == 'phone') {

                    $(this).removeAttr("target");
                    $(this).attr("href","tel:+1<?= " + prefix + key + suffix + " ?>");
                    $(this).html("<?= " + prefix + key + suffix + " ?>");

                } else if(type == 'email') {

                    $(this).removeAttr("target");
                    $(this).attr("href","mailto:+<?= " + prefix + key + suffix + " ?>");
                    $(this).html("<?= " + prefix + key + suffix + " ?>");

                } else if(type == 'button') {

                    $(this).removeAttr("target");

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

                    if($(this).attr('cybdata') == 'profileurl') {

                        $(this).removeAttr("target");
                        $(this).attr("href","<?= isset($ITEM->url) ? $ITEM->url : amsdProfileSlug($page, $amsd, $ITEM); ?>");

                    } else {

                        $(this).removeAttr("target");
                        $(this).attr("href","<?= " + prefix + key + suffix + " ?>");

                    }

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

            var phpOutput = $('#parsing').html().replace(/<!--\?/g, '<?').replace(/\?-->/g, '?>').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/=-->/g, '=>').replace(/--->/g, '->').replace('{{greaterthan}}', '>');

            $('#php-output').val(phpOutput);

            $('#parsing').html('');
            $('#php-output').focus();

        });

        $('.textarea').focus(function() { 
            this.select(); 
        });

    });

}
