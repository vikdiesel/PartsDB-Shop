<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/options') ?>" class="btn"><i class="icon-wrench"></i> Все настройки</a></div>
	<h1>Замена шапки</h1>
</div>

<? if (_cfg('head_image_filename')): ?>
	<p class="thumbnail">
		<img src="/e/files/_headers/<?=_cfg('head_image_filename')?>">
	</p>
	<p><a href="<?=site_url('admin/options_headimage_delete')?>" class="btn"><i class="icon-trash"></i> Удалить изображение</a><span class="help-inline">При нажатии будет восстановлено изображение по умолчанию</span></p>
	<p>&nbsp;</p>
	<h2 id="upload_point">Загрузка нового изображения</h2>

<? endif ?>
<?=$errors?>
<?=$success?>
<p class="thumbnail-">
	<img src="/e/images/sample-head.jpg">
</p>
<p>&nbsp;</p>
<div class="well">
<h4>Рекомендации к файлу</h4>
<ul>
	<li>Ширина - 1240px</li>
	<li>Высота - 200px</li>
	<li>Формат - JPG, PNG</li>
	<li>Размер файла 100-200kb</li>
</ul>
</div>

<?=form_open_multipart('admin/options_headimage/do_upload#upload_point', array('class'=>'well form-inline')) ?>

	<input type="file" name="userfile" id="userfile">
	<button type="submit" class="btn btn-primary">Загрузить</button> <span class="help-inline">Рекомендуется не более <i>200kb</i></span>

</form>