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
    <h3>This Months Items</h3>
   	<p class="clear"></p>
   	<form action="<?=site_url('pcp/set_new_tyi')?>" method="post">
	    <div class="parent_item">
	        <label for="tyi_name">Items name:</label>
	        <input type="text" name="tyi_name" value="<?=$this->system->site_config['donation_name']?>" id="tyi_name">
	      	
	      	
	        <label for="dir_name">Files from folder:</label>
		    <select id="dir_name" name="dir_name">
		    	<?php foreach($files as $file): ?>
				<option value="<?=$file?>"><?=$file?></option>
				<?php endforeach; ?>
			</select>
			<br /><br />
			<label for="large_donation_image">Main (large) image:</label>
			<select name="large_donation_image" id="large_donation_image"></select>
			<br /><br />
			<label for="dashboard_donation_image">Small image:</label>
			<select name="dashboard_donation_image" id="dashboard_donation_image"></select>
	  		<br /><br />
	        <label for="item_id_1">Item Id's:</label>
	        <?php $items = unserialize($this->system->site_config['donation_item_ids']) ?>
		    <input type="text" name="item_ids[]" value="<?=$items[0]?>" id="item_id_1" style="width: 100px; text-align: center;">
		    <input type="text" name="item_ids[]" value="<?=$items[1]?>" id="item_id_2" style="width: 100px; text-align: center;">
	    </div>
	    <p class="clear"></p>
	    <input type="submit" value="Release!" />
    </form>
    <br clear="all" />
    <br clear="all" />
   
    
    <span id="loading">Granting items...</span>
</div>
<div id="side_items">
    <h3>Upload New Images</h3>
    <p class="clear"></p>
        <?= form_open_multipart('pcp/upload_tyi_images') ?>
    <div class="parent_item">	  
    	<label for="upload_dir">Upload files to:</label> 
	    <select name="upload_dir" name="upload_dir">
	    	<?php foreach($files as $file): ?>
			<option value="<?=$file?>"><?=$file?></option>
			<?php endforeach; ?>
		</select>
		<br /><br />
		<label for="upload_item">File:</label>
		<input style="display: inline; width: 225px;" type="file" name="upload_item" id="upload_item" />
		<br /><br />
  	 	<input type="submit" value="Upload image" />
	</div>
	</form>
 	<p class="clear"></p>	
    <div class="parent_item">
    	<form method="post" action="<?= site_url('pcp/new_tyi_dir') ?>"> 
        	<label for="new_dir_name">New Directory:</label>
        	<input type="text" name="new_dir_name" value="" id="new_dir_name">
       	 	<input type="submit" value="Create" />
        </form>
    </div>
   		
    
</div>

