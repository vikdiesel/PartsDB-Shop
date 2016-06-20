<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Заказ №<?=$order_human_id?> от <?=$order_date_rus?> &mdash; <?=$sitetitle?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
		
		<link rel="stylesheet" type="text/css" media="all" href="<?=base_url()?>e/frontend.css" media="screen" />

		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->

		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<style type="text/css">
			div[class^='col-'] h3:first-child {margin-top:10px;}
			hr.signature-line {border-top-color:#000; width:85%; margin:30px 0 60px 0;}
			.disclaimer {font-size:10px;}
			.total_str {-text-transform:capitalize;}
		</style>
</head>
<body>
<div class="container">
	
	<div class="row">
		
		<div class="col-xs-12 hidden-print">
			<div class="well well-sm" style="margin-top:20px;">
				<a href="javascript:window.print()" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Печать</a>
			</div>
		</div>
		
		<!--<div class="col-xs-4">
			<div class="thumbnail">
				<img src="http://placehold.it/200x100">
			</div>
		</div>-->
		<div class="col-xs-12">
			<h3>Заказ №<?=$order_human_id?> от <?=$order_date_rus?></h3>
			<p><strong><?=$sitetitle?></strong> <?=$sitesubtitle?><br>
			Заказ принят: <?=date_tz('d.m.Y H:i', $order->order_date)?></p>
			<hr>
		</div>
		
	</div>
	<div class="row">
		<? if (!empty($order->user_vehicle_id)): ?>
			<div class="col-xs-6">
		<? else: ?>
			<div class="col-xs-12">
		<? endif ?>
			<? if (!empty($order->user_id)): ?>
				<div class="well">
					<h4><?=$order->userdata->name?></h4>
					<ul class="unstyled">
						<? if (!empty($order->email)): ?><li>E-mail: <?=$order->email?></li><? endif ?>
						<? if (!empty($order->userdata->phone)): ?><li>Тел: <?=$order->userdata->phone?></li><? endif ?>
						<? if (!empty($order->userdata->address)): ?><li>Адрес: <?=$order->userdata->address?></li><? endif ?>
						<? if (!empty($order->userdata->corp_inn)): ?><li>ИНН / КПП: <?=$order->userdata->corp_inn?></li><? endif ?>
						<? if (!empty($order->userdata->corp_ogrn)): ?><li>ОГРН / ОГРНИП: <?=$order->userdata->corp_inn?></li><? endif ?>
					</ul>
					<p>Скидка клиента: <?=$order->user_default_discount?>%</p>
				</div>
			<? endif ?>
		</div>
		<? if (!empty($order->user_vehicle_id)): ?>
			<div class="col-xs-6">
				<div class="well">				
					<h4><?=$order->vehicle_title ?> <!--<a href="<?=site_url("find/{$order->vehicle_brand_id}/{$order->vehicle_model_id}/{$order->vehicle_type_id}")?>" class="btn btn-mini" title="Страница авто в базе запчастей"><i class="icon-share"></i></a>--></h4>
					
					<ul class="unstyled">
						<li class="inline-edit-btns">Гос. номер: <?=$order->vehicle_doc_plateid ?></li>
						<li class="inline-edit-btns">Год выпуска: <?=$order->vehicle_mfg_year ?></li>
						<li class="inline-edit-btns">Пробег: <?=$order->vehicle_haulage ?></li>
						<? if (!empty($order->vehicle_vin)): ?><li class="inline-edit-btns">VIN: <?=$order->vehicle_vin ?></li><? endif ?>
						<? if (!empty($order->vehicle_doc_passid)): ?><li class="inline-edit-btns">Св-во: <?=$order->vehicle_doc_passid ?></li><? endif ?>
					</ul>
					
					<? if (!empty($order->vehicle_comment)): ?><p class="inline-edit-btns"><?=$order->vehicle_comment ?></p><? endif ?>
				</div>
			</div>
		<? endif ?>
	</div>
		
	<div class="row">
		<div class="col-xs-12">

		<? if (!empty($order_items)): ?>
			<table id="orders_list" class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th></th>
						<th>Склад</th>
						<th>Арт.</th>
						<th>Бренд</th>
						<th>Описание</th>
						<th>Кол.</th>
						<th>Цена</th>
						<th>Скидка (%)</th>
						<th>&sum; (-%)</th>
					</tr>
				</thead>
				<tbody>
					<? foreach ($order_items as $item): ?>
						<tr>
							<td><?=$order_statuses[$item->status]['name']?></td>
							<td><?=$item->vendor_name?></td>
							<td><?=$item->art_number?></td>
							<td><?=$item->sup_brand?></td>
							<td><?=$item->description?></td>
							<td><?=$item->qty?></td>
							<td><?=$item->price?></td>
							<td><?=$item->discount?></td>
							<td>
								<?=price_format($item->item_subtotal, $item->discount) ?>
							</td>
						</tr>
					<? endforeach ?>
				</tbody>
				<caption>
					<tr>
						<td colspan="8" class="order_total_key">
							ИТОГО ЗАПЧАСТИ:
							<br>ИТОГО ДОСТАВКА (<?=$order->dmthd_title ?>):
						</td>
						<td class="order_total_val">
							<?=price_format($order->order_items_total_f)?>
							<br><?=price_format($order->dmthd_price) ?>

						</td>
					</tr>
					
				</caption>
			</table>
		<? endif ?>
		</div>
	</div>


<div class="well order_table_group_block">
	<h4>Итого (с доставкой): <?=price_format($order->order_items_total_dlvr) ?><div class="total_str"><?=$order_total_dlvr_str ?></div></h4>
</div>


<div class="row">
	<div class="col-xs-6">
		<h4>Исполнитель</h4>
		<p>Заказ оформлен.<br>&nbsp;</p>
		<hr class="signature-line">
	</div>
	<div class="col-xs-6">
		
		<h4>Заказчик</h4>
		<p>С Правилами поставки запчастей и предоставления услуг ознакомлен. С первоначальной стоимостью согласен.</p>
		<hr class="signature-line">
	</div>
</div>
		
</div>
<script type="text/javascript">
	window.print();
</script>
</body>
</html>