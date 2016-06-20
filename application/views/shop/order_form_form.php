<?=$errors?>

<?=form_open($form_action, array('id'=>'order_form', 'class'=>'form-horizontal')) ?>

	<div class="order_required">
		
		<div class="control-group">
			<? form__label('email2', 'E-mail') ?>
			<div class="controls">
				<? form__input('email', $form, $email_no_edit_mode, 'email2') ?>
				<p class="help-block">На e-mail высылается информация о заказе</p>
			</div>
		</div>
		
		<? if ($form_type == 'register'): ?>
		
			<div class="control-group">
				<? form__label('password', 'Пароль') ?>
				<div class="controls">
					<? form__pass2('password', 32) ?>
					<p class="help-block">Придумайте пароль от 6-ти символов, например: <em>gy7624</em></p>
				</div>
			</div>
			
			<div class="control-group">
				<? form__label('password2', 'Пароль еще раз') ?>
				<div class="controls">
					<? form__pass2('password2', 32) ?>
					<p class="help-block">Введите пароль еще раз</p>
				</div>
			</div>
			
			<hr>
		
		<? endif ?>
		
		<div class="control-group">
			<? form__label('name', 'Имя и фамилия') ?>
			<div class="controls">
				<? form__input('name', $form, $no_edit_mode) ?>
				<p class="help-block">Например: <em>Егоров Олег</em></p>
			</div>
		</div>
		
		<div class="control-group">
			<? form__label('phone', 'Телефон') ?>
			<div class="controls">
				<? form__input('phone', $form, $no_edit_mode) ?>
				<p class="help-block">Мобильный, или телефон с кодом города, например: <em>(343) 777-777-1</em></p>
			</div>
		</div>
		
		<div class="control-group">
			<? form__label('address', 'Адрес доставки') ?>
			<div class="controls">
				<? form__input('address', $form, $no_edit_mode) ?>
				<p class="help-block">С указанием города и индекса, например: <em>123000, г. Москва, ул. Тверская, 12-22</em></p>
			</div>
		</div>
		
		
		
		<input type="hidden" name="order_comment" value="">
	
	</div>
	
	<div class="form-actions">
		
		<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> <?=($form_type == 'register')?"Регистрация":"Оформить заказ" ?></button>
		
		<? if ($form_type != 'register'): ?>
			<? if ($is_single_page_form): ?>
				<a href="<?=site_url('cart')?>" class="btn">Корзина</a>
			<? elseif (!isset($no_edit_mode) or !$no_edit_mode): ?>
				<button class="btn btn-default" type="reset">Сброс</button>
			<? else: ?>
				<a href="<?=site_url('order/make')?>" class="btn">Изменить данные</a>
			<? endif ?>
		<? else: ?>
			<button class="btn btn-default" type="reset">Сброс</button>
		<? endif ?>
	</div>

</form>