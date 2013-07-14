		<h1>Compose item</h1>		
		<div class="grid_4">
<form method="post">
		<ul class="form mini">
			<li><label>Item name:</label> <input type="text" class="large-input large" name="name" /></li>
			<li><label>Image name:</label> <input type="text" class="large-input large" name="image" /></li>
			<li><label>Gender:</label> 
			<select class="large-input large" name="gender">
				<option value="Male">Male</option>
				<option value="Female">Female</option>
				<option value="Unisex">Both</option>
			</select>
			</li>
			<li><label>Layer:</label> 
			<select class="large-input large" name="layer">
				<option value="1">Tops</option>
				<option value="2">Base</option>
				<option value="3">Eyes</option>
				<option value="4">Hairs</option>
				<option value="5">Bottoms</option>
			</select>
			</li>
			<li class="right" style="margin:-15px 0 0;"><?=button('Create item','submitBtn')?></button></li> </ul>
			</form>
	</div>
 