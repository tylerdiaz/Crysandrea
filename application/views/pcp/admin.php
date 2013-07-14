<h1 id="manage_crys">Manage Crys</h1>
<form action="<?=site_url('pcp/admin_save')?>" method="post" accept-charset="utf-8">
    <? foreach($config as $key => $value): ?>
        <label for=""><?=ucfirst($key)?>:</label><input type="text" name="config[<?=$key?>]" value="<?=$value?>" id="some_name"><br>
    <? endforeach; ?>
    <br>
    <p><input type="submit" value="Save changes"></p>
</form>