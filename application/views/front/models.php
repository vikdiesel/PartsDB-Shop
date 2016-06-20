
<div class="page-header">

	<h1><?=$brand_name ?> <small><?=lang('jb_models_ttl')?></small></h1>

</div>

<?=$breadcrumbs ?>

<?=$yearfilter ?>
	
<div class="table-responsive">
	<table class="table table-striped">

		<thead>
		<tr>
			<th><?=lang('jb_models_model')?></th>
			<th><?=lang('jb_models_years')?></th>
		</tr>
		</thead>
		<tbody>
	<? foreach ($models as $model): ?>

		<tr class="model_line <?=jb_year_classes('model_year_', $model->start_year, $model->end_year)?>">
			<td>
				<a href="<?=site_url( "find/$this_id/{$model->id}" )?>" title="<?=$brand_name ?> <?=$model->name ?> (<?=$model->start_year ?>-<?=$model->end_year ?>) - <?=lang('jb_models_linklabel')?>"><?=$brand_name?> <?=$model->name ?></a></td>
			<td>
				<abbr title="<?=$model->start_year ?>/<?=$model->start_month ?>"><?=$model->start_year ?></abbr> -
				<? if (empty($model->end_year)): ?>
					<abbr title="Модель все еще выпускается">наст. время</abbr>
				<? else: ?>
					<abbr title="<?=$model->end_year ?>/<?=$model->end_month ?>"><?=$model->end_year ?></abbr>
				<? endif ?>
			</td>
		</tr>
		
	<? endforeach ?>
		</tbody>
	</table>
</div>
