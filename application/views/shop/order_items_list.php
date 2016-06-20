<table id="orders_list" class="table">
	<thead>
		<tr>
			<th>Статус</th>
			<th>Поставка</th>
			<th>Артикул</th>
			<th>Бренд</th>
			<th>Описание</th>
			<th>Кол.</th>
			<th>Цена</th>
			<th>&sum; со скидкой</th>
			
		</tr>
	</thead>
	<tbody>
	<? foreach ($order as $item): ?>
		<tr class="<?=$order_statuses[$item->status]['class']?>">
			<td><abbr title="<?=$order_statuses[$item->status]['comment']?>"><?=$order_statuses[$item->status]['name']?></abbr></td>
			<td><?=$item->delivery_days?> дн.</td>
			<td><strong><?=$item->art_number?></strong></td>
			<td><?=$item->sup_brand?></td>
			<td><?=$item->description?></td>
			<td><?=$item->qty?></td>
			<td><?=price_format($item->price);?></td>
			<td><?=price_format($item->item_subtotal, $item->discount)?></td>
		</tr>
	<?endforeach?>
	
	</tbody>
</table>

<div class="well"><strong>Постоянная ссылка</strong> на эту страницу: <a href="<?=site_url('order/' . $order[0]->vericode)?>"><?=site_url('order/' . $order[0]->vericode)?></a></div>