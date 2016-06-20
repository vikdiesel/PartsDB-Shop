<div class="page-header">
	<h1>Заказ №<?=$order_human_id?> <small>от <?=$order_date_r?></small></h1>
</div>

<div class="alert alert-info">
	<i class="icon-info-sign"></i> <strong>Прикрепить заказ к клиенту</strong>. Выберите клиента из перечня ниже, либо добавьте нового.
</div>

<!--<p><a href="" class="btn"><i class="icon-plus-sign"></i> Добавить клиента</a></p>-->

<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs nav-stacked">
		<li class="<? if ($userid == 0) echo "active" ?>"><a href="<?=site_url("admin/order_change_user/$orderid/0")?>"><i class="icon-asterisk"></i> Без привязки к клиенту</a></li>
		<? foreach ($users as $user): ?>
			
			<li class="<? if ($user->selected) echo "active" ?>"><a href="<?=site_url("admin/order_change_user/$orderid/$user->id")?>"><i class="icon-user"></i> <?=$user->userdata->name?> <span class="muted">(Скидка <?=$user->discount?>%)</span></a></li>
			
		<? endforeach ?>
		</ul>
		
	</div>
	<div class="span4">
<!--
		<ul class="nav nav-tabs nav-stacked">

			<li class="<? if ($userid == 0) echo "active" ?>"><a href="<?=site_url("admin/order_change_user/$orderid/0")?>"><i class="icon-asterisk"></i> Без привязки к клиенту</a></li>
			<li class=""><a href="<?=site_url("admin/user_add/$orderid")?>"><i class="icon-plus-sign"></i> Добавить нового клиента</a></li>
			
		</ul>-->
		
		<a href="<?=site_url("admin/user_add/$orderid")?>" class="btn"><i class="icon-plus-sign"></i> Добавить нового клиента</a>
		
	</div>
</div>

<div class="form-actions">
	<a href="<?=site_url("admin/order_data/$orderid")?>" class="btn">Вернуться к заказу</a>
</div>