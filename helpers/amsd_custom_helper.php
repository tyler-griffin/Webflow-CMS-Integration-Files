<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
-------------------------------------------------------------------
 CUSTOM AMSD HELPER FUNCTIONS
-------------------------------------------------------------------

If you are here, it's because you need to make a fully custom AMSD module for a specific client.
This file exists to "extend" the core CMS presets for building form fields in the backend,
so that we can set up custom rules for a specific client.

You can check out "/applications/modules/amsd/libraries/field_builder.php" to see the core library that
this file extends.

Sites that were installed and confgured prior to this update (2015-06-19) will still use the old "amsdInput()", and as such, the depreciated "amsdCustomInput()" function has been left at the bottom of this file.

For a detailed look at how to work with these new functions, see the documentation:
https://cms.cybernautic.com/#documentation

-------------------------------------------------------------------
 Field Builder / Custom Preset
-------------------------------------------------------------------

Override core CMS field builder presets	here.

Arguments:

    - $KEY = (String) The name of the field
    - $CONFIG = (Array) Array of configuration values passed in from the backend view
    - $BLOCK = (Array) The entire contents of the getBlock() function for this item's block
    - $ITEM = (Array) The entire AMSD item's key => value pairs
    - $GRID = (Boolean) TRUE if field is in the FlexGrid, FALSE if it's in the edit screen
    - $FILTER = (Boolean) TRUE if field is being used as the AMSD Filter
    - $FIELD = (Object) Contains entire Class/Object passed in from Field Builder library

*/

