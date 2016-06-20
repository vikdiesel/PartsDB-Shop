<div class="page-header">
	<div class="pull-right">
		<a href="<?=site_url('user/orders')?>" class="btn"><i class="icon-list-alt"></i> Ваши заказы</a>
	</div>
	<h1>Заказ №<?=$order[0]->order_human_id?> <small>от <?=date_tz('d.m.Y H:i', $order[0]->order_date)?></small></h1>
</div>

<div class="alert alert-success"><strong>Доставка:</strong> <?=$order[0]->dmthd_title?> (<?=$order[0]->dmthd_price?> <?=$currency_symbol?>)</div>


