	<? if (($bgimg = _cfg('bgimage_filename')) !== FALSE): ?>
	<style type="text/css">
		body {background:url('<?=base_url()?>e/files/_backgrounds/<?=$bgimg?>'); <?=_cfg('bgimage_css')?>}
		<? if (_cfg('bgwrapper_transp')): ?>
			#sexywrapper {background:rgba(255,255,255,0.95);}
		<? endif ?>
	</style>
	<? endif ?>