function field_builder_custom_preset($KEY, $CONFIG, $BLOCK, $ITEM, $GRID, $FILTER, $FIELD) {

    // Return the current CodeIgniter instance
    $ci=& get_instance();

    /* -- Options that may be passed in via $CONFIG ------------------------------ */

    // Field type.  If not specified use the $KEY.
    $TYPE = isset($CONFIG["type"]) ? ($CONFIG["type"]) : $KEY;

    // Field value.  If not specified look for the value in $ITEM by $KEY.
    $VAL = isset($CONFIG["value"]) ? ($CONFIG["value"]) : (isset($ITEM[$KEY]) ? $ITEM[$KEY] : "");

    // Field style.  If not specified use no style.
    $STYLE = isset($CONFIG["style"]) ? 'style="' . $CONFIG["style"] . '"' : "";

    // Field attributes.  If not specified use none.
    $ATTRIBUTES = isset($CONFIG["attributes"]) && is_array($CONFIG["attributes"]) ? $CONFIG["attributes"] : Array();

    // Field label.  If not specified use the $KEY (remove underscores and uppercase words)
    $LABEL = isset($CONFIG["label"]) ? $CONFIG["label"] : ucwords(str_replace("_", " ", amsdCustomKey($KEY, $BLOCK["id"])));

    // Automatically wrap field with HTML markup and <label>
    $LABEL_MARKUP = (isset($CONFIG["markup"]) && !$CONFIG["markup"]) ? FALSE : (!$GRID && $LABEL ? TRUE : FALSE);

    // Extra info for the strings key (if applicable)
    $STRINGS_KEY_LABEL_SUFFIX = false;

    /*
    -------------------------------------------------------------------------------------
        EXTRA ATTRIBUTES
        - String including style attribute + custom attributes passed by config
    -------------------------------------------------------------------------------------
    */

    $EXTRA_ATTRIBUTES = $STYLE;

    foreach($ATTRIBUTES as $ATTR_KEY => $ATTR_VAL) { $EXTRA_ATTRIBUTES .= ' ' . $ATTR_KEY . '="' . $ATTR_VAL . '"'; }

    /*
    -------------------------------------------------------------------------------------
     CUSTOM FIELD "TYPE" CONVERSION
      - Use the switch below to create your custom fields.
      - Remember that $TYPE, if not explicitly specified by passing it into the field's
        configuration, will be equal to the field's name/key.
    -------------------------------------------------------------------------------------
    */

    $OUTPUT = false;

    switch($TYPE) {

        case "value":

            if($BLOCK["settings"]["Table"] == "amsd_strings") {



            }

            break;

        case "categories":

                $categoriesBlock = getBlock($BLOCK["settings"]['Category Table Block']);

                $categoriesArray = array();
                foreach($categoriesBlock["data"] as $k => $CATEGORY) {
                    $categoriesArray[$CATEGORY->slug] = $CATEGORY->title;
                }

                $OUTPUT = $FIELD->build($KEY, [ 
                    "type" => "checkbox_group",
                    "options" => $categoriesArray
                ], false);
         
                break;

        case "icon":
 
            $OUTPUT = $FIELD->build($KEY, [
                "type" => "font_awesome"
            ], false);
     
            break;

        case "social_icon":
 
            $OUTPUT = $FIELD->build($KEY, [
                "type" => "font_awesome",
                "options" => [
                    "preset" => "social"
                ]
            ], false);
     
            break;

         case "contact_info_icon":
 
                $OUTPUT = $FIELD->build($KEY, [
                    "type" => "font_awesome",
                    "options" => [
                        "include" => [
                            "envelope",
                            "phone",
                            "phone-alt",
                            "mobile-alt",
                            "fax",
                            "map-marker-alt",
                            "map-signs",
                            "home",
                            "paper-plane",
                            "external-link-alt",
                            "arrow-alt-circle-right",
                            "info-circle",
                            "check"
                        ]
                    ]
                ], false);
         
                break;

        case "social_links":

            if($GRID) {

                $OUTPUT = '<a class="fg-edit-html-in-strings-table"><span>Click Here to Edit Social Links</span></a>';

            } else {

                $LABEL_MARKUP = false;

                $fields = [
                    [
                        "key" => "title",
                        "label" => "Title",
                        "config" => [
                            "type" => "text"
                        ]
                    ],[
                        "key" => "icon",
                        "label" => "Icon",
                        "config" => [
                            "type" => "font_awesome",
                            "options" => [
                                "preset" => "social"
                            ]
                        ]
                    ],
                    [
                        "key" => "url",
                        "label" => "URL",
                        "config" => [
                            "type" => "url"
                        ]
                    ]
                ];

                $FIELD_HTML .= $FIELD->special($KEY, [
                    "type" => "sorted_list",
                    "fields" => $fields
                ]);

                $OUTPUT = '<div class="amsd-hr"></div>';

                $OUTPUT .= '<br><span class="backend-label">Social Links</span><div class="field"><div class="field-inner">' . $FIELD_HTML . '</div></div>';

            }

            break;

    }

    /*
    -------------------------------------------------------------------------------------
     Append the standard CMS backend markup around the field.
     (Can be switched off by passing "markup" => FALSE into the field's config.
    -------------------------------------------------------------------------------------
    */

    if($OUTPUT !== FALSE && $LABEL_MARKUP && strpos($OUTPUT, '<div class="field">') === FALSE) {

        $OUTPUT = '<div class="field"><label>' . $LABEL . '</label><div class="field-inner">' . $OUTPUT . '</div></div>';

    }

    /*
    -------------------------------------------------------------------------------------
     If $STRINGS_KEY_LABEL_SUFFIX is defined, append it.
     This will get picked up on by some core javascript and will pop it over 
     into the key field.
    -------------------------------------------------------------------------------------
    */

    if($STRINGS_KEY_LABEL_SUFFIX && strpos($STRINGS_KEY_LABEL_SUFFIX, '<div class="strings-key-label-suffix">') === FALSE) {

        $OUTPUT .= '<div class="strings-key-label-suffix">' . $STRINGS_KEY_LABEL_SUFFIX . '</div>';

    }

    return $OUTPUT;

}

