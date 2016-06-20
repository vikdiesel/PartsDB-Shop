<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/options') ?>" class="btn"><i class="icon-wrench"></i> Все настройки</a></div>
	<h1>Параметры фона</h1>
</div>

<?=$errors?>
<?=$success?>

<?=form_open('', array('class'=>'form-horizontal')) ?>
	
	<fieldset>
		<div class="control-group">
			<label class="control-label">Фон подложки</label>
			<div class="controls">
				<? $x = 0; ?>
				<label class="radio">
				  <input type="radio" name="bgwrapper_transp" value="0" <?=setradio('0', (string) $form['bgwrapper_transp'], 'bgwrapper_transp', $x) ?>>
				  Фон подложки монолитно белый
				</label>
				<? $x++; ?>
				<label class="radio">
				  <input type="radio" name="bgwrapper_transp" value="1" <?=setradio('1', (string) $form['bgwrapper_transp'], 'bgwrapper_transp', $x) ?>>
				  Фон подложки с легкой прозрачностью
				</label>
				<span class="help-block">
					<strong>Эта функция</strong> позволяет задать <abbr title="Используется технология CSS3 (она не работает в старых браузерах и для них подложка останется белой)">легкую прозрачность</abbr> для подложки сайта.<br>
					Великолепно работает с фонами, которые не нарушают читабельность!<br>
					Попробуйте! В любой момент можно вернуть назад :-)
				</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="bgimage_css">CSS-стили фона</label>
			<div class="controls">
				<input class="input-xxlarge" id="bgimage_css" name="bgimage_css" type="text" value="<?=setval('bgimage_css', '', $form); ?>" placeholder="background-repeat:no-repeat; background-position:top left;">
				<span class="help-block">Вы можете задать произвольные стили для фона<br>
				<em>Например: background-repeat:no-repeat; background-position:top left;</em>
				</span>
			</div>
		</div>
	</fieldset>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Сохранить">&nbsp;<a href="<?=site_url('admin/options_bgimage')?>" class="btn">Назад</a>
	</div>
</form>