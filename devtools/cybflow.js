
window.onload = function(e) {

    $(document).ready(function() {

        titleCase = (s) => s.replace(/\b\w/g, c => c.toUpperCase());

        $('textarea').removeAttr('maxlength');

        function makeTitle(slug) {
            var words = slug.replace(/_/g, '-').split('-');
            for (var i = 0; i < words.length; i++) {
                var word = words[i];
                words[i] = word.charAt(0).toUpperCase() + word.slice(1);
            }
            return words.join(' ');
        }

        var skipTypes = ['amsd', 'profile', 'common', 'header', 'footer', 'block', 'buttontext', 'nav', 'logo', 'footerlogo', 'alertbar', 'popup', 'tag', 'ifisset', 'style', 'script', 'pagebanner', 'pagetitle', 'pagesubtitle', 'breadcrumbs', 'printblocks', 'inner', 'preloader'];

        function isSkipType(type) {
            return skipTypes.indexOf(type) !== -1;
        }

        function makeSlug(str) {
            return str.replace(/ /g, "_").replace(/[^\w-]+/g, "").toLowerCase();
        }

        function tokenizeHtml(html) {
            var tokens = [];
            var i = 0;
            while (i < html.length) {
                while (i < html.length && /\s/.test(html[i])) i++;
                if (i >= html.length) break;

                if (html.substring(i, i + 2) === '<?') {
                    var end = html.indexOf('?>', i);
                    if (end === -1) end = html.length - 2;
                    tokens.push({ type: 'php', value: html.substring(i, end + 2) });
                    i = end + 2;
                } else if (html[i] === '<') {
                    var j = i + 1;
                    var inSingleQuote = false;
                    var inDoubleQuote = false;
                    while (j < html.length) {
                        if (html[j] === "'" && !inDoubleQuote) {
                            inSingleQuote = !inSingleQuote;
                        } else if (html[j] === '"' && !inSingleQuote) {
                            inDoubleQuote = !inDoubleQuote;
                        } else if (html[j] === '>' && !inSingleQuote && !inDoubleQuote) {
                            break;
                        }
                        j++;
                    }
                    tokens.push({ type: 'tag', value: html.substring(i, j + 1) });
                    i = j + 1;
                } else {
                    var nextTag = html.length;
                    for (var j = i; j < html.length; j++) {
                        if (html[j] === '<') { nextTag = j; break; }
                    }
                    var text = html.substring(i, nextTag).trim();
                    if (text) {
                        tokens.push({ type: 'text', value: text });
                    }
                    i = nextTag;
                }
            }
            return tokens;
        }

        function formatHtml(html) {
            var tokens = tokenizeHtml(html.trim());
            var indent = 0;
            var output = [];
            var tab = '    ';
            var voidElements = ['area','base','br','col','embed','hr','img','input','link','meta','param','source','track','wbr'];

            for (var i = 0; i < tokens.length; i++) {
                var token = tokens[i];

                if (token.type === 'php' || token.type === 'text') {
                    output.push(tab.repeat(indent) + token.value);

                } else if (token.type === 'tag') {
                    var tag = token.value;
                    var isClosing = tag.charAt(1) === '/';
                    var isSelfClosing = tag.slice(-2) === '/>';
                    var tagNameMatch = tag.match(/^<\/?(\w+)/);
                    var tagName = tagNameMatch ? tagNameMatch[1].toLowerCase() : '';
                    var isVoid = voidElements.indexOf(tagName) !== -1;

                    if (isClosing) {
                        indent--;
                        if (indent < 0) indent = 0;

                        /* Merge opening tag + single content + closing tag onto one line */
                        if (tagName && output.length >= 2) {
                            var contentLine = output[output.length - 1].trim();
                            var openLine = output[output.length - 2].trim();
                            if (openLine.match(new RegExp('^<' + tagName + '(\\s|>)')) &&
                                !contentLine.match(/^<[^?]/)) {
                                output.splice(output.length - 2, 2);
                                output.push(tab.repeat(indent) + openLine + contentLine + tag);
                                continue;
                            }
                        }

                        /* Merge empty elements onto one line (only if prev line is a pure opening tag) */
                        if (tagName && output.length >= 1) {
                            var prevLine = output[output.length - 1].trim();
                            if (prevLine.match(new RegExp('^<' + tagName + '(\\s|>)')) && prevLine.indexOf('</') === -1) {
                                output.pop();
                                output.push(tab.repeat(indent) + prevLine + tag);
                                continue;
                            }
                        }

                        output.push(tab.repeat(indent) + tag);

                    } else if (isSelfClosing || isVoid) {
                        output.push(tab.repeat(indent) + tag);

                    } else {
                        output.push(tab.repeat(indent) + tag);
                        indent++;
                    }
                }
            }

            return output.join('\n') + '\n';
        }

        function formatCss(css) {
            var result = '';
            var indent = 0;
            var tab = '    ';
            /* Remove extra whitespace and normalize */
            css = css.replace(/\s+/g, ' ').trim();
            for (var i = 0; i < css.length; i++) {
                var ch = css[i];
                if (ch === '{') {
                    result += ' {\n';
                    indent++;
                    /* Skip whitespace after { */
                    while (i + 1 < css.length && css[i + 1] === ' ') i++;
                } else if (ch === '}') {
                    indent--;
                    if (indent < 0) indent = 0;
                    result = result.replace(/\s+$/, '\n');
                    result += tab.repeat(indent) + '}\n';
                    if (indent === 0) result += '\n';
                    /* Skip whitespace after } */
                    while (i + 1 < css.length && css[i + 1] === ' ') i++;
                } else if (ch === ';') {
                    result += ';\n';
                    /* Skip whitespace after ; */
                    while (i + 1 < css.length && css[i + 1] === ' ') i++;
                } else if (result.slice(-1) === '\n') {
                    result += tab.repeat(indent) + ch;
                } else {
                    result += ch;
                }
            }
            return result.trim() + '\n';
        }

        function formatJs(js) {
            var result = '';
            var indent = 0;
            var tab = '    ';
            var inString = false;
            var stringChar = '';
            var inLineComment = false;
            var inBlockComment = false;

            js = js.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
            var lines = js.split('\n');

            for (var l = 0; l < lines.length; l++) {
                var line = lines[l].trim();
                if (!line) {
                    result += '\n';
                    continue;
                }

                /* Decrease indent for lines starting with } or ] */
                if (line.match(/^[}\]]/)) {
                    indent--;
                    if (indent < 0) indent = 0;
                }

                result += tab.repeat(indent) + line + '\n';

                /* Track braces outside of strings and comments for indent */
                for (var i = 0; i < line.length; i++) {
                    var ch = line[i];
                    var next = i + 1 < line.length ? line[i + 1] : '';

                    if (inLineComment) break;
                    if (inBlockComment) {
                        if (ch === '*' && next === '/') { inBlockComment = false; i++; }
                        continue;
                    }
                    if (inString) {
                        if (ch === '\\') { i++; continue; }
                        if (ch === stringChar) inString = false;
                        continue;
                    }

                    if (ch === '/' && next === '/') break;
                    if (ch === '/' && next === '*') { inBlockComment = true; i++; continue; }
                    if (ch === '"' || ch === "'" || ch === '`') { inString = true; stringChar = ch; continue; }
                    if (ch === '{' || ch === '[') indent++;
                    if (ch === '}' || ch === ']') indent--;
                }
                if (indent < 0) indent = 0;
                inLineComment = false;
            }
            return result.trim() + '\n';
        }

        /* Stores combined HTML from zip upload, bypassing the textarea */
        window.zipInputHtml = null;

        /* Clear zip data when user manually edits the textarea */
        $('#input').on('input', function() {
            window.zipInputHtml = null;
            window.zipAssets = null;
            $('#zip-status').html('');
            $('#zip-upload').val('');
        });

        /* --- Webflow Export Zip Upload --- */
        $('#zip-upload').on('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

            $('#zip-status').text(' Loading...');

            JSZip.loadAsync(file).then(function(zip) {
                /* Find index.html in the zip (may be in a subfolder) */
                var indexFile = null;
                zip.forEach(function(path, entry) {
                    if (!entry.dir && path.match(/\/index\.html$/) || path === 'index.html') {
                        if (!indexFile || path.length < indexFile.length) {
                            indexFile = path;
                        }
                    }
                });

                if (!indexFile) {
                    $('#zip-status').text(' Error: No index.html found in zip');
                    return;
                }

                /* Determine the base folder path */
                var basePath = indexFile.replace('index.html', '');

                /* Read index.html first to extract nav links */
                zip.file(indexFile).async('string').then(function(indexHtml) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(indexHtml, 'text/html');

                    /* Find all nav links that point to local .html files */
                    var navEl = doc.querySelector('[cybdata="nav"]');
                    var linkedPages = [];
                    if (navEl) {
                        var links = navEl.querySelectorAll('a[href]');
                        links.forEach(function(a) {
                            var href = a.getAttribute('href');
                            if (href && href.match(/^[^#\/][^:]*\.html$/) && href !== 'index.html' && linkedPages.indexOf(href) === -1) {
                                linkedPages.push(href);
                            }
                        });
                    }

                    /* Extract the body content from index.html */
                    var bodyEl = doc.querySelector('body');
                    var combinedHtml = bodyEl ? bodyEl.innerHTML : indexHtml;

                    /* Load each linked page and extract non-common content */
                    var pagePromises = linkedPages.map(function(page) {
                        var pagePath = basePath + page;
                        var pageFile = zip.file(pagePath);
                        if (!pageFile) return Promise.resolve('');

                        return pageFile.async('string').then(function(pageHtml) {
                            var pageDoc = parser.parseFromString(pageHtml, 'text/html');
                            var pageBody = pageDoc.querySelector('body');
                            if (!pageBody) return '';

                            /* Remove all common/header/footer sections */
                            var commonEls = pageBody.querySelectorAll('[cybdata="common"], [cybdata="header"], [cybdata="footer"]');
                            commonEls.forEach(function(el) { el.remove(); });

                            /* Only return content that has cybdata attributes (skip pages with no CMS content) */
                            if (pageBody.querySelectorAll('[cybdata]').length === 0) return '';

                            return '\n<!-- Content from Page: ' + page + ' -->\n' + pageBody.innerHTML + '\n<!-- End of Content from Page: ' + page + ' -->\n';
                        });
                    });

                    Promise.all(pagePromises).then(function(pageContents) {
                        var loadedPages = [];
                        var additionalContent = '';
                        pageContents.forEach(function(content, index) {
                            if (content !== '') {
                                loadedPages.push(linkedPages[index]);
                                additionalContent += content;
                            }
                        });
                        var fullHtml = combinedHtml + additionalContent;

                        window.zipInputHtml = fullHtml;
                        window.zipAssets = { js: {}, css: {}, images: {}, documents: {} };

                        /* Collect all JS files from the /js folder */
                        var assetPromises = [];
                        zip.forEach(function(path, entry) {
                            if (!entry.dir && path.startsWith(basePath + 'js/') && path.endsWith('.js')) {
                                var filename = path.substring(path.lastIndexOf('/') + 1);
                                assetPromises.push(
                                    entry.async('uint8array').then(function(data) {
                                        window.zipAssets.js[filename] = data;
                                    })
                                );
                            }
                            /* Collect all image files from the /images folder */
                            if (!entry.dir && path.startsWith(basePath + 'images/')) {
                                var imgFilename = path.substring(path.lastIndexOf('/') + 1);
                                assetPromises.push(
                                    entry.async('uint8array').then(function(data) {
                                        window.zipAssets.images[imgFilename] = data;
                                    })
                                );
                            }
                            /* Collect all document files from the /documents folder */
                            if (!entry.dir && path.startsWith(basePath + 'documents/')) {
                                var docFilename = path.substring(path.lastIndexOf('/') + 1);
                                assetPromises.push(
                                    entry.async('uint8array').then(function(data) {
                                        window.zipAssets.documents[docFilename] = data;
                                    })
                                );
                            }
                            /* Collect all CSS files from the /css folder */
                            if (!entry.dir && path.startsWith(basePath + 'css/') && path.endsWith('.css')) {
                                var cssFilename = path.substring(path.lastIndexOf('/') + 1);
                                assetPromises.push(
                                    entry.async('uint8array').then(function(data) {
                                        window.zipAssets.css[cssFilename] = data;
                                    })
                                );
                            }
                        });
                        Promise.all(assetPromises);

                        $('#input').val('(Using zip upload - ' + loadedPages.length + ' linked pages detected)');
                        var statusText = ' Loaded index.html';
                        if (loadedPages.length > 0) {
                            statusText += ' + ' + loadedPages.length + ' linked pages:<br>' + loadedPages.join('<br>');
                        }
                        $('#zip-status').html(statusText);
                    });
                });
            }).catch(function(err) {
                $('#zip-status').text(' Error: ' + err.message);
            });
        });

        $('#parse').click(function(){

            /* Revoke previous Blob URLs to free memory */
            if (window.blockBlobUrls) {
                window.blockBlobUrls.forEach(function(url) {
                    URL.revokeObjectURL(url);
                });
            }
            window.blockBlobUrls = new Array();

            window.blockBuilderData = '';
            window.customFieldData = '';
            window.amsdTableSQL = '';
            window.customStyles = '';
            window.customScripts = '';
            window.viewFileNames = '';
            window.blocksCount = 0;
            window.customFieldCount = 0;
            window.existingCustomFields = new Array();
            window.existingBlockSlugs = new Array();
            window.blockFiles = new Array();
            window.innerCaptured = false;
            window.headerCaptured = false;
            window.footerCaptured = false;
            window.preloaderCaptured = false;
            window.preloaderContent = null;

            /* Use zip content if available, otherwise use textarea */
            var inputHtml = window.zipInputHtml || $('#input').val();

            $('#data-parsing').html(inputHtml);

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

                var itemSlug = makeSlug(key);

                if($(this).find('[cybdata]').length !== 0) {

                    if ($.inArray(itemSlug, existingCustomFields) != -1) {
                        return;
                    }
                    existingCustomFields.push(itemSlug);

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

                        if(isSkipType(nestedConfig) || nestedConfig == 'list') {

                            /* Skip these field types */
                            return;

                        } else if(nestedConfig == '' || nestedConfig == 'title' || nestedConfig == 'text' || nestedConfig == 'txt' || nestedConfig == 'videobg' || nestedConfig == 'vimeobg' || nestedConfig == 'youtubebg') {

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
                        var nestedSlug = makeSlug(nestedKey);

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

                if ($.inArray(blockSlug, existingBlockSlugs) != -1) {
                    return;
                }
                existingBlockSlugs.push(blockSlug);

                blocksCount++;

                viewFileNames += '/strings/' + blockSlug + '.php<br>';
                blockFiles.push({
                    filename: blockSlug + '.php',
                    displayPath: '/strings/' + blockSlug + '.php',
                    startMarker: '<?/* Strings Block Template for /blocks/amsd/templates/strings/' + blockSlug + '.php */?>',
                    endMarker: '<?/* End of Strings Block Template */?>'
                });

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

                    var itemSlug = makeSlug(key);

                    if(isSkipType(config)) {

                        /* Skip these field types */
                        return;

                    } else if(config == '' || config == 'title' || config == 'text' || config == 'txt' || config == 'videobg' || config == 'vimeobg' || config == 'youtubebg') {

                        config = '';

                    } else if(config == 'list') {

                        config = itemSlug;

                    } else if(config.substring(0,11) == 'previewtext') {

                        if(itemSlug == config) {
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

                        var additionalSettingsItemSlug = makeSlug(additionalSettingsItemKey);

                        if(isSkipType(additionalSettingsItemConfig)) {

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

                        hasAdditionalSettings = true;
                        existingAdditionalSettingsFields.push(additionalSettingsItemKey);

                        additionalSettingsData += '\n                [';
                        additionalSettingsData += '\n                    "key" => "' + additionalSettingsItemKey + '",';
                        additionalSettingsData += '\n                    "value" => NULL,';
                        additionalSettingsData += '\n                    "config" => ' + additionalSettingsItemConfig + '';
                        additionalSettingsData += '\n                ],';

                    });
                }

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

                if ($.inArray(blockSlug, existingBlockSlugs) != -1) {
                    return;
                }
                existingBlockSlugs.push(blockSlug);

                blocksCount++;

                var amsdSlug = 'amsd_' + blockSlug.replace(/-/g, "_");

                viewFileNames += amsdSlug + '.php<br>';
                blockFiles.push({
                    filename: amsdSlug + '.php',
                    displayPath: amsdSlug + '.php',
                    startMarker: '<?/* AMSD Block Template for /blocks/amsd/templates/' + amsdSlug + '.php */?>',
                    endMarker: '<?/* End of AMSD Block Template */?>'
                });

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

                    var itemSlug = makeSlug(key);

                    var name = config;
                    var dataType = 'varchar(255)';

                    if(isSkipType(config)) {

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
                                if ($.inArray(itemSlug, existingCustomFields) != -1) {
                                    return;
                                }
                                existingCustomFields.push(itemSlug);

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

            $('#block-builder-data-output').val(blockBuilderData);
            $('#amsd-table-sql-output').val(amsdTableSQL);
            $('#custom-field-data-output').val(customFieldData);
            $('#data-parsing').html('');

            /* --- CREATE PHP VIEW FOR TEMPLATE FILE --- */

            var parsingInput = inputHtml;

            /* Extract cybdata="style" content before DOM injection (browsers consume <style> tags) */
            /* Use a temporary detached element to parse without the browser consuming styles */
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = parsingInput.replace(/<style/gi, '<pre data-was-style ').replace(/<\/style>/gi, '</pre>');
            $(tempDiv).find('[cybdata="style"]').each(function() {
                var styleEls = $(this).is('[data-was-style]') ? $(this) : $(this).find('[data-was-style]');
                styleEls.each(function() {
                    var css = $(this).text().trim();
                    if (css && customStyles.indexOf(css) === -1) {
                        customStyles += css + '\n\n';
                    }
                });
            });
            $('#custom-styles-output').val(customStyles.trim() ? formatCss(customStyles) : '');

            /* Create download link for custom styles */
            if (customStyles.trim()) {
                var formattedStyles = formatCss(customStyles);
                var stylesBlob = new Blob([formattedStyles], { type: 'text/scss' });
                var stylesUrl = URL.createObjectURL(stylesBlob);
                blockBlobUrls.push(stylesUrl);
                $('#custom-styles-download').html('<a href="' + stylesUrl + '" download="custom_styles.scss">Download custom_styles.scss</a>');
            } else {
                $('#custom-styles-download').html('');
            }

            /* Extract cybdata="script" content before DOM injection (browsers execute <script> tags) */
            var tempDivJs = document.createElement('div');
            tempDivJs.innerHTML = parsingInput.replace(/<script/gi, '<pre data-was-script ').replace(/<\/script>/gi, '</pre>');
            $(tempDivJs).find('[cybdata="script"]').each(function() {
                var scriptEls = $(this).is('[data-was-script]') ? $(this) : $(this).find('[data-was-script]');
                scriptEls.each(function() {
                    var js = $(this).text().trim();
                    if (js && customScripts.indexOf(js) === -1) {
                        customScripts += js + '\n\n';
                    }
                });
            });
            $('#custom-scripts-output').val(customScripts.trim() ? formatJs(customScripts) : '');

            /* Create download link for custom scripts */
            if (customScripts.trim()) {
                var formattedScripts = formatJs(customScripts);
                var scriptsBlob = new Blob([formattedScripts], { type: 'application/javascript' });
                var scriptsUrl = URL.createObjectURL(scriptsBlob);
                blockBlobUrls.push(scriptsUrl);
                $('#custom-scripts-download').html('<a href="' + scriptsUrl + '" download="scripts.js">Download scripts.js</a>');
            } else {
                $('#custom-scripts-download').html('');
            }

            $('#parsing').html(parsingInput);

            $('#parsing').find('[cybkey]').each(function() {
                // Give a blank cybdata attribute to anything that has a cybkey attribute but no cybdata attribute
                if(!$(this).attr('cybdata')) {
                    $(this).attr('cybdata','');
                }
            });

            $('#parsing').find('[cybdata]').each(function() {

                var type = $(this).attr('cybdata');
                var originalType = type;
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

                if(type == 'preloader') {

                    /* Capture first occurrence content for preloader.php */
                    if (!window.preloaderCaptured) {
                        window.preloaderContent = $(this).prop('outerHTML');
                        window.preloaderCaptured = true;
                    }
                    $(this).replaceWith('<? include(FRONTEND . "/partials/preloader.php"); ?>');

                } else if(type == 'inner') {

                    /* Only capture the first inner element for inner.php */
                    if (!window.innerCaptured) {
                        $(this).before('\n\n<?/* Inner Content for inner.php */?>\n');
                        $(this).after('\n<?/* End of Inner Content */?>\n\n');
                        window.innerCaptured = true;
                    }

                } else if(type == 'printblocks') {

                    /* Mark content boundaries - children will still be processed by the loop */
                    $(this).prepend('{{PRINTBLOCKS_START}}');
                    $(this).append('{{PRINTBLOCKS_END}}');

                } else if(type == 'pagetitle') {

                    $(this).html('<?= $titleText ?>');
                    $(this).before('<? if(isset($titleText)) { ?>');
                    $(this).after('<? } ?>');

                } else if(type == 'breadcrumbs') {

                    $(this).replaceWith('<? include(FRONTEND . "/partials/breadcrumbs.php"); ?>');

                } else if(type == 'pagesubtitle') {

                    $(this).html('<?= $subtitleText ?>');
                    $(this).before('<? if(isset($subtitleText)) { ?>');
                    $(this).after('<? } ?>');

                } else if(type == 'pagebanner') {

                    /* Add interior banner PHP to the element's opening tag */
                    $(this).removeAttr('style');
                    $(this).attr('data-pagebanner', 'true');

                } else if(type == 'style' || type == 'script') {

                    /* Remove from PHP output (content already extracted from raw string) */
                    $(this).remove();

                } else if(type == 'buttontext') {

                    /* Skip */

                } else if(type == 'common') {

                    $(this).before('\n\n<?/* Common Items Area */?>\n');
                    $(this).after('\n<?/* End of Common Items Area */?>\n\n');

                } else if(type == 'header') {

                    if (!window.headerCaptured) {
                        $(this).before('\n\n<?/* Header Content for /partials/header-content.php */?>\n');
                        $(this).after('\n<?/* End of Header Content */?>\n\n');
                        window.headerCaptured = true;
                    }

                } else if(type == 'footer') {

                    if (!window.footerCaptured) {
                        $(this).before('\n\n<?/* Footer Content for /partials/footer-content.php */?>\n');
                        $(this).after('\n<?/* End of Footer Content */?>\n\n');
                        window.footerCaptured = true;
                    }

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
                        /* If not inside a block, add template markers around the element */
                        if($(this).parents('[cybdata="block"]').length == 0) {
                            $(this).before('\n\n<?/* AMSD Block Template for /blocks/amsd/templates/' + amsdSlug + '.php */?>\n<? $DATA = strings($block->id); ?>\n');
                            $(this).after('\n<?/* End of AMSD Block Template */?>\n\n');
                        }
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


                } else if(type == 'videobg' || type == 'vimeobg' || type == 'youtubebg') {

                    $(this).replaceWith('<? $video = parseVideo(' + prefix + key + suffix + '); ?>\n<? if($video["host"] == "youtube") { $backgroundVideoSrc = "https://www.youtube.com/embed/" . $video["id"] . "?autoplay=1&amp;controls=0&amp;rel=0&amp;mute=1&amp;loop=1&amp;playlist=" . $video["id"]; } else if($video["host"] == "vimeo") { $backgroundVideoSrc = "https://player.vimeo.com/video/" . $video["id"] . "?background=1"; } ?>\n<? if($video["id"]) { ?>\n<div class="video-background-wrapper-outer visible">\n<div class="video-background-wrapper-inner">\n<div class="video-background">\n<iframe class="cms-video-vimeo" src="<?= $backgroundVideoSrc ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>\n</div>\n</div>\n</div>\n<? } ?>');

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
                    $(this).attr("href","mailto:<?= " + prefix + key + suffix + " ?>");
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
                    $(this).html('<?= fa_icon(' + prefix + key + suffix + ') ?>');

                } else if(type == 'url') {

                    if(originalType == 'profileurl') {

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

            $('#parsing').find('[cybdata="common"], [cybdata="header"], [cybdata="footer"]').each(function() {
                $(this).find('[cybdata]').each(function() {
                    $(this).html($(this).html().replace(/\$DATA/g,'$COMMON_ITEMS'));
                });
            });
 
            $('#parsing').find('[cybdata]').removeAttr('cybdata');
            $('#parsing').find('[cybkey]').removeAttr('cybkey');

            var phpOutput = $('#parsing').html().replace(/<!--\?/g, '<?').replace(/\?-->/g, '?>').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/=-->/g, '=>').replace(/--->/g, '->').replace('{{greaterthan}}', '>').replace(/="images\//g, '="/assets/images/').replace(/="documents\//g, '="/assets/documents/').replace(/ data-pagebanner="true"/g, ' <?= $interiorBannerStyles ?>');

            /* --- Create downloadable files --- */
            var downloadLinks = 'Download Individual PHP Files:<br>';
            var zip = new JSZip();
            var hasFiles = false;

            if (blockFiles.length > 0) {
                var templateBasePath = '/site/views/frontend/blocks/amsd/templates';
                var templatesFolder = zip.folder('site/views/frontend/blocks/amsd/templates');
                var stringsFolder = templatesFolder.folder('strings');

                blockFiles.forEach(function(block, index) {
                    var startIdx = phpOutput.indexOf(block.startMarker);
                    var endIdx = phpOutput.indexOf(block.endMarker, startIdx);
                    if (startIdx !== -1 && endIdx !== -1) {
                        var contentStart = startIdx + block.startMarker.length;
                        var content = phpOutput.substring(contentStart, endIdx).trim();
                        content = content.replace(/\{\{PRINTBLOCKS_START\}\}[\s\S]*?\{\{PRINTBLOCKS_END\}\}/g, '<? include(FRONTEND . "/partials/print-blocks.php"); ?>');
                        content = formatHtml(content);

                        var blob = new Blob([content], { type: 'application/x-php' });
                        var url = URL.createObjectURL(blob);
                        blockBlobUrls.push(url);
                        var fullDisplayPath = block.displayPath.indexOf('/strings/') === 0 ? templateBasePath + block.displayPath : templateBasePath + '/' + block.displayPath;
                        downloadLinks += '<a href="' + url + '" download="' + block.filename + '">' + fullDisplayPath + '</a>';

                        /* Add file to zip */
                        if (block.displayPath.indexOf('/strings/') === 0) {
                            stringsFolder.file(block.filename, content);
                        } else {
                            templatesFolder.file(block.filename, content);
                        }
                        hasFiles = true;
                    } else {
                        var fullFallbackPath = block.displayPath.indexOf('/strings/') === 0 ? templateBasePath + block.displayPath : templateBasePath + '/' + block.displayPath;
                        downloadLinks += fullFallbackPath;
                    }
                    if (index < blockFiles.length - 1) {
                        downloadLinks += '<br>';
                    }
                });
            }

                /* Add custom styles to zip */
                if (customStyles.trim()) {
                    zip.folder('site/views/frontend/assets/scss').file('custom_styles.scss', formatCss(customStyles));
                    hasFiles = true;
                }

                /* Add custom scripts to zip */
                if (customScripts.trim()) {
                    zip.folder('site/views/frontend/assets/js').file('scripts.js', formatJs(customScripts));
                    hasFiles = true;
                }

                /* Add header content to zip */
                var headerStartMarker = '<?/* Header Content for /partials/header-content.php */?>';
                var headerEndMarker = '<?/* End of Header Content */?>';
                var headerStartIdx = phpOutput.indexOf(headerStartMarker);
                var headerEndIdx = phpOutput.indexOf(headerEndMarker, headerStartIdx);
                if (headerStartIdx !== -1 && headerEndIdx !== -1) {
                    var headerContent = phpOutput.substring(headerStartIdx + headerStartMarker.length, headerEndIdx).trim();
                    headerContent = formatHtml(headerContent);
                    zip.folder('site/views/frontend/partials').file('header-content.php', headerContent);
                    hasFiles = true;

                    var headerBlob = new Blob([headerContent], { type: 'application/x-php' });
                    var headerUrl = URL.createObjectURL(headerBlob);
                    blockBlobUrls.push(headerUrl);
                    downloadLinks += '<br><a href="' + headerUrl + '" download="header-content.php">/site/views/frontend/partials/header-content.php</a>';
                }

                /* Add footer content to zip */
                var footerStartMarker = '<?/* Footer Content for /partials/footer-content.php */?>';
                var footerEndMarker = '<?/* End of Footer Content */?>';
                var footerStartIdx = phpOutput.indexOf(footerStartMarker);
                var footerEndIdx = phpOutput.indexOf(footerEndMarker, footerStartIdx);
                if (footerStartIdx !== -1 && footerEndIdx !== -1) {
                    var footerContent = phpOutput.substring(footerStartIdx + footerStartMarker.length, footerEndIdx).trim();
                    footerContent = formatHtml(footerContent);
                    zip.folder('site/views/frontend/partials').file('footer-content.php', footerContent);
                    hasFiles = true;

                    var footerBlob = new Blob([footerContent], { type: 'application/x-php' });
                    var footerUrl = URL.createObjectURL(footerBlob);
                    blockBlobUrls.push(footerUrl);
                    downloadLinks += '<br><a href="' + footerUrl + '" download="footer-content.php">/site/views/frontend/partials/footer-content.php</a>';
                }

                /* Add preloader content to zip */
                if (window.preloaderContent) {
                    var preloaderHtml = window.preloaderContent.replace(/<!--\?/g, '<?').replace(/\?-->/g, '?>').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/=-->/g, '=>').replace(/--->/g, '->').replace(/="images\//g, '="/assets/images/').replace(/="documents\//g, '="/assets/documents/');
                    preloaderHtml = formatHtml(preloaderHtml);
                    zip.folder('site/views/frontend/partials').file('preloader.php', preloaderHtml);
                    hasFiles = true;

                    var preloaderBlob = new Blob([preloaderHtml], { type: 'application/x-php' });
                    var preloaderUrl = URL.createObjectURL(preloaderBlob);
                    blockBlobUrls.push(preloaderUrl);
                    downloadLinks += '<br><a href="' + preloaderUrl + '" download="preloader.php">/site/views/frontend/partials/preloader.php</a>';
                }

                /* Add inner content to zip */
                var innerStartMarker = '<?/* Inner Content for inner.php */?>';
                var innerEndMarker = '<?/* End of Inner Content */?>';
                var innerStartIdx = phpOutput.indexOf(innerStartMarker);
                var innerEndIdx = phpOutput.indexOf(innerEndMarker, innerStartIdx);
                if (innerStartIdx !== -1 && innerEndIdx !== -1) {
                    var innerContent = phpOutput.substring(innerStartIdx + innerStartMarker.length, innerEndIdx).trim();
                    innerContent = innerContent.replace(/\{\{PRINTBLOCKS_START\}\}[\s\S]*?\{\{PRINTBLOCKS_END\}\}/g, '<? include(FRONTEND . "/partials/print-blocks.php"); ?>');
                    innerContent = formatHtml(innerContent);
                    innerContent = '<? include(FRONTEND . "/header.php"); ?>\n\n' + innerContent.trimEnd() + '\n\n<? include(FRONTEND . "/footer.php"); ?>\n';
                    zip.folder('site/views/frontend').file('inner.php', innerContent);
                    hasFiles = true;

                    var innerBlob = new Blob([innerContent], { type: 'application/x-php' });
                    var innerUrl = URL.createObjectURL(innerBlob);
                    blockBlobUrls.push(innerUrl);
                    downloadLinks += '<br><a href="' + innerUrl + '" download="inner.php">/site/views/frontend/inner.php</a>';
                }

                /* Add JS files from zip upload if available */
                if (window.zipAssets && window.zipAssets.js) {
                    var jsFolder = zip.folder('site/views/frontend/assets/js');
                    Object.keys(window.zipAssets.js).forEach(function(filename) {
                        jsFolder.file(filename, window.zipAssets.js[filename]);
                        hasFiles = true;
                    });
                }

                /* Add document files from zip upload */
                if (window.zipAssets && window.zipAssets.documents) {
                    var documentsFolder = zip.folder('site/views/frontend/assets/documents');
                    Object.keys(window.zipAssets.documents).forEach(function(filename) {
                        documentsFolder.file(filename, window.zipAssets.documents[filename]);
                        hasFiles = true;
                    });
                }

                /* Add image files from zip upload */
                if (window.zipAssets && window.zipAssets.images) {
                    var imagesFolder = zip.folder('site/views/frontend/assets/images');
                    Object.keys(window.zipAssets.images).forEach(function(filename) {
                        imagesFolder.file(filename, window.zipAssets.images[filename]);
                        hasFiles = true;
                    });
                }

                /* Add CSS files from zip upload as .scss */
                if (window.zipAssets && window.zipAssets.css) {
                    var scssFolder = zip.folder('site/views/frontend/assets/scss');
                    Object.keys(window.zipAssets.css).forEach(function(filename) {
                        var scssFilename = filename.replace(/\.css$/, '.scss');
                        scssFolder.file(scssFilename, window.zipAssets.css[filename]);
                        hasFiles = true;
                    });
                }

                /* Generate zip download link */
                if (hasFiles) {
                    zip.generateAsync({ type: 'blob' }).then(function(zipBlob) {
                        var zipUrl = URL.createObjectURL(zipBlob);
                        blockBlobUrls.push(zipUrl);
                        $('#zip-download-button').html('<a href="' + zipUrl + '" download="cybflow.zip" class="button green w-button">Download Parsed Output (cybflow.zip)</a>');
                        $('#view-files-output').html(downloadLinks);
                    });
                } else {
                    $('#view-files-output').html(downloadLinks);
                    $('#zip-download-button').html('');
                }

            /* Replace printblocks and preloader markers with includes (after template files have been extracted) */
            phpOutput = phpOutput.replace(/\{\{PRINTBLOCKS_START\}\}[\s\S]*?\{\{PRINTBLOCKS_END\}\}/g, '<? include(FRONTEND . "/partials/print-blocks.php"); ?>');

            $('#php-output').val(formatHtml(phpOutput));

            $('#parsing').html('');
            $('#php-output').focus();

        });

        $('.textarea').focus(function() { 
            this.select(); 
        });

    });

}