<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/vendor_crosses_list') ?>" class="btn"><i class="icon-align-justify"></i> Список групп</a></div>
	<h1>Просмотр кросс-листа</h1>
</div>

<? if ($import_group_id == $vendor->import_group_id): ?>
	<div class="alert alert-success">
		Данный кросс-лист <strong>используется</strong> в работе сайта. <a class="btn btn-mini" href="<?=site_url('admin/vendor_crosses_list') ?>">Вернуться</a>
	</div>
<? else: /* this behaviour is deprecated */ ?>
	<div class="alert alert-block alert-info">
		<p><strong>Кросс-лист не используется.</strong> Данный кросс-лист находится в базе данных, но в работе сайта не используется, т.к. похоже импорт данного файла был прерван.
		<p>Если вы считаете, что кросс-лист нормальный, можете выставить его как рабочий (<strong>все остальные кросс-листы этого поставщика кроссов в этом случае будут удалены</strong>).</p>
		
		<p>
			<a class="btn small" href="<?=site_url('admin/import_finish_call/' . $vendor->id . '/' . $import_group_id) ?>">Сделать основным</a> <a class="btn small" href="<?=site_url('admin/vendor_crosses_list') ?>">Вернуться</a>
		</p>
	</div>
<? endif ?>

<h4><?=$vendor->vendor_name?> / Группа <?=$import_group_id?> / <?=$total_rows?> строк</h4>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Артикульные номера (аналоги разделены пробелом)</th>
		</tr>
	</thead>
	<tbody>
	
	<? foreach ($crosses->result() as $cr): ?>
	
	<tr>
		
		<td><?=$cr->art_numbers?></td>
		
	</tr>
	
	<? endforeach ?>
	</tbody>
</table>

<div class="pagination">
	<ul>

		<?=$pagination ?>
		
	</ul>
</div>