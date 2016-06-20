<? if (isset($justlogin)): ?>
	<div class="page-header">
		<? if (!_cfg('hideadmlink')): ?>
		<div class="pull-right">
			<a href="<?=site_url('admin')?>" class="btn"><i class="icon-tasks"></i> Вход администратора</a>
		</div>
		<? endif ?>
		<h1>Вход клиента</h1>
	</div>
	<?=$errors?>
	
	<p>Войдите, чтобы получить доступ к персональным разделам и видеть цены с учетом вашей скидки&hellip;</p>
<? else: ?>
	<h3>Заказ для существующих клиентов </h3>
	<?=$errors?>
	<p>Введите ваши E-mail и пароль в соответствующие поля ниже:</p>
<? endif ?>

<?=form_open($formaction, array('id'=>'order_auth_form', 'class'=>'well form-inline')) ?>
		
			<?=form_input('email', set_value('email', $form['email']), 'class="input-medium form-control" placeholder="E-mail"'); ?>
			<?=form_password('password', '', 'class="input-medium form-control" placeholder="Пароль"'); ?>
	
	<button type="submit" class="btn <? if (isset($justlogin)): ?>btn-primary<? else: ?>btn-default<? endif ?>">Войти</button> <span class="help-inline"><a href="<?=site_url('user/remind-pass')?>">Напомнить пароль</a></span>
	
</form>

<div class="alert alert-info"><i class="icon-info-sign"></i> <strong>Новый пользователь?</strong> Вы будете зарегистрированы в момент первого заказа.</div>
<!-- <div class="alert alert-info"><i class="icon-info-sign"></i> <strong>Нужен административный раздел?</strong> Вход для администратора <a href="<?=site_url('admin/login')?>" class="btn btn-mini">здесь</a></div> -->