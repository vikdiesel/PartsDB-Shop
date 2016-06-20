<div class="page-header">
	<h1>Импорт наличия и цен</h1>
</div>

<? if ($message == 'added'): ?>
	<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Склад добавлен.</strong> Теперь вы можете загрузить для него цены.</div>
<? elseif ($message == 'deleted'): ?>
	<div class="alert alert-error"><i class="icon-trash"></i> <strong>Успешно.</strong> Склад удален.</div>
<? elseif ($message == 'cleared'): ?>
	<div class="alert alert-error"><i class="icon-trash"></i> <strong>Успешно.</strong> Данные очищены.</div>
<? elseif ($message == 'consider_deleting'): ?>
	<div class="alert alert-block">
		<h4>Хотите чего-нибудь удалить?</h4>
		<p>Загрузите прайс с меньшим количеством строк, либо выберите пункт <i>Удалить</i> в подменю, справа от кнопки <i>Импорт</i>.<br>
		Ваш основной склад (первый в списке) удалить нельзя.</p>
	</div>
<? else: ?>
	<div class="alert alert-info alert-fadeout"><strong>Ваши склады.</strong> Вы можете создать неограниченное количество складов с различными параметрами.</div>
<? endif ?>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Склад</th>
			<th>Поставка</th>
			<th>Цена</th>
			<th>Обновлено</th>
			<th>Строк</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<? foreach ($vendors->result() as $vend): ?>
	
	<tr <? if ($vend->is_primary): ?> class="success" <? endif ?>>
		
		<td><?=$vend->vendor_name?></td>
		<td><?=$vend->delivery_days?> дн.</td>
		<td>цена &times; <?=$vend->price_correction?></td>
		<? if ($vend->api_id !== null): ?>
			<td colspan="2"><span class="label">Автоматическая выгрузка</span></td>
		<? else: ?>
			<td><? if ($vend->last_update > 0) echo date_tz('d.m.Y', $vend->last_update); else echo "---"?></td>
			<td><?=$vend->rows_cache?></td>
		<? endif ?>
		<td class="table-row-actions">
			<div <? if ($no_prices and $vend->is_primary): ?> class="btn-group help-guide" data-title="Куда же нажать?" data-placement="left" data-content="Не знаете куда нажать? Начните с импорта для вашего склада." <? else: ?> class="btn-group" <? endif ?>>
				<? if ($vend->api_id !== null): ?>
					<a href="<?=site_url('admin/vendor_edit/'.$vend->id)?>" class="btn -btn-primary btn-small btn-fw-8"><i class="icon-adjust -icon-white"></i> Настройка</a>
				<? else: ?>
					<a href="<?=site_url('admin/import_prices/'.$vend->id)?>" class="btn btn-primary btn-small btn-fw-8"><i class="icon-download-alt icon-white"></i> Импорт</a>
				<? endif ?>
				<button class="btn <? if ($vend->api_id === null): ?>btn-primary<? endif ?> btn-small dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				<ul class="dropdown-menu pull-right">
					<? if ($vend->api_id === null): ?>
						<li><a href="<?=site_url('admin/import_prices/'.$vend->id)?>"><i class="icon-download-alt"></i> Импорт файла</a></li>
						<li class="divider"></li>
					<? endif ?>
					<li><a href="<?=site_url('admin/vendor_edit/'.$vend->id)?>"><i class="icon-adjust"></i> Параметры склада</a></li>
					<? if ($vend->api_id === null): ?>
						<li <? if ($vend->rows_cache == 0): ?> class="disabled" <? endif ?>><a href="<?=site_url('admin/prices_lookup/' . $vend->id . '/' . $vend->import_group_id)?>"><i class="icon-file"></i> Просмотр строк</a></li>
					<? endif ?>
					<? if ($vend->allow_delete): ?>
						<li><a href="<?=site_url('admin/delete_vendor/' . $vend->id . '/vendors_list' ) ?>"><i class="icon-trash"></i> Удалить</a></li>
					<? else: ?>
						<li <? if ($vend->rows_cache == 0): ?> class="disabled" <? endif ?>><a href="<?=site_url('admin/delete_vendor/' . $vend->id . '/vendors_list' ) ?>"><i class="icon-trash"></i> Очистить</a></li>
					<? endif ?>
				</ul>
			</div>
		</td>
		
	</tr>
	
	
	<? endforeach ?>
	
	
	</tbody>
	
</table>

<div class="form-actions"><a href="<?=site_url('admin/vendor_add')?>" class="btn small"><i class="icon-plus-sign"></i> Добавить новый склад</a></div>


