<?php

/*
-------------------------------------------------------------------
 PageBuilder / Custom Pages
-------------------------------------------------------------------

    These functions make it possible to enter custom page types into the "+" button
    in the sitemap, and define their page type, and build out the blocks and data that
    should populate the page.

    This should be a replacement for telling the client that they must "clone" a page
    to get custom funcionality we have defined for them.

*/

/* Define the custom entries in the pagebuilder menu */

function customGeneratePageBuilderMenu() {

    // Return the current CodeIgniter instance
    $ci=& get_instance();

    $output = [];

    /*
    -------------------------------------------------------------------
     Available Fields:
    -------------------------------------------------------------------

        $output[] = [
            "title" => "Custom Page Name",   (Required) (String) Label for the entry
            "value" => "custom-page-name",   (Required) (String) Used in the other functions to identify this entry
            "icon" => "fa-cog",             (Optional) (String) Use font-awesome class name here.  If not supplied, it will use a fallback "custom" icon
            "last" => "text-block",          (Optional) ("text-block" | "link" | "link-inner" | false) Controls the option right before cliking save
            "dev" => true,                   (Optional) (Boolean) Make the choice only available to dev login
            "styles" => [                    (Optional) (Array) This controlls the 2nd tier of options when creating the page
                [
                    "title" => "Option 1",
                    "value" => "custom-option-1",
                    "icon" => "fa-bolt"
                ], [
                    "title" => "Option 2",
                    "value" => "custom-option-2",
                    "icon" => "fa-link"
                ]
            ]
        ];

    -------------------------------------------------------------------
     Example:
    -------------------------------------------------------------------

        $output[] = [
            "title" => "AMSD With Categories",
            "value" => "amsd-with-categories",
            "icon" => "fa-cog",
            "last" => "text-block"
        ];

    */

    return $output;

}

/* Define the page type ID */

function customCreatePageTypeToID($type) {

    // Return the current CodeIgniter instance
    $ci=& get_instance();

    $return = false;

    switch($type) {

        /*
        -------------------------------------------------------------------
         Example:
        -------------------------------------------------------------------

            case "amsd-with-categories":

                $return = 3;  // Set the page type to "news"

                break;

        */

        default:

            break;

    }

    return $return;

}

/* Define the page tags */

function customCreatePageTypeToTags($type) {

    // Return the current CodeIgniter instance
    $ci=& get_instance();

    $return = [];

    switch($type) {

        /*
        -------------------------------------------------------------------
         Example:
        -------------------------------------------------------------------

            case "amsd-with-categories":

                $return[] = "amsd-with-categories";  // Add "amsd-with-categories" to tags

                break;

        */

        default:

            break;

    }

    return $return;

}

/* Define the blocks that belong to the page by default */

function customCreatePageBlocks($config) {

    // Return the current CodeIgniter instance
    $ci=& get_instance();

    $blocks = [];

    $type = isset($config["type"]) ? $config["type"] : false;
    $layout = isset($config["layout"]) ? $config["layout"] : false;

    $blockLayouts = customSortedListBlocksByValue();

    switch($type) {

        /*
        -------------------------------------------------------------------
         Example:
        -------------------------------------------------------------------

            case "amsd-with-categories":

                // Create a standard AMSD Block w/ Category Field

                $blocks[] = [
                    "type" => 2,
                    "settings" => [
                        ["Table", "amsd_standard_with_categories"],
                        ["AMSD Category Table", "amsd_categories"],
                        ["AMSD Filter Key", "category"],
                        ["AMSD Filter Value", "1"],
                        ["AMSD Config", "[\"title\",\"sub_title\"]"]
                    ]
                ];

                // Create a categories table

                $blocks[] = [
                    "type" => 2,
                    "title" => "Categories",
                    "settings" => [
                        ["Table", "amsd_categories"],
                        ["Heading Read Only", "true"]
                    ],
                    "items" => [
                        ["title" => "Preset Category Name 1", "slug" => "preset-category-name-1", "pos" => 0],
                        ["title" => "Preset Category Name 2", "slug" => "preset-category-name-2", "pos" => 1]
                    ]
                ];

                // Create an AMSD strings table and populate with preset inputs

                $blocks[] = [
                    "type" => 2,
                    "title" => "Settings",
                    "settings" => [
                        ["Table", "amsd_strings"],
                        ["Hidden", "true"],
                        ["AMSD Columns", "2"],
                        ["AMSD Delete", "false"],
                        ["AMSD Edit", "false"],
                        ["Heading Read Only", "true"]
                    ],
                    "items" => [
                        ["key" => "Custom Option 1", "value" => 1, "config" => "checkbox", "pos" => 0],
                        ["key" => "Custom Option 2", "value" => "", "config" => "textarea", "pos" => 1]
                    ]
                ];

                break;

        */
                
    }

    return $blocks;

}

