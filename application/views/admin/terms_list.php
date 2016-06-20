<div class="page-header">
	<? if (isset($post_typedata)): ?>
	<div class="pull-right"><a href="<?=site_url('admin/posts/' . $typedata->posttypeid ) ?>" class="btn"><i class="icon-list-alt"></i> <?=$post_typedata->title ?></a></div>
	<? endif ?>
	<h1><?=$typedata->title?></h1>
</div>

<? if ($completed_action == 'added'): ?>
	<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Категория добавлена.</div>
<? elseif ($completed_action == 'deleted'): ?>
	<div class="alert alert-error"><i class="icon-trash"></i> <strong>Успешно.</strong> Категория удалена.</div>
<? elseif ($num_terms > 0): ?>
	<div class="alert alert-info alert-block alert-fadeout">
		<h4>Управление категориями</h4> 
		<p><strong>Название</strong> &mdash; Общедоступное название категории<br>
		<strong>Порядковый #</strong> &mdash; чем меньше значение, тем выше категория в списках (и здесь, и у пользователей)<br>
		<strong>Подкатегория</strong> &mdash; Означает, что категория является частью родительской категории</p>
	</div>
<? endif ?>

<? if ($num_terms == 0): ?>
	
	<div class="alert alert-info"><i class="icon-info-sign"></i> <strong>Не создано ни одной категории.</strong> Вы можете создать пару-тройку, щелкнув по кнопке ниже&hellip;</div>
	
<? else: ?>
	
<table class="table table-striped">
	<thead>
		<tr>
			<th>Название</th>
			<th>Порядковый #</th>
			<th>Подкатегория?</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<? foreach ($terms as $term): ?>
	
	<tr>
		<td><i class="icon-folder-open"></i> <?=$term->title?></td>
		<td># <?=$term->order ?></td>
		<td><? if ($term->parent_id > 0): ?><i class="icon-chevron-down" title="Категория является подкатегорией"></i><?endif?></td>
		<td class="table-row-actions">
			<a class="btn btn-small" title="Удалить категорию" href="<?=site_url('admin/term_delete/' . $typedata->id . '/' . $term->id ) ?>"><i class="icon-trash"></i></a>
			<a href="<?=site_url('admin/term_edit/' . $typedata->id . '/' . $term->id)?>" class="btn btn-primary btn-small"><i class="icon-pencil icon-white"></i> Редактировать</a>
		</td>
		
	</tr>
	
	<? endforeach ?>
	
	</tbody>
	
</table>

<? endif ?>

<div class="form-actions">
	
	<a href="<?=site_url('admin/term_add/' . $typedata->id)?>" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i> <?=$typedata->new_item?></a>
	
	<? if (isset($post_typedata)): ?>
	
		<a href="<?=site_url('admin/posts/' . $typedata->posttypeid ) ?>" class="btn"><i class="icon-list-alt"></i> <?=$post_typedata->title ?></a>
	
	<? endif ?>
</div>