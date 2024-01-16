<style type="text/css">

    #event-date-table {
        width: 100%;
    }

        #event-date-table .event-date-section {

            display: inline-block;
            vertical-align: middle;
            padding: 5px;

        }

        #event-date-table input[type='text'] {
            width: 120px;
            padding: 4px;
            font-size: 20px;
            background: #fff;
            border: 1px solid #ddd;
        }

        #event-date-to {
            width: 40px !important;
            text-align: center;
        }

        #all-day {
            width: 80px !important;
            text-align: center;
        }

        #all-day label {
            width: auto !important;
        }

</style>

<div class="amsd-calendar">

    <div class="amsd-section table">

        <form>

            <? if(array_key_exists("category", $item)) { ?>
            <?= $FIELD->build("category"); ?> 
            <div class="amsd-hr"></div>  
            <? } ?>
        
            <?= $FIELD->build(array_key_exists("focused_img", $item) ? "focused_img" : "img", [
                "label" => "Image"
            ]); ?>
            
            <div class="amsd-hr"></div>

            <?= $FIELD->build("event_title", Array(
                "style" => "font-size: 25px; width: 500px;"
            )); ?>

            <?= $FIELD->build("slug", Array(
                "label" => "Event Slug",
                "style" => "width: 500px;"
            )); ?>

            <div class="amsd-hr"></div>

            <div id="event-date-table">

                <div class="event-date-section">
                    <?= $FIELD->build("start_date", Array(
                        "default" => date("Y-m-d"),
                        "markup" => FALSE
                    )); ?>
                </div>

                <div class="event-date-section">
                    <?= $FIELD->build("start_time", Array(
                        "default" => date('h:ia'),
                        "markup" => FALSE
                    )); ?>
                </div>

                <div class="event-date-section" id="event-date-to">to</div>

                <div class="event-date-section">
                    <?= $FIELD->build("end_time", Array(
                        "default" => date('h:ia', strtotime("+1 hour")),
                        "markup" => FALSE
                    )); ?>
                </div>

                <div class="event-date-section">
                    <?= $FIELD->build("end_date", Array(
                        "default" => date("Y-m-d"),
                        "markup" => FALSE
                    )); ?>
                </div>

                <div class="event-date-section">
                    <?= $FIELD->build("all_day", Array(
                        "type" => "checkbox"
                    )); ?>
                </div>

            </div>

        </form>

    </div>

    <div class="amsd-hr"></div>

    <?= $FIELD->special("recurrence"); ?>

    <div class="amsd-hr"></div>
    Event Description
    <div class="amsd-hr"></div>

    <?= $FIELD->build("html"); ?>

</div>

<script type="text/javascript">

    var calendar = function(container) {

        var C = this;

        C.init = function() {

            C.listeners();

        }

        C.allDay = function(selected) {

            $("input[data-type='time']", container).each(function(i,v) {

                if(selected) {

                    $(this).attr("data-original", $(this).val());
                    $(this).attr("data-original-value", $(this).attr("data-value"));
                    $(this).removeAttr("value");
                    $(this).removeAttr("data-value");

                    $(this).hide();

                } else {

                    $(this).attr("value", $(this).attr("data-original"));
                    $(this).attr("data-value", $(this).attr("data-original-value"));

                    $(this).show();

                }

            });

        }

        C.dateTimeChange = function(input, e) {

            var sd = $("input[name='start_date']", container),
                ed = $("input[name='end_date']", container);

            if($("input[name='all_day']", container).is(":checked")) {

                var start = C.dateObj(sd.val(), false),
                    end = C.dateObj(ed.val(), false);

                    if(end < start) {

                        ed.val(sd.val());
                        ed.attr("data-value", sd.attr("data-value"));
                        ed.attr("data-original", sd.val());

                    }

            } else {

                var st = $("input[name='start_time']", container),
                    et = $("input[name='end_time']", container)

                var start = C.dateObj(sd.val(), st.attr("data-value")),
                    end = C.dateObj(ed.val(), et.attr("data-value"));

                    if(end < start) {

                        ed.val(sd.val());
                        ed.attr("data-value", sd.attr("data-value"));
                        ed.attr("data-original", sd.val());

                        et.val(st.val());
                        et.attr("data-value", st.attr("data-value"));
                        et.attr("data-original", st.val());

                    }

            }

        }

        C.dateObj = function(date, time) {

            var d = date ? date.split("-") : Array(0,0,0),
                t = time ? time.split(":") : Array(0,0,0);

            return new Date(d[0], parseInt(d[1]) - 1, d[2], t[0], t[1], t[2]);

        }

        C.listeners = function() {

            $("input[name='all_day']", container).on('change', function() { C.allDay($(this).is(":checked")) });
            $("input[data-type='time']", container).on('change', function(e) { C.dateTimeChange($(this), e); });
            $("input[data-type='date']", container).on('change', function(e) { C.dateTimeChange($(this), e); });

            if($("input[name='all_day']", container).val() == 1) C.allDay(true);

        }

        C.init();

    }

    $(document).ready(function() {

        var blockID = parseInt("<?= $block ?>"),
            block = $(".block[data-id='" + blockID + "']");

        var C = new calendar($(".amsd-calendar", block));

    });

</script>
