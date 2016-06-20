
<div class="page-header">
	<h1>Заказ №<?=$human_id?> обновлен</h1>
</div>

<div class="alert alert-info alert-block">
	<h4>Новые параметры заказа сохранены</h4>
	<p>Вы можете отправить пользователю уведомление об изменении статуса его заказа.</p>
	
	<p><strong>Старайтесь отсылать только существенные уведомления и не слишком часто</strong>, &mdash; частая отправка сообщений может вызвать блокировку ваших писем спам-фильтрами.</p>
</div>
	
	
	<div class="form-actions">
		<a class="btn btn-primary" href="<?=site_url('admin/orders/updated_non_notified')?>"><i class="icon-ok-sign icon-white"></i> К списку заказов</a>
		
		<a href="<?=site_url('admin/order_update/'.$id.'/msg')?>" class="btn"><i class="icon-envelope"></i> Уведомить покупателя</a>
		
		<span class="help-inline">
			<a class="" href="<?=site_url("admin/order_data/$id")?>">Вернуться к заказу №<?=$human_id?></a>
		</span>
	</div>
