<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/options') ?>" class="btn"><i class="icon-wrench"></i> Все настройки</a></div>
	<h2>Бренды на главной</h2>
</div>

<? if ($success): ?>
	<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Данные сохранены</div>
<? else: ?>
	<div class="alert alert-info">Перечень автопроизводителей, которые присутствуют на главной вашего сайта</div>
<? endif ?>



<?=form_open('admin/mfg_update', array('id'=>'mfgs_list', 'class'=>'form-inline labels-inline checkboxes-group')) ?>
	<label><input type="checkbox" id="all_brands_trigger" title="Выделить/Отменить все"> Выделить все</label>
	<? foreach ($brands as $brand): ?>
		<label><? form__checkbox('mfg['.$brand->id.']', '1', $form) ?><?=$brand->name ?></label>
	<? endforeach ?>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Сохранить">&nbsp;<a href="<?=site_url('admin/options')?>" class="btn">Назад</a>
	</div>

</form>
