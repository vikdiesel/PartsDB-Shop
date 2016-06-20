<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/options_delivery') ?>" class="btn">Все способы доставки</a></div>
	<h1>Создание способа доставки</h1>
</div>

<?=$errors?>
<?=$success?>

<?=form_open('admin/options_delivery_add', 'class="form-horizontal"') ?>

		<legend>Параметры способа доставки</legend>
		<div class="control-group">
			<label class="control-label" for="title">Название</label>
			<div class="controls">
				<input class="input-xlarge" id="title" name="title" type="text" value="<?php echo set_value('title'); ?>">
				<span class="help-block">Понятное для клиента название способа доставки</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="price">Стоимость</label>
			<div class="controls">	
				<div class="input-append" >
					<input class="input-small" id="price" name="price" size="30" type="text" value="<?php echo set_value('price'); ?>">
					<span class="add-on"><?=$currency_symbol ?></span>
				</div>
				<span class="help-block"><strong>Только цифры.</strong> Стоимость доставки данным способом (будет добавлена к сумме заказа)</span>
			</div>
		</div>
	
		<legend>Параметры сортировки</legend>
		<div class="control-group">
			<label class="control-label" for="order">Порядковый номер</label>
			<div class="controls">
				<input class="input-small" id="order" name="order" size="30" type="text" value="<?php echo set_value('order', '0'); ?>">
				<span class="help-block">При выводе в корзине клиента, способы доставки сортируются от меньшего к большему</span>
			</div>
		</div>
	
		<div class="form-actions">
			<input type="submit" class="btn btn-primary" value="Создать способ доставки" name="vendor_add_new">&nbsp;<a href="<?=site_url('admin/options_delivery')?>" class="btn">Назад</a>
		</div>
	
</form>