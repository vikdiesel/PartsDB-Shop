<? if (_cfg('head_image_filename')): ?>
<div id="the_head" class="container_fluid">
	<img src="/e/files/_headers/<?=_cfg('head_image_filename')?>">
</div>
<? else: ?>
<div id="the_head">
	<div id="head_caption">
		<h1><?=_jb_sitedata('title')?></h1>
		<h3><?=_jb_sitedata('subtitle')?></h3>
	</div>
	<img src="/e/images/head.jpg">
</div>
<? endif ?>