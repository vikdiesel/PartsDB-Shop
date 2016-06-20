<div class="page-header">
	<? if (isset($term_typedata)): ?>
	<div class="pull-right"><a href="<?=site_url('admin/terms/' . $typedata->termtypeid ) ?>" class="btn"><i class="icon-folder-open"></i> <?=$term_typedata->title ?></a></div>
	<? endif ?>
	<h1><?=$typedata->title?></h1>
</div>

<? if ($completed_action == 'added'): ?>
	<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Запись добавлена.</div>
<? elseif ($completed_action == 'deleted'): ?>
	<div class="alert alert-error"><i class="icon-trash"></i> <strong>Успешно.</strong> Запись удалена.</div>
<? elseif ($num_posts > 0): ?>
	<div class="alert alert-info alert-block alert-fadeout">
		<p><strong>Название</strong> &mdash; Человекопонятное название элемента<br>
		<strong>Порядковый #</strong> &mdash; чем меньше значение, тем выше элемент в списках (и здесь, и у пользователей)<br>
		
	</div>
<? endif ?>

<? if ($num_posts == 0): ?>
	
	<div class="alert alert-info"><i class="icon-info-sign"></i> <strong>Не создано ни одной записи.</strong> Вы можете создать пару-тройку, щелкнув по кнопке ниже&hellip;</div>
	
<? else: ?>
	
<table class="table table-striped">
	<thead>
		<tr>
			<th>Название</th>
			<th>Порядковый #</th>
			<? if ($typedata->termtypeid): ?>
			<th>Категория</th>
			<? endif ?>
			<? if ($typedata->meta): ?>
			<th><?=$typedata->meta[0]?></th>
			<? endif ?>
			<? if ($typedata->meta2): ?>
			<th><?=$typedata->meta2[0]?></th>
			<? endif ?>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<? foreach ($posts as $post): ?>
	
	<tr>
		<td><i class="icon-list-alt"></i> <?=$post->title_prepped?></td>
		<td># <?=$post->menu_order ?></td>
		<? if ($typedata->termtypeid): ?>
		<td><?=$post->post_term_title?></td>
		<? endif ?>
		<? if ($typedata->meta): ?>
		<td><?=$post->meta_prepped?></td>
		<? endif ?>
		<? if ($typedata->meta2): ?>
		<td><?=$post->meta2_prepped?></td>
		<? endif ?>
		<td class="table-row-actions">
			<? if ($post->allow_delete):?>
				<a class="btn btn-small" title="Удалить" href="<?=site_url('admin/post_delete/' . $typedata->id . '/' . $post->id ) ?>"><i class="icon-trash"></i></a>
			<? else: ?>
				<button class="btn btn-small" title="Удаление запрещено" disabled><i class="icon-trash"></i></button>
			<? endif ?>
			<a href="<?=site_url('admin/post_edit/' . $typedata->id . '/' . $post->id)?>" class="btn btn-primary btn-small"><i class="icon-pencil icon-white"></i> Редактировать</a>
		</td>
		
	</tr>
	
	<? endforeach ?>
	
	</tbody>
	
</table>

<? endif ?>

<div class="form-actions">
	<a href="<?=site_url('admin/post_add/' . $typedata->id)?>" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i> <?=$typedata->new_item ?></a>
	
	<? if (isset($term_typedata)): ?>
		<a href="<?=site_url('admin/terms/' . $typedata->termtypeid ) ?>" class="btn"><i class="icon-folder-open"></i> <?=$term_typedata->title ?></a>
	<? endif ?>
</div>

<? if (isset($posts_list_advice)): ?>

	<?=$posts_list_advice?>
	
<? endif ?>