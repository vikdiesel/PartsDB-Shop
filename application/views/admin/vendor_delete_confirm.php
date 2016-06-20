
<div class="page-header">
	<h1>Подтвердите удаление</h1>
</div>

	
	<p>Все связанные с данным объектом данные также будут удалены.<br>
	<strong>Действие безвозвратно.</strong></p>
	
	
	<div class="form-actions">
		<a href="<?=site_url('admin/delete_vendor/'.$vendor_id.'/' . $calling_method . '/confirmed')?>" class="btn btn-danger"><i class="icon-trash icon-white"></i> Подтвердить удаление</a>
		<a class="btn" href="<?=site_url('admin/' . $calling_method)?>">Назад</a>
	</div>