/* Runs after the page is created to allow you to do extra work like linking blocks and whatnot */

function customCreatePageAfter($config, $pageID) {

    // Return the current CodeIgniter instance
    $ci=& get_instance();

    // Load ei_model for manipulating block settings
    $ci->load->model("editorinstance/ei_post");

    $type = isset($config["type"]) ? $config["type"] : false;
    $layout = isset($config["layout"]) ? $config["layout"] : false;

    $page = getPage($pageID);

    switch($type) {

        /*
        -------------------------------------------------------------------
         Example:
        -------------------------------------------------------------------

            // Let's set the "Category Table Block" ID for "amsd_standard_with_categories"

            case "amsd-with-categories":

                $amsd_block_id = false;
                $category_block_id = false;

                foreach($page->blocks as $block) {

                    if(isset($block->settings["Table"])) {

                        if($block->settings["Table"] == "amsd_standard_with_categories") {

                            $amsd_block_id = $block->id;

                        }

                        if($block->settings["Table"] == "amsd_categories") {

                            $category_block_id = $block->id;

                        }

                    }

                }

                if($category_block_id) {

                    $ci->ei_post->setBlockSetting($amsd_block_id, "Category Table Block", $category_block_id);

                }

                break;

        */



    }

}

/* Runs after the block is created to allow you to do extra work like linking blocks and whotnot */

function customCreateBlockAfter($config, $pageID, $blockID) {

    // Return the current CodeIgniter instance
    $ci=& get_instance();

    // Load ei_model for manipulating block settings
    $ci->load->model("editorinstance/ei_post");

    $type = isset($config["type"]) ? $config["type"] : false;

    //$page = getPage($pageID);
    $block_settings = $ci->core->getBlockSettings($blockID);

    // Set all block headings to read only
    $ci->ei_post->setBlockSetting($blockID, "Heading Read Only", "true");

    // Create variable for custom block headings, gets sent to db at the end of customCreateBlockAfter
    $custom_title = false;

    // Set text block headings
    if($type == 1) {
        $custom_title = 'Text Area';
    }

    // Set standard amsd block headings
    if($block_settings["Table"] == 'amsd_standard' && $block_settings["AMSD Layout"] == 'vertical-list') {
        $custom_title = 'Sorted List';
    }

    // Set standard amsd block headings
    if($block_settings["Table"] == 'amsd_standard' && $block_settings["AMSD Layout"] == 'grid-list') {
        $custom_title = 'Grid List';
    }

    // Set categorized amsd block headings
    if($block_settings["Table"] == 'amsd_standard_with_categories') {
        $custom_title = 'Sorted List with Categories';
    }

    // Set news block headings
    if($block_settings["Table"] == 'amsd_news') {
        $custom_title = 'News List';
    }

    // Set photo album block headings
    if($block_settings["Table"] == 'module_gallery' && $block_settings["Template"] == 'album') {
        $custom_title = 'Photo Album';
    }

    // Set photo gallery block headings
    if($block_settings["Table"] == 'module_gallery' && $block_settings["Template"] == 'gallery') {
        $custom_title = 'Gallery of Photo Albums';
    }

    // Update block heading if a custom title is set
    if($custom_title) {
        $ci->db->where("id", $blockID)->limit(1)->update("core_blocks", [
            "title" => $custom_title
        ]);
    }

}

