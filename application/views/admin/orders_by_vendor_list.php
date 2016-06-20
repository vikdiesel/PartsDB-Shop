<div class="page-header">
		<div class="pull-right">
			<div class="btn-group">
				<button class="btn dropdown-toggle" data-toggle="dropdown">
					<i class="icon-briefcase"></i> По поставщикам
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="<?=site_url('admin/orders')?>"><i class="icon-ok-circle"></i> По дате поступления</a></li>
					<li><a href="<?=site_url('admin/orders/archived')?>"><i class="icon-inbox"></i> Архив заказов</a></li>
					<li class="divider"></li>
					<li><a href="<?=site_url('admin/users')?>" ><i class="icon-user"></i> Клиенты</a></li>
				</ul>
			</div>
		</div>
		<h1>Заказы по поставщикам</h1>
</div>

<div class="alert alert-info alert-fadeout">Показаны позиции со статусом &laquo;В заказ поставщику&raquo;.</div>