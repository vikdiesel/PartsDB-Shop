<? if (!empty($with_header)): ?>
	<div class="page-header">
		<h1>Запрос наличия</h1>
	</div>
<? else: ?>
	<hr>
<? endif ?>

<div class="alert alert-info"><i class="icon-info-sign"></i> <b>Возможно, деталь есть в наличии у поставщиков.</b> Оставьте контакт, мы сможем дать дополнительную информацию.</div>
	
<?=$errors?>

<?=form_open('front/search_inquiry', array('id'=>'search_inquiry', 'class'=>'form-horizontal')) ?>

	<div class="control-group">
		<label class="control-label" for="name">Имя</label>
		<div class="controls">
			<input class="input-large" id="name" name="name" type="text" value="<?=setval('name', '', $form); ?>">
			<span class="help-block"><b>Обязательно.</b> Например: <em>Иван Егоров.</em></span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="contact">Контакт</label>
		<div class="controls">
			<input class="input-large" id="contact" name="contact" type="text" value="<?=setval('contact', '', $form); ?>">
			<span class="help-block"><b>Обязательно.</b> E-mail или телефон. Например: <em>i@example.com</em> или <em>+79120000000</em></span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="search">Искомый номер</label>
		<div class="controls">
			<input class="input-xlarge" id="search" name="search" type="text" value="<?=setval('search', $search_string, $form); ?>">
			<span class="help-block"><b>Обязательно.</b> Например: <em>Filtron OP5701</em></span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="car">Автомобиль или VIN</label>
		<div class="controls">
			<input class="input-xlarge" id="car" name="car" type="text" value="<?=setval('car', '', $form); ?>" placeholder="Необязательно">
			<span class="help-block">Необязательно. Например: <em>Opel Astra H 2006 Z16XEP</em> или <em>WX293812824922&hellip;</em></span>
		</div>
	</div>
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Отправить запрос</button>
		<span class="help-inline">Мы сохраняем конфиденциальность информации</span>
	</div>
</form>