function customSortedListBlocksByValue() {

    $customSortedListBlocks = customSortedListBlocks();
    $blockLayouts = [];

    foreach($customSortedListBlocks as $cslb) {
        $blockLayouts[$cslb["value"]] = $cslb["block"];
    }

    return $blockLayouts;

}

/* Allows additional "Sorted List" presets in the developer-only "Add Section" menu bar */

function customSortedListBlocks() {

    $items = [];

    /*
    -------------------------------------------------------------------
     Example:
    -------------------------------------------------------------------

    Add a custom AMSD strings block with preset block settings and
    AMSD items:

    $items[] = [
        "title" => "Menu Title",   // (Required) (String) Label for the entry in the "Add Section" toolbar
        "value" => "menu-title",   // (Required) (String) Value for the entry to be passed into pagebuilder
        "block" => [               // (Required) (Array) Block configuration
            "type" => 2,
            "title" => "Block Title",
            "settings" => [
                ["AMSD Config", "[\"key\", \"value\"]"],
                ["Heading Read Only", "true"],
                ["Table", "amsd_strings"]
            ],
            "items" => [
                [
                    "key" => "Test",
                    "value" => "1",
                    "config" => "checkbox"
                ]
            ]
        ]
    ];

    */

    $items[] = [
        "title" => "Dev Config",
        "value" => "dev-config",
        "block" => [
            "type" => 2,
            "title" => "Dev Config",
            "settings" => [
                ["AMSD Columns", 2],
                ["AMSD Delete", "false"],
                ["AMSD Edit", "false"],
                ["Heading Read Only", "true"],
                ["Table", "amsd_strings"],
                ["Developer Only", "true"],
                ["Hidden", "true"]
            ],
            "items" => [
                [
                    "key" => "Logo",
                    "config" => "photo"
                ],[
                    "key" => "Logo on Scroll",
                    "config" => "photo"
                ],[
                    "key" => "Logo in Footer",
                    "config" => "photo"
                ]
            ]
        ],
        "dev" => true
    ];

    $items[] = [
        "title" => "Common Items",
        "value" => "common-items",
        "block" => [
            "type" => 2,
            "title" => "Common Items",
            "settings" => [
                ["AMSD Columns", 2],
                ["AMSD Delete", "false"],
                ["AMSD Edit", "false"],
                ["Heading Read Only", "true"],
                ["Table", "amsd_strings"],
                ["Hidden", "true"]
            ],
            "items" => [
                [
                    "key" => "Default Page Banner Photo",
                    "config" => "focused_img"
                ],[
                    "key" => "Email Address"
                ],[
                    "key" => "Phone Number"
                ],[
                    "key" => "Address",
                    "config" => "textarea"
                ],[
                    "key" => "Hours"
                ],[
                    "key" => "Social Media Links",
                    "config" => "social_media_links"
                ],[
                    "key" => "Footer Links",
                    "value" => '[{"title":"Accessibility Statement","url":""},{"title":"Privacy Policy","url":""}]',
                    "config" => "links"
                ]
            ]
        ],
        "dev" => true
    ];

    $items[] = [
        "title" => "Form/Code Embed",
        "value" => "embed-code",
        "block" => [   
            "type" => 2,
            "title" => "Form/Code Embed",
            "settings" => [
                ["Table", "amsd_strings"],
                ["Heading Read Only", "true"],
                ["AMSD Columns", "2"],
                ["AMSD Delete", "false"],
                ["AMSD Edit", "false"],
                ["Template", "embed-code"]
            ],
            "items" => [
                [
                    "key" => "Embed Code",
                    "config" => "textarea"
                ]
            ]
        ],
        "dev" => true
    ];

    $items[] = [
        "title" => "Alert Bar",
        "value" => "alert-bar",
        "block" => [
            "type" => 2,
            "title" => "Alert Bar",
            "settings" => [
                ["Heading Read Only", "true"],
                ["AMSD Limit", 1],
                ["AMSD Noun", "Alert"],
                ["Table", "amsd_alert_bar"],
                ["Hidden", "true"]
            ],
            "items" => [
                [
                    "active" => 1,
                    "icon" => "fas fa-triangle-exclamation",
                    "textarea" => 'Lorem ipsum dolor sit amet, eam everti tractatos cu, ea vis brute ullamcorper, nominavi probatus posidonium cu his.',
                    "button" => '{"text":"Learn More","url":""}'
                ]
            ]
        ],
        "dev" => true
    ];

    $items[] = [
        "title" => "Popup Window",
        "value" => "popup",
        "block" => [
            "type" => 2,
            "title" => "Popup Window",
            "settings" => [
                ["Heading Read Only", "true"],
                ["AMSD Limit", 1],
                ["AMSD Noun", "Popup"],
                ["Table", "amsd_popup"],
                ["Hidden", "true"]
            ],
            "items" => [
                [
                    "active" => 1,
                    "title" => "Test",
                    "html" => '<div class="heading">Lightbox <span style="color:#63a34a;">Heading</span></div><p>The Rural Energy for America Program provides guaranteed loan financing and grant funding to agricultural producers and rural small businesses to purchase or install renewable energy systems or make energy efficiency improvements.</p><a class="cms-btn cms-btn-primary" data-btn-text="Button Text" href="#">Button Text</a></p>'
                ]
            ]
        ],
        "dev" => true
    ];

    $items[] = [
        "title" => "Accordion List",
        "value" => "accordion-list",
        "block" => [
            "type" => 2,
            "title" => "Accordion List",
            "settings" => [
                ["Heading Read Only", "true"],
                ["Table", "amsd_accordion"]
            ]
        ],
        "dev" => true
    ];

    $items[] = [
        "title" => "Reviews Embed",
        "value" => "reviews-embed",
        "block" => [
            "type" => 2,
            "title" => "Reviews Embed",
            "settings" => [
                ["AMSD Columns", 2],
                ["AMSD Delete", "false"],
                ["AMSD Edit", "false"],
                ["Heading Read Only", "true"],
                ["Table", "amsd_strings"],
                ["Template", "reviews-embed"]
            ]
        ],
        "items" => [
            [
                "key" => "Reviewability ID",
                "value" => ""
            ]
        ],
        "dev" => true
    ];
    
    return $items;

}

