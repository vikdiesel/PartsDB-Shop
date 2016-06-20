<? if (isset($id)): ?>
	
	<div class="page-header">
		<div class="pull-right"><a href="<?=site_url('admin/posts/' . $typedata->id) ?>" class="btn"><i class="icon-align-justify"></i> <?=$typedata->title ?></a></div>
		<h1><?=$typedata->edit_item ?></h1>
	</div>
	
	<?=$success?>
	<?=$errors?>
	
<? else: ?>
	
	<div class="page-header">
		<div class="pull-right"><a href="<?=site_url('admin/posts/' . $typedata->id) ?>" class="btn"><i class="icon-align-justify"></i> <?=$typedata->title ?></a></div>
		<h1><?=$typedata->new_item ?></h1>
	</div>
	
	<?=$errors?>
	<?=$success?>

	<? endif ?>
	
<? if (!$typedata->thumbnail): ?>
<?=form_open('', 'class="form-inline"') ?>
<? else: ?>
<?=form_open_multipart('', 'class="form-inline"') ?>
<? endif ?>
	
	<? if (!isset($typedata->no_editor)): ?>
	<div class="control-group">
		<div class="controls">
			<input class="input-block-level title-field" id="title" name="title" size="30" type="text" value="<?=setval('title','',$form); ?>" placeholder="Название">
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<textarea id="text" name="text" class="input-block-level" rows="20"  placeholder="Текст"><?=setval('text','',$form); ?></textarea>
		</div>
	</div>
	
	<? endif ?>
	
	<div class="form-horizontal">
		<? if (!isset($typedata->no_editor)): ?>
		<legend>Дополнительно</legend>
		<? else: ?>
			<div class="control-group">
				<label class="control-label" for="title"><?=(isset($typedata->title_field_name))?$typedata->title_field_name:"Название"?></label>
				<div class="controls">
					<input class="input-large" id="title" name="title" type="text" value="<?=setval('title', '', $form); ?>" placeholder="<?=(isset($typedata->title_field_name))?$typedata->title_field_name:"Название"?>">
					<span class="help-block"><?=(isset($typedata->title_field_desc))?$typedata->title_field_desc:"<strong>Обязательно</strong>. Укажите здесь человекопонятное название."?></span>
				</div>
			</div>
		<? endif?>
		
		<? if ($typedata->meta and !isset($typedata->meta['options'])): ?>
			<div class="control-group">
				<label class="control-label" for="meta"><?=$typedata->meta[0]?></label>
				<div class="controls">
					<input class="<?=(isset($typedata->meta['field-css-class']))?$typedata->meta['field-css-class']:'input-medium'?>" id="meta" name="meta" type="text" placeholder="<?=$typedata->meta['placeholder']?>" value="<?=setval('meta', '', $form); ?>">
					<span class="help-block"><?=$typedata->meta[1]?></span>
				</div>
			</div>
		<? elseif ($typedata->meta and isset($typedata->meta['options'])): ?>
			<div class="control-group">
				<label class="control-label" for="meta"><?=$typedata->meta[0]?></label>
				
				<? $x = 0; ?>
				<? foreach ($typedata->meta['options'] as $value=>$label): ?>
					<div class="controls">
						<label class="radio">
						  <input type="radio" name="meta" value="<?=$value?>" <?=setradio((string) $value, (string) $form['meta'], 'meta', $x) ?>>
						  <?=$label?>
						</label>
					</div>
					<? $x++; ?>
				<? endforeach ?>	
				
				<div class="controls">
					<span class="help-block"><?=$typedata->meta[1]?></span>
				</div>
			</div>
		<? endif ?>
		
		<? if ($typedata->meta2 and !isset($typedata->meta2['options'])): ?>
			<div class="control-group">
				<label class="control-label" for="meta2"><?=$typedata->meta2[0]?></label>
				<div class="controls">
					<input class="<?=(isset($typedata->meta2['field-css-class']))?$typedata->meta2['field-css-class']:'input-medium'?>" id="meta2" name="meta2" type="text" placeholder="<?=$typedata->meta2['placeholder']?>" value="<?=setval('meta2', '', $form); ?>">
					<span class="help-block"><?=$typedata->meta2[1]?></span>
				</div>
			</div>
		<? elseif ($typedata->meta2 and isset($typedata->meta2['options'])): ?>
			<div class="control-group">
				<label class="control-label" for="meta2"><?=$typedata->meta2[0]?></label>
				
				<? $x = 0; ?>
				<? foreach ($typedata->meta2['options'] as $value=>$label): ?>
					<div class="controls">
						<label class="radio">
						  <input type="radio" name="meta2" value="<?=$value?>" <?=setradio((string) $value, (string) $form['meta2'], 'meta2', $x) ?>>
						  <?=$label?>
						</label>
					</div>
					<? $x++; ?>
				<? endforeach ?>	
				
				<div class="controls">
					<span class="help-block"><?=$typedata->meta2[1]?></span>
				</div>
			</div>
		<? endif ?>
		
		<? if ($typedata->termtypeid): ?>
			<div class="control-group">
				<label class="control-label" for="term_id"><?=$term_typedata->post_formlabel?></label>
				<div class="controls">
					<select name="term_id" id="term_id">
						<option value="0">--</option>
						
						<? foreach ($terms as $term): ?>
							<option value="<?=$term->id ?>" <?=setselect($term->id, $form['term_id'], 'term_id')?>><?=$term->title ?></option>
						<? endforeach ?>
						
					</select>
					<span class="help-block"><?=$term_typedata->post_formcomment?>
				</div>
			</div>
		
		<? endif ?>
		
		<? if ($typedata->thumbnail): ?>
			<div class="control-group">
				<label class="control-label" for="thumbnail"><?=$typedata->thumbnail[0] ?></label>
				<div class="controls">
					<input type="file" name="thumbnail" id="thumbnail">
					<span class="help-block"><?=$typedata->thumbnail[1] ?></span>
				</div>
			</div>
		
		<? endif ?>
		
		<div class="control-group">
			<label class="control-label" for="menu_order">Порядковый номер</label>
			<div class="controls">
				<input class="input-mini" id="menu_order" name="menu_order" type="number" min="-99999" max="99999" value="<?=setval('menu_order', '0', $form); ?>">
				<span class="help-block"><strong>Только цифры</strong>. Порядковый номер для сортировки. <i>Можно оставить 0.</i></span>
			</div>
		</div>
		
		<? if (isset($editor_advice)): ?>
			
			<?=$editor_advice?>
			
		<? endif ?>
		
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Сохранить</button>
			<a href="<?=site_url('admin/posts/' . $typedata->id)?>" class="btn">Назад</a>
		</div>
		
	</div>
	
</form>