<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/options') ?>" class="btn"><i class="icon-wrench"></i> Все настройки</a></div>
	<h1>Способы доставки</h1>
</div>

<? if ($action_done == 'added'): ?>

	<div class="alert-message success"><p>Способ доставки <strong>добавлен</strong>.</p></div>

<? elseif ($action_done == 'deleted'): ?>

	<div class="alert-message info"><p>Способ доставки <strong>удален</strong>.</p></div>

<? endif ?>

<table class="table table-striped">
	<thead>
	<tr class="head">
		<th>Наименование способа доставки</th>
		<th>Цена</th>
		<th>Порядковый №</th>
		
		<th></th>
	</tr>
	<thead>
	<tbody>
	
	<? foreach ($d_mthds->result() as $dm): ?>
	
	<tr>
		
		<td><?=$dm->title?></td>
		<td><?=$dm->price?>&nbsp;<?=$currency_symbol ?></td>
		<td><?=$dm->order?></td>
		<td class="table-row-actions"><a title="Удалить" href="<?=site_url('admin/options_delivery_delete/' . $dm->id ) ?>" class="btn btn-small"><i class="icon-trash"></i></a></td>
		
	</tr>
	
	<? endforeach ?>
	
	</tbody>
	
</table>

<div class="form-actions"><a href="<?=site_url('admin/options_delivery_add')?>" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i> Создать новый способ доставки</a></div>