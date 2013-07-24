<?php
/**
 * Mapper web application
 * See processor.php for application Docbloc notation.
 * @author kari.eve.trace@gmail.com
 * @todo arrow up/down/left/right to change field focus for version 0.2.4
 */
// Set generic row_data form
$row_data = null;
$row_data .= '<div class="data_set '.rand (1 , 100).'">';
$row_data .= '<div class="row_data row_new_button"><img src="./images/plus.png" alt="New Row" /></div>';
$row_data .= '<form>'; // Form needed for 'serialize' to work. As serialize is a core of the CRUD system, we must keep it
$row_data .= '<div class="row_data hidden"><input type="text" class="input" maxlength="32" name="id" /></div>';
$row_data .= '<div class="row_data hidden"><input type="text" class="input" maxlength="32" name="map_group" /></div>';
$row_data .= '<div class="row_data"><input type="text" class="input" maxlength="32" name="map_data_col_1" /></div>';
$row_data .= '<div class="row_data"><input type="text" class="input" maxlength="32" name="map_data_col_2" /></div>';
$row_data .= '<div class="row_data"><input type="text" class="input" maxlength="32" name="map_data_col_3" /></div>';
$row_data .= '<div class="row_data"><input type="text" class="input" maxlength="32" name="map_data_col_4" /></div>';
$row_data .= '<div class="row_data"><input type="text" class="input" maxlength="32" name="map_data_col_5" /></div>';
$row_data .= '<div class="row_data"><input type="text" class="input" maxlength="4" name="sort_group" /></div>';
$row_data .= '</form>';
$row_data .= '<div class="row_data row_del_button"><img src="./images/delete.png" alt="Delete Row" /></div>';
$row_data .= '</div>';
$row_data .= '<div class="clear"></div>';
?>
<!DOCTYPE html>
    <!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
    <!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
    <!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
    <!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <title> Field Mapper</title>

        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="components/normalize.css/normalize.css">
        <link rel="stylesheet" href="components/html5-boilerplate/css/main.css">
        <script src="components/modernizr/modernizr.js"></script>

        <link rel="stylesheet" href="application.css">

        <!-- if IE 8 or below styles-->
        <!--[if lt IE 9]>
            <link rel="stylesheet" href="ie_fix.css">
        <![endif]-->
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->



        <?php
            $whitelist = array('mapper.localhost', '127.0.0.1');
            if(in_array($_SERVER['HTTP_HOST'], $whitelist)){
                echo '<div class="app_warning"><p>You are in the LOCALHOST env.</p></div>';
            };
        ?>



        <div id="map_group_div" style="margin-left:25px">
            <div    id="map_group_text">Mapping Data Source(s):</div>
            <select id="map_group_ddl"></select>
        </div>
        <div class="clear"></div>



        <div style="margin-left:25px;position:relative;top:-15px">
            <h2>Did you know: The arrow keys move your cursor between input fields!</h2>
        </div>
        <div class="clear"></div>



        <div id="titles_container">
            <div class="row_title">Add</div>
            <div class="row_title hidden">Row ID</div>
            <div class="row_title hidden">Group ID</div>
            <div class="row_title map_data_title_1"></div>
            <div class="row_title map_data_title_2"></div>
            <div class="row_title map_data_title_3"></div>
            <div class="row_title map_data_title_4"></div>
            <div class="row_title map_data_title_5"></div>
            <div class="row_title">Sort</div>
            <div class="row_title">Delete</div>
        </div>
        <div class="clear"></div>



        <!-- dyanically loaded `old` data-->
        <div id="loaded_data_container"></div>
        <div class="clear"></div>


        <hr />



        <!-- static 'new' data row-->
        <div id="new_data_container">
            <?= $row_data; ?>
        </div>
        <div class="clear"></div>


        <!-- Framework (jQ) includes -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.1.min.js"><\/script>')</script>

        <!-- CRUD Mapping jQuery logic-->
        <script>
            // Create dummy console.log object if the browser does not support console.log
            if (typeof window.console == 'undefined') {
                window.console = {log:function(){}};
            }

            // Global Vars
            // Set default map_group, but if the map group is set via the querystring, set map_group to that value
            // TODO Right now "1" is the defualt. @version 0.1.5 or something set this to the first option of the DDL
            var map_group = 1;
            var tmp = window.getParameterByName('map_group');
            if (tmp != null) {

                map_group = tmp;
            }



            // Helper functions
            /**
             * set the mapping__group ID so we have it when saving data
             * integer id [required] IT of the map_group to set the data to when saving
             * @author kari.eve.trace@gmail.com
             * @version 0.1
             * @since 2013-07-09
             */
            function setMapGroupID(id) {

                $("[name='map_group']").val(id);
            }

            /**
             * Remove an element based on selector id
             * @author kari.eve.trace@gmail.com
             * @version 0.1
             * @since 2013-07-09
             * @param object selector [required]
             * @todo Need to wrap the guts in a try{}
             */
            function removeElement(dom_obj) {
                $(dom_obj).slideUp('slow', function() {
                    $(this).remove();
                    makeCursorable()
                });

                console.log("Removed element via selector: "+dom_obj);

                return true;
            }

            /**
             * get the map_group ID to set as the defualt
             * @source http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values
             * @param  string name 
             * @return string
             */
            function getParameterByName(name) {
                var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
                return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
            }

            // CRUD jQ<->PHP functions
            /**
             * Create new data
             * @author kari.eve.trace@gmail.com
             * @version 0.1
             * @since 2013-07-09
             */
            function createItem(data, type, src_obj) {
                var data = data.split("&");  // Split string based on the & character
                data = JSON.stringify(data); // Convert array to JSON
                data = data.replace(/[+]/g, "%20");// Remove spaces(+), make %20.


                var promise = $.ajax({
                    type: "POST",
                    data: {action: 'createItem', data: data, type: type},
                    dataType: "json",
                    url: "./processor.php"
                });

                promise.success(function(data) {
                    console.log('New row data.row_id: '+data.row_id);

                    // Set row's ID
                    $(src_obj).parent().siblings().children('input[name="id"]').val(data.row_id);

                    return true;
                });

                promise.error(function(data) {
                    console.log("AJAX "+type+" creation error.");
                    return false;
                });

                promise.complete(function(){
                    makeCursorable();
                });
            }

            /**
             * Read data typer item
             * @author kari.eve.trace@gmail.com
             * @version 0.1.3
             * @since 2013-07-11
             */
            function readItem(data, type, map_group, src_obj) {

                // Get initial data
                var promise = $.ajax({
                    type: "POST",
                    data: {action: 'readItem', data: data, type: type, map_group: map_group},
                    dataType: "json",
                    url: "./processor.php"
                });

                promise.success(function(data) {

                    if (type == 'data') {

                        // No data returned from the processor
                        if (data.boolean == false) {
                            console.log('Data response empty');
                            return true;
                        }

                        // If no data returned (besides an predefined object structure), return true
                        if (
                            typeof data == 'array' &&
                            data[0].id == null
                        ) {

                            return true;
                        }



                        var counter = 1;
                        for(index in data) {
                            var obj = data[index];

                            // Create a new form row
                            var new_row = '<?php echo $row_data; ?>';



                            // Add row to DOM
                            $("#loaded_data_container").prepend(new_row);

                            // what position is the 
                            var jQv = $("#loaded_data_container > :nth-child(1)");

                            //Add indicator to form so we can tell each row apart
                            jQv.addClass(" old "+counter+"");

                            // For each HTML element to be populated
                            // Populate new elements with data
                            $.each( obj, function( key3, value3 ) {

                                if (value3 != undefined && value3 != '') {
                                    value3 = unescape(value3);

                                    // Update field with vale from JSON obj
                                    $(jQv).find("input[name='"+key3+"']").val(value3);
                                }

                            });

                            // null new_row var container
                            new_row = null;
                            counter++;
                        }
                    } else if (type == 'title') {
                        $.each( data, function( key, value ) {
                            // Populate new elements with data
                            $.each( value, function( key2, value2 ) {
                                $("#titles_container div."+key2+"").html(value2);
                            });
                        });
                    } else {
                        console.log('Data "'+type+'"" not recognized.');
                        return false;
                    }

                    console.log('AJAX readItem() for '+type+' successful')
                    return true;
                });

                promise.error(function(data) {
                    console.log(data);
                    if (type == 'title') {
                        alert('Error while read '+type+'! Ensure some exists!');
                    }

                    return false;
                });

                promise.complete(function(){
                    makeCursorable();
                });
            }

            /**
             * Update an existing (previously created) row
             * @author kari.eve.trace@gmail.com
             * @version 0.1.3
             * @since 2013-07-10
             */
            function updateItem(data, type) {
                var data = data.split("&");  // Split string based on the & character
                data = JSON.stringify(data); // Convert array to JSON
                data = data.replace(/[+]/g, "%20");// Remove spaces(+), make %20.



                var promise = $.ajax({
                    type: "POST",
                    data: {action: 'updateItem', data: data, type: type},
                    dataType: "json",
                    url: "./processor.php"
                });

                promise.success(function(data) {
                    console.log(data);
                    console.log('AJAX '+type+' update success.');
                    return true;
                });
                promise.error(function(data) {
                    console.log(data);
                    console.log('AJAX '+type+' update error.');
                    return false;
                });
                promise.complete(function(){
                    makeCursorable();
                });
            }

            /**
             * If all data fields are blank, delete the row from being collected durinf cRUd calls
             * @author kari.eve.trace@gmail.com
             * @version 0.1
             * @since 2013-07-10
             */
            function deleteItem(data, type, class_selector) {
                if(type == 'undefined') {type = 'data';}

                var data = data.split("&");  // Split string based on the & character
                data = JSON.stringify(data); // Convert array to JSON

                var promise = $.ajax({
                    type: "POST",
                    data: {action: 'deleteItem', data: data, type: type},
                    dataType: "json",
                    url: "./processor.php"
                });

                promise.success(function(data) {
                    // jQuery animation to close and then remove the container element.                      
                    if (data.boolean == true) {
                        //console.log('element removal '+type+' ajax success');
                        return true;
                    }
                    
                    console.log('element removal '+type+' ajax failure');
                    return false;
                });

                promise.error(function(data) {
                    console.log('AJAX '+type+' delete error. data: '+data);
                    return false;
                });

                promise.complete(function(){
                    //makeCursorable(); this does not work. HAve to call the function from removeElement()
                });
            }

            /**
             * Get and populate the mapping group DDL
             * @author kari.eve.trace@gmail.com
             * @version 0.1.3
             * @since 2013-07-11
             */
            function getMapGroupOptions() {
                var promise = $.ajax({
                    type: "POST",
                    data: {action: 'getMapGroupOptions'},
                    dataType: "json",
                    url: "./processor.php"
                });

                promise.success(function(data){

                    var map_group_ddl = $("#map_group_ddl");
                    
                    // For each return
                    $.each( data, function( key, value ) {
                       
                       // And each object
                       $.each( value, function( key2, value2 ) {

                            // Append to the DDL the ID and text
                            map_group_ddl.append(
                                $("<option />").val(value2.map_group).text(
                                    value2.map_data_title_1+" - "+
                                    value2.map_data_title_2+" - "+
                                    value2.map_data_title_3+" - "+
                                    value2.map_data_title_4+" - "+
                                    value2.map_data_title_5
                                )
                            );
                       });
                    });

                    console.log('AJAX map_group_dll successful');

                    // Select the DDL option based on the QS var
                    $("#map_group_ddl").val(map_group);
                });

                promise.error(function(data){
                    console.log('AJAX map_group_dll error')
                });
                promise.complete(function(){});

                return true;
            }

            /**
             * Arrows control cursor location among input elements
             * @author kari.eve.trace@gmail.com
             * @version 0.2.2
             * @since 2013-07-24
             * @source http://www.webdbtips.com/83577/
             */
            function makeCursorable() {

                var baseIndex = 100;  

                // The "column" container, pass it in
                $("body").find("form").each(function(r) {
                    //console.log("row: "+r);

                    // The "row" container, pass it in
                    $(this).find("div.row_data:not(.hidden)").each(function(c) {  
                        //console.log("column: "+c);

                        $(this).find("input").attr("tabindex", r * 100 + c + baseIndex).addClass("arrowControlled");  
                    });

                });

                return true;
            };


            // Add a new row for input
            $("body").on('click', '.row_new_button', function() {
                console.log('Insert a new data row');

                // Get vars to be passed to the new data_set
                data            = new Object();
                data.map_group  = $(this).siblings('form').children('div').children("input[name='map_group']").val();
                data.sort_group = $(this).siblings('form').children('div').children("input[name='sort_group']").val();
                data.endDom     = $(this).parent().next();

                // Add new data_set HTML
                $(data.endDom).after('<?=$row_data; ?>');


                // Assign old but needed data to new row
                new_row_datas   = $(this).parent().next().next().children('form').children();
                $(new_row_datas[1]).children('input').val(data.map_group);
                $(new_row_datas[7]).children('input').val(data.sort_group);
                
                makeCursorable();
                return true;
            });

            $("body").on('click', '.row_del_button', function() {
                console.log('Deleting row_data');

                // Get the row_data id
                row_data        = new Object();
                row_data.id     = $(this).siblings('div').children("input[name='id']").val();



                // TODO Opps! Prevent the last row from being
                // Row does not have an ID, no data to delete. Just remove the row
                if (row_data.id == undefined) {

                    return removeElement($(this).parent());
                // if no idea is set, the row has not been saved yet. Do nothing
                // If an id is present, pass the id to the delete function
                } else if (row_data.id) {

                    // delete data, then remove element if successful                 
                    if (
                        deleteItem($(this).siblings('form').serialize(), 'data', $(this).parent().attr('class'))
                    ) {
                        // remove elements (it recalls makeCursorable()) after deletion
                        
                        return removeElement($(this).parent());
                    } else {
                        alert ('Data deleted, but could not remove row. Please press F5 to reload.');
                        return false;
                    }

                    return false;
                // Just delete the HTML elements, no data to remove
                } else {
                    alert('Could not remove row.');
                    return false;
                }



                // Remove the HTML element that contains the row_data
                return true;
            });

            // Input field changed? Create or update it in the DB.
            $("body").on('change', 'input[name^="map_data_col_"], input[name="sort_group"]', function() {
                console.log('Data has changed that needs updating');

                row_id = $(this).parent().siblings().children('input[name="id"]').val();

                // Row does not have an ID = create
                if (!row_id) {
                    console.log('Input field creating new data row');

                    return createItem($(this).parent().parent().serialize(), 'data', $(this));
                // Else, update
                } else {
                    console.log('Input field updating data row');

                    return updateItem($(this).parent().parent().serialize(), 'data', $(this));
                }

                // Failback final option
                return false;
            });

            // If the DDL option changes, reload page and pass the QS var
            $("#map_group_ddl").on('change', function(){
                window.location = "?map_group="+$("#map_group_ddl").val();
            });

            // Arrow button actions. see makeCursorable() for notes
            $("body").on("keydown", ".arrowControlled", function(evt) {  
                var tabIndex = parseInt($(this).attr("tabindex"));  
                switch (evt.which) {  
                    case 38: //  
                        tabIndex -= 100;  
                        break;  
                    case 40: //  
                        tabIndex += 100;  
                        break;  
                    case 37: //()  
                        tabIndex--;  
                        break;  
                    case 39: //()  
                        tabIndex++;  
                        break;  
                    default:  
                        return;  
                }

                if (tabIndex > 0) {  
                    $(".arrowControlled[tabindex=" + tabIndex + "]").focus();  
                    return false;  
                }

                return true;  
            });


            // Get the initial data once the doc has loaded.
            $(document).ready(function() {
                
                // Get initial titles
                readItem(null , 'title', map_group);
                readItem(null , 'data',  map_group);
                setMapGroupID(map_group);

                // Get DDL data, IE populate the DDL
                getMapGroupOptions();

                // Collate current input fields for curser controls
                makeCursorable();
            });
        </script>
    </body>
</html>
