<table border="1">

	<tr>
		<th>Номер</th>
		<th>Дата</th>
		<th>Артикул</th>
		<th>Бренд</th>
		<th>Наименование</th>
		<th>Цена</th>
		<th>Кол-во</th>
		<th>Подитог</th>
		<th>Скидка</th>
		<th>Статус</th>
		<th>Поставка (дн)</th>
		<th>Способ доставки</th>
		<th>Цена доставки</th>
		<th>Поставщик</th>
		<th>E-mail заказчика</th>
		<th>Имя заказчика</th>
		<th>Телефон заказчика</th>
		<th>Адрес заказчика</th>
	</tr>
	
	<? foreach ($os as $o): ?>
	
	<tr>	
		<td><?=$o->order_human_id?></td>
		<td><?=date("d.m.Y H:i", $o->order_date) ?></td>
		<td><?=$o->art_number ?></td>
		<td><?=$o->sup_brand ?></td>
		<td><?=htmlspecialchars(str_replace("&shy;","",$o->description)) ?></td>
		<td><?=$o->price ?></td>
		<td><?=$o->qty ?></td>
		<td><?=$o->item_subtotal ?></td>
		<td><?=$o->discount ?></td>
		<td><?=$sts[$o->status]['name'] ?></td>
		<td><?=$o->delivery_days ?></td>
		<td><?=$o->delivery_method ?></td>
		<td><?=$o->dmthd_price ?></td>
		<td><?=$o->vendor_name ?></td>
		<td><?=$o->email ?></td>
		<td><?=$o->userdata->name ?></td>
		<td><?=$o->userdata->phone ?></td>
		<td><?=$o->userdata->address ?></td>
	</tr>	
	<? endforeach ?>
	
</table>