/*
-------------------------------------------------------------------
 Field Builder / Custom Group
-------------------------------------------------------------------

Override core CMS field builder groups here.

Arguments:

    - $GROUP = (String) The name of the group (last segment of the table name)
    - $CONFIG = (Array) Array of configuration values passed in from the backend view
    - $BLOCK = (Array) The entire contents of the getBlock() function for this item's block
    - $ITEM = (Array) The entire AMSD item's key => value pairs
    - $GRID = (Boolean) TRUE if field is in the FlexGrid, FALSE if it's in the edit screen
    - $FILTER = (Boolean) TRUE if field is being used as the AMSD Filter
    - $FIELD = (Object) Contains entire Class/Object passed in from Field Builder library

*/

function field_builder_custom_group($GROUP, $CONFIG, $BLOCK, $ITEM, $GRID, $FILTER, $FIELD) {

    $ci=& get_instance();

    $GROUP_KEY = $GROUP->name;

    if(array_key_exists($GROUP_KEY, $ITEM)) {

        switch($GROUP_KEY) {

            default:

                $OUTPUT = false;

                break;

        }

        return $OUTPUT;

    } else {
        return false;
    }

}

/*
-------------------------------------------------------------------------------------
 AMSD Custom Key
-------------------------------------------------------------------------------------

Changes the name of a field heading/label in the FlexGrid / Edit screen

*/

function amsdCustomKey($key, $blockID = null) {

    $MAP = [];

    return isset($MAP[$key]) ? $MAP[$key] : $key;

}

/*
-------------------------------------------------------------------------------------
 AMSD Activate / Deactivate Hooks
-------------------------------------------------------------------------------------

These allow you to execute extra code after an item is activated or deactivated

*/

function amsdCustomActivation($blockID, $itemID) {

    $ci=& get_instance();

}

function amsdCustomDeactivation($blockID, $itemID) {

    $ci=& get_instance();

}

/*
-------------------------------------------------------------------
 AMSD Custom Title
-------------------------------------------------------------------

This is here for the benefit of the popup notifications that 
occur when you are deleting or saving AMSD items.

It allows you to craft a unique title for the item if you need 
it to be more complex than what you can achieve using the 
"AMSD Slug Key" block setting, which combines fields in the 
item together to form the slug/title. 

Generally, when the CMS can't figure out what the title should be,
it will just say "Item #1" or whatever the row ID is.

Requires the block to have "AMSD Custom Title Function" set to "true"

*/

function amsdCustomTitle($item_id, $block_id) {

    return false;

}

/*
-------------------------------------------------------------------------------------
 AMSD Custom Validate Hooks
-------------------------------------------------------------------------------------

These hooks execute prior to the add, save, inline save, or delete methods, and 
give you a chance to provide custom validation logic to prevent data from 
being entered incorrectly.

If you return false, that signals that the validation has "passed" and nothing
will happen.

You have two choices for other return types.  Either "invalid" or "verify". 

To return "invalid", which shows a red border around the field, with a custom
error message in the notification center, you would return this structure, 
following typical JSON response format:

return [
    "status" => "invalid", 
    "message" => "There is a problem with example_field!", 
    "data" => [
        "invalid_fields" => [
            "example_field"
        ]
    ]
];

To return "verify", which shows the larger banner prompt with actionable buttons,
you would return this structure:

return [
    "status" => "verify", 
    "message" => "Are you sure you want to do a dangerous thing?  You can, but let
    it be known that there are consequences!", 
    "data" => [
        "options" => [
            "positive" => [
                "text" => "Yes, Re-assign"
            ],
            "neutral" => [
                "text" => "No, Cancel"
            ]
        ],
        "revert" => [
            "example_field" => $changed_data["example_field"]["old"]
        ]
    ]
]; 

For "verify", in the "data" field, you pass a list of "options".  These apply to the 
actionable buttons shown.

By default, you have the options of "positive" and "neutral".  No extra code is required
if you stick to these choices.

"positive" action will allow the previously attempted action to proceed as normal, 
and "neutral" will cancel it.

You can also pass in options for "revert" which will map field names to the value they 
should revert back to if cancelled.

If you want to take it to another level, you can add "before" and "after" callbacks to 
any of the options, as well as provide fully custom options/actionable buttons.

To do that you will need to provide some javascript.

It is recommended to use the "Extra Backend JS" developer setting to load up a custom 
javascript file in the backend, then simply pass global function names into the callbacks.

A fully custom setup looks like this (taken from the Cybernautic Leads setup):

return [
    "status" => "verify", 
    "message" => "Are you sure you want to delete this true lead?" . $lead_info . "<br/><br/>The original/possible lead will remain in the contact submission block.", 
    "data" => [
        "options" => [
            "positive" => [
                "text" => "Yes, Delete True Lead"
            ],
            "custom" => [
                "text" => "Yes, Delete True Lead AND Delete From OnePage",
                "callbacks" => [
                    "before" => "delete_contact_from_one_page",
                    "after" => "run_positive" 
                ]
            ],
            "neutral" => [
                "text" => "No, Cancel"
            ]
        ]
    ]
];

"delete_contact_from_one_page" references a global function in the included "backend.js" file, 
which is set to run "before" the action after clicking the button.

The "after" callback is set to "run_positive" which will simply do the default "positive" action, 
after the "before" callback has run and returned "true".

*/

