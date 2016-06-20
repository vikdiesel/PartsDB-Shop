<div class="page-header">
	<h1>Смена пароля</h1>
</div>

<?=$errors?>
<?=$success?>

<?=form_open('user/change_pass', array('class'=>'form-horizontal')) ?>
	
		<legend>Текущий пароль</legend>
		
		<div class="control-group">
			<? form__label('password', 'Пароль') ?>
			<div class="controls">
				<? form__pass2('password', 32) ?>
				<p class="help-block">Ваш текущий пароль</p>
			</div>
		</div>
	
		<legend>Новый пароль</legend>
		
		<div class="control-group">
			<? form__label('new_password', 'Новый пароль') ?>
			<div class="controls">
				<? form__pass2('new_password', 32) ?>
				<p class="help-block">Придумайте пароль от 6-ти символов, например: <em>gy7624</em></p>
			</div>
		</div>
		<div class="control-group">
			<? form__label('new_password2', 'Новый пароль (еще раз)') ?>
			<div class="controls">
				<? form__pass2('new_password2', 32) ?>
				<p class="help-block">Введите пароль еще раз</p>
			</div>
		</div>
	
		<div class="form-actions">
			<input type="submit" class="btn btn-primary" value="Сменить пароль" name="options_common_submit">
		</div>
	
</form>