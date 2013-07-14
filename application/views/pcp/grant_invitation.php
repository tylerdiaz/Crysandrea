<style type="text/css" media="screen">
    h2 {
        border-bottom:1px solid #999;
        padding:10px;
        font-size:17px;
    }
    #main_item {
        width:300px;
        float:left;
        height:400px;
        position:relative;
        background:#eee;
    }
    #side_items {
        width:440px;
        float:left;
        min-height:400px;
        padding-bottom:30px;
        border-left:3px solid #999;
    }
    #super_arrow {
        position:absolute;
        right:0;
        top:37%;
        margin-right:-59px;
    }
    h3 {
        padding:10px 15px;
        border-bottom:1px solid #ccc;
        color:#555;
        font-size:14px;
    }
    #add_child {
        background:#F2BF38;
        font-size:16px;
        color:white;
        padding:14px 14px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        margin:0 0 0 120px;
        border:2px solid #CFA235;
    }
    #save {
        background:#B2D139;
        font-size:16px;
        color:white;
        padding:14px 18px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        margin:0 0 0 70px;
        border:2px solid #8EA430;
        font-weight:bold;
    }
    #save:active {
        background:#92A831;
        border:2px solid #7E902D;
     }
    #add_child:active {
        background:#C19732;
        border:2px solid #A17D2D;
    }
    #parent_item {
        outline:3px solid #ccc;
        border:1px solid #aaa;
        margin:0 20px;
        padding:20px 20px 30px;
        background:white;
    }
    .item_found {
        margin-top:5px;
        margin-right:8px;
        background:#ddd url(http://crysandrea.com/images/arrow_pointers.png)repeat-x left top;
        padding:10px 0 0;
        overflow:hidden;
    }
    .item_found div {
        padding:8px;
        background:white;
        overflow:hidden;
        border:2px solid #ddd;
        border-top:0;
    }
    .item_found_left {
        float:left;
        width:215px;
        background:#ddd url(http://crysandrea.com/images/arrow_pointers.png)repeat-x left -50px;
        padding:0 0 0 10px;
        overflow:hidden;
    }
    .item_found_left div {
        padding:8px;
        background:white;
        overflow:hidden;
        border:2px solid #ddd;
        border-left:0;
    }
    .item_found img {
        float:left;
        margin-right:10px;
    }
    .item_found_left img {
        float:left;
        margin-right:10px;
    }
    .sub_item {
        padding:0 0 10px 20px;
        margin:0 20px 20px 35px;
        clear:both;
        border-bottom:1px dashed #aaa;
        overflow:hidden;
    }
    .sub_item label {
        width:60px;
        line-height:28px;
        float:left;
        margin-top:15px;
    }
    .sub_item input[type="text"] {
        margin-top:15px;
        font-size:18px;
        padding:1px 4px;
        width:60px;
        float:left;
    }
    #parent_item input[type="text"] {
        font-size:18px;
        padding:1px 4px;
        width:90%;
    }
    #loading {
        display:block;
        display:none;
        text-align:center;
        margin:20px 60px;
        background:white url(http://crysandrea.com/images/loaders/ajax.gif)no-repeat 11px 11px;
        padding:10px 10px 10px 32px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        color:#888;
        border:2px solid #ccc;
    }
</style>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){

        $("#save").live('click', function(){
            $(this).fadeOut(600, function(){
                $('#loading').fadeIn(300);
                $.post(baseurl+"pcp/grant_prizes", $("#handy_form").serialize(), function(json){
                    $('#loading').html('Items granted!');
                    setTimeout(function(){
                        $('#loading').fadeOut(200, function(){
                            $("#save").fadeIn(300);
                        })
                    }, 1500);
                }, "json");
            })
            return false;
        });
        
        $("#parent_item input[type='text']").live('blur', function(){
            if($(this).val().length > 0){
                $.getJSON(baseurl+"pcp/user_info/"+$(this).val(), function(json){
                    var tooltip_item = '<div class="item_found"> \
                        <div> \
                            <img src="http://crysandrea.com/avatar/headshot/'+json.user_id+'" width="40" height="40" alt=""> \
                            <strong>'+json.username+'</strong><br> \
                            <span>Invited '+json.reffered+' people</span> \
                        </div> \
                    </div>';
                    $("#parent_item .item_found").fadeOut(200);
                    $("#parent_item").append(tooltip_item);
                    $("#pallaas").val((parseInt(json.reffered)*75));
                });
            }
        });
        
        $("input[type=checkbox]").live('click', function(){
            if($(this).attr('checked')){
                var palla = $("#pallaas").val();
                $("#pallaas").val(parseInt(palla)-75);
            } else {
                var palla = $("#pallaas").val();
                $("#pallaas").val(parseInt(palla)+75);
            }
        })
    });
</script>
<form accept-charset="utf-8" id="handy_form">
<h2>Grant invitation items</h2>
<div id="main_item">
    <?=image('images/big_arrow.png', 'id="super_arrow"')?>
    <h3>User info</h3>
    <br clear="all" />
    <div id="parent_item">
        <label for="username">Username:</label>
        <input type="text" name="username" value="" id="username">
    </div>
    <br clear="all" />
    <br clear="all" />
    <a href="#" id="save">Grant prizes</a>
    <span id="loading">Granting items...</span>
</div>
<div id="side_items">
    <h3>Invite Prizes</h3>
    <br clear="all" />
    <ul style="margin:0 40px">
        <li><input type="checkbox" name="prizes[]" value="p_1" id="prize_1"> <label for="prize_1">1) 150+ Palladium</label></li>
        <li><input type="checkbox" name="prizes[]" value="p_2" id="prize_2"> <label for="prize_2">2) Friendship belt</label></li>
        <li><input type="checkbox" name="prizes[]" value="p_3" id="prize_3"> <label for="prize_3">3) 2+ Pro nets - +1 Basic net</label></li>
        <li><input type="checkbox" name="prizes[]" value="p_4" id="prize_4"> <label for="prize_4">4) Friendship shirt</label></li>
        <li><br /><br /><br /><br /> <label for="pallaas">Bonus Pallaium</label>: <input type="text" name="pallaas" id="pallaas" /></li>
    </ul>
</div>
</form>
