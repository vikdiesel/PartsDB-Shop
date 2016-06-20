<?=form_open('setup', array('id'=>'admin_login_form', 'class'=>'form-box form-signin')) ?>
<h2 class="form-signin-heading"><?=_cfg('jb_domain_branded', TRUE)?></h2>

<?=$errors?>

<div class="auth-fields">

  <label for="title">Название сайта</label>
  <input type="text" name="title" id="title" class="input-block-level" placeholder="Автозапчасти" value="<?=setval('title', 'Автозапчасти', $form)?>">

  <label for="subtitle">Описание сайта</label>
  <input type="text" name="subtitle" id="subtitle" class="input-block-level" placeholder="Автозапчасти" value="<?=setval('subtitle', 'Автозапчасти оптом и в розницу', $form)?>">

  <label for="adminemail">E-mail администратора</label>
  <input type="email" name="adminemail" id="adminemail" class="input-block-level" placeholder="john@example.com" value="<?=setval('adminemail', '', $form)?>">

  <label for="adminpass-field">Пароль администратора</label>
  <input type="text" name="adminpass" id="adminpass-field" class="input-block-level" placeholder="Придумайте пароль" value="<?=setval('adminpass', '', $form)?>">

</div>

<div class="auth-button-centered">
  <button class="btn btn-primary" type="submit"><i class="icon-white icon-ok-sign"></i> Запустить установку</button>
</div>

</form>
	  

