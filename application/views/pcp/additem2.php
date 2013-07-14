 <script src="/public/javascripts/jquery/jquery.js"></script>
<script>
$(function(){
	var skel = '<hr><table>'+$("#skel").html()+'</table>';
	$(".imageAdded").blur(function(){
		if($(this).val() != ''){
			$(skel).appendTo("#grouped");
		}
		$(this).removeClass('imageAdded');
		$(".imageAdded").blur(function(){
			if($(this).val() != ''){
				$(skel).appendTo("#grouped");
			}
		});
	});
});
</script>

    <?php 
    	$part_gender_value = $this->session->flashdata('part_gender');
    	$part_layer_value = $this->session->flashdata('part_layer');
    	$item_single_value = $this->session->flashdata('item_single');
    ?>

<form method="post" action="<?=site_url('pcp/docreate');?>" enctype="multipart/form-data">
<table>
	<tr valign="top">
    	<td valign="top">
           <h2>Item Details</h2>
            <table style="position:relative; top:0px;">
            	<tr>
                	<td>Name: </td>
                    <td><input type="text" value="<?=$item_single_value['name']?>" name="item[name]" /></td>
                </tr>
            	<tr>
                	<td>Gender: </td>
                    <td>
                    	<select name="item[gender]" id="itemgender">
                    			<option <?php if($item_single_value['gender'] == 'Unisex'){ ?> selected="selected" <?php } ?>>Unisex</option>
                        	<option <?php if($item_single_value['gender'] == 'Male'){ ?> selected="selected" <?php } ?> >Male</option>
                            <option <?php if($item_single_value['gender'] == 'Female'){ ?> selected="selected" <?php } ?>>Female</option>
                            
                        </select>
                	</td>
                </tr>
                <tr>
                	<td>Thumbnail: </td>
                    <td><input type="file" name="item[thumbnail]" /></td>
                </tr>
                <tr>
                	<td>Layer: </td>
                    <td>
                    	<select name="item[layer]">
                        	<?php
							foreach($layers as $k => $v){
							?>
                            <option <?php if($item_single_value['layer'] == $k){ ?> selected="selected" <?php } ?> value="<?=$k;?>"><?=$v;?></option>
                            <?php
							}
							?>
                        </select>
                  	</td>
                </tr>
                <tr>
                	<td>Compulsive: </td>
                    <td><input type="checkbox" name="item[composite]" value="1" /></td>
                </tr>
                <tr>
                	<td>Default (starter) Item: </td>
                    <td><input type="checkbox" name="item[default]" value="1" /></td>
                </tr>
            </table>
        </td>
       <td id="grouped">
        	<h2>Parts</h2>
        <?php for($i = 0; $i < 10; $i++) : ?>
        	<table id="skel">
            	<tr>
                	<td>Image Part: </td>
                	<td><input type="file" name="part[path][]" size="45" /></td>
                </tr>
                <tr>
                	<td>Image Part Layer: </td>
                    <td>
                    	<select name="part[layer][]">
                        	<?php
							foreach($layers as $k => $v){
							?>
								 <option <?php if($k == $part_layer_value[$i]) {?> selected="selected" <?php } ?> value="<?=$k;?>"><?=$v;?></option>
                            <?php
							}
							?>
                   		</select>
                  	</td>
                </tr>
                <tr>
                	<td>Gender: </td>
                    <td>
                    	<select name="part[gender][]">
                        	<option <?php if($part_gender_value[$i] == ''){ ?> selected="selected" <?php } ?> value="">Not Used</option>
                    		<option <?php if($part_gender_value[$i] == 'Male'){ ?> selected="selected" <?php } ?> value="Male">Male</option>
                    		<option <?php if($part_gender_value[$i] == 'Female'){ ?> selected="selected" <?php } ?>  value="Female">Female</option>
                        </select>
                   	</td>
                </tr>
            </table>
            <hr />
            <?php endfor; ?>        	
        	
        </td>
    </tr>
    <tr>
    	<td colspan="2"><input type="submit" name="submit" value="Add" style="font-size:24px; padding:5px 9px; font-weight:bold; width:100%; background:#ff1; border-color:#ff0;" /></td>
    </tr>
</table>

<div style="width:400px; background:#eee; border:1px solid #aaa; padding:20px:">
<?php foreach($latest as $item) {
	echo $item['name']."<br />";
}?>
</div>
</form>