
<?

// If you need to print a strings block, make sure the block settings "Hidden" is not set to "true".
// Then add the block setting "Template", and set it to the same name as a file in /blocks/amsd/templates/strings.

$TEMPLATE = isset($block->settings["Template"]) ? $block->settings["Template"] : false;
$TEMPLATE_DIR = FRONTEND . "/blocks/amsd/templates/strings";

if($TEMPLATE) {

    if(file_exists($TEMPLATE_DIR . "/" . $TEMPLATE . ".php")) {

        $DATA = strings($block->id);

        include($TEMPLATE_DIR . "/" . $TEMPLATE . ".php");

    }

}

?>
