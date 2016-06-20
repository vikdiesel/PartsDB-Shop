<div class="row-fluid">
	<div class="span7">

		<div class="well">
			
			<? if (empty($order->user_id)): ?>
				
				<? if (!$order->is_archived): ?>
					<a href="<?=site_url("admin/order_change_user/{$order->order_id}")?>" class="btn btn-primary"><i class="icon-edit icon-white"></i> Прикрепить к клиенту</a>
				<? else: ?>
					Заказ не прикреплен к клиенту
				<? endif ?>
			
			<? else: ?>
				
				<div class="row-fluid">
					<div class="span6">
						
						<h4><?=$order->userdata->name?></h4>
						
						<ul class="unstyled">
							<? if (!empty($order->email)): ?><li>E-mail: <?=$order->email?></li><? endif ?>
							<? if (!empty($order->userdata->phone)): ?><li>Тел: <?=$order->userdata->phone?></li><? endif ?>
							<? if (!empty($order->userdata->address)): ?><li>Адрес: <?=$order->userdata->address?></li><? endif ?>
						</ul>
						
						<p>Скидка клиента: <?=$order->user_default_discount?>%</p>
						
						<? if (!$order->is_archived): ?>
							<p>
								<div class="btn-group">
									<button class="btn btn-mini dropdown-toggle" data-toggle="dropdown" >
										<i class="fa fa-edit"></i> Изменить
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li><a href="<?=site_url("admin/user_add/{$order->order_id}/{$order->user_id}")?>"><i class="fa fa-edit"></i> Редактировать</a></li>
										<li><a href="<?=site_url("admin/order_change_user/{$order->order_id}")?>"><i class="fa fa-user"></i> Выбрать другого</a></li>
									</ul>
								</div>

							</p>
						<? endif ?>
						
					</div>
					<div class="span6">
						
					</div>
				</div>
			<? endif ?>
		</div>
	</div>
	<div class="span5">
	
		<? if (!empty($order->order_comment)):?>
			<h4>Комментарий</h4>
			<p><i class="icon-comment"></i> <?=$order->order_comment?></p>
		<? endif ?>
		
		<h4>Способ доставки</h4>

		<? if (!$order->is_archived): ?>
			<select name="d_mthd" class="input-medium order_change_d_mthd">
				<? foreach ($d_mthds as $did=>$dttl): ?>
					<option value="<?=$did?>" <?=($order->delivery_method == $did)?"selected":""?>><?=$dttl?></option>
				<? endforeach ?>
			</select>
		<? else: ?>
			<p><?=$order->dmthd_title?> (<?=$order->dmthd_price?> <?=$currency_symbol ?>)</p>
		<? endif ?>

		
	</div>
</div>

<hr>