<? if (count($results) > 0): ?>

	<div class="alert alert-info sliding-alert"><i class="icon-ok-circle"></i> <strong><?=lang('jb_search_found') ?></strong>. 
		<? if (isset($limit_to) and $limit_to == 'originals'): ?>
			<?=sprintf(lang('jb_search_g'), count($results)) ?>
		<? else: ?>
			<?=sprintf(lang('jb_search_n'), count($results)) ?>
		<? endif ?>
	</div>
	
	<div class="table-responsive">
		<table class="table table-striped table-search-results">
			<thead>
			<tr>
				<th><?=lang('jb_search_tbl_partid') ?></th>
				<th><?=lang('jb_search_tbl_brand') ?></th>
				<th class="hidden-xs hidden-sm"></th>
				<th><?=lang('jb_search_tbl_description') ?></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			
		<? foreach ($results as $r): ?>

			<tr class="article_brand_<?=$r->brand_clear?> article_number_<?=$r->number_clear?>">
				<td class="article_number">
					<? if (!isset($r->is_prices_article) or $r->is_number_matches): ?>
						<i class="icon-star" title="<?=lang('jb_search_tbl_foundbyid') ?>"></i> <strong><?=$r->number ?></strong>
					<? else: ?>
						<abbr title="<?=lang('jb_search_tbl_foundbycr') ?>"><?=$r->number ?></abbr>
					<? endif ?>
					</td>
				<td><?=$r->brand ?></td>
				<td class="hidden-xs hidden-sm">
					<? if ($r->ARL_KIND == 3): ?>
						<span class="label label-success" title="<?=lang('jb_search_tbl_genuine_lbl') ?>"><?=lang('jb_search_tbl_genuine') ?></span>
					<? elseif (!isset($r->is_prices_article) or $r->is_number_matches): ?>
						<span class="label label-info" title="<?=lang('jb_search_tbl_requested_lbl') ?>"><?=lang('jb_search_tbl_requested') ?></span>
					<? else: ?>
						<span class="label label-info"><?=lang('jb_search_tbl_cross') ?></span>
					<? endif ?>
				</td>
				<td><?=$r->name ?></td>
				<td class="table-buttons table-row-actions">
					<a href="<?=site_url( "autopart/{$r->brand_clear}/{$r->number_clear}" )?>" class="btn btn-small btn-sm btn-primary"><i class="icon icon-plus icon-white fa fa-plus"></i> <?=$more_btn_text ?></a>
				</td>
			</tr>
			
		<? endforeach ?>
		</tbody>
		</table>
	</div>

<? endif ?>