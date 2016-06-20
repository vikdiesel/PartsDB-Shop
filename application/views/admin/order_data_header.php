<? if ($msg == 'user_updated'): ?>

	<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Клиент изменен.</div>

<? endif ?>

<div id="jb_orderedit_orderpage"></div>
<!-- this signals to cancel orderedit mode -->

<div class="page-header">
	<div class="pull-right">
		
		<? if (!$is_archived): ?>
			<a href="<?=site_url('admin/orders')?>" class="btn"><i class="icon-list"></i> Список заказов</a>
		<? else: ?>
			<a href="<?=site_url('admin/orders/archived')?>" class="btn"><i class="icon-inbox"></i> Архив заказов</a>
		<? endif ?>
		
		<a href="<?=site_url('admin/order_data/' . $order->order_id . '/print')?>" target="_blank" class="btn btn-primary" title="Печать заказа"><i class="icon-print icon-white"></i> Печать</a> 
	</div>
	<h1>Заказ №<?=$order->order_human_id?> <small>от <?=$order->order_date_r?> <? if ($order->is_archived): ?>[архив]<?endif?> </small></h1>
</div>