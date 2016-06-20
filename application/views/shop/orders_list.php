	<div class="page-header">
		<h1>Ваши заказы <small><?=$email?></small></h1>
	</div>

	<div class="alert alert-info alert-fadeout">
		<strong>Ваша история заказов.</strong> Для просмотра более подробной информации о заказе щелкните <a class="btn btn-primary btn-mini disabled" title="Подробнее"><i class="icon-align-justify icon-white"></i></a>
	</div>
	
<table id="orders_list" class="table">
	<thead>
		<tr>
			<th></th>
			<th>Статус</th>
			<th>№</th>
			<th>Дата</th>
			<th>Наименование</th>
			<th>Кол.</th>
			<th>Цена</th>
			<th>&sum; со скидкой</th>
			
		</tr>
	</thead>
	<tbody>
	<? foreach ($order as $item): ?>
		<tr class="<?=$order_statuses[$item->status]['class']?>">
			<td><a href="<?=site_url('order/' . $item->vericode)?>" class="btn btn-primary btn-mini" title="Побробнее"><i class="icon-align-justify icon-white"></i></a></td>
			<td><abbr title="<?=$order_statuses[$item->status]['comment']?>"><?=$order_statuses[$item->status]['name']?></abbr></td>
			<td><strong>№<?=$item->order_human_id?></strong></td>
			<td><abbr title="<?=date_tz('d.m.Y H:i', $item->order_date) ?>"><?=date_tz('d.m', $item->order_date) ?></abbr></td>
			<td><strong><?=$item->art_number?></strong> <?=$item->sup_brand?> <?=$item->description?></td>
			<td><?=$item->qty?></td>
			<td><?=$item->price?></td>
			<td>
				<?=price_format($item->item_subtotal, $item->discount)?>
				
				<!--<strong><? if ($item->discount > 0) echo number_format($item->price*$item->qty*((100-$item->discount)/100), 2);
				else echo $item->price*$item->qty; ?></strong>
				(-<?=$item->discount?>%)-->
			</td>
			
		</tr>
	<?endforeach?>
	
	</tbody>
</table>