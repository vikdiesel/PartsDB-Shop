<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/vendor_crosses_list') ?>" class="btn"><i class="icon-align-justify"></i> Список групп</a></div>
	<h2>Создание группы кроссов</h2>
</div>

<div class="alert alert-info alert-fadeout"><strong>Придумайте название понятное вам</strong>. Вы можете создать неограниченное количество групп.</div>

<?=$errors?>

<?=form_open('admin/vendor_crosses_add', 'class="form-inline well"') ?>
	
	<input class="input-xlarge" id="vendor_name" name="vendor_name" type="text" value="<?php echo set_value('vendor_name'); ?>" placeholder="Название группы">
	<button type="submit" class="btn btn-primary" name="vendor_add_new"><i class="icon-plus-sign icon-white"></i> Создать</button>
	
</form>