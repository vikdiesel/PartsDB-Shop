<div class="page-header">
	<h1>Импорт кроссов</h1>
</div>

<? if (isset($message) and $message == 'added'): ?>
	<div class="alert alert-success">
		<i class="icon-ok-sign"></i> <strong>Группа создана.</strong> Теперь вы можете загрузить для нее данные.
	</div>
<? elseif (isset($message) and $message == 'deleted'): ?>
	<div class="alert alert-error">
		<i class="icon-trash"></i> <strong>Успешно.</strong> Группа удалена.
	</div>
<? else: ?>
	<div class="alert alert-info alert-fadeout">Данная функция позволяет добавить к поиску автозапчасти которых нет в наших базах.</div>
<? endif ?>

<? if ($num_vendors > 0): ?>

	<table class="table table-striped">
		<thead>
			<tr>
				<th>Название группы</th>
				<th>Обновлено</th>
				<th>Строк</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		
		<? foreach ($vendors->result() as $vend): ?>
		
		<tr>
			
			<td><?=$vend->vendor_name?></td>
			<td><? if ($vend->last_update > 0) echo date_tz('d.m.Y', $vend->last_update); else echo "---"?></td>
			<td><?=$vend->rows_cache ?><!--<a href="<?=site_url('admin/crosses_lookup/' . $vend->id . '/' . $vend->import_group_id)?>" title="Просмотр строк"><?=$vend->rows_cache ?></a>--></td>
			<td class="table-row-actions">
				<!--<? if ($vend->allow_delete): ?>
					<a title="Удалить" href="<?=site_url('admin/delete_vendor/' . $vend->id . '/vendor_crosses_list' ) ?>" class="btn btn-small"><i class="icon-trash"></i></a>
				<? else: ?>
					<a title="Удалить" href="#" class="btn btn-small disabled"><i class="icon-trash"></i></a>
				<? endif ?>
				<a href="<?=site_url('admin/import_crosses/'.$vend->id)?>" class="btn btn-small btn-primary"><i class="icon-download-alt icon-white"></i> Импорт</a>-->
				
				<div class="btn-group">
					<a href="<?=site_url('admin/import_crosses/'.$vend->id)?>" class="btn btn-primary btn-small"><i class="icon-download-alt icon-white"></i> Импорт</a>
					<button class="btn btn-primary btn-small dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
					<ul class="dropdown-menu pull-right">
						<li><a href="<?=site_url('admin/import_crosses/'.$vend->id)?>"><i class="icon-download-alt"></i> Импорт файла</a></li>
						<li class="divider"></li>
						<li <? if ($vend->rows_cache == 0): ?> class="disabled" <? endif ?>><a href="<?=site_url('admin/crosses_lookup/' . $vend->id . '/' . $vend->import_group_id)?>"><i class="icon-file"></i> Просмотр строк</a></li>
						<? if ($vend->allow_delete): ?>
							<li><a href="<?=site_url('admin/delete_vendor/' . $vend->id . '/vendor_crosses_list' ) ?>"><i class="icon-trash"></i> Удалить</a></li>
						<? endif ?>
					</ul>
				</div>
			</td>
			
		</tr>
		
		<? endforeach ?>
		
		</tbody>
		
	</table>
<? endif ?>

<div class="form-actions">
	<? if ($num_vendors == 0): ?>
		<a href="<?=site_url('admin/vendor_crosses_add')?>" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i> Создать группу</a>
	<? else: ?>
		<a href="<?=site_url('admin/vendor_crosses_add')?>" class="btn"><i class="icon-plus-sign"></i> Создать группу</a>
	<? endif ?>
	
	<span class="help-inline">Группы позволяют разделять кроссы различных поставщиков</span>
</div>