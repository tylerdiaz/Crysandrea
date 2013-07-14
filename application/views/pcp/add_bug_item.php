<style type="text/css" media="screen">
    h2 {
        border-bottom:1px solid #999;
        padding:10px;
        font-size:17px;
    }
    #main_item {
        width:400px;
        float:left;
        height:400px;
        position:relative;
        background:#eee;
    }
    #side_items {
        width:340px;
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
    .parent_item {
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
    .parent_item input[type="text"] {
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
    	$dir_name = $('#dir_name');
		get_dir_files($dir_name.val());
    	$dir_name.change(function () {
    		var dirName = $dir_name.val();
    		get_dir_files(dirName);
    	});
    	function get_dir_files($name){
		  		var $items;
	   			$.get('tyi_read_dir/' + $name, function(data) {
	      			$items = data;
	      			$('#large_donation_image').html(data);
	      			$('#dashboard_donation_image').html(data);
	  		 	
	  		 	});
	    	};    	
    	});
</script>
<h2>Grant invitation items</h2>
<div id="main_item">
    <h3>Add bug item!</h3>
   	<p class="clear"></p>
   	<form action="<?=site_url('pcp/add_bug_item')?>" method="post">
	    <div class="parent_item">
	        <label for="item_id">Item ID:</label>
	        <input type="text" name="item_id" id="item_id">
			
	        <label for="item_price">Item price (bugs):</label>
	        <input type="text" name="item_price" id="item_price">			
			
	        <label for="insect_id">Insect ID:</label>
	        <input type="text" name="insect_id" id="insect_id">	
			
	        <label for="insect_id">Palla Price:</label>
	        <input type="text" name="second_price" id="second_price">	
			
<!--			<select name="item_currency">
				<option value="Bugs">Bugs</option>
				<option value="Palladium">Palladium</option>
			</select>
			
			<select name="bug_name">
				<option value="">Bug name</option>
			</select>			
-->
	    </div>
	    <p class="clear"></p>
	    <input type="submit" value="Add bug item!" />
    </form>
    <br clear="all" />
    <br clear="all" />
   
    
    <span id="loading">Granting items...</span>
</div>
<div id="side_items">
    
</div>

