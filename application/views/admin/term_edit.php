<? if (isset($id)): ?>
	
	<div class="page-header">
		<div class="pull-right"><a href="<?=site_url('admin/terms/' . $typedata->id) ?>" class="btn"><i class="icon-align-justify"></i> Список категорий</a></div>
		<h1>Редактирование категории</h1>
	</div>
	
	<?=$errors?>
	<?=$success?>
	
<? else: ?>
	
	<div class="page-header">
		<div class="pull-right"><a href="<?=site_url('admin/terms/' . $typedata->id) ?>" class="btn"><i class="icon-align-justify"></i> Список категорий</a></div>
		<h1>Добавление категории</h1>
	</div>
	
	<?=$errors?>
	<?=$success?>
	
	<div class="alert alert-info alert-fadeout">Вы можете добавить неограниченное количество категорий с различными параметрами.</div>

	<? endif ?>


<?=form_open('', 'class="form-horizontal"') ?>
	
	<legend>Данные категории</legend>
	<div class="control-group">
		<label class="control-label" for="title">Название</label>
		<div class="controls">
			<input class="input-xlarge" id="title" name="title" size="30" type="text" value="<?=setval('title','',$form); ?>">
			<span class="help-block">Название категории. Будет доступно публично.</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="parent_id">Родительская</label>
		<div class="controls">
			<select name="parent_id" id="parent_id">
				<option value="0">--</option>
				
				<? foreach ($parent_terms as $term): ?>
					<option value="<?=$term->id ?>" <? if (isset ($form['parent_id']) and $form['parent_id'] == $term->id): ?> selected <?endif?>><?=$term->title ?></option>
				<? endforeach ?>
				
			</select>
			<span class="help-block"><strong>Необязательно.</strong> Можно вложить эту категорию в другую.</span>
		</div>
	</div>
	
	<div class="control-group">
	<label class="control-label" for="order">Порядковый номер</label>
		<div class="controls">
			<input class="input-mini" id="order" name="order" size="3" type="text" value="<?=setval('order', '0', $form); ?>">
			<span class="help-block"><strong>Только цифры</strong>. Порядковый номер для сортировки. <i>Можно оставить 0.</i></span>
		</div>
	</div>
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Сохранить</button>
		<a href="<?=site_url('admin/terms/' . $typedata->id)?>" class="btn">Назад</a>
	</div>
	
</form>