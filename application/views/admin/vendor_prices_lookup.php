<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/vendors_list') ?>" class="btn"><i class="icon-align-justify"></i> Список складов</a></div>
	<h1>Просмотр прайса в базе данных</h1>
</div>

<? if ($import_group_id == $vendor->import_group_id): ?>
	<div class="alert alert-success">
		Данный прайс-лист <strong>используется</strong> в работе сайта. <a class="btn btn-mini" href="<?=site_url('admin/vendors_list') ?>">Вернуться</a>
	</div>
<? else:  /* this behaviour is deprecated */ ?>
	<div class="alert alert-block alert-info">
		<p><strong>Прайс не используется.</strong> Данный прайс находится в базе данных, но в работе сайта не используется, так как, по нашим данным, импорт данного файла был прерван.
		<p>Если вы считаете, что прайс нормальный, можете выставить его как рабочий (<strong>все остальные прайсы этого поставщика в этом случае будут удалены</strong>).</p>
		
		<p>
			<a class="btn btn-primary btn-small" href="<?=site_url('admin/import_finish_call/' . $vendor->id . '/' . $import_group_id) ?>">Сделать основным</a> <a class="btn btn-small" href="<?=site_url('admin/vendors_list') ?>">Вернуться</a>
		</p>
	</div>
<? endif ?>

<h4><?=$vendor->vendor_name?> / Группа <?=$import_group_id?> / <?=$total_rows?> строк</h4>

<!--<p class="brands">
	<strong>Фильтр по брендам:</strong>
	<a href="<?=site_url('admin/prices_lookup/' . $vendor->id . '/' . $import_group_id)?>" class="<?=(!$thisbrand)?"selected":""?>">Все</a>
	<? foreach($brands->result() as $brand): ?>
		
		<a href="<?=site_url('admin/prices_lookup/' . $vendor->id . '/' . $import_group_id . '/' . base64_encode($brand->sup_brand))?>" class="<?=($brand->sup_brand == $thisbrand)?"selected":""?>"><?=$brand->sup_brand?></a>
	<? endforeach ?>
</p>-->

<ul class="inline brands-filter">
	<li class="<?=(!$thisbrand)?"selected":""?>"><a href="<?=site_url('admin/prices_lookup/' . $vendor->id . '/' . $import_group_id)?>">Все</a></li>
	<? foreach($brands->result() as $brand): ?>
		<li class="<?=($brand->sup_brand == $thisbrand)?"selected":""?>"><a href="<?=site_url('admin/prices_lookup/' . $vendor->id . '/' . $import_group_id . '/' . base64_encode($brand->sup_brand))?>"><?=$brand->sup_brand?></a></li>
	<? endforeach ?>
</ul>

<table class="table table-striped table-condensed">
	<thead>
	
		<tr class="head">
			<th>Артикул</th>
			<th>Бренд</th>
			<th>Описание</th>
			<th>Кол-во</th>
			<th>Цена</th>
		</tr>
	</thead>
	<tbody>
	<? foreach ($prices->result() as $pr): ?>
	
	<tr>
		
		<td><?=$pr->art_number?></td>
		<td><?=$pr->sup_brand?></td>
		<td><?=$pr->description?></td>
		<td><?=$pr->qty?></td>
		<td><?=$pr->price ?></td>
		
	</tr>
	
	<? endforeach ?>
	</tbody>
</table>

<div class="pagination">
	<ul>
		<?=$pagination ?>
	</ul>
</div>