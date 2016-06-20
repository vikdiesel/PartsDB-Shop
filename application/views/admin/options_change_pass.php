<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/options') ?>" class="btn"><i class="icon-wrench"></i> Все настройки</a></div>
	<h1>Смена пароля</h1>
</div>

<?=$errors?>
<?=$success?>

<?=form_open('admin/options_change_pass', array('id'=>'options_change_pass', 'class'=>'form-horizontal')) ?>
	
	<legend>Текущий пароль</legend>
	<div class="control-group">
		<label class="control-label" for="adminpass">Текущий пароль администратора</label>
		<div class="controls">
			<input class="input-large" id="adminpass" name="adminpass" size="30" type="password" value="<?=set_value('adminpass'); ?>">
			<span class="help-block">Введите текущий пароль администратора</span>
		</div>
	</div>

	<legend>Новый пароль</legend>

	<div class="control-group">
		<label class="control-label" for="adminpass_new">Новый пароль администратора</label>
		<div class="controls">
			<input class="input-large" id="adminpass_new" name="adminpass_new" size="30" type="password" value="<?=set_value('adminpass_new'); ?>">
			<span class="help-block">Введите новый пароль администратора</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="adminpass_new_confirm">Новый пароль (еще раз)</label>
		<div class="controls">
			<input class="input-large" id="adminpass_new_confirm" name="adminpass_new_confirm" size="30" type="password" value="<?=set_value('adminpass_new_confirm'); ?>">
			<span class="help-block">Повторите ввод нового пароля администратора</span>
		</div>
	</div>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Сменить пароль" name="options_common_submit">&nbsp;<a href="<?=site_url('admin/options')?>" class="btn">Назад</a>
	</div>
	
</form>