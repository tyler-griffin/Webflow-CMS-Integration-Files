<div class="amsd-section">

    <form>
        
        <? if(array_key_exists("category", $item)) { ?>
        <?= $FIELD->build("category"); ?>   
        <? } ?>

        <div class="amsd-hr"></div>
    
        <?= $FIELD->build(array_key_exists("focused_img", $item) ? "focused_img" : "img"); ?>
        
        <div class="amsd-hr"></div>
        
        <div class="grid">
    
            <div class="unit half">
        
                <?= $FIELD->build("title", [
                    "style" => "font-size: 20px; width: 100%;"
                ]); ?>
                
                <?= $FIELD->build("slug", [
                    "style" => "width: 100%;"
                ]); ?>
            
            </div>
            
            <div class="unit half">
            
                <? if(array_key_exists("author", $item)) { ?>
                <?= $FIELD->build("author"); ?>             
                <? } ?>

                <? if(array_key_exists("keywords", $item)) { ?>
                <?= $FIELD->build("keywords"); ?>   
                <? } ?>

                <?= $FIELD->build("publish_date"); ?>
            
            </div>
            
        </div>

        <div class="amsd-hr"></div>
        <?= $FIELD->build("url"); ?>
        </div>

        <? if(array_key_exists("preview_text", $item)) { ?>

        <div class="amsd-hr"></div>
        
        <div class="grid">
    
            <div class="unit half">
        
                <?= $FIELD->build("preview_text"); ?>
            
            </div>
            
        </div>

        <? } ?>
    
    </form>
    
    <div class="amsd-hr"></div>
    
    <?= $FIELD->build("html"); ?>

    <? if(array_key_exists("photos", $item)) { ?>
    
    <div class="amsd-hr"></div>
    
    <?= $FIELD->group("photos"); ?>    
    
    <? } ?>

</div>
