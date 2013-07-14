<?php echo script('/global/scripts/crysandrea.avatar.js'); ?>
<noscript>
	<style type="text/css" media="screen">
		ul.avatar_tabs {
			display:none;
		}
		div#tab_glove {
			padding:0;
			background:white;
			overflow:hidden;
			border:5px solid #aaa;
			-moz-border-radius:6px;
		}
		div#tab_glove > div{
			min-height:1px;
			border:none;
			float:left;
			padding:0;
			display:inline;
			overflow:none;
		}
		div#tab_glove div span{
			float:left;
		}
		.multi_item {
		    background:#ddd;
		}
		.multi_item ul {
		    display:block;
		    position:static;
		    background:none;
		    border:none;
		    box-shadow:none;
		    -moz-box-shadow:none;
		}
		.multi_item .pull_down {
		    display:none;
		}
	</style>
</noscript>
<style type="text/css">
	.avatar_tabs {
		overflow:hidden;
	}
</style>
<script type="text/javascript">

function unequip_all(link){

	$.post(link+'avatar/unequip',{  },function(data){
		$('#tops').children().children().removeClass('equipped');
		$('#bottom').children().children().removeClass('equipped');
		$('#head').children().children().removeClass('equipped');
		$('#feet').children().children().removeClass('equipped');
		$('#accessories').children().children().removeClass('equipped');
		$('#items').children().children().removeClass('equipped');
		$("#avatar_loading").css('display','block');
			var img		= new Image();
			var src		= link+"avatar/preview/"+microtime(true);
			img.onLoad	= $("#avatar_preview_img").attr('src', src);
			img.src		= src;
	});
}

function revert(link){

	$.post(link+'avatar/revert',{  },function(data){
		$('#tab_glove').children().children().children().removeClass('equipped');
		 $.each(
			data.split(';'),
		 function( intIndex, objValue ){
			$(objValue).addClass('equipped');
  	});
		$("#avatar_loading").css('display','block');
			var img		= new Image();
			var src		= link+"avatar/preview/"+microtime(true);
			img.onLoad	= $("#avatar_preview_img").attr('src', src);
			img.src		= src;
	});
}

function swap_gender(link){
	$.post(link+'profile/gender_swap',{  },function(data){
		$("#avatar_loading").css('display','block');
			var img		= new Image();
			var src		= link+"avatar/preview/"+microtime(true);
			img.onLoad	= $("#avatar_preview_img").attr('src', src);
			img.src		= src;
	});
}

