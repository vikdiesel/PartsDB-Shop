
<? if (!empty($autos)): ?>
	
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
			<tr>
				<th><?=lang('jb_art_info_compat_model') ?></th>
				<th><?=lang('jb_art_info_compat_engine') ?></th>
				<th><?=lang('jb_art_info_compat_pwr') ?></th>
				<th><?=lang('jb_art_info_compat_years') ?></th>
			</tr>
			</thead>
			<tbody>
		<? foreach ($autos as $auto): ?>
			<tr>
				<td><?=$auto->brand ?> <?=$auto->name ?><br><span class="label label-default"><?=$auto->body ?></span></td>
				<td><?=$auto->ccm ?><?=lang('jb_ccm') ?><br><?=$auto->engine ?> / <?=$auto->engine_txt ?></td>
				<td><span class="label label-success"><?=$auto->hp ?><?=lang('jb_hp') ?> / <?=$auto->kw ?><?=lang('jb_kw') ?></span></td>
				<td><abbr title="<?=$auto->start_year ?>/<?=$auto->start_month ?>"><?=$auto->start_year ?></abbr> - <abbr title="<?=$auto->end_year ?>/<?=$auto->end_month ?>"><?=$auto->end_year ?></abbr></td>
			</tr>
		<? endforeach ?>
		</table>
	</div>
<? else: ?>
	
	<div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> Dataset is empty</div>

<? endif ?>