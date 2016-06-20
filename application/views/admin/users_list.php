<div class="page-header">
	<div class="pull-right">
		<a href="<?=site_url('admin/orders')?>" class="btn btn-primary"><i class="icon-list icon-white"></i> Список заказов</a>
	</div>
	<h1>Клиенты</h1>
</div>

<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#users-list" data-toggle="tab"><i class="icon-list"></i> Список клиентов</a></li>
		<li class=""><a href="#export" data-toggle="tab"><i class="icon-share"></i> Экспорт</a></li>
		<li class=""><a href="#import-form" data-toggle="tab"><i class="icon-download-alt"></i> Импорт</a></li>
	</ul>
	
	<div class="tab-content">
		<div class="tab-pane active" id="users-list">

			<? if ($result == 'updated'): ?>
				
				<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Данные обновлены.</strong> Существующие заказы не затронуты.</div>
				
			<? elseif ($result == 'added'): ?>
				
				<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Клиент добавлен.</strong></div>
				
			<? elseif ($result == 'client-updated'): ?>
				
				<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Клиент обновлен.</strong></div>

			<? else: ?>

				<div class="alert alert-info alert-fadeout">
					<strong>Перечень ваших клиентов.</strong> Здесь можно задать скидки для каждого конкретного клиента - указанная 
					скидка будет применена ко всем последующим заказам данного клиента. Уже поступившие заказы затронуты не будут.
				</div>
				
			<? endif ?>

			<?=form_open('admin/users_update', array('id'=>'users_list', 'class'=>'table_form js-form')) ?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Имя/E-mail</th>
						<th>Телефон</th>
						<th>Адрес</th>
						<th>Скидка (%)</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				
				<? foreach ($users as $user): ?>
				<tr>
					<td>
						<? if ($user->is_sample): ?><span class="label" title="Учетная запись клиента создана для примера в момент регистрации. Можно удалить.">Тестовый</span><? endif ?>
						<?=$user->userdata->name?><br><?=mailto($user->email)?>
					</td>
					<td><?=$user->userdata->phone?></td>
					<td><?=$user->userdata->address?></td>
					<td><input type="number" name="user_discount[<?=$user->id ?>]" maxlength="2" class="input-mini" step="1" min="0" max="100" value="<?=set_value('user_discount['.$user->id.']', $user->discount)?>"></td>
					<td class="table-row-actions">
						<div class="btn-group">
							<a title="Изменить" href="<?=site_url('admin/user_add/0/' . $user->id)?>" class="btn btn-small"><i class="fa fa-edit"></i> Изменить</a>
							<button class="btn btn-small dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu pull-right">
								<li><a title="Изменить" href="<?=site_url('admin/user_add/0/' . $user->id)?>"><i class="fa fa-edit"></i> Изменить</a></li>
								<li><a title="Удалить" href="<?=site_url('admin/user_delete/' . $user->id)?>"><i class="fa fa-trash"></i> Удалить</a></li>
							</ul>
						</div>
					</td>
				</tr>
				
				<? endforeach ?>
				</tbody>
			</table>

				<div class="form-actions">

				<button type="submit" class="btn btn-primary"><i class="icon-ok-sign icon-white"></i> Сохранить</button> <a href="<?=site_url('admin/user_add')?>" class="btn"><i class="icon-plus-sign"></i> Добавить клиента</a>
				
				</div>
					
			</form>
		</div>
		
		<div class="tab-pane" id="export">
			
			<div class="tabbable tabs-left">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#export-csv" data-toggle="tab"><i class="icon-file"></i> Экспорт базы клиентов</a></li>
					<li class=""><a href="#export-emails" data-toggle="tab"><i class="icon-share"></i> Экспорт адресов e-mail</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="export-csv">
						<div class="well">
							<a href="<?=site_url('admin/users_export') ?>" class="btn"><i class="icon-file"></i> Скачать файл</a> <span class="help-inline">Формат CSV</span>
						</div>
					</div>
					<div class="tab-pane" id="export-emails">
						<div class="alert alert-info">
							<i class="icon-info-sign"></i> Здесь можно <b>скопировать список адресов e-mail</b> ваших клиентов для использования, например, в сервисе рассылок Unisender.
						</div>
						
						<textarea class="input-block-level" rows="30"><? 
							foreach ($users as $user)
							{
								if (isset($user->email) and $user->email != '')
								{
									echo $user->email . "\n"; 
								}
							}
						?></textarea>
					</div>
				</div>
			</div>
		</div>
		
		<div class="tab-pane" id="import-form">
			<?=$import_form ?>
		</div>
		
	</div>
</div>