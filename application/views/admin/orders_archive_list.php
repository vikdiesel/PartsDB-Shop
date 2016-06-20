<div class="page-header">
	<h1>Архив заказов</h1>
</div>

<div class="alert alert-info">Вы просматриваете заказы перемещенные в архив. Перейти к <a href="<?=site_url('admin/orders')?>">обычному просмотру</a>.</div>

<table id="orders_list" class="table table-striped">
	<thead>
		<tr>
			<th></th>
			<th></th>
			<th>Заказчик</th>
			<th>Заказ</th>
			<th>Сумма</th>
			<th>Статус</th>
		</tr>
	</thead>
	<tbody>
<? foreach ($orders as $order): ?>
	<tr>
		<td class="num"><a name="order-<?=$order->id?>"></a><strong>№<?=$order->id?></strong></td>
		<td class="date"><?=$order->date_f ?><br /><?=$order->time_f ?> </td>
		<td>
			<strong><?=$order->user->userdata['name'] ?></strong><br>
			<?=$order->user->userdata['phone'] ?><br>
			<a href="mailto:<?=$order->user->email ?>"><?=$order->user->email ?></a>
		</td>
		<td class="items">
			<ul class="unstyled">
			<? foreach ($order->order['items'] as $item): ?>
				<li><strong><?=$item->art_number ?> <?=$item->sup_brand ?></strong> <em><?=$item->description ?></em> (<?=$item->vendor_name?>, <?=$item->delivery_days?> дн.)<br /><?=$item->qty ?> шт. &times; <?=$item->price ?> руб. = <strong><?=$item->subtotal ?> руб.</strong></li>
			<? endforeach ?>
			
				<li><strong>Доставка</strong> <em><?=$order->order['delivery']->title ?></em><br /><?=$order->order['delivery']->price ?> руб.</p>
				
			</ul>

		</td>
		<td class="order_total">

				<strong><?=$order->order['total'] ?> руб. </strong>

		</td>
		<td class="order_status">
			<div class="btn-group">
				<a class="btn btn-small" href="#"><i class="icon-pencil"></i> <?=$order->status->name?></a>
				<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
				<ul class="dropdown-menu">
					<? foreach ($order_statuses as $stid=>$st): ?>
						<li><a href="<?=site_url('admin/status/'.$order->id.'/'.$stid)?>"><?=$st['name']?></a></li>
					<? endforeach ?>
				</ul>
			</div>
		</td>
	</tr>
	
	

<? endforeach ?>
</thead>
</table>