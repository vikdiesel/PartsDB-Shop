<div class="page-header">
	<h1>Напоминание пароля</h1>
</div>
<div class="alert alert-success">
	<i class="icon-ok-sign"></i> <strong>Успешно.</strong> 
	<? if (!empty($email)): ?>
		Пароль выслан на e-mail <strong><?=$email?></strong>.
	<? else: ?>
		Пароль выслан на указанный e-mail.
	<? endif ?>
</div>
<p>Вернуться на <a href="<?=site_url('user/login')?>">страницу авторизации</a></p>
