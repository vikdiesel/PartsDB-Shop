
<a name="order_form_anchor"></a>
<? if ($is_single_page_form): ?>
	<div class="page-header">
		<h1><?=$order_full_title?></h1>
	</div>
<? else: ?>
	<h3><?=$order_full_title ?></h3>
<? endif ?>

<? if (!is_authorized()): ?>
	<div class="alert alert-info">В момент первого заказа, для вас будет заведена учетная запись с накопительной скидкой. <strong>Пароль будет выслан на ваш e-mail.</strong></div>
	<div class="alert alert-info"><i class="icon-info-sign"></i> <strong>Ознакомьтесь с Офертой.</strong> Оформляя заказ, вы подтверждаете свое согласие с <a href="<?=site_url('page/agreement')?>">Офертой</a>.</div>
<? endif ?>

<? if ($no_edit_mode): ?>
	<div class="alert alert-info">Вы вошли как <strong><?=$form['email']?></strong>: <a href="<?=site_url('order/make')?>" class="btn btn-mini"><i class="icon-pencil"></i> Изменить данные</a> <a href="/user/logout/cart" class="btn btn-mini">Выйти</a></div>
<? endif; ?>

<?=$errors?>

<?=form_open('order/make', array('id'=>'order_form', 'class'=>'form-horizontal')) ?>

	<div class="order_required">
		
		<div class="control-group">
			<? form__label('email2', 'E-mail') ?>
			<div class="controls">
				<? form__input('email', $form, $email_no_edit_mode, 'email2') ?>
				<p class="help-block">На e-mail высылается информация о заказе</p>
			</div>
		</div>
		
		<div class="control-group">
			<? form__label('name', 'Ваше имя') ?>
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
		<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Оформить заказ</button>
		
		<? if ($is_single_page_form): ?>
			<a href="<?=site_url('cart')?>" class="btn">Корзина</a>
		<? elseif (!isset($no_edit_mode) or !$no_edit_mode): ?>
			<button class="btn btn-default" type="reset">Сброс</button>
		<? else: ?>
			<a href="<?=site_url('order/make')?>" class="btn">Изменить данные</a>
		<? endif ?>
	</div>

</form>
