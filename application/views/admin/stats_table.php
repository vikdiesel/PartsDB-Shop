<div class="page-header">
	<h1><?=lang('jb_stats_search_ttl') ?></h1>
</div>

<div class="alert alert-info"><i class="icon-info-sign"></i> <?=lang('jb_stats_search_txt') ?></div>

<table id="" class="table table-striped">
	<thead>
		<tr>
			<!--<th class="table-th-checkbox"><label class="btn btn-mini magic-checkbox"><input type="checkbox" class="order-all-lines-checkbox"></label></th>-->
			<th><?=lang('jb_stats_search_tbl_query') ?></th>
			<th><?=lang('jb_stats_search_tbl_datetime') ?></th>
		</tr>
	</thead>
	<tbody>
	<? foreach ($stats as $s): ?>
		<tr>
			<td><i class="fa fa-search"></i> <?=$s->q ?></td>
			<td><?=$s->date_r ?></td>
		</tr>
	<? endforeach ?>
	</tbody>
</table>