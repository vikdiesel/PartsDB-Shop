<table id="orders_list" class="table table-striped">
	<thead>
		<tr>
			<th>Склад</th>
			<th>Арт.</th>
			<th>Бренд</th>
			<th>Описание</th>
			<th>Кол.</th>
			<th>Цена</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<? foreach ($prices as $item): ?>
		<tr>
			<td><?=$item->vendor_name ?></td>
			<td><?=$item->art_number ?></td>
			<td><?=$item->sup_brand ?></td>
			<td><?=$item->description ?></td>
			<td><?=$item->qty ?></td>
			<td><?=$item->price ?></td>
			<td class="table-row-actions"><a href="<?=site_url("admin/order_add_item/{$orderid}/{$item->id}")?>" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i> Добавить</a></td>
		</tr>
	<? endforeach ?>
	</tbody>
</table>