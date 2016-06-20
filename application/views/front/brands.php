<a name="catalogue"></a>
<div id="twof-cats">
	<div class="page-header page-header-centered">

		<h2>Общий каталог <small>Подбор по общему каталогу</small></h2>

	</div>

	<p>
	<? foreach ($brands_with_images as $brand): ?><a href="<?=site_url( "find/{$brand->id}" ) ?>" class="btn btn-default btn-large cat_btn ttp" title="<?=$brand->name ?>"><img src="<?=$brand->image_url ?>"></a><? endforeach ?>
	</p>

	<div class="well well-sm form-search" id="mfgfilter">
		<div class="input-append input-group">
			<input type="text" class="input-xlarge form-control search-query mfgfilter" placeholder="<?=lang('jb_filter_helpline')?>">
			<span class="input-group-btn">
				<button type="submit" class="btn btn-default"><?=lang('jb_filter')?></button>
			</span>
		</div>
	</div>

	<? $first_letter = FALSE ?>
	<? $x_col = 1 ?>
	<? $x_brand = 1 ?>

	<? foreach ($brands as $brand): ?>
		<? if ($x_brand > $brands_per_col and $brand->first_letter != $first_letter): $x_brand = 1; $x_col++; $tag_open = FALSE; ?>
			</ul></div>
		<? endif ?>
		<? if ($x_brand == 1): ?>
			<div class="mfgcol mfgcol_<?=$x_col?>"><ul class="nav nav-list">
			<? $tag_open = TRUE ?>
		<? endif ?>
		<? if ($brand->first_letter != $first_letter): $first_letter = $brand->first_letter; ?>
			<li class="nav-header mfg-navheader"><?=$brand->first_letter ?></li>
		<? endif ?>
		<li class="mfg_<?=$brand->name_clear ?> auto_manufacturer"><a href="<?=site_url( "find/{$brand->id}" ) ?>" title="<?=$brand->name ?>"><?=$brand->name ?></a></li>
		<? $x_brand++ ?>
	<? endforeach ?>

	<? if ($tag_open): ?>
		</ul></div>
	<? endif ?>

	<div class="clearfix"></div>

	<? if ($is_extcats_present): ?>
		<p>&nbsp;</p>
		<div class="alert alert-info">
			<i class="icon-info-sign"></i> <strong>Здесь показаны общие каталоги</strong>. Можно посмотреть также <a href="<?=site_url('auto/genuine')?>">оригинальные каталоги</a>.
		</div>
	<? endif ?>
</div>