$(document).ready(function(){
	$('#revert_button').on('click', function(){
		revert('/');
		return false;
	});
	$('#unequip_all_button').on('click', function(){
		unequip_all('/');
		return false;
	});
});
</script>
<style type="text/css" media="screen">
#save_avatar {
	display:block;
	margin:2px 2px 5px;
	text-align:center;
	font-size:16px;
	font-weight:bold;
	padding:8px 2px 7px 2px;
	border-radius:5px;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	-moz-transition: none;
	-webkit-transition: none;
	-o-transition: color 0 ease-in;
	transition: none;
	background: #e7f7a5; /* Old browsers */
	background: -moz-linear-gradient(top,  #e7f7a5 2%, #d9f477 18%, #c2e24a 44%, #9ec415 87%, #87aa06 99%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(2%,#e7f7a5), color-stop(18%,#d9f477), color-stop(44%,#c2e24a), color-stop(87%,#9ec415), color-stop(99%,#87aa06)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #e7f7a5 2%,#d9f477 18%,#c2e24a 44%,#9ec415 87%,#87aa06 99%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #e7f7a5 2%,#d9f477 18%,#c2e24a 44%,#9ec415 87%,#87aa06 99%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #e7f7a5 2%,#d9f477 18%,#c2e24a 44%,#9ec415 87%,#87aa06 99%); /* IE10+ */
	background: linear-gradient(top,  #e7f7a5 2%,#d9f477 18%,#c2e24a 44%,#9ec415 87%,#87aa06 99%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e7f7a5', endColorstr='#87aa06',GradientType=0 ); /* IE6-9 */
	color:#354e12;
	font-family:helvetica;
	border:1px solid #88a462;
	-webkit-box-shadow: 0px 0px 0px 3px #e7ffc5;
	box-shadow: 0px 0px 0px 3px #e7ffc5;
}
#save_avatar:hover {
	background: #eaf9af; /* Old browsers */
	background: -moz-linear-gradient(top,  #eaf9af 2%, #dff787 18%, #ccea5e 44%, #bcea16 87%, #a9d606 99%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(2%,#eaf9af), color-stop(18%,#dff787), color-stop(44%,#ccea5e), color-stop(87%,#bcea16), color-stop(99%,#a9d606)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #eaf9af 2%,#dff787 18%,#ccea5e 44%,#bcea16 87%,#a9d606 99%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #eaf9af 2%,#dff787 18%,#ccea5e 44%,#bcea16 87%,#a9d606 99%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #eaf9af 2%,#dff787 18%,#ccea5e 44%,#bcea16 87%,#a9d606 99%); /* IE10+ */
	background: linear-gradient(top,  #eaf9af 2%,#dff787 18%,#ccea5e 44%,#bcea16 87%,#a9d606 99%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#eaf9af', endColorstr='#a9d606',GradientType=0 ); /* IE6-9 */
	color:#49682b;
	border:1px solid #88a462;
	text-decoration:none;
}
#save_avatar:active {
	background: #7b9a0c; /* Old browsers */
	background: -moz-linear-gradient(top,  #7b9a0c 1%, #89a818 13%, #abd026 91%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#7b9a0c), color-stop(13%,#89a818), color-stop(91%,#abd026)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #7b9a0c 1%,#89a818 13%,#abd026 91%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #7b9a0c 1%,#89a818 13%,#abd026 91%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #7b9a0c 1%,#89a818 13%,#abd026 91%); /* IE10+ */
	background: linear-gradient(top,  #7b9a0c 1%,#89a818 13%,#abd026 91%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7b9a0c', endColorstr='#abd026',GradientType=0 ); /* IE6-9 */
	color:#3a492c;
	border:1px solid #88a462;
	text-decoration:none;
}

ul.avatar_tabs{ list-style:none; margin:0; padding:0; overflow:hidden}
ul.avatar_tabs li{float:left; margin:0 1px 0 0}
ul.avatar_tabs li a{padding:7px 13px 5px 13px;display:block; background:#BCDFB0; color:#555; text-decoration:none; -moz-transition: none; -webkit-transition: none; -o-transition: color 0 ease-in; transition: none;}
ul.avatar_tabs li a:hover{background-color:#C7E5BE; color:#111}
ul.avatar_tabs li a.current{background:#82C36B; color:#fff; padding:7px 13px 5px 13px;}
ul.avatar_tabs li:first-child a{ border-radius:6px 0 0 0;}
ul.avatar_tabs li:last-child a{ border-radius:0 6px 0 0;  padding:7px 13px 5px 14px;}
ul.avatar_tabs li a:focus{outline:0}

button#swap_gender {
	font-size:12px;
	padding:10px 13px 8px;
}
</style>
<div class="success" style="display:none;">
	<h3><?=image('images/yay.png')?> {compliment}</h3>
	<p>Your avatar has been saved!</p>
</div>
<div id="avatar_preview">
	<img src="{avatar_preview_url}" id="avatar_preview_img" width="180" height="270" />

	<div style="margin:5px 10px;">
		<a href="<?=site_url('avatar/save');?>" id="save_avatar">Save changes</a>
		<div style="text-align:center; margin-right:auto; margin-left:auto;">
			<a href="#" id="revert_button">Revert</a> or <a href="#" id="unequip_all_button">Unequip all</a>
<!-- 			<button id="revert" onclick="revert('<?=base_url()?>');" class="proceed">Revert</button>
			<button id="unequip_all" class="proceed"  onclick="unequip_all('<?=base_url()?>');">Unequip all</button><br class="clear" />
 -->		</div>
	 	<div style="margin:16px 0 0 0; padding:10px 0 0 30px; border-top:1px solid #ddd; font-size:13px;">
	 		<button id="swap_gender" class="proceed" onclick="swap_gender('<?=base_url()?>');">Swap gender</button>
	 	</div>
	</div>

</div>


<div class="grid_7 tabs" style="margin:0; width:565px">
	<?php if (count($items) > 0): ?>
		<ul class="avatar_tabs">
			<?php foreach ($items as $tab => $tab_name): ?>
				<?php foreach ($tab_name as $key => $item1): ?>
					<li><?php echo '<a href="#'.strtolower($key).'" class="'.strtolower($key).'">'.$key.'</a>'; ?></li>
				<?php endforeach ?>
			<?php endforeach ?>
		</ul>
		<div id="tab_glove">
		<? foreach($items as $tab => $item1): ?>
			<? $i=0; foreach($item1 as $order => $item2): ?>
				<div id="<?=strtolower($order)?>" <?php if($i==0){ $i++; echo 'style="display:none;"';} ?>>
				<? foreach($item2 as $key_1 => $item3): ?>
					<span class="<?=$key_1?>">
					<? foreach($item3 as $key_2 => $item): ?>
					    <? if(isset($item['sub_items'][0])): ?>
					    <? $sub_active = false; ?>
					        <div class="multi_item" id="<?=$item['group_key']?>">
					            <a href="<?=site_url('avatar/equip/'.$item['id'].'/'.(count($item['sub_items']) > 0 ? '-1' : '')); ?>" class="stack<?=($item['equipped'] == true ? ' equipped' : '');?>" layer="<?=$item['layer']?>" id="<?=$item['id']?>" parent="true" style="margin:0.5px;">
        						<? if($item['num'] > 1):?>
        						    <?=image('avatar/thumbnail/'.$item['id'], 'title="'.$item['name'].'"')?>
        						<? else: ?>
        						    <?=image('/images/items/'.$item['thumb'], 'title="'.$item['name'].'"')?>
        						<? endif; ?>
        						</a>
        						<ul>
        						<? foreach($item['sub_items'] as $sub_item): ?>
        						<? ($sub_item['equipped'] == true ? $sub_active = true : ''); ?>
        						  <li>
        						      <a href="<?=site_url('avatar/equip/'.$item['id'].'-'.$sub_item['sub_item_key']);?>" class="stack <?=($sub_item['equipped'] == true ? 'equipped' : '');?>" layer="<?=$sub_item['layer']?>" id="<?=$item['id']?>" sub_item="true" style="margin:0.5px;" width="42" height="42">
              							<?=image('images/items/'.$sub_item['thumb'], 'title="'.$sub_item['name'].'"')?>
              						</a>
        						  </li>
        						<? endforeach; ?>
        						</ul>
        						<a href="#" class="pull_down <?=($sub_active == true ? 'glowing' : '');?>" style="margin:1px; margin-right:-2px;"></a>
					        </div>
					    <? else: ?>
					        <a href="<?=site_url('avatar/equip/'.$item['id'].'/');?>" class="stack<?=($item['equipped'] == true ? ' equipped' : '');?>" layer="<?=$item['layer']?>"  id="<?=$item['id']?>" style="margin:0.5px;">
    						<? if($item['num'] > 1):?>
    						    <?=image('avatar/thumbnail/'.$item['id'].'/'.$item['num'], 'title="'.$item['name'].'" width="42" height="42"')?>
    						<? else: ?>
    						    <?=image('/images/items/'.$item['thumb'], 'title="'.$item['name'].'" width="42" height="42"')?>
    						<? endif; ?>
    						</a>
					    <? endif;
					endforeach;
					echo '</span>';
				endforeach;
				echo "</div>";
			endforeach;
		endforeach;
	else:
		echo "<strong>You have no items in your inventory.</strong>";
	endif;
	?>
	</div>

	<br class="clear" />

</div>
<br clear="all" />
<div id="test"></div>

<br />
