<? if (isset($id)): ?>
	<div class="page-header">
		<div class="pull-right"><a href="<?=site_url('admin/vendors_list') ?>" class="btn"><i class="icon-align-justify"></i> Список складов</a></div>
		<h1>Параметры склада</h1>
	</div>
	
	<?=$errors?>
	<?=$success?>
	
	<? if ($form['allow_delete']): ?>
		<!--<div class="alert alert-info">
			<strong>Удаление склада</strong>. Вы можете удалить данный склад (и все свзянные данные). Действие безвозвратно. <a class="btn btn-mini" href="<?=site_url('admin/delete_vendor/' . $id . '/vendors_list' ) ?>"><i class="icon-trash"></i> Удалить склад</a>
		</div>-->
	<? endif ?>
	
<? else: ?>
	<div class="page-header">
		<div class="pull-right"><a href="<?=site_url('admin/vendors_list') ?>" class="btn"><i class="icon-align-justify"></i> Список складов</a></div>
		<h1>Добавление склада</h1>
	</div>
	
	<?=$errors?>
	<?=$success?>
	
	<div class="alert alert-info alert-fadeout">Вы можете добавить неограниченное количество складов с различными параметрами.</div>
<? endif ?>


<?=form_open('', 'class="form-horizontal"') ?>
	
	<legend>Данные склада</legend>
	
	<div class="control-group">
		<label class="control-label" for="vendor_name">Название</label>
		<div class="controls">
			<input class="input-xlarge" id="vendor_name" name="vendor_name" size="30" type="text" value="<?=setval('vendor_name','',$form); ?>" />
			<span class="help-block">Название склада для внутреннего использования</span>
		</div>
	</div>
	
	
	<legend>Параметры склада</legend>
	
	<div class="control-group">
	<label class="control-label" for="delivery_days">Срок поставки</label>
		<div class="controls">
			<div class="input-append">
				<input class="input-mini" id="delivery_days" name="delivery_days" size="3" type="text" value="<?=setval('delivery_days','0',$form); ?>" />
				<span class="add-on">дней (в среднем)</span>
			</div>
			<span class="help-block"><strong>Только цифры</strong>. Сколько дней запчасти идут со склада до покупателя?</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="price_correction">Коррекция цены</label>
		<div class="controls">
			<div class="input-prepend">
				<span class="add-on">цена &times;</span>
				<input class="input-mini" id="price_correction" name="price_correction" size="6" type="text" value="<?=setval('price_correction','1.00',$form); ?>">
			</div>
			<span class="help-inline">Дает эффект в момент загрузки прайса</span>
			<span class="help-block">
				<strong>Цифры. Десятичные значения отделяются точкой.</strong> Коэффициент для автоматического умножения цены.<br>
				<i>Можно указать, например, 38, если валюта сайта Рубли, а цены поставщика в Долларах.</i>
			</span>
		</div>
	</div>
	
	<legend>Отправка заказа поставщику <small>(Опционально)</small></legend>
	
	<div class="alert alert-info"><i class="icon-info-sign"></i> Нажав специальную кнопку, вы сможете сформировать и отправить заказ прямо поставщику в виде файла CSV.</div>
	
	<div class="control-group">
		<label class="control-label" for="orderemail">E-mail поставщика для заказа</label>
		<div class="controls">
			<input class="input-xlarge" id="orderemail" name="orderemail" size="30" type="text" value="<?=setval('orderemail','',$form); ?>">
			<span class="help-block"><b>Опционально.</b> Уточните информацию у постащика.</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="ordername">Ваше наименование для заказа</label>
		<div class="controls">
			<input class="input-xlarge" id="ordername" name="ordername" size="30" type="text" value="<?=setval('ordername','',$form); ?>" placeholder="<?=$ordername_default?>">
			<span class="help-block"><b>Опционально.</b> При отправке заказа, в письме будет указано ваше наименование как заказчика.</span>
		</div>
	</div>
	
	<!--
	<div class="control-group">
		<label class="control-label" for="orderfrom">Адрес e-mail с которого производить отправку</label>
		<div class="controls">
			<input class="input-xlarge" id="orderfrom" name="orderfrom" size="30" type="text" value="<?=setval('orderfrom','',$form); ?>" placeholder="<?=$orderfrom_default?>">
			<span class="help-block"><b>Опционально.</b> При отправке заказа, письмо будет отправлено с указанного адреса.</span>
		</div>
	</div>-->
	
	<legend>Порядок колонок файла <small>(Опционально)</small></legend>
	
	<div class="alert alert-info"><i class="icon-info-sign"></i> <strong>Работает при ручном импорте.</strong> Если ваш прайс отличен от стандартного. Задает порядок компоновки колонок в вашем прайсе.</div>
	
	<div class="control-group">
		<label class="control-label" for="struct_art_number">Артикул</label>
		<div class="controls">
			<input class="input-mini" id="struct_art_number" name="struct_art_number" min="1" max="99" size="2" type="number" value="<?=setval('struct_art_number','1',$form); ?>">
			<span class="help-block"><b>Можно оставить по умолчанию.</b> Порядковый номер колонки Артикул (от 1 до 99)</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="struct_sup_brand">Бренд</label>
		<div class="controls">
			<input class="input-mini" id="struct_sup_brand" name="struct_sup_brand" min="1" max="99" size="2" type="number" value="<?=setval('struct_sup_brand','2',$form); ?>">
			<span class="help-block"><b>Можно оставить по умолчанию.</b> Порядковый номер колонки Бренд (от 1 до 99)</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="struct_description">Наменование</label>
		<div class="controls">
			<input class="input-mini" id="struct_description" name="struct_description" min="1" max="99" size="2" type="number" value="<?=setval('struct_description','3',$form); ?>">
			<span class="help-block"><b>Можно оставить по умолчанию.</b> Порядковый номер колонки Наименование (от 1 до 99)</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="struct_qty">Количество</label>
		<div class="controls">
			<input class="input-mini" id="struct_qty" name="struct_qty" min="1" max="99" size="2" type="number" value="<?=setval('struct_qty','4',$form); ?>">
			<span class="help-block"><b>Можно оставить по умолчанию.</b> Порядковый номер колонки Количество (от 1 до 99)</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="struct_price">Цена</label>
		<div class="controls">
			<input class="input-mini" id="struct_price" name="struct_price" min="1" max="99" size="2" type="number" value="<?=setval('struct_price','5',$form); ?>">
			<span class="help-block"><b>Можно оставить по умолчанию.</b> Порядковый номер колонки Цена (от 1 до 99)</span>
		</div>
	</div>
	
	<legend>Автоматическая выгрузка <small>(Опционально)</small></legend>

	<div class="alert alert-info"><i class="icon-info-sign"></i> <strong>Выгружается не так, как ожидалось?</strong> Иногда сервисы требуют доп. настроек.</div>
	
	<div class="control-group">
		<label class="control-label" for="api_id">Сервер поставщика</label>
		<div class="controls">
			<select name="api_id" id="api_id">
				<option value="0">(опционально)</option>
				<? foreach ($apis as $api_id => $api_data): ?>
					<option value="<?=$api_id ?>" <?=set_select('api_id', $api_id, ($form['api_id'] == $api_id)?TRUE:FALSE)?>><?=$api_data[0]?></option>
				<? endforeach ?>
			</select>
			<span class="help-block"><strong>Опционально.</strong> Сервер для автоматической выгрузки цен.</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="api_key1">Ключ 1</label>
		<div class="controls">
			<input class="input-medium" id="api_key1" name="api_key1" size="30" type="text" value="<?=setval('api_key1','', $form); ?>" placeholder="(опционально)">
			<span class="help-block"><strong>Опционально.</strong> Обычно, это логин или API-key <span class="muted">(предоставляется поставщиком)</span>.</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="api_key2">Ключ 2</label>
		<div class="controls">
			<input class="input-medium" id="api_key2" name="api_key2" size="30" type="text" value="<?=setval('api_key2','', $form); ?>" placeholder="(опционально)">
			<span class="help-block"><strong>Опционально.</strong> Обычно пустое или пароль <span class="muted">(предоставляется поставщиком)</span>.</span>
		</div>
	</div>
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Сохранить</button>
		<a href="<?=site_url('admin/vendors_list')?>" class="btn">Назад</a>
	</div>
	
</form>