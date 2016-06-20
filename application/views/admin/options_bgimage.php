<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/options') ?>" class="btn"><i class="icon-wrench"></i> Все настройки</a></div>
	<h1>Замена фона</h1>
</div>

<? if (_cfg('bgimage_filename')): ?>
	<p class="thumbnail">
		<img src="/e/files/_backgrounds/<?=_cfg('bgimage_filename')?>">
	</p>
	<p><a href="<?=site_url('admin/options_bgimage_params')?>" class="btn"><i class="icon-fullscreen"></i> Редактировать СSS фона</a> 
	<a href="<?=site_url('admin/options_bgimage_delete')?>" class="btn"><i class="icon-trash"></i> Удалить изображение</a>
	<span class="help-inline">При удалении будет восстановлено изображение по умолчанию</span></p>
	<p>&nbsp;</p>
	<h2 id="upload_point">Загрузка нового изображения</h2>

<? endif ?>
<?=$errors?>
<?=$success?>
<div class="well">
<h4>Рекомендации к файлу</h4>
<ul>
	<li>Ширина - <i>не более 1240px</i></li>
	<li>Высота - <i>не более 1240px</i></li>
	<li>Формат - <i>JPG, PNG</i></li>
	<li>Размер файла <i>5-100 Кб, максимум 512 Кб</i></li>
</ul>
</div>

<?=form_open_multipart('admin/options_bgimage/do_upload#upload_point', array('class'=>'well form-inline')) ?>

	<input type="file" name="userfile" id="userfile">
	<button type="submit" class="btn btn-primary">Загрузить</button>

</form>