function amsdCustomValidateAddItem($item_id, $block_id, $data, $groups, $table) {

    return false;

}

function amsdCustomValidateSaveItem($item_id, $block_id, $data, $original_data, $changed_data, $groups, $table) {

    return false;

}

function amsdCustomValidateInlineSaveItem($item_id, $block_id, $key, $val, $original_val, $table, $sub_table) {

    return false;

}

function amsdCustomValidateDeleteItem($item_id, $block_id, $table) {

    return false;

}

/*
-------------------------------------------------------------------------------------
 AMSD Custom After Hooks
-------------------------------------------------------------------------------------

These functions run after a successful add, save, inline save, or delete action.

amsdCustomItemChanged() runs no matter which action, but the data passed into it
isn't as detailed as some of the individual hooks.  Use whatever makes the most sense.

The return value doesn't matter or change anything about the result.  If these 
functions are firing, the data has already been added, saved, or deleted.

*/

function amsdCustomItemChanged($action, $item_id, $block_id, $table, $sub = null) {

    return false;

}

function amsdCustomAddItem($item_id, $block_id, $data, $groups, $table) {

    return false;

}

function amsdCustomSaveItem($item_id, $block_id, $data, $original_data, $changed_data, $groups, $table) {

    return false;

}

function amsdCustomInlineSaveItem($item_id, $block_id, $key, $val, $original_val, $table, $sub_table) {

    return false;

}

function amsdCustomDeleteItem($item_id, $block_id, $table) {

    return false;

}

/*
-------------------------------------------------------------------
 AMSD Custom Unique Slug
-------------------------------------------------------------------

By default, the AMSD module will avoid duplicate slugs by 
appending a "-1", "-2", "-3", etc to the end.

This function allows you to add your own behavior.  One example
might be: for a property manager site, add in the number of 
bedrooms as part of the slug.

$data = the full AMSD item/row
$date = if it's a news article or calendar event, this will 
        include the year/month or year/month/day.

*/

function amsdCustomUniqueSlug($item_id, $block_id, $data, $table, $date = false) {

    return false;

}

/*
-------------------------------------------------------------------------------------
 AMSD Portal Hooks
-------------------------------------------------------------------------------------

These functions apply to sites using the Portal Builder

*/

function amsdPortalLoginBefore($portal, $data) {

    return false;

}

function amsdPortalLoginAfter($portal, $data) {

    return false;

}

function amsdPortalLogoutBefore($portal, $data) {

    return false;

}

function amsdPortalLogoutAfter($portal, $data) {

    return false;

}

function amsdPortalLockout($portal, $data) {

    return false;

}

function amsdPortalPasswordReset($portal, $data) {

    return false;

}

function amsdPortalEditBefore($portal, $data) {

    return false;

}

function amsdPortalEditAfter($portal, $data) {

    return false;

}

function amsdPortalRegisterBefore($portal, $data) {

    return false;

}

function amsdPortalRegisterAfter($portal, $data) {

    return false;

}

