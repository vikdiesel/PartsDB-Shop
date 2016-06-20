<div class="page-header">
	<? if (isset($is_archived)): ?>
		<div class="pull-right">
			<a href="<?=site_url('admin/orders')?>" class="btn btn-primary"><i class="icon-inbox icon-white"></i> Покинуть архив</a>
		</div>
		<h1>Архив заказов</h1>
	<? else: ?>
		<div class="pull-right">
			
			
			<div class="btn-group">
				<button class="btn dropdown-toggle" data-toggle="dropdown">
					<i class="icon-ok-circle"></i> По дате поступления
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="<?=site_url('admin/orders_by_vendor')?>"><i class="icon-briefcase"></i> По поставщикам</a></li>
					<li><a href="<?=site_url('admin/orders/archived')?>"><i class="icon-inbox"></i> Архив заказов</a></li>
					<li class="divider"></li>
					<li><a href="<?=site_url('admin/users')?>" ><i class="icon-user"></i> Клиенты</a></li>
				</ul>
			</div>
		</div>
		<h1>Ваши заказы</h1>
	<? endif ?>
</div>

<? if ($msg == 'updated'): ?>
	
	<div class="alert alert-success"><strong>Заказ обновлен</strong>. Пользователю выслано уведомление.</div>
	
<? elseif ($msg == 'updated_non_notified'): ?>
	
	<div class="alert alert-success"><strong>Заказ обновлен</strong>. Уведомление не отправлено.</div>
	
<? elseif ($msg == 'sent_to_archive'): ?>
	
	<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Заказ перенесен в архив. <a href="<?=site_url('admin/orders/archived')?>" class="btn btn-mini">Перейти в архив</a></div>

<? elseif ($msg == 'sent_out_of_archive'): ?>
	
	<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Заказ удален из архива. <a href="<?=site_url('admin/orders')?>" class="btn btn-mini">Перейти к списку заказов</a></div>
	
<? else: ?>

	<div class="alert alert-info alert-fadeout alert-default-message">Для изменения статуса заказа и редактирования щелкните <a class="btn btn-primary btn-mini disabled"><i class="icon-align-justify icon-white"></i></a></div>

<? endif ?>

<? if ($is_sample_orders): ?>
	
	<div class="alert alert-block">
		<h4>Заказы в этой таблице &mdash; тестовые</h4>
		
		<p>
		<? if ($is_non_sample_exist): ?>
			<strong>Точнее, &mdash; некоторые из них.</strong>
		<? endif ?>
		
		Они были созданы для примера в момент запуска веб-сайта.<br>Вы можете удалить их, удалив соответствующих клиентов с пометкой &laquo;Тестовый&raquo; (вместе с клиентами будут удалены и их заказы).</p>
		<p><a href="<?=site_url('admin/users')?>" class="btn btn-small"><i class="icon-user"></i> Управление клиентами</a></p>
		
	</div>
	
<? endif ?>

<!--
<div class="alert alert-info twof_filter_container orders-combo-list-status-change hiddenUtilityMessage"><b>Поменять статус выбранных на:</b> 

	<? foreach ($order_statuses as $stid=>$st): ?>
		
		<? 
			if ($item->status == $stid) $selected = 'selected';
			else $selected = '';
		?>
		
		<a href="#<?=$stid?>" class="<?=$selected?>" data-setstatus="<?=$stid?>"><?=$st['name']?></a>
	<? endforeach ?>

</div> -->

<!--<?=form_open('admin/orders_set_status', 'id="orders-combo-list-form"') ?>-->
<table id="orders_list" class="table table-striped table-condensed orders-combo-list">
	<thead>
		<tr>
			<th class="table-th-checkbox"><!--<label class="btn btn-mini magic-checkbox"><input type="checkbox" class="order-all-lines-checkbox"></label>--></th>
			<th>Дата</th>
			<th class="order_status_col">Статус</th>
			<th>№</th>
			<th class="order_vendorname_col">Склад</th>
			<th>Заказчик</th>
			<th class="order_itemdescr_col">Наименование</th>
			<th class="order_itemqty_col">Кол.</th>
			<th class="order_itemsubtotal_col">Сумма</th>
		</tr>
	</thead>
	<tbody>
	<? foreach ($orders as $order): ?>
		<tr class="<? if (!isset($is_archived)) echo $order_statuses[$order->status]['class']?> order_line order_line_<?=$order->order_human_id?> order_statuses_<?=($order->is_statuses_equal)?"equal":"different"?>" data-orderid="<?=$order->order_human_id?>" data-subtotal="<?=$order->item_subtotal?>">
			<td class="table-row-actions-left">
				<!--<label class="btn btn-mini magic-checkbox"><input type="checkbox" class="order-line-checkbox" name="data[<?=$order->order_line_id?>][status]" value=""></label>-->
				<!--<a class="btn btn-default btn-mini" data-toggle="button" href="#"><i class="icon-stop"></i></a>-->
				<a href="<?=site_url('admin/order_data/'.$order->order_id)?>" class="btn btn-primary btn-mini" title="Изменение статуса и редактирование"><i class="icon-align-justify icon-white"></i></a>
			</td>
				
			<td><abbr title="<?=date_tz('d.m.Y H:i', $order->order_date) ?>"><?=date_tz('d.m', $order->order_date) ?></abbr><? if (!empty($order->order_comment)): ?><br><i class="icon-comment" title="<?=$order->order_comment?>"></i><? endif ?></td>
			<td class="order_status_col"><abbr title="Заказ обновлен: <?=date_tz('d.m.Y H:i', $order->status_change_date) ?>"><?=$order_statuses[$order->status]['name'] ?></abbr><br><?=$order->dmthd_title?> (<?=$order->dmthd_price?> <?=$currency_symbol?>)</td>
			<td><strong>№<?=$order->order_human_id?></strong></td>
			<td class="order_vendorname_col"><abbr title="Поставка <?=$order->delivery_days?> дн."><?=$order->vendor_name?></a></td>
			<td><abbr title="Тел: <?=$order->userdata->phone?> / Адрес: <?=$order->userdata->address?> / E-mail: <?=$order->email?>"><?=$order->userdata->name?></abbr></td>
			<td class="order_itemdescr_col"><strong><?=$order->art_number?></strong> <?=$order->sup_brand?> <?=$order->description?></td>
			<td class="order_itemqty_col"><?=$order->qty?></td>
			<td class="order_itemsubtotal_col">
				<!--<strong><? if ($order->discount > 0) echo number_format($order->price*$order->qty*((100-$order->discount)/100), 2);
				else echo $order->price*$order->qty; ?></strong>
				(-<?=$order->discount?>%) -->
				
				<?=price_format($order->price*$order->qty*((100-$order->discount)/100), $order->discount) ?>
			</td>
		</tr>
	<?endforeach?>
	
	</tbody>
</table>
<!--</form>-->

<div class="pagination">
	<ul>

		<?=$pagination ?>
		
	</ul>
</div>