/* Define schema for custom page settings */

function customPageSettingsSchema($pageID = NULL) {

    $items = [];

    /*
    -------------------------------------------------------------------
     Example:
    -------------------------------------------------------------------

    Add a page banner setting to each page:

    $items[] = [
        "key" => "Page Banner",
        "value" => NULL,
        "config" => "focused_img"
    ];

    Pull the default value from another block:

    $defaults = strings(DEFAULT_SETTINGS_BLOCK);

    $items[] = [
        "key" => "Page Banner",
        "value" => $defaults["Default Page Banner"],
        "config" => "focused_img"
    ];

    Only apply this to root-level pages:

    $page = getPage($pageID);

    if(is_null($page->parent)) {

        $items[] = [
            "key" => "Page Banner",
            "value" => $defaults["Default Page Banner"],
            "config" => "focused_img"
        ];

    }

    */

    $page = getPage($pageID);

    if(is_null($page->parent)) {
        // Only applies to root level main menu items
    }

    $items[] = [
        "key" => "Page Banner",
        "value" => NULL,
        "config" => "focused_img"
    ];

    $items[] = [
        "key" => "Title Override",
        "value" => NULL,
        "config" => "text"
    ];

    $items[] = [
        "key" => "Subtitle",
        "value" => NULL,
        "config" => "text"
    ];

    return $items;

}

?>
