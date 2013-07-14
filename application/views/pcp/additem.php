<?php
$part_gender_value = FALSE;
$part_layer_value = FALSE;
$item_single_value = FALSE;
?>

<form method="post" action="<?=site_url('pcp/docreate');?>" enctype="multipart/form-data" style="overflow:hidden">
<style type="text/css">
    input[type=file] {
        line-height:1;
        margin:3px 0;
    }
</style>
<div class="grid_3">
    <fieldset>
      <legend>Item details</legend>
      <label>Item name:</label>
      <input type="text" name="item[name]" placeholder="Type something…" />
      <br />
      <label>Gender</label>
      <select name="item[gender]" id="itemgender">
          <option selected="selected">Unisex</option>
          <option>Male</option>
          <option>Female</option>
      </select>
      <br />
      <label>Item thumbnail</label>
      <input type="file" name="item[thumbnail]" />
      <br />
      <label>Main item layer</label>
      <select name="item[layer]">
          <?php
          foreach($layers as $k => $v){
          ?>
          <option <?php if($item_single_value['layer'] == $v['id']){ ?> selected="selected" <?php } ?> value="<?=$v['id'];?>"><?=$v['name'];?> (<?=$k;?>)</option>
          <?php
          }
          ?>
      </select>
      <br />
      <button type="submit" class="btn">Install item</button>
    </fieldset>
    <div style="width:320px; background:#eee; border:1px solid #aaa; padding:10px; margin-top:20px;">
        <?php foreach($latest as $item) {
            echo $item['name']." <a href='".site_url('item_manager/item_id/'.$item['item_id'])."' title='".$item['name']."' target='_blank' style='font-size:0.5em;' >(".$item['item_id'].")</a>"."<br />";
        }?>
    </div>

</div>

<div class="grid_3">
    <fieldset>
      <legend>Item pieces</legend>
      <?php for($i = 0; $i < 8; $i++) : ?>
          <table id="skel" width="100%">
              <tr>
                  <td width="150px">Image part: </td>
                  <td><input type="file" name="part[path][]" /></td>
              </tr>
              <tr>
                  <td width="150px">Piece layer:</td>
                  <td>
                      <select name="part[layer][]">
                          <?php
                          foreach($layers as $k => $v){
                          ?>
                               <option <?php if($v['id'] == $part_layer_value[$i]) {?> selected="selected" <?php } ?> value="<?=$v['id'];?>"><?=$v['name'];?> (<?=$k;?>)</option>
                          <?php
                          }
                          ?>
                      </select>
                  </td>
              </tr>
              <tr>
                  <td width="150px">Gender: </td>
                  <td>
                      <select name="part[gender][]">
                          <option <?php if($part_gender_value[$i] == ''){ ?> selected="selected" <?php } ?> value="">Unisex</option>
                          <option <?php if($part_gender_value[$i] == 'Male'){ ?> selected="selected" <?php } ?> value="Male">Male</option>
                          <option <?php if($part_gender_value[$i] == 'Female'){ ?> selected="selected" <?php } ?>  value="Female">Female</option>
                      </select>
                  </td>
              </tr>
          </table>
          <hr />
          <?php endfor; ?>
    </fieldset>
</div>

</form>
