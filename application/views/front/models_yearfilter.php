<div class="alert alert-block alert-info year_filter_container" id="year_filter_top">
	<strong class="yr_filter_title"><?=lang('jb_models_yrfltr_ttl')?>:</strong>
	<a href="#all-years" class="year_filter year_filter_selected" rel="all" title="<?=lang('jb_models_yrfltr_showall_lbl')?>"><?=lang('jb_models_yrfltr_showall_lnk')?></a>
	<? for ($x=$year_min; $x<=$year_max; $x++): ?>
		<a href="#<?=$x?>" class="year_filter" rel="<?=$x?>" title="<?=sprintf(lang('jb_models_yrfltr_lnk_lbl'), $x)?>"><?=$x?></a>
	<? endfor ?>
</div>