/*
-------------------------------------------------------------------------------------
 AMSD CSV Export Hooks
-------------------------------------------------------------------------------------

This will run as the very last thing in the core $this->amsd_export->process() 
function.

*/

function amsdCSVExportTransformData($block_id, $selection, $data) {

    return false;

}

/*
-------------------------------------------------------------------------------------
 Stripe Processing Hooks
-------------------------------------------------------------------------------------

Validate:

    // These are the fields that are actually passed into the 
    // Stripe processPayment() model
    $data = [
        "config" => $CONFIG,
        "customer" => $CUSTOMER,
        "recurring" => $RECURRING
    ];

    If there is no validation error, return false.

    If there is, return:
    return ["status" => "error", "message" => "Error Message", "data" => []];

Error:

    // If the error is related to invalid fields:
    $data = ["fields" => ["field_name"]];

    or 

    // If the error is a SendGrid error
    $data = $sendgrid_response["data"];

    of 

    // If the error is a Stripe error
    $data = $PAY["data"];

Success:

    $data = [
        "stripe" => $PAY["data"],      // The data returned from Stripe
        "cms" => [                     // The data from each row in the site's DB
            "customer" => $CUSTOMER,
            "order" => $ORDER,
            "charge" => $CHARGE
        ]
    ];

Webhook:

    $data = $event["data"];            // The data returned from Stripe

*/

function stripeTransactionValidate($page, $block, $post, $data = null) {

    return false;

}

function stripeTransactionError($page, $block, $post, $error, $data = null) {

    return false;
    
}

function stripeTransactionSuccess($page, $block, $post, $message, $data = null) {

    return false;

}

function stripeWebhookSuccess($type, $data = null) {

    return false;

}

/*
-------------------------------------------------------------------------------------
 AMSD Custom Image / File Fields
-------------------------------------------------------------------------------------	

These exist to allow you to specify fields for the file manager's "inUse()" 
function to search in.

return [
    "table_name" => ["field_name" => "field_type_key"]
];

*/

function amsdCustomImageFields() {

    return [];  

}

function amsdCustomFileFields() {

    return [];

}


/*
-------------------------------------------------------------------
 amsdCustomInput() (DEPRICATED)
-------------------------------------------------------------------

This is only used on sites that were produced prior to: 
2015-06-19

It is left here for legacy purposes.

It is only utilized if the "Use Field Builder" developer setting
is set to "off".

*/

function amsdCustomInput($input, $type = false) {

    $ci=& get_instance();

    // Set up defaults for each option
    $key = isset($input["key"]) ? $input["key"] : false;
    $val = isset($input["value"]) ? html_entity_decode($input["value"]) : false;
    $style = isset($input["style"]) ? ' style="' . $input["style"] . '"' : false;
    $block = isset($input["block"]) ? $input["block"] : false;
    $options = isset($input["options"]) ? $input["options"] : false;
    $item = isset($input["item"]) ? $input["item"] : false;
    $grid = isset($input["grid"]) ? $input["grid"] : false;

    // Check for the last character of the $key.
    $lastLetter = substr($key, strlen($key) - 1, strlen($key) - 1);

    if(is_numeric($lastLetter)) {
        // If the last char is numeric, remove it.  This way 'html', 'html1', 'html2', etc will all have the same definition.
        $typeKey = substr($key, 0, strlen($key) - 1);
    } else {
        $typeKey = $key;
    }

    // Load block settings if a block ID is provided.

    if($block) {

        $bs = $ci->core->getBlockSettings($block);
        $table = isset($bs["FlexGrid Table"]) ? $bs["FlexGrid Table"] : (isset($bs["Table"]) ? $bs["Table"] : "amsd_standard");

    } else {

        $bs = false;
        $table = false;

    }

    // Switch by "key"

    if($typeKey && !$type) {

        switch($typeKey) {

            default:

                $type = $typeKey;

                break;

        }

    }

    // Switch by "type"

    switch($type) {

        default:

            $output = false;

            break;

    }

    return $output;


}