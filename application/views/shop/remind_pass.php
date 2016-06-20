<div class="page-header">
	<h1>Напоминание пароля</h1>
</div>

	<?=$errors?>

	<?=form_open('user/remind-pass', array('id'=>'remind_pass_form', 'class'=>'well form-inline')) ?>

		
				<?=form_input('email', set_value('email', $form['email']), 'class="input-large form-control" placeholder="Ваш E-mail"'); ?>
			

		<button type="submit" class="btn btn-default">Выслать пароль</button>  <span class="help-inline">Введите E-mail с которым вы регистрировались</span>

	</form>
