<div class="page-header">
	<h1><?=$title ?> <small><?=lang('jb_types_ttl')?></small></h1>
</div>

<?=$breadcrumbs ?>

<div class="jb_addcar_hide">
	<?=sprintf(lang('jb_types_txttop'), "$title") ?>
</div>

<div class="table-responsive">
	<table class="table table-striped">

		<thead>
		<tr>
			<th><? if ($vehicle_group == 'commercial'): ?><?=lang('jb_types_eng_comm')?><? else: ?><?=lang('jb_types_eng')?><? endif?></th>
			<th><?=lang('jb_types_pwr')?></th>
			<th><?=lang('jb_types_type')?></th>
			<th><?=lang('jb_types_vol')?></th>
			<th><?=lang('jb_types_years')?></th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		
	<? foreach ($types as $type): ?>

		<tr>
			<td>
				<abbr title="<?=$title ?> <?=$type->body ?> <?=$type->engine ?>"><?=$type->engine ?> <?=$type->body ?></abbr>
				<? if (!empty($type->axle)): ?> / <?=$type->axle ?><? endif ?>
				<? if (!empty($type->max_weight)): ?> / <?=$type->max_weight ?><?=lang('jb_types_tonnes')?><? endif ?>
			</td>
			<td><span class="label label-default"><?=$type->hp ?><?=lang('jb_types_hp')?> / <?=$type->kw ?><?=lang('jb_types_kw')?></span></td>
			<td><abbr title="<?=$type->engine_txt ?>"><?=$type->petrol ?></abbr></td>
			<td><?=$type->ccm ?> <?=lang('jb_types_ccm')?></td>
			<td>
				<abbr title="<?=$type->start_year ?>/<?=$type->start_month ?>"><?=$type->start_year ?></abbr> -
				<? if (empty($type->end_year)): ?>
					<abbr title="Модель все еще выпускается">наст. время</abbr>
				<? else: ?>
					<abbr title="<?=$type->end_year ?>/<?=$type->end_month ?>"><?=$type->end_year ?></abbr>
				<? endif ?>
			</td>
			<td class="table-buttons table-row-actions">
				<a href="<?=site_url( "find/$brand_id/$model_id/{$type->id}" )?>" class="btn btn-primary btn-small btn-sm jb_addcar_hide" title="<?=$title ?> / <?=$type->body ?> / <?=$type->engine ?> - <?=lang('jb_types_btnlbl')?>"><i class="fa fa-cog"></i> <?=lang('jb_types_btntxt')?></a>
			</td>
		</tr>
		
	<? endforeach ?>
	</tbody>
	</table>
</div>